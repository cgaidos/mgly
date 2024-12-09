<?php

namespace Moowgly\Lib\Utils;

use DOMDocument;
use DOMNode;
use DateTime;
use Moowgly\Lib\Controller\Source;

/**
 * Utility class for Atom DOM manipulations.
 */
class DomUtil
{
    const ATOM_NAMESPACE = 'http://www.w3.org/2005/Atom';

    const XHTML_NAMESPACE = 'http://www.w3.org/1999/xhtml';

    const OPENSEARCH_NAMESPACE = 'http://a9.com/-/spec/opensearch/1.1/';

    public static $_historic_id = null;

    /**
     * Function to add or modify elements and their value to a DOM.
     * Accepted XPath are :
     * /a:entry/a:content/x:div/x:div[@class='vcard']
     * /x:span[@class='n']/x:span[@class='given-name']
     * /a:entry/a:content/x:div/x:div[@class='vcard']
     * /x:span[@class='n']/x:span[@class='given-name']/@id
     * /a:entry/a:content/x:div/x:div[@class='vcard']/x:span[@class='n']/x:span
     * /a:entry/a:content/x:div/x:div[contains(@class,'vcard administre') and
     * ./x:time[@class='sadr' and
     * text()='2010-07-22T22:41:11+00:00Z']]/x:span[@class='xxx']
     * Conditions 'and ...' should be after ones on attributes.
     *
     * @xxx= or contains(@...)
     *
     * @param DOMDocument $dom
     * @param String      $xpath
     * @param String      $value
     * @param bool        $removeOnEmpty
     * @param bool        $addOnEmpty
     */
    public static function addElement(DOMDocument $dom, $xpath, $value, $histoFieldName = false, $removeOnEmpty = true, $addOnEmpty = true, DOMNode $rootNode = null, $userLogin = 'notset')
    {
        $nodes = XslUtil::findNodes($dom, $xpath, $rootNode);
        $node = (false !== $nodes && 1 == $nodes->length) ? $nodes->item(0) : null;
        $newNode = false;

        if (null == $node) {
            if (null != $rootNode && '.' != $xpath[0]) {
                // Force relative xpath to begin with ./
                // to comply with our algorithm
                $xpath = './'.$xpath;
            }

            $cutPattern = '((?:\]|(?:\/|\[)?[\:\.@A-Za-z0-9${}+\-=_\(\)\'", ]+))';
            $cutTest = preg_match_all($cutPattern, $xpath, $matches);

            if (false == $cutTest) {
                // xpath parsing failure
                // /A/B[...]/C/...
                // To test this, try on http://regex101.com/
                // Your regular expression in: PCRE (PHP)
                // ((?:\]|(?:\/|\[)?[\:\.@A-Za-z0-9${}\-=_\(\)'", ]+)) / g
                // Your test string
                // /a:entry/a:content/x:div
                // /x:div[contains(@class,'vcard administre')]
                // /x:div[contains(@class,'adr principale')
                // and ./x:time[@class='sadr'
                // and text()='2010-07-22T22:41:11+00:00Z'
                // and ./x:time[@class='eadr'
                // and text()='2011-07-22T22:41:11+00:00Z']
                // /x:span[@class='xxx']
                throw new \Exception("XPath parsing failure '$xpath'");
            }

            $bracketLevel = 0;
            $newPath = '';
            $offsetElement = 0;

            foreach ($matches[0] as $index => $token) {
                if (0 == $bracketLevel) {
                    if ('/' == $token[0]) {
                        if (0 < strlen($newPath)) {
                            // Store long paths end final element in an array
                            $listPath[] = array(
                                'path' => $newPath,
                                'element' => substr($newPath, $offsetElement),
                            );
                        }
                        $offsetElement = 1 + strlen($newPath);
                    }
                }
                if ('[' == $token[0]) {
                    $bracketLevel++;
                } elseif (']' == $token[0]) {
                    $bracketLevel--;
                }
                $newPath .= $token;
            }
            // Add last element and sub-xpath in $listPath
            $listPath[] = array(
                'path' => $newPath,
                'element' => substr($newPath, $offsetElement),
            );

            // Iterate on array from the end to minimize number of visited elements.
            for ($length = count($listPath) - 1; 0 < $length; $length--) {
                $nodes = XslUtil::findNodes($dom, $listPath[$length]['path'], $rootNode);
                $node = (false !== $nodes && 1 == $nodes->length) ? $nodes->item(0) : null;

                if (null == $node) {
                    // Create parent by recurrence.
                    $xpathParent = $listPath[$length - 1]['path'];
                    if (0 < $length) {
                        $node = self::addElement($dom, $xpathParent, '', false, false, false, $rootNode);
                    }
                    if (null == $node && 1 == $length) {
                        throw new \Exception('Failue during DOM branch creation'.' with xpath '.$xpathParent);
                    }

                    if (0 === strpos($listPath[$length]['element'], '@')) {
                        // No existing attribute.
                        $domAttribute = $dom->createAttribute(substr($listPath[$length]['element'], 1));
                        $node->appendChild($domAttribute);
                        $node = $domAttribute;
                        $newNode = true;
                    } else {
                        // XPath example "x:span[@class='given-name']"
                        // XPath example  "x:div[contains(@class,'vcard
                        // administre')]"
                        $debut = stripos($listPath[$length]['element'], '[');
                        $fin = strripos($listPath[$length]['element'], ']');

                        if (false === $debut) {
                            // if $debut == false, there is no attribute.
                            $elementName = $listPath[$length]['element'];
                        } else {
                            $elementName = substr($listPath[$length]['element'], 0, $debut);
                        }

                        $prefixLength = strpos($elementName, ':');

                        if (false == $prefixLength) {
                            // Default document prefix is 'atom'
                            // It is better to force error than create
                            // xhtml elements (div, span, ...) in Atom namespace
                            throw new \Exception("Element $elementName without prefix in".' xpath '.$listPath[$length]['path']);
                        }

                        $prefix = substr($elementName, 0, $prefixLength);
                        switch ($prefix) {
                            case 'a':
                                $namespace = self::ATOM_NAMESPACE;
                                break;
                            case 'x':
                                $namespace = self::XHTML_NAMESPACE;
                                break;
                            case 'os':
                                $namespace = self::OPENSEARCH_NAMESPACE;
                                break;
                            default:
                                throw new \Exception("Unauthorized namespace prefix $prefix ".' in xpath '.$listPath[$length]['path']);
                        }
                        $elementName = substr($elementName, 1 + $prefixLength);

                        if ($removeOnEmpty && $value == '') {
                            //No need to create element because it is the one to delete.
                            $node = null;
                        } else {
                            // Element to create
                            $eltToCreate = $dom->createElementNS($namespace, $elementName);
                            $node->appendChild($eltToCreate);
                            if (false !== $debut) {
                                // If $debut == false, there is no attribute.
                                // Extract attribute name and value.
                                // Handle following syntaxes
                                // @class='xxx' and contains(@class,'xxx yyy')
                                $attribute = substr($listPath[$length]['element'], 1 + $debut, ($fin - $debut - 1));
                                $bracketContent = strtok($attribute, ' ');

                                while ($bracketContent !== false) {
                                    $patternExtraction = '/^[ ]*'.'(?:contains[ ]*\\()?@([a-zA-Z0-9_\\-]+)'.'[ ]*[=,][ ]*[\'"]?([^\'"]*)[\'"]?[ ]*\)?'.'[ ]*'.'(?:and.*)?'.'$/';
                                    preg_match($patternExtraction, $bracketContent, $matches);
                                    if (3 == count($matches)) {
                                        $attributeName = $matches[1];
                                        $attributeValue = $matches[2];
                                        $domAttribute = $dom->createAttribute($attributeName);
                                        $domAttribute->value = $attributeValue;
                                        $eltToCreate->appendChild($domAttribute);
                                    }
                                    $bracketContent = strtok(' ');
                                }
                            }
                            $node = $eltToCreate;
                            $newNode = true;
                        }
                    }
                }
                break;
            }
        }

        $before = null;
        $after = null;
        $parent = null;

        if ($node != null) {
            // Modify xpath end value
            if ('DOMElement' === get_class($node)) {
                // Treating an element.
                if ($removeOnEmpty && '' == $value) {
                    $before = $newNode ? null : $node->nodeValue;
                    $parent = $node->parentNode;
                    $parent->removeChild($node);
                    $node = null;
                } else {
                    if ($addOnEmpty) {
                        $before = $newNode ? null : $node->nodeValue;
                        $node->nodeValue = $after = $value;
                    }
                    $parent = $node->parentNode;
                }
            } elseif ('DOMAttr' === get_class($node)) {
                $before = $newNode ? null : $node->value;
                $node->value = $after = $value;
                $parent = $node->parentNode;
            } else {
                throw new \Exception('Alert : Can not treat a value'.' on an element of type '.get_class($node).' in addElement with xpath '.$xpath);
            }
        }

        if ($histoFieldName && $before !== $after) {
            self::addHisto($dom, $before, $after, $xpath, $histoFieldName);
        }

        return $node;
    }

