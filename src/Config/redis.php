<?php
/**
 * redis缓存配置
 * User: 39096
 * Date: 2016/11/13
 * Time: 12:19
 */

$master = [
    'index' => 0,
    'host' => '127.0.0.1',
    'port' => '6379',
    'timeout' => 0,
    'password' => ''
];
return [
  'master' => $master
];