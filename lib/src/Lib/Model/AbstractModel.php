<?php

namespace Moowgly\Lib\Model;

use DateTime;
use Exception;
use Moowgly\Lib\Utils\DomUtil;
use Moowgly\Lib\Utils\UUID;
use Moowgly\Lib\Utils\XslUtil;
use Moowgly\Lib\Utils\Logger;

abstract class AbstractModel
{
    const XSL_ATOM_CONTENT_PREFIX = '/a:entry/a:content/x:div';

    protected $category = null;

    /**
     * Contructor
     *
     * @throws Exception
     */
    public function __construct()
    {
        if (!defined('static::CATEGORY')) {
            throw new Exception(
                'Constant CATEGORY is not defined on subclass '.get_class($this)
            );
        } else {
            $this->category = static::CATEGORY;
        }
    }

    /**
     * Check if resource parameters are valid.
     *
     * @param $post resource parameters.
     * @param $isPut Indicate if resource is modified.
     *  Mandatory parameters are not checked on PUT request to apply partial
     *  updates.
     *
     * @return true if parameters are valid, otherwise an array containing errors.
     */
    public function checkParam($post, $isPut = false)
    {
        $listError = array();
        $listWarning = array();
        $checkArray = $this->getParametersToCheck();

        foreach ($checkArray as $name => $carac) {
            // Step 1 : Patterns validation
            if (array_key_exists($name, $post) && array_key_exists('pattern', $carac) && $carac['pattern'] != '') {
                if (is_array($post[$name])) {
                    // If we get an array, we test each array paramter pattern.
                    foreach ($post[$name] as $idParam => $valueParam) {
                        if (preg_match('/^'.$carac['pattern'].'$/', urldecode($valueParam)) === 0) {
                            $listError[$name.'-'.$idParam] = "Bad parameter $name ($idParam) value ".$valueParam;
                            Logger::getInstance()->error('Check param: Bad parameter '.$name.' ( '.$idParam.' ) .value: '.$valueParam.' doesn\'t match with regex: '.'/^'.$carac['pattern'].'$/');
                        }
                    }
                } elseif (preg_match('/^'.$carac['pattern'].'$/', urldecode($post[$name])) === 0) {
                    // Otherwise, we test parameter value.
                    $listError[$name] = "Bad parameter $name value ".$post[$name];
                    Logger::getInstance()->error('Check param: Bad parameter '.$name.' value: '.$post[$name].' doesn\'t match with regex: '.'/^'.$carac['pattern'].'$/');
                }
            }

            // Step 2 : Mandatory validation
            if (!$isPut && array_key_exists('mandatory', $carac)) {
                // Step 2.1 : Mandatory is a boolean set to true
                if ($carac['mandatory'] === true || $carac['mandatory'] === 'true') {
                    if (!array_key_exists($name, $post) || $post[$name] == '') { // Parameter is missing OR empty --> error.
                        $listError[$name] = "Missing parameter $name";
                        Logger::getInstance()->error('Check param : Missing parameter '.$name);
                    } elseif (is_array($post[$name])) {
                        // Parameter is an array, we check each element
                            foreach ($post[$name] as $idParam => $valueParam) {
                                if (urldecode($valueParam) == '') { // Parameter is an empty string --> error.
                                    $listError[$name.'-'.$idParam] = "Missing parameter $name-$idParam";
                                    Logger::getInstance()->error('Check param : Missing parameter '.$name);
                                }
                            }
                    }
                } // Step 2.2 : It is a string referencing another element, we check it
                elseif (is_string($carac['mandatory']) && $carac['mandatory'] != '' && $carac['mandatory'] != 'false') {
                    if (array_key_exists($carac['mandatory'], $post)) {
                        // Parent element exist, we check.
                            if (!array_key_exists($name, $post) || $post[$name] == '') {
                                // Child does not exist OR is empty --> error.
                                $listError[$name] = "Missing parameter $name";
                                Logger::getInstance()->error('Check param : Missing parameter '.$name);
                            } elseif (is_array($post[$name]) && is_array($post[$carac['mandatory']])) {
                                foreach ($post[$carac['mandatory']] as $idParamParent => $valueParamParent) {
                                    if ($valueParamParent != '' && (!array_key_exists($idParamParent, $post[$name]) || $post[$name][$idParamParent] == '')) {
                                        $listError[$name.'-'.$idParamParent] = "Missing parameter $name ($idParamParent)";
                                        Logger::getInstance()->error("Check param : Missing parameter  $name ($idParamParent)");
                                    }
                                }
                            }
                    }
                }
            }

            // Step 3 : Rules validation
            if (array_key_exists($name, $post) && isset($carac['rule']) && $carac['rule'] != '') {
                if (is_array($post[$name])) {
                    foreach ($post[$name] as $idParam => $valueParam) {
                        if (preg_match('/^'.$carac['rule'].'$/', urldecode($valueParam)) === 0) {
                            $listWarning[$name.'-'.$idParam] = "Bad parameter $name ($idParam) value ".$valueParam;
                            Logger::getInstance()->warning('Check param: Bad parameter '.$name.' ('.$idParam.') value: '.$valueParam.' doesn\'t match with regex: '.'/^'.$carac['rule'].'$/');
                        }
                    }
                } elseif (preg_match('/^'.$carac['rule'].'$/', urldecode($post[$name])) === 0) {
                    // Invalid field format.
                        $listWarning[$name] = "Bad parameter $name value ".$post[$name];
                    Logger::getInstance()->warning('Check param: Bad parameter '.$name.' value: '.$post[$name].' doesn\'t match with regex: '.'/^'.$carac['rule'].'$/');
                }
            }
        }

        if (!empty($listError)) {
            return array(
                'isValid' => false,
                'listError' => $listError,
            );
        }

        return array(
            'isValid' => true,
            'listWarning' => $listWarning,
        );
    }

