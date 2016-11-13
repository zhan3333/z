<?php
// bootstrap.php
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

require_once "vendor/autoload.php";

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
$conn = [
    'driver' => 'pdo_mysql',
    'user' => 'root',
    'password' => 'Z283779377g',
    'dbname' => 'z'
];

// obtaining the entity manager
$entityManager = EntityManager::create($conn, $config);