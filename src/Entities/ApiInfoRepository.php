<?php
/**
 * Created by PhpStorm.
 * User: zhan
 * Date: 2016/11/25
 * Time: 12:45
 */

namespace App\Entities;


use App\Factory;
use App\RepositoryClass;
use Doctrine\ORM\EntityRepository;

class ApiInfoRepository extends EntityRepository
{
    /**
     * 添加一个api数据
     * @param $data
     * <pre>
     * [
     *  'apiName' => ''
     * ]
     * </pre>
     * @return integer
     * @throws \Exception
     */
    public static function addApiInfo($data)
    {
        if (empty($data['apiName'])) throw new \Exception(E_API_NAME_NOT_ALLOW_AIR);
        $ApiInfo = new ApiInfo();
        $ApiInfo->setApiName($data['apiName']);
        $ApiInfo->setPostTime(date_create());
        Factory::em()->persist($ApiInfo);
        Factory::em()->flush();
        $id = $ApiInfo->getId();
        return $id;
    }

    /**
     * 根据id查询apiInfo数据
     * @param $id
     * @return null|object
     */
    public static function getApiInfoById($id)
    {
        $qb = RepositoryClass::ApiInfo()->createQueryBuilder('s');
        $result = $qb->select('s')
            ->where('s.id =:id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
        return $result;
    }

    /**
     * 根据api名称查询apiInfo数据
     * @param $apiName
     * @return null|object
     */
    public static function apiName2ApiInfo($apiName)
    {
        $ApiInfo = RepositoryClass::ApiInfo()->findOneBy(['apiName' => $apiName]);
        return $ApiInfo;
    }

    /**
     * 获取api列表
     * @param $filter
     * @param $where
     * @param $orderBy
     * <pre>
     * [
     *  [
     *      'order' => 'ASC',
     *      'value' => 'id'
     *  ]
     * ]
     * </pre>
     * @param $length
     * @param $first
     * @return array
     *  $where = [], $orderBy = [], $length = 0, $first = 0
     */
    public static function getApiInfoList(&$filter, $where = [], $orderBy = [], $first = 0,  $length = 0)
    {
        return ApiInfo::getList($filter, $where, $orderBy, $first,  $length);
    }

    /**
     * 更新数据库数据
     * @param array $where      修改限定条件
     * @param array $data       修改数据键值对
     * @return bool             返回是否修改成功
     */
    public static function updateApiInfo($where, $data)
    {
        return ApiInfo::update($where, $data);

    }

}