    /**
     * Check if every node from Atom document are valid.
     *
     * @param $dom Atom to be checked.
     *
     * @return true if parameters are valid, otherwise an array containing errors.
     */
    public function checkAtom($dom)
    {
        $listError = array();
        $listWarning = array();
        if (!isset($dom)) {
            $listError['dom'] = 'Bad parameter. checkAtom function input MUST be a valid DOMDocument.';
            Logger::getInstance()->error('CheckAtom: Bad parameter. checkAtom function input MUST be a valid DOMDocument.');
        } else {
            $checkArray = $this->getParametersToCheck();

            foreach ($checkArray as $name => $carac) {
                if (isset($carac['xpath'])) {
                    $xpath = self::XSL_ATOM_CONTENT_PREFIX.$carac['xpath'];
                    $nodeValue = XslUtil::findFirstNodeValue($dom, $xpath);

                    if (isset($carac['mandatory']) && $carac['mandatory'] == true && $nodeValue == null && $nodeValue == '') {
                        $listError[$name] = "Missing parameter $name";
                        Logger::getInstance()->error('Check param : Missing parameter '.$name);
                    }

                    if (isset($nodeValue) && isset($carac['pattern']) && $nodeValue != null && $carac['pattern'] != null && $carac['pattern'] != '' && preg_match('/^'.$carac['pattern'].'$/', $nodeValue) === 0) {
                        $listError[$name] = "Bad parameter $name value ".$nodeValue;
                        Logger::getInstance()->error('Check param: Bad parameter '.$name.' value: '.$nodeValue.' doesn\'t match with regex: '.'/^'.$carac['pattern'].'$/');
                    }

                    if (isset($nodeValue) && isset($carac['rule']) && $nodeValue != null && $carac['rule'] != null && $carac['rule'] != '' && preg_match('/^'.$carac['rule'].'$/', $nodeValue) === 0) {
                        $listWarning[$name] = "Bad parameter $name value ".$nodeValue;
                        Logger::getInstance()->warning('Check param: Bad parameter '.$name.' value: '.$nodeValue.' doesn\'t match with regex: '.'/^'.$carac['rule'].'$/');
                        $uuid = new UUID();
                        $uuid = $uuid->uuid();
                        DomUtil::addElement($dom, "/a:entry/a:content/x:div/x:ul[@class='warnings']/x:li[last()+1]/@id", $uuid);
                        DomUtil::addElement($dom, "/a:entry/a:content/x:div/x:ul[@class='warnings']/x:li[last()]/x:time[@class='date']", date("Y-m-d\TH:i:s\Z")); // $date->format("Y-m-d\TH:i:s\Z"));
                        DomUtil::addElement($dom, "/a:entry/a:content/x:div/x:ul[@class='warnings']/x:li[last()]/x:span[@class='champ']", $name);
                        DomUtil::addElement($dom, "/a:entry/a:content/x:div/x:ul[@class='warnings']/x:li[last()]/x:span[@class='message']", 'Invalid field '.$name.' format ('.$nodeValue.')');
                    }
                }
            }
        }

        if (!empty($listError)) {
            return array(
                'isValid' => false,
                'listError' => $listError,
            );
        }

        return array(
            'isValid' => true,
            'listWarning' => $listWarning,
        );
    }

