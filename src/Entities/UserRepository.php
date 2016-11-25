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
    public static function addUser($data)
    {
        $em = Factory::em();
        $User = new User();
        $User->setPostTime(date_create());
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
}