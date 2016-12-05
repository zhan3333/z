<?php
/**
 * swoole服务器配置
 * @author  carry
 * @date: 16-3-27 16:04
 */

return [
    //监听的HOST
    'host'   => 'ws://0.0.0.0',
    //监听的端口
    'port'   => '2001',
    'debug' => 1,
    'log_file'        => APPPATH . '/log/app.log',
    'pid_file' => APPPATH . '/master.pid',
    'worker_num'      => 4,
    //不要修改这里
    'max_request'     => 0,
    'task_worker_num' => 0,
    'task_ipc_mode' => 3,
    'dispatch_mode' => 3,
    'heartbeat_idle_time' => 600,
    'heartbeat_check_interval' => 60,
    //是否要作为守护进程
    // 'daemonize'       => 1,

    //'sslPort' => '8001',
    //'ssl_cert_file' => APPPATH.'/src/Config/ssl/ssl.crt',
    //'ssl_key_file' => APPPATH.'/src/Config/ssl/ssl.key',

    // 最大上传
//    'open_length_check' => 1,
    'package_max_length' => 838860800,    // 最大上传8M,

    // 网络层拆包
//    'open_eof_check' => true,           //打开EOF检测
//    'package_eof' => "\r\n",            //设置EOF
//    'open_eof_split' => true,
];