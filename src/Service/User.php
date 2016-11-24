<?php
/**
 * Created by PhpStorm.
 * User: zhan
 * Date: 2016/11/24
 * Time: 16:56
 */

namespace App\Service;

/**
 * 用户类，用于提供用户操作接口
 * Class User
 * @package App\Service
 */
class User extends Base
{
    /**
     * 统一登陆入口
     * @param $userId       integer     用户唯一标识
     * @param $passwd       string      用户登陆凭据
     * @param array $ext                登陆额外信息（登陆ip，登陆时间，登陆方式...）
     */
    private static function login($userId, $passwd, $ext = [])
    {

    }

    /**
     * 普通账号登陆
     * @param $account      string  账号
     * @param $passwd       string  密码
     */
    public static function normalLogin($account, $passwd)
    {

    }

    /**
     * 普通账号注册
     * @param $account  string  账号
     * @param $passwd   string  密码
     * @param array $ext        额外注册信息
     */
    public static function normalReg($account, $passwd, $ext = [])
    {

    }
}