    private static function addHisto(&$dom, $before, $after, $xpath, $name)
    {
        // First generated element in historic for that session.
        if (self::$_historic_id === null) {
            $uuidObj = new UUID();
            self::$_historic_id = $uuidObj->uuid();
            self::addElement($dom, "/a:entry/a:content/x:div/x:ul[@class='historics']/x:li[last()+1]/@id", self::$_historic_id);
            $author = '';
            self::addElement($dom, "/a:entry/a:content/x:div/x:ul[@class='historics']/x:li[last()]/x:time", $author);
            $date = new \DateTime();
            self::addElement($dom, "/a:entry/a:content/x:div/x:ul[@class='historics']/x:li[last()]/x:time/@datetime", $date->format("Y-m-d\TH:i:s\Z"));
        }
        self::addElement($dom, "/a:entry/a:content/x:div/x:ul[@class='historics']/x:li[last()]/x:div[@class='$name']/x:p[@class='before']", $before);
        self::addElement($dom, "/a:entry/a:content/x:div/x:ul[@class='historics']/x:li[last()]/x:div[@class='$name']/x:p[@class='after']", $after);
        self::addElement($dom, "/a:entry/a:content/x:div/x:ul[@class='historics']/x:li[last()]/x:div[@class='$name']/x:span[@class='xpath']", $xpath);
        self::addElement($dom, "/a:entry/a:content/x:div/x:ul[@class='historics']/x:li[last()]/x:div[@class='$name']/x:span[@class='src']", Source::getInstance()->getSource());
    }

