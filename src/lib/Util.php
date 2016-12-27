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

    /**
     * 获取调用接口者的ip
     * @param array $server
     * @return string
     */
    public static function getClientIp($server = [])
    {
        $ip = '';
        if (empty($server)) $server = $_SERVER;
        if (!empty($server['REMOTE_ADDR'])) $ip = $server['REMOTE_ADDR'];
        if (!empty($server['HTTP_X_REAL_IP'])) $ip = $server['HTTP_X_REAL_IP'];
        return $ip;
    }

    /**
     * 获取带时差的date对象
     * @param null|integer $timestamp       时间戳
     * @param string $dz                    时差，默认为系统时差
     * @return \DateTime
     */
    public static function getDateObj($timestamp = null, $dz = APPTIMEZONE)
    {
        $dz = new \DateTimeZone($dz);
        if ($timestamp == null) {
            return date_create(null, $dz);
        } else {
            return date_create()->setTimestamp($timestamp)->setTimezone($dz);
        }
    }

    /**
     * 对象转为数组
     * @param $object
     * @return array
     */
    public static function obj2Arr($object)
    {
        $object = json_decode(json_encode($object), true);
        return $object;
    }

    /**
     * 检测手机号
     * @param $mobile
     * @return bool
     */
    public static function checkMobile($mobile)
    {
        return preg_match('/^(\+?86-?)?(18|15|13|17)[0-9]{9}$/', $mobile) ? true : false;
    }

    /**
     * 生成内部用订单号
     */
    public static function generateOrderNum()
    {
        $id = dk_get_next_id();
        return $id;
    }
}