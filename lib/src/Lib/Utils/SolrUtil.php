<?php

namespace Moowgly\Lib\Utils;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;

class SolrUtil
{
    /**
     * Send a solr transaction commit.
     *
     * @param string $core
     *                     Solr core name
     *
     * @return \Guzzle\Http\Message\Response Solr Response HTTP
     */
    public static function commit($core = null)
    {
        if (empty($core)) {
            $core = strtolower($_SERVER['PROJECT_NAME']);
        }

        $client = new Client();

        $urlSolr = $_SERVER['PROJECT_SOLR_URL'].'/'.$core;
        Logger::getInstance()->debug('commit - '.$_SERVER['PROJECT_NAME'].' url : '.$urlSolr);

        $response = $client->get($urlSolr.'/update?commit=true');

        Logger::getInstance()->debug('commit - response : '.$response->getBody());

        return $response;
    }

    /**
     * Add a document in the Solr Index.
     *
     * @param string $xml
     *                       Document XML décrivant la ressource à indexer.
     * @param bool   $commit
     *                       Indique s'il faut commiter cet ajout.
     * @param string $core
     *                       Solr core name
     *
     * @return \Guzzle\Http\Message\Response La réponse HTTP retournée par Solr.
     */
    public static function sendToSolr($xml, $commit = true, $core = null)
    {
        if (empty($core)) {
            $core = strtolower($_SERVER['PROJECT_NAME']);
        }

        Logger::getInstance()->debug('sendToSolr');
        Logger::getInstance()->error('sendToSolr - document : '.$xml);

        $client = new Client();

        $urlSolr = $_SERVER['PROJECT_SOLR_URL'].'/'.$core;
        Logger::getInstance()->debug('sendToSolr - '.$_SERVER['PROJECT_NAME'].' url : '.$urlSolr);

        $response = $client->post($urlSolr.'/update'.($commit ? '?commit=true' : ''), [
            'headers' => [
                'Content-Type' => 'text/xml',
            ],
            'body' => $xml,
        ]);

        Logger::getInstance()->error('sendToSolr : posted. Response status: '.self::getSolrResponseStatus($response));

        return $response;
    }

    /**
     * Return the solr response status.
     *
     * @param string $solrResponse
     *                             Solr response
     *
     * @return string
     */
    public static function getSolrResponseStatus($solrResponse)
    {
        $dom = new \DOMDocument();
        $dom->loadXml($solrResponse->getBody(true));

        return XslUtil::findFirstNodeValue($dom, '/response/lst[@name="responseHeader"]/int[@name="status"]');
    }

    /**
     * Send a document JSON to solr.
     *
     * @param string $json
     *                     Document JSON
     * @param string $core
     *                     Solr core name
     *
     * @return Response
     */
    public static function sendJsonToSolr($json, $core = null)
    {
        if (empty($core)) {
            $core = strtolower($_SERVER['PROJECT_NAME']);
        }

        $client = new Client();

        $urlSolr = $_SERVER['PROJECT_SOLR_URL'].'/'.$core;
        Logger::getInstance()->debug('sendJsonToSolr - '.$_SERVER['PROJECT_NAME'].' url : '.$urlSolr);

        $response = $client->post($urlSolr.'/update', [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'body' => $json,
        ]);

        return $response;
    }

    /**
     * Delete a document with the document identifiant.
     *
     * @param mixed  $ids
     *                       document id
     * @param bool   $commit
     *                       commit the transaction
     * @param string $core
     *                       Solr core name
     *
     * @return Response
     */
    public static function deleteFromSolr($ids, $commit = true, $core = null)
    {
        if (empty($core)) {
            $core = strtolower($_SERVER['PROJECT_NAME']);
        }

        if (is_array($ids)) {
            $xml = new \DOMDocument('1.0', 'utf-8');
            $xmlDelete = $xml->createElement('delete');
            foreach ($ids as $id) {
                $xmlId = $xml->createElement('id', $id);
                $xmlDelete->appendChild($xmlId);
            }
            $xml->appendChild($xmlDelete);
            $xml = $xml->saveXML();
        } else {
            $xml = '<delete><id>'.$ids.'</id></delete>';
        }

        Logger::getInstance()->debug('prepare Solr delete command: '.$xml);

        $client = new Client();

        $urlSolr = $_SERVER['PROJECT_SOLR_URL'].'/'.$core;
        Logger::getInstance()->debug('sendJsonToSolr - '.$_SERVER['PROJECT_NAME'].' url : '.$urlSolr);

        $response = $client->post($urlSolr.'/update'.($commit ? '?commit=true' : ''), [
            'headers' => [
                'Content-Type' => 'text/xml; charset=UTF8',
            ],
            'body' => $xml,
            'timeout' => 120,
        ]);

        return $response;
    }

