<?php
require_once "vendor/autoload.php";

use App\Factory;

define('WEBPATH', __DIR__);

$client = new \Hprose\Swoole\Client("ws://127.0.0.1:2001");
$count = 0;
$client->subscribe('time', function($date) use ($client, &$count) {
    if (++$count > 10) {
        $client->unsubscribe('time');
        swoole_event_exit();
    }
    else {
        var_dump($date);
    }
});