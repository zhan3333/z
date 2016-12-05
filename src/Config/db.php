<?php
/**
 * Created by PhpStorm.
 * User: zhan
 * Date: 2016/11/22
 * Time: 12:14
 */
$master = [
    'driver' => 'pdo_mysql',
    'user' => '',
    'password' => '',
    'dbname' => 'z',
    'host' => '127.0.0.1',
    'charset' => 'utf8',
    // [doctrine-mysql-come-back] settings
    'wrapperClass' => 'Facile\DoctrineMySQLComeBack\Doctrine\DBAL\Connection',
    'driverClass' => 'Facile\DoctrineMySQLComeBack\Doctrine\DBAL\Driver\PDOMySql\Driver',
    'driverOptions' => [
        'x_reconnect_attempts' => 3
    ]
];
return [
    'master' => $master
];
