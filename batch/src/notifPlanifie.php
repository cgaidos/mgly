<?php
namespace Moowgly\Pilote\Controller;

$_SERVER['PROJECT_SILO_URL'] = 'http://localhost/ws-moowgly';
// Define application path
define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../..'));
require APPLICATION_PATH . '/vendor/autoload.php';
include APPLICATION_PATH . '/batch/conf/' . $argv[1] . '/conf.php';
include APPLICATION_PATH . '/pilote/src/Pilote/Controller/PlanningProjetController.php';

use Moowgly\Lib\Utils\XslUtil;
use Moowgly\Pilote\Model\Common;
use Moowgly\Pilote\Model\Projet;
use Moowgly\Pilote\Model\Notif;
use Moowgly\Pilote\Model\Pointage;
use Moowgly\Pilote\Model\Staffing;
use Moowgly\Pilote\Model\Livrable;
use Moowgly\Pilote\Model\Boite;
use Moowgly\Pilote\Model\Collaborateur;

function main()
{
	$jour = date ('N');
	$heure = date ('H');

	switch ($heure){
		case '10':
			// vérifie l'échéance du livrable et MEP tous les jour à 10H 
			$paramsLivrable = getParamsLivrable ();
			foreach ($paramsLivrable as $parameters){
				Notif::postNotification ($parameters);
			}
			break;
		case '14':
			switch ($jour){
				case '4':
					// Chaque jeudi à 14H
					$paramsStaffing = getParamsStaffing ();
					foreach ($paramsStaffing as $parameters){
						Notif::postNotification ($parameters);
					}
					break;
				case '5':
					// Chaque vendredi à 14H
					$paramsPointage = getParamsPointage ();
					foreach ($paramsPointage as $parameters){
						Notif::postNotification ($parameters);
					}
					break;
			}
			
			// Dernier jour du mois à 14H
			if (date('t') == date('d')){
				$paramsPointage = getParamsValidPointage ();
				foreach ($paramsPointage as $parameters){
					Notif::postNotification ($parameters);
				}
			}
			break;
	}
	
	// Notif. projets en retard
	$paramsProjet = getParamsPlanning ();
	foreach ($paramsProjet as $parameters){
		Notif::postNotification ($parameters);
	}
	
}

/**
 * Paramètres notif. «Projet en retard»
 * @return string[][]
 */
function getParamsPlanning ()
{
	$paramsNotif = array();
	$parameters = array();
	$parameters ['id'] = 'new';
	$parameters ['type'] = 'planning';
	$parameters ['lu'] = '0';
	$parameters ['date_creation'] = Common::getDateSolr();
	
	// cherche les projets en retard
	$planningController = new PlanningProjetController();
	$q = 'etat_s:"actif"';
	$itemXML = Projet::getProjets ($q);
	$nodes = XslUtil::findNodes ( $itemXML, '/a:feed/a:entry' );
	foreach ($nodes as $node) {
		$projet_id = XslUtil::getNodeValue(XslUtil::findOneNode($itemXML, "a:span[@class='projet_id_s']", $node, 0));
		$nom = XslUtil::getNodeValue(XslUtil::findOneNode($itemXML, "a:span[@class='nom_s']", $node, 0));
		// Différentiel consolidé du projet
		$arrPlanning = $planningController->getPlanning( $projet_id );
		$projetDiff = $planningController->calcDiffPhase($arrPlanning['arrLivDiff'], $arrPlanning['arrPhases'], true);
		// Vérifie si la notif. n'a pas été encore envoyée
		$niveau = 0;
		if ($projetDiff > 30){			
			$niveau = 30;
		} elseif ($projetDiff > 20){
			$niveau = 20;
		} elseif ($projetDiff > 10){
			$niveau = 10;
		}
		
		if ($niveau > 0){
			$q = 'niveau_s:"' . $niveau . '" AND projet_id_s:"' . $projet_id . '"';
			$itemNotifXML = Notif::getNotifications ($q);
			$total = XslUtil::findFirstNodeValue($itemNotifXML, '/a:feed/opensearch:totalResults');
			if ($total == 0){
				$parameters ['niveau'] = $niveau;
				$parameters ['projet_id'] = $projet_id;
				$parameters ['nom_uri'] = '/moowgly/planning-projet/' . $projet_id;
				$parameters ['nom'] = 'Le projet «' . $nom . '» a un retard de ' . $projetDiff . ' jours';
				$parameters ['profil'] = 'administrateur';
				$paramsNotif [] = $parameters;
				$parameters ['profil'] = 'ceo';
				$paramsNotif [] = $parameters;
			}
		}
		
		return $paramsNotif;		
	}
	
}

/**
 * Prépare les paramètres pour les notifications de Livrable
 * @return string[]
 */
