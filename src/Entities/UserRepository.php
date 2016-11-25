<?php
/**
 * Created by PhpStorm.
 * User: zhan
 * Date: 2016/11/24
 * Time: 17:29
 */

namespace App\Entities;


use App\Factory;
use App\RepositoryClass;
use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{
    /**
     * @param $data
     * <pre>
     * [
     *  'userType' => 0,        // 用户类型
     * ]
     * </pre>
     * @return bool
     */
    public static function addUser($data)
    {
        if (empty($data['userType'])) $data['userType'] = User::USER_TYPE_NORMAL;   // 默认为普通用户
        $em = Factory::em();
        $User = new User();
        $User->setPostTime(date_create());
        $User->setUserType($data['userType']);
        $em->persist($User);
        $em->flush();
        return $User->getId();
    }

    /**
     * 创建一个普通账户
     * @param $account
     * @param $passwd
     * @return array
     * <pre>
     * // 成功时返回
     * [
     *  'ok' => true,
     *  'userId' => 1000        // 用户id
     * ]
     * // 失败时返回
     * [
     *  'ok' => false,
     *  'code' => 9,            // 错误代码
     * ]
     * </pre>
     */
    public static function normalReg($account, $passwd)
    {
        $em = Factory::em();
        $em->beginTransaction();
        try {
            $userId = self::addUser([]);
            if (empty($userId)) throw new \Exception('创建用户失败', E_USER_CREATE_FAIL);
            $normalAccountId = RepositoryClass::NormalAccount()->addNormalAccount([
                'userId' => $userId,
                'passwd' => $passwd,
                'account' => $account
            ]);
            if (empty($normalAccountId)) throw new \Exception('创建用户账号失败', E_USER_CREATE_NORMAL_ACCOUNT_FAIL);
            $em->commit();
            return [
                'ok' => true,
                'userId' => $userId
            ];
        } catch (\Exception $e) {
            $em->rollback();
            Factory::logger('error')->addError(__CLASS__, [__FUNCTION__, __LINE__, $e]);
            return [
                'ok' => false,
                'code' => $e->getCode()
            ];
        }
    }

    /**
     * 获取用户的类型
     * @param $userId
     */
    public static function getUserTypeByUserId($userId)
    {
        /** @var User $User*/
        $User = RepositoryClass::User()->find($userId);
        return $User->getUserType();
    }

    /**
     * 判断用户是否为管理员
     * @param $userId
     * @return bool
     */
    public static function isAdmin($userId)
    {
        return (self::getUserTypeByUserId($userId) == User::USER_TYPE_ADMIN)?true:false;
    }

    /**
     * 判断用户是否为普通用户
     * @param $userId
     * @return bool
     */
    public static function isNormal($userId)
    {
        return (self::getUserTypeByUserId($userId) == User::USER_TYPE_NORMAL)?true:false;
    }

    /**
     * 判断用户是否为超级管理员
     * @param $userId
     * @return bool
     */
    public static function isSuperAdmin($userId)
    {
        return (self::getUserTypeByUserId($userId) == User::USER_TYPE_SUPER_ADMIN)?true:false;
    }
}