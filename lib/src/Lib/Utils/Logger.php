<?php

namespace Moowgly\Lib\Utils;

use Psr\Log\AbstractLogger;
use Psr\Log\LogLevel;

/**
 * PSR-3 compliant logger class.
 *
 * @link http://www.php-fig.org/psr/psr-3/fr/
 */
class Logger extends AbstractLogger
{
    private static $logger = null;

    const MODULE_TYPE = 'MODULE_TYPE';

    const MODULE_NAME = 'MODULE_NAME';

    /**
     * @return The Logger instance
     */
    public static function getInstance()
    {
        if (is_null(self::$logger)) {
            self::$logger = new self();
        }

        return self::$logger;
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed  $level
     * @param string $message
     * @param array  $context
     */
    public function log($level, $message, array $context = array())
    {
        $replace = array();

        if ($context != null && array_key_exists(self::MODULE_NAME, $context)) {
            $message = '[{'.self::MODULE_NAME.'}] '.$message;
        }

        if ($context != null && array_key_exists(self::MODULE_TYPE, $context)) {
            $message = '[{'.self::MODULE_TYPE.'}] '.$message;
        }
        foreach ($context as $key => $val) {
            $replace['{'.$key.'}'] = $val;
        }

        // interpolate replacement values into the message and return
        $message = strtr($message, $replace);

        switch ($level) {
            case LogLevel::EMERGENCY:
                $level = LOG_EMERG;
                break;
            case LogLevel::ALERT:
                $level = LOG_ALERT;
                break;
            case LogLevel::CRITICAL:
                $level = LOG_CRIT;
                break;
            case LogLevel::ERROR:
                $level = LOG_ERR;
                break;
            case LogLevel::WARNING:
                $level = LOG_WARNING;
                break;
            case LogLevel::NOTICE:
                $level = LOG_NOTICE;
                break;
            case LogLevel::INFO:
                $level = LOG_INFO;
                break;
            case LogLevel::DEBUG:
                $level = LOG_DEBUG;
                break;
            default:
                $level = LOG_INFO;
                break;
        }

        //syslog($level, $message);
    }
}
