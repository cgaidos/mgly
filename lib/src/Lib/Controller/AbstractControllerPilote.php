<?php
namespace Moowgly\Lib\Controller;

use DOMDocument;
use Moowgly\Lib\Utils\DomUtil;
use Moowgly\Lib\Utils\XslUtil;
use Moowgly\Lib\Utils\ClientSilo;
use Moowgly\Lib\Controller\AbstractController;
use Moowgly\Pilote\Model\Common;
use Moowgly\Pilote\Model\UserConnected;
use Moowgly\Pilote\Model\Collaborateur;
use Moowgly\Pilote\Model\Notif;

abstract class AbstractControllerPilote extends AbstractController
{

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    public static function getAtom($id, $resource, $version)
    {
        // Call the client silo
        $client = ClientSilo::getInstance();
        // Return request result
        return $client->get($_SERVER['PROJECT_SILO_URL'] . '/v' . $version . '/' . $resource . '/' . $id);
    }

    public static function removeAtom($id, $resource, $version)
    {
        // Call the client silo
        $client = ClientSilo::getInstance();
        // Return request result
        return $client->delete($_SERVER['PROJECT_SILO_URL'] . '/v' . $version . '/' . $resource . '/' . $id);
    }

	/**
	 * Retourne le mail de l'utilisateur authentifié
	 * Mail à modifier lors du DEV
	 * @return string
	 */
    public static function getUserMail(){
    	$requestHeaders = apache_request_headers();
    	if(isset($requestHeaders['OIDC_CLAIM_email'])) {
    		$mail = strtolower($requestHeaders['OIDC_CLAIM_email']);
    	} elseif(isset($_SERVER["PROJECT_ENV"]) && $_SERVER["PROJECT_ENV"] == "dev") {
    	    $mail = 'cgaidos@eptamel.com';
    	} else {
    		$mail = null;
    	}
    	return (string)$mail;
    }

    public static function getXmlDocument($id, $resource, $version)
    {
        // Get resource Atom
        $atom = self::getAtom($id, $resource, $version);

        $documentXML = NULL;

        if ($atom) {
            $documentXML = new DOMDocument();
            $documentXML->loadXML($atom->getBody());
        }

        return $documentXML;
    }

    /**
     * Methode retounant les infomations de l'utilisateur connecté.
     * Par défaut getCollaborateurInfo retourne collab_id_s, family-name_s, given-name_s, role_s, email_s, competence_ss , cout_journalier_f et boite_id_s
     * @param array $fields Les champs de la ressource Collaborateur devant être retounés via SOLR
     * @return array $collaborateurInfos
     */
    public static function getCollaborateurInfo($fields = array('collab_id_s', 'family-name_s', 'given-name_s', 'role_s', 'email_s', 'competence_ss', 'cout_journalier_f', 'boite_id_s', 'sous_boite_id_s', 'collab_type_s')){
    	$collaborateurInfos = array();
    	$itemCollaborateurXML =  Collaborateur::getEmail(self::getUserMail(), array('-actif_s:"0"'));
    	if (!Common::solrResponseIsEmpty($itemCollaborateurXML)){
    		foreach ($fields as $field){
				switch ($field) {
					case 'competence_ss':
						$nodes = XslUtil::findNodes ( $itemCollaborateurXML, "/a:feed/a:entry/a:ul[@class='". $field ."']/a:li" );
						$competences = [];
						foreach ( $nodes as $node ) {
							$competences [] = XslUtil::getNodeValue ( XslUtil::findOneNode ( $itemCollaborateurXML, '.', $node, 0 ) );
						};
						foreach ($competences as $competence){
							$collaborateurInfos[$field][] = $competence;
						}
						break;
					default:
						$collaborateurInfos[$field] = XslUtil::findFirstNodeValue($itemCollaborateurXML, "/a:feed/a:entry/a:span[@class='". $field ."']");
						break;
				}
    		}
    	}
    	return $collaborateurInfos;
    }

    public static function getHeaderFeed($itemXML){
    	$u = UserConnected::getInstance();
    	$collabInfo = $u->getUser();
    	DomUtil::addElement($itemXML, "/a:feed/a:header/a:user/a:title", 'Informations de base de l\'utilisateur connecte');
    	DomUtil::addElement($itemXML, "/a:feed/a:header/a:user/a:span[@class='user_id']", $collabInfo['collab_id']);
    	DomUtil::addElement($itemXML, "/a:feed/a:header/a:user/a:span[@class='user_profil']", $collabInfo['profil']);
    	DomUtil::addElement($itemXML, "/a:feed/a:header/a:user/a:span[@class='user_nom_prenom']", $collabInfo['given_name'] . ' ' . $collabInfo['family_name']);
    	DomUtil::addElement($itemXML, "/a:feed/a:header/a:user/a:span[@class='user_sous_boite']", $collabInfo['sous_boite_id']);
    	// Ajouter les notifications dans le header
//     	$itemXML = self::getNotifications ($itemXML);
    	return $itemXML;
    }

    /**
     * Transform the document with XSL file
     *
     * @param DOMDocument $xml
     *            Dom of the XML file will be transformed.
     * @param string $xsl
     *            Name of the xsl file.
     * @param array $param
     *            Optionals parameters for the xsl file.
     */
    protected static function applyXslt($xml, $xsl, $param = array())
    {
        $pathXsl = APPLICATION_PATH . "/pilote/src/static/xsl/$xsl";
        return XslUtil::applyXSL($pathXsl, $xml, array_merge(array(
            'projectName' => $_SERVER['PROJECT_NAME'],
            'version' => VERSION_NUMBER
        ), $param));
    }

    protected static function getNotifications ($itemXML)
    {
    	DomUtil::addElement($itemXML, '/a:feed/a:notif/a:title', "Notifications de l'utilisateur");
    	$profil_id = XslUtil::findFirstNodeValue($itemXML, "/a:feed/a:header/a:user/a:span[@class='user_id']");
    	$profil = XslUtil::findFirstNodeValue($itemXML, "/a:feed/a:header/a:user/a:span[@class='user_profil']");

    	$client = ClientSilo::getInstance();
    	// Afficher les alertes pendant 5 jours max
    	$start = Common::getDateSolr(date('Y-m-d', strtotime('- 5 days')));
    	$end = Common::getDateSolr();
    	$q = 'notif_lu_s:0 AND date_creation_s:[' . $start . ' TO ' . $end . ']';
    	$notifXML = Notif::getNotifications($q, "", "notif_id_s,notif_nom_s,collab_id_s,profil_s,notif_uri_s");
    	// On va chercher les data mysql pour avoir les uri
    	$nodes = XslUtil::findNodes($notifXML, "/a:feed/a:entry");
    	foreach ($nodes as $node) {
    		$id = XslUtil::getNodeValue(XslUtil::findOneNode($notifXML, "a:span[@class='notif_id_s']", $node, 0));
    		$notifie_id = XslUtil::getNodeValue(XslUtil::findOneNode($notifXML, "a:span[@class='collab_id_s']", $node, 0));
    		$notifie_profil = XslUtil::getNodeValue(XslUtil::findOneNode($notifXML, "a:span[@class='profil_s']", $node, 0));
    		if ( ((strlen($notifie_profil) > 0) && ($notifie_profil != $profil))
    				|| ((strlen($notifie_id) > 0) && ($notifie_id != $profil_id)) ){
    			$node->parentNode->removeChild($node);
    		}
    	}
    	DomUtil::addAtomResourceByTag($itemXML, '/a:feed/a:notif', $notifXML);
    	return $itemXML;
    }

}