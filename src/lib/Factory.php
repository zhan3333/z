<?php

namespace App;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Doctrine\ORM;
use Doctrine\Common\Cache as OrmCache;

/**
 * 工厂类
 * User: 39096
 * Date: 2016/8/24
 * Time: 22:25
 */
class Factory
{
    /**
     * @var \main
     */
    private static $server = null;   //服务器对象
    private static $config = [];     //配置信息
    private static $logger = [];     //日志对象

    private static $objects = [];   // 实例对象数组
    private static $allowMultipleInstances = [];    // 允许多实例的对象


    /**
     * 允许多实例的模块
     * @var array
     */
    private static $multiInstance = [
        'db' => true,
        'redis' => true,
        'mail' => true
    ];

    /**
     * ['配置文件名称' => '插件所在文件夹']
     * @var array
     */
    private static $plugins = [
    ];

    public static function __callStatic($func, $param)
    {
        if (!empty(self::$multiInstance[$func]) ) {
            $modelKey = (empty($param[0]) or !is_string($param[0]) )?'master':$param[0];
            return self::loadModule($func, $modelKey);
        }
        else {
            throw new \Exception("call an undefine method[$func].");
        }
    }

    /**
     * 加载应用模块
     * @param $module
     * @param $key
     * @return mixed
     */
    protected static function loadModule($module, $key = 'master')
    {
        $objectId = $module;
        if(isset(self::$multiInstance[$module]) ) $objectId .= '_' .$key;
        if (empty(self::$objects[$objectId]) )
        {
            $className = __NAMESPACE__ .'\\'.self::$modules[$module];
            if(empty(self::$multiInstance[$module]) ) {
                $moduleConfig = self::getConfig($module);
            }
            else {
                $moduleConfig = self::getConfig($module, $key);
            }
            self::$objects[$objectId] = new $className($moduleConfig);
        }
        return self::$objects[$objectId];
    }

    /**
     * 加载三方插件
     * @param string $plugin        配置文件名称
     * @param string $api           扩展名称；类名称
     * @return mixed
     */
    protected static function loadPlugin($plugin, $api = '')
    {
        $objectId = $plugin;
        if (empty(self::$objects[$objectId]) ) {
            $pluginConfig = self::getConfig($plugin);
            if(empty($api) ) $api = key($pluginConfig);
            $className = __NAMESPACE__ .'\\'.self::$plugins[$plugin].'\\'.$api;
            $pluginConfig = $pluginConfig[$api];
            self::$objects[$objectId] = new $className($pluginConfig);
        }
        return self::$objects[$objectId];
    }

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
        $handle = opendir(CONFPATH);
        while (false !== ($file = readdir($handle))) {
            if ($file != '.' && $file != '..') {
                $configExt =explode('.', $file)[1];
                if ($configExt == 'php') {
                    $configName = explode('.', $file)[0];
                    self::$config[$configName] = require CONFPATH . $file;
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