function getParamsLivrable ()
{
	$paramsNotif = $parameters = array();
	$parameters ['id'] = 'new';
	$parameters ['type'] = 'planning';
	$parameters ['lu'] = '0';
	$parameters ['date_creation'] = Common::getDateSolr();
	$parameters ['nom_uri'] = '/moowgly/planning-global/';
	
	$q = 'liv_date_fin_theo_s:' . date("Y-m-d") . '*';
	$itemXML = Livrable::getLivrables ($q, '', 'liv_type_s,liv_nom_s,projet_id_s');
	$nodes = XslUtil::findNodes ( $itemXML, '/a:feed/a:entry' );
	foreach ($nodes as $node) {
		$liv_nom = XslUtil::getNodeValue(XslUtil::findOneNode($itemXML, "a:span[@class='liv_nom_s']", $node, 0));
		$liv_type = XslUtil::getNodeValue(XslUtil::findOneNode($itemXML, "a:span[@class='liv_type_s']", $node, 0));
		$projet_id = XslUtil::getNodeValue(XslUtil::findOneNode($itemXML, "a:span[@class='projet_id_s']", $node, 0));
		
		$q = 'projet_id_s:"' . $projet_id . '"';
		$itemProjetXML = Projet::getProjets ($q, '', 'chef_id_s,nom_s');
		$total = XslUtil::findFirstNodeValue($itemProjetXML, '/a:feed/opensearch:totalResults');
		if ($total > 0){
			$parameters ['collab_id'] = XslUtil::findFirstNodeValue($itemProjetXML, "/a:feed/a:entry/a:span[@class='chef_id_s']");
			$projet_nom = XslUtil::findFirstNodeValue($itemProjetXML, "/a:feed/a:entry/a:span[@class='nom_s']");
			switch ($liv_type){
				case 'LIV':
					$parameters ['nom'] = "Le livrable «" . $liv_nom . "» du projet «" . $projet_nom . "» est dû aujourd'hui";
					break;
				case 'MEP':
					$parameters ['nom'] = "La mise en prod. «" . $liv_nom . "» du projet «" . $projet_nom . "» est dû aujourd'hui";
					break;
			}
			$paramsNotif [] = $parameters;
		}
	}
	
	return $paramsNotif;
}

/**
 * Prépare les paramètres pour la notification «Rappel de validation»
 * @return string[]
 */
function getParamsValidPointage ()
{
	$paramsNotif = $parameters = array();
	$parameters ['id'] = 'new';
	$parameters ['type'] = 'pointage';
	$parameters ['lu'] = '0';
	$parameters ['date_creation'] = Common::getDateSolr();
	$parameters ['nom'] = "Le pointage du mois va être bloqué. Veuillez vous assurer que vous avez bien pointé sur toute la période";
	$parameters ['nom_uri'] = '/moowgly/pointage/';
	$start = date ('Y\WW', strtotime( date ('Y-m-01') ));
	$q = 'semaine_s:["' . $start . '" TO "' . date("Y\WW") . '"] AND nature_s:"Affectation"';	
	$itemXML = Staffing::getStaffing ($q, '', '', 'collab_id_s', ['max-results' => 0]);
	$nodes = XslUtil::findNodes ( $itemXML, '/a:feed/x:facetResult/result/item[@count > 0]' );
	foreach ($nodes as $node) {
		$parameters ['collab_id'] = XslUtil::getNodeValue(XslUtil::findOneNode($itemXML, ".", $node, 0));
		$paramsNotif [] = $parameters;
	}

	return $paramsNotif;
}

/**
 * Prépare les paramètres pour la notification «Rappel pointage»
 * @return string[]
 */
function getParamsPointage ()
{
	$paramsNotif = $parameters = array();
	$parameters ['id'] = 'new';
	$parameters ['type'] = 'pointage';
	$parameters ['lu'] = '0';
	$parameters ['date_creation'] = Common::getDateSolr();
	$parameters ['nom'] = "N'oubliez pas de saisir votre pointage de la semaine";
	$parameters ['nom_uri'] = '/moowgly/pointage/';
	
	$q = 'semaine_s:"' . date("Y\WW") . '" AND nature_s:("Affectation" "Demande")';
	$itemXML = Staffing::getStaffing ($q, '', 'collab_id_s,duree_hebdo_f', 'collab_id_s');
	$nodes = XslUtil::findNodes ( $itemXML, '/a:feed/x:facetResult/result/item[@count > 0]' );
	foreach ($nodes as $node) {
		$collab_id = XslUtil::getNodeValue(XslUtil::findOneNode($itemXML, ".", $node, 0));
		// vérifier le profil de collaborateur
		$q = 'collab_id_s:"' . $collab_id . '"';
		$itemCollabXML = Collaborateur::getContributeurs ($q, '', 'role_s');
		$total = XslUtil::findFirstNodeValue($itemCollabXML, '/a:feed/opensearch:totalResults');
		if ($total > 0){
			$role = XslUtil::findFirstNodeValue($itemCollabXML, "/a:feed/a:entry/a:span[@class='role_s']");
			if(stristr($role, 'contributeur') || stristr($role, 'responsable de projet') || stristr($role, 'manager')) {
				// nombre de pointage de collaborateur pour la semaine en cours
				$date = new \DateTime();
				$start = $date->format ( 'Y-m-d\T00:00:00\Z' );
				$date->add(new \DateInterval('P4D'));
				$end = $date->format ( 'Y-m-d\T23:59:59\Z' );
				$q = 'collab_id_s:"' . $collab_id . '" AND journee_dt:[' . $start . ' TO ' . $end . ']';
				$itemPointageXML = Pointage::getPointage($q);
				$total_pointage = (XslUtil::findFirstNodeValue($itemPointageXML, '/a:feed/opensearch:totalResults')) / 4;
				// Vérifie si le contributeur a pointé moins de 5 jours
				if ($total_pointage < 5){
					$parameters ['collab_id'] = $collab_id;
					$paramsNotif [] = $parameters;
				}
			}
		}		
	}
	
	return $paramsNotif;
	
}

