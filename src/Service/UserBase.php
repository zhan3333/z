<?php
/**
 * Created by PhpStorm.
 * User: zhan
 * Date: 2016/11/24
 * Time: 17:57
 */

namespace App\Service;


use App\Factory;
use App\RepositoryClass;
use App\Util;

/**
 * Class UserBase
 * @package App\Service
 * @default disable
 */
class UserBase
{
    const USER_LOGIN_TOKEN = 'userLoginToken';

    /**
     * 获取当前用户userId
     */
    public static function getClientUserId()
    {
        if (empty($_REQUEST['_uid'])) {
            if (empty($_REQUEST['token'])) return 0;
            if (empty($_REQUEST['userId'])) return 0;
            $redis = Factory::redis();
            $tokenInfo = $redis->hGetAll(self::USER_LOGIN_TOKEN . $_REQUEST['userId']);
            if (empty($tokenInfo['token'])) return 0;
            if (empty($tokenInfo['credential'])) return 0;
            if (!Util::verifyPasswd($_REQUEST['userId'] . $tokenInfo['credential'] . $tokenInfo['deviceIdentification'], $_REQUEST['token'])) {
                $redis->del(self::USER_LOGIN_TOKEN . $_REQUEST['userId']);
                return 0;
            }
            $_REQUEST['_uid'] = $_REQUEST['userId'];
        }
        $uid = $_REQUEST['_uid'];
        return $uid;
    }

    /**
     * @param $userId       integer     userId
     * @param $credential   string      登陆密钥
     * @param array $ext                扩展数据，存放设备信息
     * @return string                   登陆后生成凭据
     */
    public static function login($userId, $credential, $ext = [])
    {
        $deviceIdentification = empty($ext['uuid'])?self::getClientIp():$ext['uuid'];
        $redis = Factory::redis();
        $token = Util::createPasswd($userId . $credential . $deviceIdentification);
        $redis->hMset(self::USER_LOGIN_TOKEN . $userId, [
            'deviceIdentification' => $deviceIdentification,
            'credential' => $credential,
            'token' => $token
        ]);
        return $token;
    }

    /**
     * 获取客户端所在ip
     * @return string
     */
    public static function getClientIp()
    {
        $ip = '127.0.0.1';
        if (!empty($_SERVER['HTTP_X_REAL_IP'])) {
            return $_SERVER['HTTP_X_REAL_IP'];
        }
        return $ip;
    }

    /**
     * 判断登陆用户是否为管理员
     * @return bool
     */
    public static function isAdmin()
    {
        $userId = self::getClientUserId();
        if (empty($userId)) return false;
        return RepositoryClass::User()->isAdmin($userId);
    }

    /**
     * 判断登陆用户是否为普通用户
     * @return bool
     */
    public static function isNormal()
    {
        $userId = self::getClientUserId();
        if (empty($userId)) return false;
        return RepositoryClass::User()->isNormal($userId);
    }

    /**
     * 判断登陆用户是否为超级管理员
     * @return bool
     */
    public static function isSuperAdmin()
    {
        $userId = self::getClientUserId();
        if (empty($userId)) return false;
        return RepositoryClass::User()->isSuperAdmin($userId);
    }
}