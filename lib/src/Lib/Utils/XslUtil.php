<?php

namespace Moowgly\Lib\Utils;

class XslUtil
{
    const ATOM_NAMESPACE = 'http://www.w3.org/2005/Atom';

    const XHTML_NAMESPACE = 'http://www.w3.org/1999/xhtml';

    const OPENSEARCH_NAMESPACE = 'http://a9.com/-/spec/opensearch/1.1/';

    /**
     * Generate xml file from given xsl $xslFile and data $xml.
     *
     * @param $xslFile string
     *            : path + xsl filename
     * @param $xml DOMDocument
     *            : xml data file
     * @param $xslParams Array
     *            : optional, XSL parameters
     *
     * @return string : generated xml
     */
    public static function applyXSL($xslFile, $xml, $xslParams = null)
    {
        if (!class_exists('xsltCache')) {
            $proc = new \XSLTProcessor();
            // create DOM object
            $xsl = new \DOMDocument();
            $xslFilePath = stream_resolve_include_path($xslFile);
            if (@$xsl->load($xslFilePath) === false) {
                throw new \Exception("XSL style sheet $xslFile not found.");
            }
            // Transform xml with xsl
            $proc->importStylesheet($xsl);
        } else {
            $proc = new \xsltCache();
            $proc->importStyleSheet($xslFile);
        }
        if (null !== $xslParams) {
            $proc->setParameter('', $xslParams);
        }

        return $proc->transformToXML($xml);
    }

    /**
     * Find document ($dom) nodes matching with XPath query ($xPathQuery).
     *
     * @param DOMDocument $dom
     *                                : DOM object to query
     * @param String      $xPathQuery
     *                                : XPath query
     * @param DOMNode     $domNode
     *                                optional, if XPath relative to that node
     *
     * @return DOMNodeList : List of query matching nodes
     */
    public static function findNodes(\DOMDocument $dom, $xPathQuery, \DOMNode $domNode = null)
    {
        try {
            $xpath = new \DOMXpath($dom);
            $xpath->registerNamespace('a', self::ATOM_NAMESPACE);
            $xpath->registerNamespace('x', self::XHTML_NAMESPACE);
            $xpath->registerNamespace('os', self::OPENSEARCH_NAMESPACE);
            // Execute XPath query
            $domNodeList = $xpath->query($xPathQuery, $domNode);

            if ($domNodeList === false) {
                Logger::getInstance()->debug('invalid xpath->query(xPathQuery='.$xPathQuery.', domNode)');
            }

            return $domNodeList;
        } catch (\Exception $e) {
            Logger::getInstance()->debug('xpath->query(xPathQuery='.$xPathQuery.', domNode) unknown');

            return;
        }
    }

    /**
     * Find document ($dom) first node matching with XPath query ($xPathQuery).
     *
     * @param DOMDocument $dom
     *                                : DOM object to query
     * @param String      $xPathQuery
     *                                : XPath query
     * @param DOMNode     $domNode
     *                                optional, if XPath relative to that node
     *
     * @return DOMNode : First node matching XPath query, null if no result
     */
    public static function findFirstNode(\DOMDocument $dom, $xPathQuery, \DOMNode $domNode = null)
    {
        $nodes = self::findNodes($dom, $xPathQuery, $domNode);
        if (false === $nodes || 0 == $nodes->length) {
            return;
        }

        return $nodes->item(0);
    }

    /**
     * Find document ($dom) node matching with XPath query ($xPathQuery)
     * indicating node position.
     *
     * @param DOMDocument $dom
     *                                : DOM object to query
     * @param String      $xPathQuery
     *                                : XPath query
     * @param DOMNode     $domNode
     *                                optional, if XPath relative to that node
     * @param Integer     $position
     *                                matching node position
     *
     * @return DOMNode : Node matching XPath query and position, null if no result
     */
    public static function findOneNode(\DOMDocument $dom, $xPathQuery, \DOMNode $domNode = null, $position)
    {
        $nodes = self::findNodes($dom, $xPathQuery, $domNode);
        if (false === $nodes || 0 == $nodes->length) {
            return;
        }

        return $nodes->item($position);
    }

    /**
     * Count how many nodes in document ($dom) match with XPath query ($xPathQuery).
     *
     * @param DOMDocument $dom
     *                                : DOM object to query
     * @param String      $xPathQuery
     *                                : XPath query
     * @param DOMNode     $domNode
     *                                optional, if XPath relative to that node
     *
     * @return int : number of matching nodes
     */
    public static function countNodes(\DOMDocument $dom, $xPathQuery, \DOMNode $domNode = null)
    {
        $nodes = self::findNodes($dom, $xPathQuery, $domNode);

        if (false === $nodes) {
            return 0;
        }

        return $nodes->length;
    }

    /**
     * Return value of first node matching with XPath query ($xPathQuery).
     *
     * @param DOMDocument $dom
     *                                : DOM object to query
     * @param String      $xPathQuery
     *                                : XPath query
     * @param DOMNode     $domNode
     *                                optional, if XPath relative to that node
     *
     * @return string value of first node matching XPath query, null if no result
     */
    public static function findFirstNodeValue(\DOMDocument $dom, $xPathQuery, \DOMNode $domNode = null)
    {
        $nodes = self::findNodes($dom, $xPathQuery, $domNode);

        if (false === $nodes || 0 == $nodes->length) {
            return;
        }
        $node = $nodes->item(0);

        return self::getNodeValue($node);
    }

    /**
     * Return value of node matching with XPath query ($xPathQuery)
     * indicating node position.
     *
     * @param DOMDocument $dom        DOM object to query
     * @param string      $xPathQuery XPath query
     * @param DOMNode     $domNode    optional, if XPath relative to that node
     * @param int         $position   matching node position
     *
     * @return string value of node matching XPath query and position, null if no result
     */
    public static function findOneNodeValue(\DOMDocument $dom, $xPathQuery, \DOMNode $domNode = null, $position)
    {
        $node = self::findOneNode($dom, $xPathQuery, $domNode, $position);
        if (is_null($node)) {
            return;
        }

        return self::getNodeValue($node);
    }

    /**
     * Return value of given node.
     *
     * @param DOMNode|DOMAttr|null $node Node
     *
     * @return Node value, null if no nodes given
     */
    public static function getNodeValue($node)
    {
        if (!isset($node)) {
            return;
        }

        return is_a($node, 'DOMAttr') ? $node->value : $node->nodeValue;
    }
}