    /**
     * Method for the solr search.
     *
     * @param array $options
     *                       solr parameters
     *
     * @return string
     */
    public static function searchInSolr(array $options)
    {
        $q = urlencode(isset($options['q']) ? $options['q'] : '');
        $qf = urlencode(isset($options['qf']) ? $options['qf'] : '');
        $df = urlencode(isset($options['df']) ? $options['df'] : '');
        if (isset($options['fq']) && is_array($options['fq'])) {
            $fq = implode('&fq=', $options['fq']);
        } else {
            $fq = urlencode(isset($options['fq']) ? $options['fq'] : '');
        }
        $fl = urlencode(isset($options['fl']) ? $options['fl'] : '');
        $rows = urlencode(isset($options['rows']) ? $options['rows'] : 10);
        $start = urlencode(isset($options['start']) ? $options['start'] : 0);
        $sort = urlencode(isset($options['sort']) ? $options['sort'] : '');
        $wt = isset($options['wt']) ? $options['wt'] : 'xml';
        $defType = isset($options['defType']) ? $options['defType'] : 'edismax';
        $mm = isset($options['mm']) ? $options['mm'] : '100%25';
        $indent = isset($options['indent']) ? $options['indent'] : 'true';
        $fieldAliases = isset($options['fieldAliases']) ? $options['fieldAliases'] : '';
        $facetFields = isset($options['facetFields']) && $options['facetFields'] != '' ? 'true&facet.field='.$options['facetFields'] : 'false';
        $facetMincount = isset($options['facetMincount']) && $options['facetMincount'] != '' ? '&facet.mincount='.$options['facetMincount'] : '';
        $facetLimit = isset($options['facetLimit']) && $options['facetLimit'] != '' ? '&facet.limit='.$options['facetLimit'] : '';
        $statsFields = isset($options['statsFields']) && $options['statsFields'] != '' ? 'true&stats.field='.$options['statsFields'] : 'false';
        $groupFields = isset($options['groupFields']) && $options['groupFields'] != '' ? 'true&group.field='.$options['groupFields'].'&group.limit=2147483647' : 'false';
        $rows = $groupFields != 'false' ? 2147483647 : $rows;
        $jsonFacet = isset($options['jsonFacet']) ? '&json.facet='.$options['jsonFacet'] : '';
        $core = urlencode(isset($options['core']) ? $options['core'] : strtolower($_SERVER['PROJECT_NAME']));

        $urlSolr = $_SERVER['PROJECT_SOLR_URL'].'/'.$core;
        Logger::getInstance()->debug('searchInSolr - '.$_SERVER['PROJECT_NAME'].' url : '.$urlSolr);

        $client = new Client();

        $url = $urlSolr.'/select?'.'q='.$q.'&df='.$df.'&qf='.$qf.'&fq='.$fq.'&fl='.$fl;
        $url .= '&rows='.$rows.'&start='.$start.'&sort='.$sort;

        if (!empty($defType)) {
            $url .= '&defType='.$defType;
        }
        $url .= '&mm='.$mm.'&wt='.$wt;
        $url .= '&indent='.$indent.$fieldAliases.'&facet='.$facetFields.'&stats='.$statsFields.$facetMincount.$facetLimit.'&group='.$groupFields.$jsonFacet;

        Logger::getInstance()->debug('search in Solr url: '.$url);

        try {
            $response = $client->get($url);
        } catch (RequestException $e) {
            Logger::getInstance()->debug('e->getRequest() : '.var_export($e->getRequest(), true));
            if ($e->hasResponse()) {
                Logger::getInstance()->debug('e->getResponse() : '.var_export($e->getResponse(), true));
            }
        } catch (ClientException $e) {
            Logger::getInstance()->debug('e->getRequest() : '.var_export($e->getRequest(), true));
            Logger::getInstance()->debug('e->getResponse() : '.var_export($e->getRequest(), true));
        }

        return $response;
    }

    /**
     * Method for solr suggest.
     *
     * @param array $options
     *
     * @return string
     */
    public static function suggestFromSolr(array $options)
    {
        $q = urlencode(isset($options['q']) ? $options['q'] : '');
        $wt = isset($options['wt']) ? $options['wt'] : 'xml';
        $dictionary = isset($options['dictionary']) ? $options['dictionary'] : 'default';
        $build = isset($options['build']) ? $options['build'] : 'true';
        $indent = isset($options['indent']) ? $options['indent'] : 'true';
        $core = urlencode(isset($options['core']) ? $options['core'] : strtolower($_SERVER['PROJECT_NAME']));
        $count = isset($options['count']) ? $options['count'] : '10';

        $urlSolr = $_SERVER['PROJECT_SOLR_URL'].'/'.$core;
        Logger::getInstance()->debug('suggestFromSolr - '.$_SERVER['PROJECT_NAME'].' url : '.$urlSolr);

        $client = new Client();
        $url = $urlSolr.'/suggest?'.'suggest.q='.$q.'&wt='.$wt.'&indent='.$indent.'&suggest.dictionary='.$dictionary.'&suggest.build='.$build.'&suggest.count='.$count;

        return $client->get($url);
    }

    public static function searchContentByJsonFacet($query, $field, $sumField = '', $rawParam = '', $sortOrder = 'desc', $limit = 0)
    {
        if ($limit == 0) {
            $limit = 2147483647;
        }
        $query .= '&rows=0&json.facet={categories:{type : terms,field : ' . $field . ',limit : ' . $limit;
        if ($sumField != '') {
            $query .= ',sort : { sum : ' . $sortOrder . '},facet:{sum : "sum(' . $sumField . ')"}}}';
        } else {
            $query .= '}}';
        }

        if ($rawParam != '') {
            $query = $rawParam . '&' . $query;
        }
        $url = $_SERVER['PROJECT_SOLR_URL'] . '/' . strtolower($_SERVER['PROJECT_NAME']) . '/select';
        $client = new Client();

        try {
            $response = $client->post($url, [
                'headers' => ['Content-Type' => 'application/x-www-form-urlencoded'],
                'form_params' => array($query)
            ]);
        } catch (RequestException $e) {
            Logger::getInstance()->debug('e->getRequest() : '.var_export($e->getRequest(), true));
            if ($e->hasResponse()) {
                Logger::getInstance()->debug('e->getResponse() : '.var_export($e->getResponse(), true));
            }
        } catch (ClientException $e) {
            Logger::getInstance()->debug('e->getRequest() : '.var_export($e->getRequest(), true));
            Logger::getInstance()->debug('e->getResponse() : '.var_export($e->getRequest(), true));
        }
        return $response;
    }
}
