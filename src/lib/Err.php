<?php

namespace App;


class Err
{
    /**
     * 错误消息定义
     * @var array
     */
    private static $errMsg = [];

    /**
     * 最后错误码定义
     * @var int
     */
    private static $lastErrCode = 0;

    /**
     * 消息定义初始化
     */
    public static function init()
    {
        self::$errMsg = include __DIR__ . '/../err.inc';
    }

    public static function errMsg($errNo)
    {
        if(empty(self::$errMsg[$errNo]) ) return 'Unknown Error';
        return self::$errMsg[$errNo];
    }

    /**
     * 获取错误消息字符串
     * @return mixed|string
     */
    public static function getErrMsg()
    {
        if(empty(self::$errMsg[self::$lastErrCode]) ) return 'Unknown Error';
        return self::$errMsg[self::$lastErrCode];
    }

    /**
     * 设置错误码
     * @param integer $errNo
     *
     * @return boolean
     */
    public static function setLastErr($errNo)
    {
        self::$lastErrCode = $errNo;
        return false;
    }

    /**
     * 获取错误码
     * @return int
     */
    public static function getLastErr()
    {
        return self::$lastErrCode;
    }
}