    /**
     * Construct XML from parameters.
     *
     * @param $parameters   resource parameters.
     * @param $resource     resource type.
     * @param $listWarning  warning list from checkParam
     * @param $src          author of modification
     */
    public function constructXML($parameters, $resource, $listWarning, $src)
    {
        $emptyAtom = <<<ATOMFRAG
<entry xmlns="http://www.w3.org/2005/Atom">
    <title/>
    <link/>
    <id/>
    <author><name/><email/></author>
    <updated/>
    <content type="xhtml">
        <div xmlns="http://www.w3.org/1999/xhtml">
        </div>
    </content>
</entry>
ATOMFRAG;
        $xmlAtom = new \DOMDocument('1.0', 'utf-8');
        $xmlAtom->loadXML($emptyAtom);
        $xmlAtom->encoding = 'UTF-8';
        DomUtil::addElement($xmlAtom, '/a:entry/a:title', $resource);
        DomUtil::addElement($xmlAtom, '/a:entry/a:category/@term', $resource);
        if ($parameters['id'] == 'new') {
            $uuid = new UUID();
            $uuid = $uuid->uuid();
            $parameters['id'] = $uuid;
        } else {
            $uuid = new UUID();
            $uuid = $uuid->md5touuid(md5($parameters['id']));
            $parameters['id'] = $uuid;
        }
        DomUtil::addElement($xmlAtom, '/a:entry/a:link/@href', $_SERVER['PROJECT_SILO_URL'].'/'.$resource.'/v1/'.$uuid);
        DomUtil::addElement($xmlAtom, '/a:entry/a:id', 'urn:uuid:'.$uuid);
        DomUtil::addElement($xmlAtom, '/a:entry/a:author/a:name', 'moowgly');
        DomUtil::addElement($xmlAtom, '/a:entry/a:author/a:email', 'moowgly@moowgly.fr');
        $date = new DateTime();
        DomUtil::addElement($xmlAtom, '/a:entry/a:hash', md5(serialize($parameters)));
        DomUtil::addElement($xmlAtom, '/a:entry/a:hash/@src', $src);
        DomUtil::addElement($xmlAtom, '/a:entry/a:hash/@date', $date->format("Y-m-d\TH:i:s\Z"));
        DomUtil::addElement($xmlAtom, '/a:entry/a:updated', $date->format("Y-m-d\TH:i:s\Z"));

        $this->addXpathToDom($parameters, $xmlAtom, false, $date);

        if (!empty($listWarning)) {
            $uuid = new UUID();
            $uuid = $uuid->uuid();
            foreach ($listWarning as $name => $warning) {
                DomUtil::addElement($xmlAtom, "/a:entry/a:content/x:div/x:ul[@class='warnings']/x:li[last()+1]/@id", $uuid, false);
                DomUtil::addElement($xmlAtom, "/a:entry/a:content/x:div/x:ul[@class='warnings']/x:li[last()]/x:time[@class='date']", $date->format("Y-m-d\TH:i:s\Z"), false);
                DomUtil::addElement($xmlAtom, "/a:entry/a:content/x:div/x:ul[@class='warnings']/x:li[last()]/x:span[@class='champ']", $name, false);
                DomUtil::addElement($xmlAtom, "/a:entry/a:content/x:div/x:ul[@class='warnings']/x:li[last()]/x:span[@class='message']", $warning, false);
            }
        }

        return $xmlAtom;
    }

