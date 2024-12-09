<?php

namespace Moowgly\Lib\Utils;

class HttpPutParser
{
    public static function parse($rawHttpData)
    {
        $datas = array();

        // grab multipart boundary from content type header
        preg_match('/boundary=(.*)$/', $_SERVER['CONTENT_TYPE'], $matches);
        $boundary = $matches[1];

        // split content by boundary and get rid of last -- element
        $a_blocks = preg_split("/-+$boundary/", $rawHttpData);
        array_pop($a_blocks);

        // loop data blocks
        foreach ($a_blocks as $id => $block) {
            if (empty($block)) {
                continue;
            }

            // you'll have to var_dump $block to understand this and maybe replace \n or \r with a visibile char

            // parse uploaded files
            if (preg_match("/(Content\-Length:[\s]+)([a-f0-9]+)\r\n/i", $block, $match)) {
                $block = str_replace(array($match[1], $match[2]), '', $block);
            }

            if (strpos($block, 'application/octet-stream') !== false) {
                // match "name", then everything after "stream" (optional) except for prepending newlines
                preg_match("/name=\"([^\"]*)\".*stream[\n|\r]+([^\n\r].*)?$/s", $block, $matches);
            }
            // parse all other fields
            else {
                // match "name" and optional value in between newline sequences
                preg_match('/name=\"([^\"]*)\"[\n|\r]+([^\n\r].*)?\r$/s', $block, $matches);
            }

            $data = null;
            mb_parse_str($matches[2], $data);
            $datas[$matches[1]] = $data;
        }
//         mb_parse_str($rawHttpData, $datas);

        return $datas;
    }
}
