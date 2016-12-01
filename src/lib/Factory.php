<?php

namespace App;

use App\Module\Cache\Redis;

use EasyWeChat\Foundation\Application;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Doctrine\ORM;
use Doctrine\Common\Cache as OrmCache;
use Symfony\Component\HttpFoundation\Request;

use Doctrine\MongoDB\Connection;
use Doctrine\ODM\MongoDB\Configuration;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;

/**
 * 工厂类
 * User: 39096
 * Date: 2016/8/24
 * Time: 22:25
 */
class Factory
{
    /**
     * @var \swoole_server
     */
    private static $server = null;   //服务器对象
    private static $config = [];     //配置信息
    private static $logger = [];     //日志对象
    /**
     * @var Application
     */
    private static $wechat = null;
    // 模块所在空间
    private static $modules = [
        'redis' => 'Module\\Cache\\Redis'
    ];       

    private static $objects = [];   // 实例对象数组

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
        if(isset(self::$multiInstance[$module]) ) $objectId .= '_' .$key;   // 模块命名
        if (empty(self::$objects[$objectId]) )
        {
            $className = __NAMESPACE__ .'\\'.self::$modules[$module];       // 获取模块路径
            $moduleConfig = self::getConfig($module, $key);                 // 加载配置文件
            self::$objects[$objectId] = new $className($moduleConfig);      // 创建对象
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

    /**
     * 初始化服务器对象
     * @param \swoole_server $server
     */
    public static function initServer($server)
    {
        self::$server = $server;
    }

    /**
     * 服务器对象
     * @return \swoole_server
     */
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
     * 获取Config文件夹中的配置信息
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
     * 获取日志打印对象
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

    /**
     * 加载redis模块
     * @param string $key
     * @return Redis
     */
    public static function redis($key = 'master')
    {
        return self::loadModule('redis', $key);
    }

    /**
     * 获取EntityManager数据库操作对象
     * @param string $module
     * @param string $key
     * @return ORM\EntityManager
     * @throws ORM\ORMException
     */
    public static function em($module = 'db', $key = 'master')
    {
        $objectId = $module;
        if(!empty(self::$multiInstance[$module]) ) $objectId .= '_' .$key;   // 模块命名
        if (!empty(self::$objects[$objectId])) return self::$objects[$objectId];

        $isDevMode = true;
        $paths = [
            __DIR__ . '/../Entities'
        ];
        $configObj = ORM\Tools\Setup::createAnnotationMetadataConfiguration($paths, $isDevMode);
        $configObj->setEntityNamespaces(['' => "App\\Entities\\", 'e' => "App\\Entities\\"]);
        $conn = self::getConfig($module, $key);
        $em = ORM\EntityManager::create($conn, $configObj);

        self::$objects[$objectId] = $em;
        return $em;
    }

    public static function dm($module = 'mongodb', $key = 'master')
    {
        $objectId = $module;
        if(!empty(self::$multiInstance[$module]) ) $objectId .= '_' .$key;   // 模块命名
        if (!empty(self::$objects[$objectId])) return self::$objects[$objectId];

        AnnotationDriver::registerAnnotationClasses();

        $config = new Configuration();
        $config->setProxyDir(APPPATH . '/src/Proxies');
        $config->setProxyNamespace('App\\Proxies');
        $config->setDocumentNamespaces(['' => 'App\\Documents']);
        $config->setHydratorDir(APPPATH . '/src/Hydrators');
        $config->setHydratorNamespace('App\\Hydrators');
        $config->setDefaultDB('doctrine_odm');
        $config->setMetadataDriverImpl(AnnotationDriver::create([APPPATH . '/src/Documents']));
        $dm = DocumentManager::create(new Connection(), $config);
        self::$objects[$objectId] = $dm;
        return $dm;
    }

    // 微信相关

    /**
     * 获取wechat对象
     * @return Application
     */
    public static function wechat()
    {
        if (empty(self::$wechat)) {
            $wechatConfig = Factory::getConfig('wechat', 'wechat');
            self::$wechat = new Application($wechatConfig);
        }
        return self::$wechat;
    }

    /**
     * 获取 Request 对象
     * @return Request
     */
    public static function getRequestObj()
    {
        $content = empty($GLOBALS['php://input'])?'':$GLOBALS['php://input'];
        $request = new Request($_GET, $_POST, [], $_COOKIE, $_FILES, $_SERVER, $content);
        return $request;
    }

    // 解析设备标识对象

    /**
     * 解析设备标识对象
     * @return Agent
     */
    public static function agent()
    {
        $objectId = __FUNCTION__;
        if (empty(self::$objects[$objectId])) {
            self::$objects[$objectId] = new Agent();
        }
        return self::$objects[$objectId];
    }

    // geoIp，解析ip地址对象

    /**
     * @param string $local
     * @return Reader
     */
    public static function geoIp($local = 'zh-CN')
    {
        $objectId = __FUNCTION__ . '.' . $local;
        if (empty(self::$objects[$objectId])) {
            $geoIp = new Reader(self::getConfig('app', 'geoIpDatabasesPath'));
            self::$objects[$objectId] = $geoIp;
        }
        return self::$objects[$objectId];
    }

    /**
     * 解析ip地址，获取ip地址对应的城市，省，国家
     * @param $ip
     * @return array
     */
    public static function parseIp($ip)
    {
        $city = null;
        $province = null;
        $country = null;
        try {
            $geoIp = self::geoIp();
            $record = $geoIp->city($ip);
            $country = $record->country->names['zh-CN'];
            $city = $record->city->names['zh-CN'];
            $province = $record->mostSpecificSubdivision->names['zh-CN'];
        } catch (\Exception $e) {
        }
        return [
            'city' => $city,
            'country' => $country,
            'province' => $province
        ];
    }
}