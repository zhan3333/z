<?php
require_once "vendor/autoload.php";

use Hprose\Swoole\Client;
use App\Factory;

define('WEBPATH', __DIR__);

$client = new Client('http://127.0.0.1:2000/');
$result = $client->Test->getApiList();
Factory::logger('zhan')->addInfo(__FUNCTION__, [__LINE__, $result]);