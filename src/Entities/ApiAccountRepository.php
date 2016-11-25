<?php
/**
 * Created by PhpStorm.
 * User: zhan
 * Date: 2016/11/25
 * Time: 12:03
 */

namespace App\Entities;


use App\Factory;
use App\RepositoryClass;
use Doctrine\ORM\EntityRepository;

class ApiAccountRepository extends EntityRepository
{
    /**
     * @param array $data
     * <pre>
     * [
     *  'apiKey' => '',
     *  'userId' => 0,
     *  'apiName' => 0,
     * ]
     * </pre>
     * @throws \Exception
     */
    public static function addApiAccount($data)
    {
        if (empty($data['apiKey'])) throw new \Exception('', E_API_KEY_NOT_ALLOW_AIR);
        if (empty($data['userId'])) throw new \Exception('', E_API_USER_ID_NOT_ALLOW_AIR);
        if (empty($data['apiId'])) throw new \Exception('', E_API_API_ID_NOT_ALLOW_AIR);
        $ApiAccount = new ApiAccount();
        $ApiAccount->setPostTime(date_create());
        $ApiAccount->setUserId($data['userId']);
        $ApiAccount->setApiId($data['apiId']);
        $ApiAccount->setApiKey($data['apiKey']);
        Factory::em()->persist($ApiAccount);
        Factory::em()->flush();
        $id = $ApiAccount->getId();
        return $id;
    }

    /**
     * apiAccount 注册
     * @param array $data
     * <pre>
     * [
     *  'apiKey' => '',
     *  'userId' => 0,
     *  'apiName' => 0,
     * ]
     * </pre>
     * @return array
     */
    public static function apiAccountReg($data)
    {
        try {
            $apiAccountId = self::addApiAccount($data);
            if (empty($apiAccountId)) throw new \Exception('创建apiAccount账号失败', E_API_CREATE_API_ACCOUNT_FAIL);
            return [
                'ok' => true
            ];
        } catch (\Exception $e) {
            Factory::logger('error')->addError(__CLASS__, [__FUNCTION__, __LINE__, $e]);
            return [
                'ok' => false,
                'code' => $e->getCode()
            ];
        }
    }

    public static function apiKey2Id($key)
    {
        /** @var ApiAccount $ApiAccount*/
        $ApiAccount = RepositoryClass::ApiAccount()->findOneBy(['apiKey' => $key]);
        return $ApiAccount?$ApiAccount->getId():null;
    }

    public static function apiKey2ApiId($key)
    {
        /** @var ApiAccount $ApiAccount*/
        $ApiAccount = RepositoryClass::ApiAccount()->findOneBy(['apiKey' => $key]);
        return $ApiAccount?$ApiAccount->getApiId():null;
    }

    public static function apiKey2AccountInfo($key)
    {
        /** @var ApiAccount $ApiAccount*/
        $ApiAccount = RepositoryClass::ApiAccount()->findOneBy(['apiKey' => $key]);
        return $ApiAccount;
    }

    public static function apiKey2UserId($key)
    {
        /** @var ApiAccount $ApiAccount*/
        $ApiAccount = RepositoryClass::ApiAccount()->findOneBy(['key' => $key]);
        return $ApiAccount?$ApiAccount->getUserId():null;
    }
}