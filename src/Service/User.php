<?php
/**
 * Created by PhpStorm.
 * User: zhan
 * Date: 2016/11/24
 * Time: 16:56
 */

namespace App\Service;
use App\Err;
use App\Factory;
use App\RepositoryClass;
use App\Util;

/**
 * 用户类，用于提供用户操作接口
 * Class User
 * @package App\Service
 */
class User extends Base
{
    /**
     * 普通账号登陆
     * @param string $account        账号
     * @param string $passwd         密码
     * @return array
     * <pre>
     * [
     *  'once' => [
     *      'userId'    => 19,      // usreId
     *      'token'     => '',      // token
     *  ]
     * ]
     * </pre>
     */
    public static function normalLogin($account, $passwd)
    {
        $account = filter_var($account, FILTER_SANITIZE_STRING);
        $passwd = filter_var($passwd, FILTER_SANITIZE_STRING);
        if (empty($account)) return Err::setLastErr(E_USER_ACCOUNT_ERROR);
        if (empty($passwd)) return Err::setLastErr(E_USER_PASSWD_ERROR);
        $userId = RepositoryClass::NormalAccount()->normalAccount2UserId($account);
        if (empty($userId)) return Err::setLastErr(E_USER_NOT_EXIST);   // 用户不存在
        $token = self::login($userId, $passwd);
        return [
            'once' => [
                'userId' => $userId,
                'token' => $token
            ]
        ];
    }

    /**
     * 普通账号注册
     * @param string $account    账号
     * @param string $passwd     密码
     * @param array $ext 额外注册信息
     * @return array
     * <pre>
     * [
     *  // 注册成功，code为0
     * ]
     * </pre>
     */
    public static function normalReg($account, $passwd, $ext = [])
    {
        $account = filter_var($account, FILTER_SANITIZE_STRING);
        $passwd = filter_var($passwd, FILTER_SANITIZE_STRING);
        if (empty($account)) return Err::setLastErr(E_USER_ACCOUNT_ERROR);
        if (empty($passwd)) return Err::setLastErr(E_USER_PASSWD_ERROR);
        $hashPasswd = Util::createPasswd($passwd);
        $addNormalAccountRet = RepositoryClass::User()->normalReg($account, $hashPasswd);
        if ($addNormalAccountRet['ok']) {
            return [];
        } else {
            return Err::setLastErr($addNormalAccountRet['code']);
        }
    }

    /**
     * 判断是否为登陆状态
     * @return array
     * <pre>
     * [
     *  'isLogin' => true   // 是否登陆成功
     * ]
     * </pre>
     */
    public static function isLogin()
    {
        $userId = self::getClientUserId();
        return [
            'isLogin' => $userId?true:false
        ];
    }
}