/**
 * Prépare les paramètres pour la notification «Demandes non affectées»
 * @return string[][]
 */
function getParamsStaffing ()
{
	$paramsNotif = array();
	$parameters = array();
	$parameters ['id'] = 'new';
	$parameters ['type'] = 'staffing';
	$parameters ['lu'] = '0';
	$parameters ['date_creation'] = Common::getDateSolr();
	
	// Cherche le staffing prévisionnel pour la semaine à venir
	$staffingBoite = $staffingProjet = array ();
	$week_next = date("Y\WW", mktime(0, 0, 0, date('n'), date('j')+8, date('Y')) - ((date('N'))*3600*24));
	$q = 'semaine_s:"' . $week_next . '" AND nature_s:("Affectation" "Demande")';
	$itemXML = Staffing::getStaffing ($q, '', 'nature_s,duree_hebdo_f,projet_id_s,sous_boite_id_s');
	$nodes = XslUtil::findNodes($itemXML, "/a:feed/a:entry");
	foreach ($nodes as $node) {
		$duree_hebdo = XslUtil::getNodeValue(XslUtil::findOneNode($itemXML, "a:span[@class='duree_hebdo_f']", $node, 0));
		$nature = XslUtil::getNodeValue(XslUtil::findOneNode($itemXML, "a:span[@class='nature_s']", $node, 0));
		$projet_id = XslUtil::getNodeValue(XslUtil::findOneNode($itemXML, "a:span[@class='projet_id_s']", $node, 0));
		$sous_boite_id = XslUtil::getNodeValue(XslUtil::findOneNode($itemXML, "a:span[@class='sous_boite_id_s']", $node, 0));
		$staffingBoite [$sous_boite_id][$nature][] = $duree_hebdo;
		$staffingProjet [$projet_id][$nature][] = $duree_hebdo;
	}
	
	// Prépare les paramètres de la notif. pour de profil manager 
	foreach ($staffingBoite as $sous_boite_id => $array){
		if (isset ($array['Demande'])){
			$total_demande = array_sum($array['Demande']);
			$total_affectation = 0;
			if (isset ($array['Affectation'])){
				$total_affectation = array_sum($array['Affectation']);
			}
			// Calcul le nombre de jours qui reste à affecter pour la semaine prohaine
			$diff = $total_demande - $total_affectation;
			if ($diff > 0){
				$q = 'id:"' . $sous_boite_id . '"';
				$itemXML = Boite::getSousBoites ($q, '', 'chef_sous_boite_s');
				$total = XslUtil::findFirstNodeValue($itemXML, '/a:feed/opensearch:totalResults');
				if ($total > 0){
					$chef_id = XslUtil::findFirstNodeValue($itemXML, "/a:feed/a:entry/a:span[@class='chef_sous_boite_s']");
					if (strlen($chef_id) > 0){
						$parameters ['collab_id'] = $chef_id;
						$parameters ['nom_uri'] = '/moowgly/staffing-manager/';
						$parameters ['nom'] = "Il vous reste " . $diff . " jours à affecter pour la semaine à venir";
						$paramsNotif [] = $parameters;
					}
				}
			}
		}
	}
	
	// Prépare les paramètres de la notif. pour de profil RDP
	foreach ($staffingProjet as $projet_id => $array){
		if (isset ($array['Demande'])){
			$total_demande = array_sum($array['Demande']);
			$total_affectation = 0;
			if (isset ($array['Affectation'])){
				$total_affectation = array_sum($array['Affectation']);
			}
			$diff = $total_demande - $total_affectation;
			if ($diff > 0){
				$q = 'projet_id_s:"' . $projet_id . '"';
				$itemXML = Projet::getProjets ($q, '', 'chef_id_s,nom_s');
				$total = XslUtil::findFirstNodeValue($itemXML, '/a:feed/opensearch:totalResults');
				if ($total > 0){
					$parameters ['collab_id'] = XslUtil::findFirstNodeValue($itemXML, "/a:feed/a:entry/a:span[@class='chef_id_s']");
					$nom = XslUtil::findFirstNodeValue($itemXML, "/a:feed/a:entry/a:span[@class='nom_s']");
					$parameters ['nom_uri'] = '/moowgly/staffing-projet/' . $projet_id;
					$parameters ['nom'] = "Vous avez " . $diff . " jours non affectés pour la semaine à venir sur le projet «" . $nom . "»";
					$paramsNotif [] = $parameters;
				}				
			}
		}
	}

	return $paramsNotif;
}


main();
