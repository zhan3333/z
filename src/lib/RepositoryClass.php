<?php
/**
 * Created by PhpStorm.
 * User: zhan
 * Date: 2016/11/25
 * Time: 9:50
 */

namespace App;
use App\Entities\NormalAccountRepository;
use App\Entities\TestRepository;
use App\Entities\UserRepository;

/**
 * 获取repository对象
 * Class RepositoryClass
 * @package App\lib
 */
class RepositoryClass
{
    /**
     * @return UserRepository
     */
    public static function User()
    {
        return Factory::em()->getRepository(':User');
    }

    /**
     * @return NormalAccountRepository
     */
    public static function NormalAccount()
    {
        return Factory::em()->getRepository(':NormalAccount');
    }

    /**
     * @return TestRepository
     */
    public static function Test()
    {
        return Factory::em()->getRepository(':Test');
    }
}