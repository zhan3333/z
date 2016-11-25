<?php
/**
 * Created by PhpStorm.
 * User: zhan
 * Date: 2016/11/25
 * Time: 9:46
 */

namespace App\Entities;


use App\Factory;
use App\RepositoryClass;
use Doctrine\ORM\EntityRepository;

class NormalAccountRepository extends EntityRepository
{
    /**
     * @param $data array
     * <pre>
     * [
     *  'userId'    => 0,   // 用户id
     *  'account'   => '',  // 用户账号
     *  'passwd'    => ''   // 用户密码
     * ]
     * </pre>
     * @return bool|integer     // 成功返回id，失败返回false
     * @throws \Exception
     */
    public static function addNormalAccount($data)
    {
        if (empty($data['userId'])) throw new \Exception('用户id不允许为空', E_USER_ID_NOT_ALLOW_AIR);
        if (empty($data['account'])) throw new \Exception('账号不允许为空', E_USER_ACCOUNT_NOT_ALLOW_AIR);
        if (empty($data['passwd'])) throw new \Exception('密码不允许为空', E_USER_PASSWD_NOT_ALLOW_AIR);
        $userId = $data['userId'];
        $login = $data['account'];
        $passwd = $data['passwd'];
        $postTime = date_create();
        $NormalAccount = new NormalAccount();
        $NormalAccount->setUserId($userId);
        $NormalAccount->setLogin($login);
        $NormalAccount->setPasswd($passwd);
        $NormalAccount->setPostTime($postTime);
        $em = Factory::em();
        $em->persist($NormalAccount);
        $em->flush();
        return $NormalAccount->getId();
    }

    /**
     * 根据账号查询userId
     * @param $account
     * @return int
     */
    public static function normalAccount2UserId($account)
    {
        $normalAccount = RepositoryClass::NormalAccount()->findOneBy(['login' => $account]);
        if (empty($normalAccount)) return 0;
        return $normalAccount->getUserId();
    }

    /**
     * 根据用户id，查询这个用户的userId
     * @param integer   $userId 用户id
     * @return string       用户账号
     */
    public static function userId2NormalAccount($userId)
    {
        $normalAccount = RepositoryClass::NormalAccount()->findOneBy(['userId' => $userId]);
        if (empty($normalAccount)) return 0;
        return $normalAccount->getLogin();
    }
}