    /**
     * Delete elements from DomDocument.
     *
     * @param DOMDocument $dom
     * @param String      $xpath
     *
     * @return modified $dom
     */
    public static function removeElement(DOMDocument $dom, $xpath, DOMNode $rootNode = null, $histo = false, $name = null)
    {
        $effectifXpath = $xpath;

        // If XPath ends with /@id, delete the whole node
        if ('/@id' === substr($xpath, -4)) {
            $effectifXpath = rtrim($xpath, '/@id');
        }

        $node = XslUtil::findFirstNode($dom, $effectifXpath, $rootNode);
        while ($node !== null) {
            $before = null;
            if ('DOMElement' === get_class($node)) {
                $before = $node->nodeValue;
                $node->parentNode->removeChild($node);
            } elseif ('DOMAttr' === get_class($node)) {
                $before = $node->value;
                $node->parentNode->removeAttributeNode($node);
            }
            if ($histo) {
                self::addHisto($dom, $before, null, $xpath, $name);
            }

            $node = XslUtil::findFirstNode($dom, $effectifXpath, $rootNode);
        }

        return $dom;
    }

    /**
     * Import a content (fragment) in another (canva)
     * Import place in canva defined by html id (rootId).
     *
     * @param DOMDocument $canva
     * @param DOMDocument $fragment
     * @param String      $rootId
     *
     * @return DOMDocument canva with injected fragment.
     */
    public static function importElement(DOMDocument $canva, DOMDocument $fragment, $rootId)
    {
        $node = $canva->importNode($fragment->documentElement, true);
        $canva->getElementById($rootId)->appendChild($node);

        return $canva;
    }

