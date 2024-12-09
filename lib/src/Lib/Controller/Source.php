<?php

namespace Moowgly\Lib\Controller;

class Source
{
    private static $_instance;

    private static $_source = 'moowgly';

    private function __construct()
    {
    }

    public static function getInstance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public static function setSource($src)
    {
        self::$_source = $src;
    }

    public static function getSource()
    {
        return self::$_source;
    }
}