    /**
     * Update atom from given parameters.
     *
     * @param $atom         atom to be updated.
     * @param $parameters   resource parameters.
     * @param $listWarning  warning list from checkParam
     * @param $src          author of modification
     *
     * @return DOMDocument
     */
    public function updateXML($atom, $parameters, $listWarning = array(), $src)
    {
        Logger::getInstance()->debug('Moowgly\Lib\Model\AbstractModel->updateXML parameters'.var_export($parameters, true));
        $xmlAtom = new \DOMDocument('1.0', 'utf-8');
        $xmlAtom->loadXML($atom);
        $xmlAtom->encoding = 'UTF-8';
        $date = new DateTime();
        DomUtil::addElement($xmlAtom, '/a:entry/a:updated', $date->format("Y-m-d\TH:i:s\Z"));
        DomUtil::addElement($xmlAtom, "/a:entry/a:hash[@src='$src']", md5(serialize($parameters)));
        DomUtil::addElement($xmlAtom, "/a:entry/a:hash[@src='$src']/@date", $date->format("Y-m-d\TH:i:s\Z"));
        DomUtil::addElement($xmlAtom, '/a:entry/a:updated', $date->format("Y-m-d\TH:i:s\Z"));

        $deleteMode = array_key_exists('deleteMode', $parameters) || isset($parameters['deleteMode']);
        if ($deleteMode) {
            Logger::getInstance()->debug('Moowgly\Lib\Model\AbstractModel->updateXML | deleteMode='.$deleteMode);
            $this->removeAllLists($xmlAtom);
        }

        $this->addXpathToDom($parameters, $xmlAtom, $src);
        if (!empty($listWarning)) {
            $uuid = new UUID();
            $uuid = $uuid->uuid();
            foreach ($listWarning as $name => $warning) {
                DomUtil::addElement($xmlAtom, "/a:entry/a:content/x:div/x:ul[@class='warnings']/x:li[last()+1]/@id", $uuid);
                DomUtil::addElement($xmlAtom, "/a:entry/a:content/x:div/x:ul[@class='warnings']/x:li[last()]/x:time[@class='date']", $date->format("Y-m-d\TH:i:s\Z"));
                DomUtil::addElement($xmlAtom, "/a:entry/a:content/x:div/x:ul[@class='warnings']/x:li[last()]/x:span[@class='champ']", $name);
                DomUtil::addElement($xmlAtom, "/a:entry/a:content/x:div/x:ul[@class='warnings']/x:li[last()]/x:span[@class='message']", $warning);
            }
        }

        return $xmlAtom;
    }