    /**
     * Generate a DOMDocument object from a HTML asbolute file path.
     *
     * @param String $htmlFile
     *                         : file path
     *
     * @return DOMDocument
     */
    public static function loadHTMLFile($htmlFile)
    {
        $dom = self::loadHTML(file_get_contents($htmlFile));

        return $dom;
    }

    /**
     * Generate a DOMDocument object from a string containning HTML.
     *
     * @param String $html
     *                     : HTML string
     *
     * @return DOMDocument
     */
    public static function loadHTML($html)
    {
        $dom = self::createDOMDocument();
        @$dom->loadHTML('<?xml encoding="UTF-8">'.$html);

        return $dom;
    }

    /**
     * Generate a DOMDocument object from a XML asbolute file path.
     *
     * @param String $xmlFile
     *                        : file path
     *
     * @return DOMDocument
     */
    public static function loadXMLFile($xmlFile)
    {
        $dom = self::createDOMDocument();
        $dom->load($xmlFile);

        return $dom;
    }

    /**
     * Crée un DOMDocument à partir d'une chaîne de caractère représentant
     * le XML.
     *
     * @param String $xml
     *                    la donnée xml
     *
     * @return DOMDocument
     */
    public static function loadXML($xml)
    {
        $dom = self::createDOMDocument();
        $dom->loadXML($xml);

        return $dom;
    }

    /**
     * Crée un Document DOM vide.
     *
     * @return le DOMDocument
     */
    public static function createDOMDocument()
    {
        $dom = new \DOMDocument('1.0', 'utf-8');
        $dom->encoding = 'UTF-8';

        return $dom;
    }

    /**
     * Crée un flux atom initialisé (vide)
     *
     * @return le flux atom
     */
    public static function createAtomFeed($title)
    {
        $emptyAtom = <<<ATOMFRAG
        <feed xmlns="http://www.w3.org/2005/Atom">
            <title/>
            <link/>
            <id/>
            <updated/>
        </feed>
ATOMFRAG;
        $xmlAtom = new \DOMDocument('1.0', 'utf-8');
        $xmlAtom->loadXML($emptyAtom);
        $xmlAtom->encoding = 'UTF-8';
        self::addElement($xmlAtom, "/a:feed/a:title", $title);
        $uuid = new UUID();
        $uuid = $uuid->uuid();
        $pathOnly = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        self::addElement(
            $xmlAtom,
            "/a:feed/a:link",
            "http://"
            . $_SERVER["SERVER_NAME"]
            . $pathOnly
            . $uuid
        );
        self::addElement($xmlAtom, "/a:feed/a:id", $uuid);
        $date = new DateTime();
        self::addElement(
            $xmlAtom,
            "/a:feed/a:updated",
            $date->format("Y-m-d\TH:i:s\Z")
        );
    return $xmlAtom;
    }

    /**
     * Insère une ressource à un flux atom
     *
     * @param  xml      $atomFeed        flux atom
     * @param  xml      $xmlResource     xml à insérer
     * @param  string   $catName         nom de la catégorie où insérer
     *
     * @return le flux atom
     */
    public static function addAtomResource($atomFeed, $xmlResource, $tagName = 'entry')
    {
        $atomFeedNode = $atomFeed->getElementsByTagName('feed')->item(0);
        $xmlResources = $xmlResource->getElementsByTagName($tagName);
        foreach ($xmlResources as $xmlResource) {
            $node = $atomFeed->importNode($xmlResource, true);
            $atomFeedNode->appendChild($node);
        }
        return $atomFeed;
    }

    /**
     * Insère une ressource à un flux atom par XPATH
     *
     * @param  xml      $atomFeed        flux atom
     * @param  xpath    $xpath           xpath du flux de sortie
     * @param  xml      $xmlResource     xml à insérer
     * @param  string   $catName         nom de la catégorie où insérer
     *
     * @return le flux atom
     */
    public static function addAtomResourceByTag($atomFeed, $xpath, $xmlResource, $tagName = 'entry')
    {
        $atomFeedNode = XslUtil::findFirstNode($atomFeed, $xpath);
        $xmlResources = $xmlResource->getElementsByTagName($tagName);
        foreach ($xmlResources as $xmlResource) {
            $node = $atomFeed->importNode($xmlResource, true);
            $atomFeedNode->appendChild($node);
        }
        return $atomFeed;
    }

