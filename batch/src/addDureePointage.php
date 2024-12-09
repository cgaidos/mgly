<?php
namespace Moowgly\Pilote\Controller;

$_SERVER['PROJECT_SILO_URL'] = 'http://localhost/ws-moowgly';
// Define application path
define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../..'));
require APPLICATION_PATH . '/vendor/autoload.php';
include APPLICATION_PATH . '/batch/conf/' . $argv[1] . '/conf.php';

use Moowgly\Lib\Utils\UUID;
use Moowgly\Lib\Utils\XslUtil;
use Moowgly\Pilote\Model\Pointage;

function main()
{
	echo "\n*************************************************************************************************\n";
	echo "****** Ajouter le champ duree dans le pointage classique ******\n";
	echo "***************************************************************************************************\n\n";
	echo "BEGIN Program\n\n";

	// Récupére les types des projets contenus dans la liste "type_projet_ss" de reference
	$itemXML = Pointage::getPointage('-duree_f:* AND collab_id_s:*', '', 'id,periode_s');

	$nodes = XslUtil::findNodes($itemXML, "/a:feed/a:entry");
	if ($nodes->length == 0){
		echo "Le champ duree existe déjà dans le pointage classique !\n";
	} else {
		foreach ($nodes as $node) {
			$id = XslUtil::getNodeValue(XslUtil::findOneNode($itemXML, "a:id", $node, 0));

			$parameters = array();
			$parameters ['id'] = $id;
			$parameters ['duree'] = '0.25';
			$response = Pointage::putPointage ($id, $parameters );
			if (!is_object($response) || !in_array($response->getStatusCode(), [201,200])) {
				echo "Erreur sur le pointage : " . $id . "\n";
			} else {
// 				echo "++ ajouter la durée 0.25 sur le pointage : " . $id . "\n";
			}
		}
		echo "Nombre de pointages à jour : " + $nodes->length;
	}





	echo "\nEND Program\n";

}

main();
