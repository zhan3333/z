<?php

namespace App;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
/**
 * 工厂类
 * User: 39096
 * Date: 2016/8/24
 * Time: 22:25
 */
class Factory
{
    public static $server = null;   //服务器对象
    public static $config = [];     //配置信息
    public static $logger = [];     //日志对象

    public static function initServer($server)
    {
        self::$server = $server;
    }

    public static function swoole()
    {
        return self::$server;
    }

    /**
     * 初始化配置
     */
    public static function initConfig()
    {
        if (false !== ($file = readdir(opendir(CONFIG_PATH)))) {
            if ($file != '.' && $file != '..') {
                $configExt =explode('.', $file)[1];
                if ($configExt == 'php') {
                    $configName = explode('.', $file)[0];
                    self::$config[$configName] = require CONFIG_PATH . $file;
                }
            }
        }
    }

    /**
     * @param $name             string      文件名
     * @param array ...$item
     * @return null
     */
    public static function getConfig($name, ...$item)
    {
        if (empty(self::$config[$name])) return null;
        $ret = self::$config[$name];
        foreach ($item as $item2) {
            $ret = empty($ret[$item2])?null:$ret[$item2];
        }
        return $ret;
    }

    /**
     * @param $name
     * @return Logger
     */
    public static function logger($name)
    {
        if (!empty(self::$logger[$name])) {
            return self::$logger[$name];
        } else {
            $log = new Logger($name);
            self::$logger[$name] = $log->pushHandler(new StreamHandler(WEBPATH . '/log/'.$name.'.log'));
            return self::$logger[$name];
        }
    }
}