<?php

namespace Moowgly\Lib\Utils;

class UUID
{
    private static $PRINT_FORMAT = '%04x%04x-%04x-%04x-%04x-%04x%04x%04x';

    private static $CHECK_FORMAT =
        '/^[a-z0-9]{8}-[a-z0-9]{4}-[a-z0-9]{4}-[a-z0-9]{4}-[a-z0-9]{12}$/';

    /**
     * Generate a random UUID.
     *
     * @return string UUID
     */
    public static function uuid()
    {
        return sprintf(
            self::$PRINT_FORMAT,
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }

    /**
     * Generate a UUID from given md5.
     *
     * @param string $md5
     *
     * @return string UUID
     */
    public static function md5touuid($md5)
    {
        if (strlen($md5) != 32) {
            return;
        }
        $md5array = array();
        for ($i = 0; $i < 32; $i += 4) {
            $md5array[] = substr($md5, $i, 4);
        }
        $md5array = array_map('hexdec', $md5array);

        return sprintf(
            self::$PRINT_FORMAT,
            $md5array[0],
            $md5array[1],
            $md5array[2],
            ($md5array[3] & 0x0fff) | 0x4000,
            ($md5array[4] & 0x3fff) | 0x8000,
            $md5array[5],
            $md5array[6],
            $md5array[7]
        );
    }

    /**
     * Generate a UUID from given email.
     *
     * @param string $str
     *
     * @return string UUID
     */
    public static function strToUuid($str)
    {
        return self::md5touuid(md5($str));
    }

    /**
     * Test if the passed $uuid has the expected format of uuid.
     *
     * @param string $uuid
     *
     * @return bool true if $uuid has the UUID format
     */
    public static function isValidUuid($uuid)
    {
        return (1 == preg_match(self::$CHECK_FORMAT, $uuid));
    }
}