     /**
     * Insère une ressource à un flux atom par XPATH
     *
     * @param  xml      $feedCible        flux atom de sortie
     * @param  xpath    $xpathCible       xpath d'emplacement d'insertion dans le flux de sortie
     * @param  xml      $feedSource       flux d'entrée
     * @param  xpath    $xpathSource      xpath de l'élément à insérer
     *
     * @return le flux atom
     */
    public static function addAtomResourceByXPath($feedCible, $xpathCible, $feedSource, $xpathSource)
    {
        $atomFeedNode = XslUtil::findFirstNode($feedCible, $xpathCible);
        $nodes = XslUtil::findNodes($feedSource, $xpathSource);
        foreach ($nodes as $node) {
            $node = $feedCible->importNode($node, true);
            $atomFeedNode->appendChild($node);
        }
        return $feedCible;
    }

     /**
     * Insère une ressource à un flux atom par XPATH
     *
     * @param  xml      $feedCible        flux atom de sortie
     * @param  xpath    $xpathCible       xpath d'emplacement d'insertion dans le flux de sortie
     * @param  string   $nodeCible        Noeud à créer
     * @param  string or array $class            Class de l'élément créé (array ou string)
     * @param  string   $namespace        namespace
     *
     * @return le flux atom
     */
    public static function addNodeByXPath($feedCible, $xpath, $nodeCible, $value = null, $class = '', $id = '', $namespace = DomUtil::ATOM_NAMESPACE)
    {
        $feed = XslUtil::findFirstNode($feedCible, $xpath);
        $doc = new DOMDocument;
        $node = $doc->createElementNS($namespace, $nodeCible, $value);
        if (is_array($class)) {
            foreach ($class as $attributeName => $value) {
                $node->setAttribute($attributeName, $value);
            }
        } else if ($class != '') {
            $node->setAttribute('class', $class);
        }
        if ($id != '') {
            $node->setAttribute('id', $id);
        }
        $newnode = $doc->appendChild($node);
        $node = $feedCible->importNode($doc->documentElement, true);
        $feed->appendChild($node);
        return $feedCible;
    }

     /**
     * Insère une ressource à un flux atom par XPATH
     *
     * @param  xml      $feedCible        flux atom de sortie
     * @param  xpath    $xpathCible       xpath d'emplacement d'insertion dans le flux de sortie
     * @param  string   $nodeCible        Noeud à créer
     * @param  string   $class            Class de l'élément créé
     * @param  string   $namespace        namespace
     *
     * @return le flux atom
     */
    public static function addNodeInNode($domDoc, $nodeCible, $newNode, $value = null, $class = '', $namespace = DomUtil::ATOM_NAMESPACE)
    {
        $doc = self::createDOMDocument();
        $node = $doc->createElementNS($namespace, $newNode, $value);
        if ($class != '') {
            $node->setAttribute('class', $class);
        }
        $doc->appendChild($node);
        $node = $domDoc->importNode($doc->documentElement, true);
        $nodeCible->appendChild($node);
        return $domDoc;
    }

     /**
     * Insère une ressource à un flux atom par XPATH
     *
     * @param  xml      $feedCible        flux atom de sortie
     * @param  xpath    $xpathCible       xpath d'emplacement d'insertion dans le flux de sortie
     * @param  string   $nodeCible        Noeud à créer
     * @param  string   $class            Class de l'élément créé
     * @param  string   $namespace        namespace
     *
     * @return le flux atom
     */
    public static function addNodeBeforeNode($domDoc, $nodeCible, $newNode, $beforeNode, $value = null, $class = '', $namespace = DomUtil::ATOM_NAMESPACE)
    {
        $doc = self::createDOMDocument();
        $node = $doc->createElementNS($namespace, $newNode, $value);
        if ($class != '') {
            $node->setAttribute('class', $class);
        }
        $doc->appendChild($node);
        $node = $domDoc->importNode($doc->documentElement, true);
        $nodeCible->insertBefore($node, $beforeNode);
        return $domDoc;
    }
}
