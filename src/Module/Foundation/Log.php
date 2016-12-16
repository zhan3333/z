<?php
/**
 * Created by PhpStorm.
 * User: zhan
 * Date: 2016/12/16
 * Time: 10:27
 */

namespace App\Module\Foundation;

use App\Factory;
use Monolog\Handler\ErrorLogHandler;
use Monolog\Handler\NullHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

class Log
{
    /**
     * Logger instance.
     *
     * @var \Psr\Log\LoggerInterface
     */
    protected static $loggers;

    /**
     * Return the logger instance.
     *
     * @param $logId
     * @return LoggerInterface
     */
    public static function getLogger($logId)
    {
        if (empty(self::$loggers[$logId])) {
            self::$loggers[$logId] = self::createDefaultLogger();
        }
        return self::$loggers[$logId];
    }

    /**
     * Set logger.
     *
     * @param $logId
     * @param \Psr\Log\LoggerInterface $logger
     */
    public static function setLogger($logId, LoggerInterface $logger)
    {
        self::$loggers[$logId] = $logger;
    }

    /**
     * Tests if logger exists.
     *
     * @param $logId
     * @return bool
     */
    public static function hasLogger($logId)
    {
        return !empty(self::$loggers[$logId]);
    }

    /**
     * Forward call.
     *
     * @param string $method
     * @param array $args
     * @return mixed
     */
    public static function __callStatic($method, $args)
    {
        $logId = array_shift($args);
        return forward_static_call_array([self::getLogger($logId), $method], $args);
    }

    /**
     * Forward call.
     *
     * @param string $method
     * @param array $args
     * @return mixed
     */
    public function __call($method, $args)
    {
        $logId = array_shift($args);
        return call_user_func_array([self::getLogger($logId), $method], $args);
    }

    /**
     * Make a default log instance.
     *
     * @return \Monolog\Logger
     */
    private static function createDefaultLogger()
    {
        $log = new Logger('default');

        if (defined('PHPUNIT_RUNNING')) {
            $log->pushHandler(new NullHandler());
        } else {
            $log->pushHandler(new ErrorLogHandler());
        }
        return $log;
    }
}