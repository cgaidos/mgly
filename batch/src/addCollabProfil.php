<?php
namespace Moowgly\Pilote\Controller;

$_SERVER['PROJECT_SILO_URL'] = 'http://localhost/ws-moowgly';
// Define application path
define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../..'));
require APPLICATION_PATH . '/vendor/autoload.php';
include APPLICATION_PATH . '/batch/conf/' . $argv[1] . '/conf.php';

use Moowgly\Lib\Utils\UUID;
use Moowgly\Pilote\Model\Collaborateur;

function addCollaborateursProfil()
{	
	// Administrateur
	$parameters = array();
	$parameters ['id'] = $parameters ['collab_id'] = 'SOF8001';
	$parameters ['nom'] = 'Admin';
	$parameters ['prenom'] = '';
	$parameters ['email'] = 'test001@ca-cf.fr';
	$parameters ['photo'] = 's3/85dcca23-09d1-40c2-9a5e-9fa01375ace2';
	$parameters ['actif'] = '1';
	$parameters ['cout_journalier'] = '610';
	$parameters ['collab_type'] = 'SNF';
	$parameters ['role'] = 'Administrateur';
	$parameters ['boite_id_uri'] = '/boite/v1/' . UUID::md5touuid(md5('boite004'));
	$parameters ['boite_id'] = 'boite004';
	$parameters ['sous_boite_id'] = 'boite004_sous_boite01';
	$parameters ['liste_competences'] ['competence01'] = 'Responsable de projets';
	$parameters ['liste_competences'] ['competence02'] = 'Architecte réseaux';
	$parameters ['liste_competences'] ['competence03'] = 'Webmaster';
	$parameters ['liste_competences'] ['competence04'] = 'Responsable de trafic';
	$parameters ['liste_competences'] ['competence05'] = 'Analyste';
	$parameters ['liste_competences'] ['competence06'] = 'Technicien réseaux';
	$parameters ['liste_competences'] ['competence07'] = 'Ingénieur qualité méthode';
	$parameters ['liste_competences'] ['competence08'] = 'Ingénieur système et réseaux';
	$parameters ['liste_competences'] ['competence09'] = 'Consultant SAP';
	
	print_r($parameters);
	$response = Collaborateur::postCollaborateur ($parameters);
	var_dump($response);
// 	echo '<h3>' . $parameters ['id'] . ' - status code: ' . $response->getStatusCode() . '</h3>';

	// Responsable de projet
	$parameters = array();
	$parameters ['id'] = $parameters ['collab_id'] = 'SOF8002';
	$parameters ['nom'] = 'Responsable de projet';
	$parameters ['prenom'] = '';
	$parameters ['email'] = 'test002@ca-cf.fr';
	$parameters ['photo'] = 's3/85dcca23-09d1-40c2-9a5e-9fa01375ace2';
	$parameters ['actif'] = '1';
	$parameters ['cout_journalier'] = '610';
	$parameters ['collab_type'] = 'SNF';
	$parameters ['role'] = 'Responsable de projet';
	$parameters ['boite_id_uri'] = '/boite/v1/' . UUID::md5touuid(md5('boite004'));
	$parameters ['boite_id'] = 'boite004';
	$parameters ['sous_boite_id'] = 'boite004_sous_boite01';
	$parameters ['liste_competences'] ['competence01'] = 'Responsable de projets';
	$parameters ['liste_competences'] ['competence02'] = 'Architecte réseaux';
	$parameters ['liste_competences'] ['competence03'] = 'Webmaster';
	$parameters ['liste_competences'] ['competence04'] = 'Responsable de trafic';
	$parameters ['liste_competences'] ['competence05'] = 'Analyste';
	$parameters ['liste_competences'] ['competence06'] = 'Technicien réseaux';
	$parameters ['liste_competences'] ['competence07'] = 'Ingénieur qualité méthode';
	$parameters ['liste_competences'] ['competence08'] = 'Ingénieur système et réseaux';
	$parameters ['liste_competences'] ['competence09'] = 'Consultant SAP';
	
	$response = Collaborateur::postCollaborateur ($parameters);
// 	echo '<h3>' . $parameters ['id'] . ' - status code: ' . $response->getStatusCode() . '</h3>';

	// Manager
	$parameters = array();
	$parameters ['id'] = $parameters ['collab_id'] = 'SOF8003';
	$parameters ['nom'] = 'Manager';
	$parameters ['prenom'] = '';
	$parameters ['email'] = 'test003@ca-cf.fr';
	$parameters ['photo'] = 's3/85dcca23-09d1-40c2-9a5e-9fa01375ace2';
	$parameters ['actif'] = '1';
	$parameters ['cout_journalier'] = '610';
	$parameters ['collab_type'] = 'SNF';
	$parameters ['role'] = 'Manager';
	$parameters ['boite_id_uri'] = '/boite/v1/' . UUID::md5touuid(md5('boite004'));
	$parameters ['boite_id'] = 'boite004';
	$parameters ['sous_boite_id'] = 'boite004_sous_boite01';
	$parameters ['liste_competences'] ['competence01'] = 'Responsable de projets';
	$parameters ['liste_competences'] ['competence02'] = 'Architecte réseaux';
	$parameters ['liste_competences'] ['competence03'] = 'Webmaster';
	$parameters ['liste_competences'] ['competence04'] = 'Responsable de trafic';
	$parameters ['liste_competences'] ['competence05'] = 'Analyste';
	$parameters ['liste_competences'] ['competence06'] = 'Technicien réseaux';
	$parameters ['liste_competences'] ['competence07'] = 'Ingénieur qualité méthode';
	$parameters ['liste_competences'] ['competence08'] = 'Ingénieur système et réseaux';
	$parameters ['liste_competences'] ['competence09'] = 'Consultant SAP';
	
	$response = Collaborateur::postCollaborateur ($parameters);
// 	echo '<h3>' . $parameters ['id'] . ' - status code: ' . $response->getStatusCode() . '</h3>';

	// Contributeur
	$parameters = array();
	$parameters ['id'] = $parameters ['collab_id'] = 'SOF8004';
	$parameters ['nom'] = 'Contributeur';
	$parameters ['prenom'] = '';
	$parameters ['email'] = 'test004@ca-cf.fr';
	$parameters ['photo'] = 's3/85dcca23-09d1-40c2-9a5e-9fa01375ace2';
	$parameters ['actif'] = '1';
	$parameters ['cout_journalier'] = '610';
	$parameters ['collab_type'] = 'SNF';
	$parameters ['role'] = 'Contributeur';
	$parameters ['boite_id_uri'] = '/boite/v1/' . UUID::md5touuid(md5('boite004'));
	$parameters ['boite_id'] = 'boite004';
	$parameters ['sous_boite_id'] = 'boite004_sous_boite01';
	$parameters ['liste_competences'] ['competence01'] = 'Responsable de projets';
	$parameters ['liste_competences'] ['competence02'] = 'Architecte réseaux';
	$parameters ['liste_competences'] ['competence03'] = 'Webmaster';
	$parameters ['liste_competences'] ['competence04'] = 'Responsable de trafic';
	$parameters ['liste_competences'] ['competence05'] = 'Analyste';
	$parameters ['liste_competences'] ['competence06'] = 'Technicien réseaux';
	$parameters ['liste_competences'] ['competence07'] = 'Ingénieur qualité méthode';
	$parameters ['liste_competences'] ['competence08'] = 'Ingénieur système et réseaux';
	$parameters ['liste_competences'] ['competence09'] = 'Consultant SAP';
	
	$response = Collaborateur::postCollaborateur ($parameters);
// 	echo '<h3>' . $parameters ['id'] . ' - status code: ' . $response->getStatusCode() . '</h3>';

	// CEO
	$parameters = array();
	$parameters ['id'] = $parameters ['collab_id'] = 'SOF8005';
	$parameters ['nom'] = 'CEO';
	$parameters ['prenom'] = '';
	$parameters ['email'] = 'test005@ca-cf.fr';
	$parameters ['photo'] = 's3/85dcca23-09d1-40c2-9a5e-9fa01375ace2';
	$parameters ['actif'] = '1';
	$parameters ['cout_journalier'] = '610';
	$parameters ['collab_type'] = 'SNF';
	$parameters ['role'] = 'CEO';
	$parameters ['boite_id_uri'] = '/boite/v1/' . UUID::md5touuid(md5('boite004'));
	$parameters ['boite_id'] = 'boite004';
	$parameters ['sous_boite_id'] = 'boite004_sous_boite01';
	$parameters ['liste_competences'] ['competence01'] = 'Responsable de projets';
	$parameters ['liste_competences'] ['competence02'] = 'Architecte réseaux';
	$parameters ['liste_competences'] ['competence03'] = 'Webmaster';
	$parameters ['liste_competences'] ['competence04'] = 'Responsable de trafic';
	$parameters ['liste_competences'] ['competence05'] = 'Analyste';
	$parameters ['liste_competences'] ['competence06'] = 'Technicien réseaux';
	$parameters ['liste_competences'] ['competence07'] = 'Ingénieur qualité méthode';
	$parameters ['liste_competences'] ['competence08'] = 'Ingénieur système et réseaux';
	$parameters ['liste_competences'] ['competence09'] = 'Consultant SAP';
	
	$response = Collaborateur::postCollaborateur ($parameters);
// 	echo '<h3>' . $parameters ['id'] . ' - status code: ' . $response->getStatusCode() . '</h3>';

	// Achat
	$parameters = array();
	$parameters ['id'] = $parameters ['collab_id'] = 'SOF8006';
	$parameters ['nom'] = 'Achat';
	$parameters ['prenom'] = '';
	$parameters ['email'] = 'test006@ca-cf.fr';
	$parameters ['photo'] = 's3/85dcca23-09d1-40c2-9a5e-9fa01375ace2';
	$parameters ['actif'] = '1';
	$parameters ['cout_journalier'] = '610';
	$parameters ['collab_type'] = 'SNF';
	$parameters ['role'] = 'Achat';
	$parameters ['boite_id_uri'] = '/boite/v1/' . UUID::md5touuid(md5('boite004'));
	$parameters ['boite_id'] = 'boite003';
	$parameters ['sous_boite_id'] = 'boite003_sous_boite03';
	$parameters ['liste_competences'] ['competence01'] = 'Responsable de projets';
	$parameters ['liste_competences'] ['competence02'] = 'Architecte réseaux';
	$parameters ['liste_competences'] ['competence03'] = 'Webmaster';
	$parameters ['liste_competences'] ['competence04'] = 'Responsable de trafic';
	$parameters ['liste_competences'] ['competence05'] = 'Analyste';
	$parameters ['liste_competences'] ['competence06'] = 'Technicien réseaux';
	$parameters ['liste_competences'] ['competence07'] = 'Ingénieur qualité méthode';
	$parameters ['liste_competences'] ['competence08'] = 'Ingénieur système et réseaux';
	$parameters ['liste_competences'] ['competence09'] = 'Consultant SAP';
	
	$response = Collaborateur::postCollaborateur ($parameters);
// 	echo '<h3>' . $parameters ['id'] . ' - status code: ' . $response->getStatusCode() . '</h3>';

}


echo "\n*************************************************************************************************\n";
echo "****** Ajouter les collaborateurs profil ******\n";
echo "***************************************************************************************************\n\n";
echo "BEGIN Program\n\n";

addCollaborateursProfil ();
	
echo "\nEND Program\n";


