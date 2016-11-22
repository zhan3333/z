<?php
// bootstrap.php
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use App\Factory;

require_once "vendor/autoload.php";
define('CONFPATH', __DIR__.'/src/Config/');
define('APPPATH', __DIR__);

// Create a simple "default" Doctrine ORM configuration for Annotations
$isDevMode = true;
$paths = [
    __DIR__ . '/src/Entities'
];
$config = Setup::createAnnotationMetadataConfiguration($paths, $isDevMode);
// or if you prefer yaml or XML
//$config = Setup::createXMLMetadataConfiguration(array(__DIR__."/config/xml"), $isDevMode);
//$config = Setup::createYAMLMetadataConfiguration(array(__DIR__."/config/yaml"), $isDevMode);

// database configuration parameters
Factory::initConfig();
$conn = Factory::getConfig('db', 'master');
if (empty($conn)) exit('读取数据库配置文件失败');
// obtaining the entity manager
$entityManager = EntityManager::create($conn, $config);