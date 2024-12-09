<?php
namespace Moowgly\Pilote\Controller;

$_SERVER['PROJECT_SILO_URL'] = 'http://localhost/ws-moowgly';
// Define application path
define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../..'));
require APPLICATION_PATH . '/vendor/autoload.php';
include APPLICATION_PATH . '/batch/conf/' . $argv[1] . '/conf.php';

use Moowgly\Lib\Utils\UUID;
use Moowgly\Lib\Utils\XslUtil;
use Moowgly\Pilote\Model\Reference;

function main()
{
	echo "\n*************************************************************************************************\n";
	echo "*** Ajout role CDS / Phase Forfait /  Niveaux 1 ***\n";
	echo "***************************************************************************************************\n\n";
	echo "BEGIN Program\n\n";
			
	// Récupére les types des projets contenus dans la liste "type_projet_ss" de reference
	$itemXML = Reference::getReferences('', '', 'id,type_projet_ss,sous_category_s');
	
	$nodes = XslUtil::findNodes($itemXML, "/a:feed/a:entry[a:span[@class='sous_category_s']='niveaux']");
	if ($nodes->length > 0){
		echo "Des donnees de niveaux 1 existantes déjà dans Solr !\n";
	} else {
		$nodes = XslUtil::findNodes($itemXML, "/a:feed/a:entry/a:ul[@class='type_projet_ss']/a:li");
		if ($nodes->length > 0){
			$id = XslUtil::findFirstNodeValue($itemXML, "/a:feed/a:entry[a:ul[@class='type_projet_ss']]/a:span[@class='id']");
			if (strlen(trim($id)) > 0){
				// Ajouter le role CDS dans reference
				$parameters = array ();
				$parameters ['id'] = $id;
				$roleId = UUID::uuid();
				$parameters ['liste_roles'] [$roleId] = 'CDS';
				$parameters ['liste_id_phases'] ['FOR'] = 'FOR';
				$parameters ['liste_nom_phases'] ['FOR'] = 'Forfait';
				$parameters ['liste_couleur_phases'] ['FOR'] = 'blue';
				$parameters ['liste_actif_phases'] ['FOR'] = '1';
				
				$response = Reference::putReference ( $parameters );
				echo "\n************************* Ajouter la phase Forfait et le role CDS *************************\n";
				if (!is_object($response) || !in_array($response->getStatusCode(), [201,200])) {
					echo "Erreur lors de l'ajout Forfait/CDS\n";
				} else {
					echo "++ ajouter la phase «Forfait» et le role «CDS»\n";
				}
				
				echo "\n************************* Ajouter les niveaux 1 *************************\n";
				// Ajouter les niveaux 1
				foreach ($nodes as $node){
					$niveau1 = XslUtil::getNodeValue(XslUtil::findOneNode($itemXML, ".", $node, 0));
					if (strlen(trim($niveau1)) > 0){
						$niveauId = UUID::uuid();
						$parameters = array ();
						$parameters ['id'] = $id;
						$parameters ['liste_actifs_niveaux'] [$niveauId] = '1';
						$parameters ['liste_noms_niveaux'] [$niveauId] = $niveau1;
						$response = Reference::putReference ( $parameters );
						if (!is_object($response) || !in_array($response->getStatusCode(), [201,200])) {
							echo "Erreur niveau 1 : " . $niveau1 . "\n";
						} else {
							echo "++ copie le niveau 1 : " . $niveau1 . "\n";
						}
					}
				}	
			}
		}
	}

	echo "\nEND Program\n";
	
}

main();
