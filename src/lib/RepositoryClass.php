<?php
/**
 * Created by PhpStorm.
 * User: zhan
 * Date: 2016/11/25
 * Time: 9:50
 */

namespace App;
use App\Entities\ApiAccount;
use App\Entities\ApiAccountRepository;
use App\Entities\ApiInfoRepository;
use App\Entities\NormalAccountRepository;
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
     * @return ApiAccountRepository
     */
    public static function ApiAccount()
    {
        return Factory::em()->getRepository(':ApiAccount');
    }

    /**
     * @return ApiInfoRepository
     */
    public static function ApiInfo()
    {
        return Factory::em()->getRepository(':ApiInfo');
    }
}