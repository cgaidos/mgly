<?php
namespace Moowgly\Pilote\Controller;

$_SERVER['PROJECT_SILO_URL'] = 'http://localhost/ws-moowgly';
// Define application path
define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../..'));
require APPLICATION_PATH . '/vendor/autoload.php';
include APPLICATION_PATH . '/batch/conf/' . $argv[1] . '/conf.php';

use Moowgly\Lib\Utils\UUID;
use Moowgly\Lib\Utils\XslUtil;
use Moowgly\Pilote\Model\Collaborateur;
use Moowgly\Pilote\Model\Boite;

function main()
{
	echo "-------------------------------------------------------";
	echo "\n--- Affilier les managers aux divisions ---";
	echo "\n-------------------------------------------------------";
	echo "\n---BEGIN Program---";
	
	// cherche les divisions qui ne sont pas liées au manager
	$q = "-chef_id_s:*";
	$itemXML = Boite::getBoites($q, '', 'boite_id_s');
	$nodes = XslUtil::findNodes($itemXML, "/a:feed/a:entry");
	$boiteIds = array();
	foreach ($nodes as $node){
		$boite_id = XslUtil::getNodeValue(XslUtil::findOneNode($itemXML, "a:span[@class='boite_id_s']", $node, 0));
		if ((strlen($boite_id) > 0)){
			$boiteIds [$boite_id] = $boite_id;
		}
	}
	
	if (! empty($boiteIds)){
		// cherche les collaborateurs de type manager
		$q = 'boite_id_s:(' . implode(' ', $boiteIds) . ') AND ((role_s:*Manager*) OR (role_s:*manager*))';
		$itemXML = Collaborateur::getContributeurs($q, '', 'collab_id_s,boite_id_s');
		$nodes = XslUtil::findNodes($itemXML, '/a:feed/a:entry');
		foreach ($nodes as $node){
			$boite_id = XslUtil::getNodeValue(XslUtil::findOneNode($itemXML, "a:span[@class='boite_id_s']", $node, 0));
			$collab_id = XslUtil::getNodeValue(XslUtil::findOneNode($itemXML, "a:span[@class='collab_id_s']", $node, 0));
			if ((strlen($boite_id) > 0) && (strlen($collab_id) > 0)){
				$parameters = array(
						'id' => UUID::md5touuid(md5($boite_id)),
						'chef_id' => $collab_id,
						'chef_id_uri' => '/collaborateur/v1/' . UUID::md5touuid(md5($collab_id))
				);
				$response = Boite::putBoite ( $parameters );
				if (!is_object($response) || !in_array($response->getStatusCode (), [201,200])) {
					print ("\nErreur PUT sur la division : " . $boite_id);
				}
			}
		}
	}

	echo "\n---END Program---";
}

main();
