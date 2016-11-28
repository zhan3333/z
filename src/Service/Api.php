<?php
/**
 * Created by PhpStorm.
 * User: zhan
 * Date: 2016/11/25
 * Time: 12:17
 */

namespace App\Service;
use App\Err;
use App\Factory;
use App\RepositoryClass;
use App\Util;

/**
 * 用于提供api注册操作接口
 * Class Api
 * @package App\Service
 */
class Api extends Base
{
    // apiAccount
    
    /**
     * 申请一个api账号
     * @param integer   $apiId  apiId
     * @internal param $applicationName
     * @return bool
     */
    public static function apiAccountReg($apiId)
    {
        $userId = self::getClientUserId();
        if (empty($userId)) return Err::setLastErr(E_USER_NO_LOGIN);    // 用户未登陆
        $ar = RepositoryClass::ApiInfo();
        if (empty($ar->getApiInfoById($apiId))) return Err::setLastErr(E_API_ID_NOT_EXIST); // apiId不存在
        $ac = RepositoryClass::ApiAccount();

        $key = Util::random(32);
        while (!empty($ac->apiKey2Id($key))) {
            $key = Util::random(32);
        }

        $addRet = $ac->apiAccountReg([
            'apiKey' => $key,
            'userId' => $userId,
            'apiId'  => $apiId
        ]);
        if ($addRet['ok']) {
            return [];
        } else {
            return Err::setLastErr($addRet['code']);
        }
    }
    
    // apiInfo

    /**
     * 添加一个apiInfo信息
     * @param array $data
     * <pre>
     * [
     *  'apiName' => '',        // 接口名称
     * ]
     * </pre>
     * @return array
     */
    public static function addApiInfo($data)
    {
        $userId = self::getClientUserId();
        if (empty($userId)) return Err::setLastErr(E_USER_NO_LOGIN);    // 未登陆
        if (!self::isAdmin() && !self::isSuperAdmin()) return Err::setLastErr(E_USER_NOT_IS_ADMIN);
        $data = array_filter(filter_var_array($data, [
            'apiName' => FILTER_SANITIZE_STRING
        ]));
        if (empty($data['apiName'])) return Err::setLastErr(E_API_NAME_IS_ERROR);   // 接口名称错误
        $ar = RepositoryClass::ApiInfo();
        if ($ar->apiName2ApiInfo($data['apiName'])) return Err::setLastErr(E_API_NAME_ALREADY_EXISTS);  // 接口名称错误
        $apiInfoId = $ar->addApiInfo($data);
        if (empty($apiInfoId)) return Err::setLastErr(E_API_CREATE_API_INFO_FAIL);  // 创建apiInfo失败
        return [];
    }

    /**
     * 获取api信息列表
     * @param array $where
     * @param array $orderBy
     * @param int $first
     * @param int $length
     * @return array
     */
    public static function getApiInfoTable($where = [], $orderBy = [], $first = 0, $length = 0)
    {
        $filter = 0;
        $result = RepositoryClass::ApiInfo()->getApiInfoList($filter, $where, $orderBy, $first, $length);
        return [
            'table' => [
                'data' => $result,
                'filter' => $filter
            ]
        ];
    }

    /**
     * 根据id，获取一条api信息
     * @param $id
     * @return array
     */
    public static function getApiInfoById($id)
    {
        $ar = RepositoryClass::ApiInfo();
        $result = $ar->getApiInfoById($id);
        return [
            'result' => $result
        ];
    }

    /**
     * @param $id
     * @param $data
     * @return array
     */
    public static function updateApiInfo($id, $data)
    {
        $ar = RepositoryClass::ApiInfo();
        $result = $ar->updateApiInfo(['id' => $id], $data);
        return [
            'result' => $result
        ];
    }
}