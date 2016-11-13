<?php
/**
 * Created by PhpStorm.
 * User: zhan
 * Date: 01/11/2016
 * Time: 09:56
 */
$options = [
    'debug'     => true,
    'app_id'    => 'wxb2ce0d5efa9a8468',
    'secret'    => 'a0bce6ada5a8b7e0d1903b789f9b836c',
    'token'     => 'zhan',
    'aes_key'   => '6MzSyoGS8ivpLaTuNQqXrFpwQWHqpsMHiP0BfHud2bb',
    'log' => [
        'level' => 'debug',
        'file'  => WEBPATH . '/log/easywechat.log',
    ],

//    // payment
//    'payment' => [
//        'app_id'             => '',
//        'merchant_id'        => '',
//        'key'                => '',
//        'cert_path'          =>  WEBPATH . '/src/Config/wechatCert/apiclient_cert.pem', // XXX: 绝对路径！！！！
//        'key_path'           =>  WEBPATH . '/src/Config/wechatCert/apiclient_key.pem',      // XXX: 绝对路径！！！！
//        'notify_url'         => '',       // 你也可以在下单时单独设置来想覆盖它
        // 'device_info'     => '',
//        'sub_app_id'      => '',
        // 'sub_merchant_id' => '',
        // ...
//    ]
];

return [
    'wechat' => $options
];