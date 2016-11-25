<?php
/**
 * Created by PhpStorm.
 * User: 39096
 * Date: 2016/10/30
 * Time: 18:31
 */

namespace App;


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

    /**
     * 返回指定长度的随机字符串
     * @param int $len          随机字符串长度
     * @param array $exclude    排除的字符数组
     * @return string   生成的随机字符串
     */
    public static function random($len = 8, $exclude = [])
    {
        static $initChars = [
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W',
            'X', 'Y', 'Z', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j',
            'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z'
        ];
        if (!empty($exclude)) {
            $validChars = array_diff($initChars, $exclude);
        } else {
            $validChars = $initChars;
        }
        $result = '';
        for ($i = 1; $i <= $len; $i ++) {
            $result .= $validChars[array_rand($validChars, 1)];
        }
        return $result;
    }
}