    /**
     * Add element to XML.
     *
     * @param array          $parameters
     * @param DomDocument    $xmlAtom
     * @param string         $histo
     * @param \DateTime|bool $date
     *
     * @throws \Exception
     */
    private function addXpathToDom($parameters, $xmlAtom, $histo = false, $date = false)
    {
        Logger::getInstance()->debug('Moowgly\Lib\Model\AbstractModel->addXpathToDom');
        $parametersArray = $this->getParametersToCheck();

        if ($date !== false && $date instanceof \DateTime) {
            $parameters['dateCreation'] = $date->format("Y-m-d\TH:i:s\Z");
        }
        Logger::getInstance()->debug(
            'Moowgly\Lib\Model\AbstractModel->addXpathToDom parametersArray='.
            var_export($parametersArray, true)
        );
        Logger::getInstance()->debug(
            'Moowgly\Lib\Model\AbstractModel->addXpathToDom parameters='.
            var_export($parameters, true)
        );
        foreach ($parametersArray as $name => $carac) {
            if (array_key_exists($name, $parameters)) {
                if (is_array($parameters[$name])) {
                    foreach ($parameters[$name] as $nameList => $caracList) {
                        Logger::getInstance()->debug(
                            'Moowgly\Lib\Model\AbstractModel->addXpathToDom '.
                            '| success addElement substr_count : '.substr_count($carac['xpath'], '__ID__')
                        );
                        // check __ID__ is once more
                        if (substr_count($carac['xpath'], '__ID__') === 2) {
                            $arrayXpath = $this->generateXpathFromArray(array($nameList => $caracList), '/a:entry/a:content/x:div'.$carac['xpath']);
                            Logger::getInstance()->debug(
                                'Moowgly\Lib\Model\AbstractModel->addXpathToDom '.
                                '| success addElement === 2 (xmlAtom, '.
                                '/a:entry/a:content/x:div'.
                                ' carac[xpath]='.$carac['xpath'].' nameList='.$nameList.' caracList='.var_export($caracList, true)
//                                 .' parameters='.var_export($parameters, true)
                                .' arrayXpath='.var_export($arrayXpath, true)
                            );
                            foreach ($arrayXpath as $key => $detailXpath) {
                                $node = DomUtil::addElement(
                                    $xmlAtom,
                                    $detailXpath['xpath'],
                                    $detailXpath['val'],
                                    $histo
                                );
                                if ($node === null) {
                                    Logger::getInstance()->debug(
                                        'Moowgly\Lib\Model\AbstractModel->addXpathToDom '.
                                        '| error addElement other (xmlAtom, '.
                                        $detailXpath['xpath'].
                                        ', '.$detailXpath['val'].''
                                    );
                                } else {
                                    Logger::getInstance()->debug(
                                        'Moowgly\Lib\Model\AbstractModel->addXpathToDom '.
                                        '| success addElement other (xmlAtom, '.
                                        $detailXpath['xpath'].
                                        ', '.$detailXpath['val'].''
                                    );
                                }
                            }
                        } else {
                            $xpath = '/a:entry/a:content/x:div'.
                                str_replace('__ID__', str_replace('"', '', $nameList), $carac['xpath']);

                            $node = DomUtil::addElement(
                                $xmlAtom,
                                $xpath,
                                $caracList,
                                $histo
                            );
                            if ($node === null) {
                                Logger::getInstance()->debug(
                                    'Moowgly\Lib\Model\AbstractModel->addXpathToDom '.
                                    '| error addElement other (xmlAtom, '.
                                    $xpath.
                                    ', '.$caracList.''
                                );
                            } else {
                                Logger::getInstance()->debug(
                                    'Moowgly\Lib\Model\AbstractModel->addXpathToDom '.
                                    '| success addElement other (xmlAtom, '.
                                    $xpath.
                                    ', '.$caracList.''
                                );
                            }
                        }
                    }
                } else {
                    try {
                    	$node = DomUtil::addElement(
                    	    $xmlAtom,
                    	    '/a:entry/a:content/x:div'.$carac['xpath'],
                    	    $parameters[$name],
                    	    $histo
                    	);
                    	if ($node === null) {
                    	    Logger::getInstance()->debug(
                    	        'Moowgly\Lib\Model\AbstractModel->addXpathToDom |'.
                    	        ' error addElement (xmlAtom, '.
                    	        '/a:entry/a:content/x:div'.
                    	        $carac['xpath'].', '.$parameters[$name].''
                    	    );
                    	}
                    } catch (\Exception $e) {
                        throw new \Exception(
                            'error addElement (xmlAtom, '.
                            '/a:entry/a:content/x:div'.$carac['xpath'].', '.
                            $parameters[$name].', histo)'
                        );
        		    }
                }
            }
        }
    }

    /**
     * Delete every list from Atom apart from historics.
     *
     * @param \DOMDocument $atom Atom document to update.
     */
    public function removeAllLists($atom)
    {
        DomUtil::removeElement($atom, "/a:entry/a:content/x:div//x:ul[@class!='historics']");
        DomUtil::removeElement($atom, '/a:entry/a:content/x:div//x:ol');
    }

    /**
     * Return category
     *
     * @return category.
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Return an array of all parameters to be checked.
     * Array must respect following pattern:
     * "PARAMETER NAME" => {"xpath" => element xpath}.
     */
    abstract public function getParametersToCheck();

    /**
     *
     * @param array $array
     * @param string $xpathOrigine
     */
    public function generateXpathFromArray($array, $xpathOrigine) {
        $arrXpath = array();
        if (!empty($array)) {
            foreach ($array as $mainKey => $mainVal) {
                $mainXpath = preg_replace('/__ID__/', str_replace('"', '', $mainKey), $xpathOrigine, 1);
                foreach ($mainVal as $key => $val) {
                    $xpath = preg_replace('/__ID__/', str_replace('"', '', $key), $mainXpath, 1);
                    $arrXpath[$key]['xpath'] = $xpath;
                    $arrXpath[$key]['val'] = $val;
                }
            }
            return $arrXpath;
        }
    }
}
