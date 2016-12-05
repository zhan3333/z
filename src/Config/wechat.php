<?php
/**
 * Created by PhpStorm.
 * User: zhan
 * Date: 01/11/2016
 * Time: 09:56
 */
$options = [
    'debug'     => true,
    'app_id'    => '',
    'secret'    => '',
    'token'     => '',
    'aes_key'   => '',
    'log' => [
        'level' => 'debug',
        'file'  => APPPATH . '/log/easywechat.log',
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
