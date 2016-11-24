<?php
/**
 * Created by PhpStorm.
 * User: 39096
 * Date: 2016/10/30
 * Time: 18:31
 */

namespace App\lib;


class Util
{
    /**
     * 创建一个哈希值
     * @param $plain    string      待转换哈希值
     * @return bool|string
     */
    public static function createPasswd($plain)
    {
        return password_hash($plain, PASSWORD_BCRYPT);
    }

    /**
     * 验证字符串和哈希值
     * @param $plain    string  密码
     * @param $encrypt  string  哈希值
     * @return bool     是否相等
     */
    public static function verifyPasswd($plain, $encrypt)
    {
        return password_verify($plain, $encrypt);
    }
}