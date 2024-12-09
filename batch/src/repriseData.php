<?php
namespace Moowgly\Pilote\Controller;

$_SERVER['PROJECT_SILO_URL'] = 'http://localhost/ws-moowgly';
// Define application path
define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../..'));
require APPLICATION_PATH . '/vendor/autoload.php';
include APPLICATION_PATH . '/batch/conf/' . $argv[1] . '/conf.php';

use Moowgly\Lib\Utils\ClientSilo;
use GuzzleHttp\Client;
use Moowgly\Lib\Utils\DomUtil;
use Moowgly\Lib\Utils\XslUtil;
use Moowgly\Pilote\Model\Common;
use Moowgly\Pilote\Model\Projet;
use Moowgly\Pilote\Model\Staffing;

ini_set('memory_limit', '-1');

// Objectif: mettre un flag actif sur le dernier staffing révisé
function reprise()
{
    $itemProjetXML = Staffing::getStaffing('nature_s:Revision', '', '', 'projet_id_s');
    $arrProjetRevise = array();
    $projetNodeXML = XslUtil::findNodes($itemProjetXML, "/a:feed/x:facetResult/result/item[@count > 0]");
    foreach ($projetNodeXML as $projetXML) {
        $arrProjetRevise[] = $projetXML->nodeValue;
    }
    print_r($arrProjetRevise) . PHP_EOL;

    // On va chercher le staffing révisé de chaque projet
    $arrStaffing = array();
    foreach ($arrProjetRevise as $projetId) {
        // On récupère le numéro de la dernière révision
        $version = Staffing::getVersionRevise($projetId);
        if ($version == 0) {
            $query = 'nature_s:Revision AND projet_id_s:' . $projetId;
        } else {
            $query = 'nature_s:Revision AND projet_id_s:' . $projetId . ' AND revise_version_i:' . $version;
        }
        $itemStaffingXML = Staffing::getStaffing($query, '', 'id');
        $staffingNodeXML = XslUtil::findNodes($itemStaffingXML, "/a:feed/a:entry/a:id");
        foreach ($staffingNodeXML as $staffingXML) {
            $arrStaffing[$projetId][] = $staffingXML->nodeValue;
        }
    }

    // On met à jour le flag du staffing révisé de chaque projet
    $client = ClientSilo::getInstance();
    $urlStaffing = $_SERVER['PROJECT_SILO_URL'] . '/staffing/v1/';
    foreach ($arrStaffing as $projetId => $arrInit) {
        foreach ($arrInit as $idStaffing) {
            $parameters = array();
            $parameters['id'] = $idStaffing;
            $parameters['revise_actif'] = '1';
            try {
                $response = $client->put($urlStaffing . $parameters['id'], [
                    'headers' => ['Content-Type' => 'application/x-www-form-urlencoded'],
                    'form_params' => $parameters
                ]);
            } catch (\Exception $e) {
                echo $idStaffing . PHP_EOL;
            }
        }
    }

    // On met le flag actif au staffing initial des projets non révisés
    $itemProjetXML = Staffing::getStaffing('nature_s:Initialisation', '', '', 'projet_id_s');
    $arrProjetInit = array();
    $projetNodeXML = XslUtil::findNodes($itemProjetXML, "/a:feed/x:facetResult/result/item[@count > 0]");
    foreach ($projetNodeXML as $projetXML) {
        if (!in_array($projetXML->nodeValue, $arrProjetRevise)) {
            echo $projetXML->nodeValue . PHP_EOL;
            $arrProjetInit[] = $projetXML->nodeValue;
        }
    }
    print_r($arrProjetInit) . PHP_EOL;

    // On va chercher le staffing initial de chaque projet
    $arrStaffing = array();
    foreach ($arrProjetInit as $projetId) {
        $itemStaffingXML = Staffing::getStaffing('nature_s:Initialisation AND projet_id_s:' . $projetId, '', 'id');
        $staffingNodeXML = XslUtil::findNodes($itemStaffingXML, "/a:feed/a:entry/a:id");
        foreach ($staffingNodeXML as $staffingXML) {
            $arrStaffing[$projetId][] = $staffingXML->nodeValue;
        }
    }
    // On met à jour le flag du staffing initial de chaque projet
    $client = ClientSilo::getInstance();
    $urlStaffing = $_SERVER['PROJECT_SILO_URL'] . '/staffing/v1/';
    foreach ($arrStaffing as $projetId => $arrRevise) {
        foreach ($arrRevise as $idStaffing) {
            $parameters = array();
            $parameters['id'] = $idStaffing;
            $parameters['revise_actif'] = '1';
            try {
                $response = $client->put($urlStaffing . $idStaffing, [
                    'headers' => ['Content-Type' => 'application/x-www-form-urlencoded'],
                    'form_params' => $parameters
                ]);
            } catch (\Exception $e) {
                echo $idStaffing . PHP_EOL;
            }
        }
    }
// file_put_contents(APPLICATION_PATH . '\atom.xml', $itemStaffingXML->saveXML());
print_r($arrStaffing);
}

reprise();
