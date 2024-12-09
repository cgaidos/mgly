<?php

// Define application path
define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../..'));
require APPLICATION_PATH . '/vendor/autoload.php';
include APPLICATION_PATH . '/batch/conf/' . $argv[1] . '/conf.php';

use Moowgly\Lib\Utils\ClientSilo;
use Moowgly\Lib\Utils\UUID;

///////////////////////////////
// php insertData.php local //
/////////////////////////////
//ini_set('memory_limit', '-1');
// Fonctions utilitaires
function cFrenchDate($fDate)
{
    if (strlen(trim($fDate)) == 0) {
        return $fDate;
    } else {
        $fArray = explode('/', $fDate);
        return $fArray[2] . '-' . $fArray[1] . '-' . $fArray[0];
    }
}
function cAmpersand($param)
{
    foreach ($param as $key => &$value) {
        $value = str_replace('&', '&amp;', $value);
    }
    return $param;
}
////////////////////////////////////////////////////////
// collaborateurs
///////////////////////////////////////////////////////
function insertCollaborateur()
{
	$boites = array(
			"Support, Corporate et Marketing" => "boite001",
			"Intégration et Services" => "boite002",
			"Pilotage Opérationnel" => "boite003",
			"Marchés, Clients et Assurances" => "boite004",
			"Normes, Innovation et Méthodes" => "boite005",
			"DSI" => "boite006",
			"Contrôle permanent" => "boite007"
	);
	
    $collaborateurFile = fopen( APPLICATION_PATH . '/batch/data/importCollaborateurUTF8.csv', 'r');
    if (!$collaborateurFile) {
        echo "erreur: problème à l'ouverture du fichier" . PHP_EOL;
        return;
    }
    $boolFirstLine = true;
//     $collaborateurPrev = '';
    echo "Start import collaborateurs" . PHP_EOL;
    $client = ClientSilo::getInstance ();
    $uuid = new UUID ();
    while($collaborateurCSV = fgetcsv($collaborateurFile, 0, ';')) {
        if (!$boolFirstLine) {
//             if ($collaborateurCSV[0] != $collaborateurPrev) {
                // On a changé de collaborateur
//                 if ($collaborateurPrev != '') {
//                     $url = PROJECT_SILO_URL . '/collaborateur/v1/';
//                     $response = $client->post ( $url, [
//                             'headers' => [
//                                     'Content-Type' => 'application/x-www-form-urlencoded'
//                             ],
//                             'form_params' => $parameters
//                     ] );
//                     echo $parameters ['id'] . ' - status code: ' . $response->getStatusCode () . PHP_EOL;
//                 }
				
                $parameters = array ();
                if(!empty($collaborateurCSV[0])) $parameters ['id'] = $parameters ['collab_id'] = $collaborateurCSV[0];
                if(!empty($collaborateurCSV[1])) $parameters ['nom'] = $collaborateurCSV[1];
                if(!empty($collaborateurCSV[2])) $parameters ['prenom'] = $collaborateurCSV[2];
                if(!empty($collaborateurCSV[3])) $parameters ['email'] = $collaborateurCSV[3];
                if(!empty($collaborateurCSV[0])) $parameters ['actif'] = '1';
                if(!empty($collaborateurCSV[4]) && array_key_exists($collaborateurCSV[4], $boites)) {
                	$parameters ['boite_id_uri'] = '/boite/v1/' . $uuid->md5touuid(md5($boites[$collaborateurCSV[4]]));
                	$parameters ['boite_id'] = $boites[$collaborateurCSV[4]];
                }
                if(!empty($collaborateurCSV[5])) $parameters ['sous_boite']  = $collaborateurCSV[5];
                if(!empty($collaborateurCSV[6])){
                	if($collaborateurCSV[6] === 'M' ){
                		$parameters ['role'] = 'Manager interne';
                	}else{
                		$parameters ['role'] = 'Contributeur interne';
                	}
                }else{
                	$parameters ['role'] = 'Contributeur interne';
                }

                $url = PROJECT_SILO_URL . '/collaborateur/v1/';
                $response = $client->post ( $url, [
                		'headers' => [
                				'Content-Type' => 'application/x-www-form-urlencoded'
                		],
                		'form_params' => $parameters
                ] );
                
//                echo 'parameters : ' . json_encode($parameters);
                echo $parameters ['id'] . ' - status code: ' . $response->getStatusCode () . PHP_EOL;
                error_log( $parameters ['id'] . ' - status code: ' . $response->getStatusCode () . PHP_EOL );
                

//                 $parameters ['cout_journalier'] = $collaborateurCSV[4];
//                 $parameters ['collab_type'] = $collaborateurCSV[5];
//                 $parameters ['role'] = $collaborateurCSV[6];
//                 $parameters ['boite_id_uri'] = '/boite/v1/' . $uuid->md5touuid ( md5 ($collaborateurCSV[7]) );
//                 $parameters ['boite_id'] = $collaborateurCSV[7];
//                 $parameters ['liste_competences'] [$collaborateurCSV[9]] = $collaborateurCSV[9];
//             } else {
//                 $parameters ['liste_competences'] [$collaborateurCSV[9]] = $collaborateurCSV[9];
//             }
//             $collaborateurPrev = $parameters ['id'];
        }
        $boolFirstLine = false;
    }

    fclose($collaborateurFile);
    echo "End import collaborateurs" . PHP_EOL;
}
////////////////////////////////////////////////////////
// projets
///////////////////////////////////////////////////////
function insertProjet()
{
    $projetFile = fopen('../data/projetUTF8.csv', 'r');
    if (!$projetFile) {
        echo "erreur: problème à l'ouverture du fichier" . PHP_EOL;
        return;
    }
    $phases = array(
        "ETU" => "Etudes",
        "LAN" => "Lancement",
        "DEF" => "Définition",
        "CTR" => "Construction",
        "VAL" => "Validation",
        "MES" => "Mise en service",
        "RUN" => "Run"
    );
    $boolFirstLine = true;
    $projetPrev = '';
    echo "Start import projets" . PHP_EOL;
    $client = ClientSilo::getInstance ();
    $uuid = new UUID ();
    while($projetCSV = fgetcsv($projetFile, 0, ';')) {
        if (!$boolFirstLine) {
            if ($projetCSV[3] != $projetPrev) {
                // On a changé de projet
                if ($projetPrev != '') {
                    $parameters = cAmpersand($parameters);
                    $url = PROJECT_SILO_URL . '/projet/v1/';
                    $response = $client->post ( $url, [
                            'headers' => [
                                    'Content-Type' => 'application/x-www-form-urlencoded'
                            ],
                            'form_params' => $parameters
                    ] );
                    echo $parameters ['id'] . ' - status code: ' . $response->getStatusCode () . PHP_EOL;
                }
                $parameters = array ();
                $parameters ['id'] = $projetCSV[3];
                $parameters ['nom'] = $projetCSV[4];
                $parameters ['date_debut'] = cFrenchDate($projetCSV[7]);
                $parameters ['date_fin'] = cFrenchDate($projetCSV[8]);
                $parameters ['chef_id'] = $projetCSV[5];
                $parameters ['chef_id_uri'] = '/collaborateur/v1/' . $uuid->md5touuid(md5($parameters['chef_id']));
                $parameters ['etat'] = 'actif';
                $parameters ['metier'] = $projetCSV[9];
                $parameters ['type'] = $projetCSV[10];
                // $parameters ['description'] = 'Nec piget dicere avide magis hanc insulam populum';
                $parameters ['sponsor'] = $projetCSV[6];
                // Phases
                $phasePrev = $projetCSV[11];
                $parameters ['liste_noms_phases'] [$projetCSV[11]] = $phases[$projetCSV[11]];
                $parameters ['liste_valid_phases'] [$projetCSV[11]] = $projetCSV[13] == 'Oui' ? '1' : '0';
                $parameters ['liste_date_debut_theo_phases'] [$projetCSV[11]] = cFrenchDate($projetCSV[12]);
                $parameters ['liste_date_debut_reelle_phases'] [$projetCSV[11]] = cFrenchDate($projetCSV[12]);
                $parameters ['liste_date_fin_theo_phases'] [$projetCSV[11]] = cFrenchDate($projetCSV[14]);
                $parameters ['liste_date_fin_reelle_phases'] [$projetCSV[11]] = cFrenchDate($projetCSV[14]);
                $parameters ['liste_budget_init_phases'] [$projetCSV[11]] = $projetCSV[15];
                // Budget prévisionnel et révisé provisoire
                $parameters ['liste_budget_prev_phases'] [$projetCSV[11]] = $projetCSV[15];
                $parameters ['liste_budget_revise_phases'] [$projetCSV[11]] = $projetCSV[15];
                $parameters ['liste_budget_consomme_phases'] [$projetCSV[11]] = $projetCSV[16];
            } else {
                if ($projetCSV[11] != $phasePrev) {
                    // On a changé de phase
                    $parameters ['liste_noms_phases'] [$projetCSV[11]] = $phases[$projetCSV[11]];
                    $parameters ['liste_valid_phases'] [$projetCSV[11]] = $projetCSV[13] == 'Oui' ? '1' : '0';
                    $parameters ['liste_date_debut_theo_phases'] [$projetCSV[11]] = cFrenchDate($projetCSV[12]);
                    $parameters ['liste_date_debut_reelle_phases'] [$projetCSV[11]] = cFrenchDate($projetCSV[12]);
                    $parameters ['liste_date_fin_theo_phases'] [$projetCSV[11]] = cFrenchDate($projetCSV[14]);
                    $parameters ['liste_date_fin_reelle_phases'] [$projetCSV[11]] = cFrenchDate($projetCSV[14]);
                    $parameters ['liste_budget_init_phases'] [$projetCSV[11]] = $projetCSV[15];
                    // Budget prévisionnel et révisé provisoire
                    $parameters ['liste_budget_prev_phases'] [$projetCSV[11]] = $projetCSV[15];
                    $parameters ['liste_budget_revise_phases'] [$projetCSV[11]] = $projetCSV[15];
                    $parameters ['liste_budget_consomme_phases'] [$projetCSV[11]] = $projetCSV[16];
                    $phasePrev = $projetCSV[11];
                }
            }
            $projetPrev = $parameters ['id'];
        }
        $boolFirstLine = false;
    }
    $url = PROJECT_SILO_URL . '/projet/v1/';
    $response = $client->post ( $url, [
            'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded'
            ],
            'form_params' => $parameters
    ] );
    echo $parameters ['id'] . ' - status code: ' . $response->getStatusCode () . PHP_EOL;
    fclose($projetFile);
    echo "End import projets" . PHP_EOL;
}
// Insertion collaborateurs
insertCollaborateur();
// Insertion projets
//insertProjet();
