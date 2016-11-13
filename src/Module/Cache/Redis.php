<?php
/**
 * Created by PhpStorm.
 * User: 39096
 * Date: 2016/10/29
 * Time: 21:10
 */

namespace App\Module\Cache;


class Redis extends \Redis
{
    public function __construct($configs)
    {
        $host = $configs['host']?:'127.0.0.1';
        $port = $configs['port']?:6379;
        $timeout = $configs['timeout']?:0.0;
        $index = $configs['index']?:1;
        $password = $configs['password']?:'';;
        parent::__construct();
        if (!empty($password)) {
            parent::auth($password);
        }
        parent::connect($host, $port, $timeout);
        parent::select($index);
    }
}