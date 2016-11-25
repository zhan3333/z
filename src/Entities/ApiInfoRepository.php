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
        $ApiInfo = RepositoryClass::ApiInfo()->find($id);
        return $ApiInfo;
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
     */
    public static function getApiInfoList(&$filter, $where, $orderBy, $length, $first)
    {
        $qb = RepositoryClass::ApiInfo()->createQueryBuilder('s');
        $className = RepositoryClass::ApiInfo()->getClassName();
        // where
        foreach ($where as $key => $value) {
            if (!property_exists($className, $key)) continue;
            $qb->andWhere('s.'.$key . '=:'.$key);
            $qb->setParameter($key, $value);
        }
        // filter
        $filter = $qb->select("COUNT(s.id)")->getQuery()->getSingleScalarResult();

        // length first
        $qb->setMaxResults($length)->setFirstResult($first);

        // orderBy
        foreach ($orderBy as $item) {
            if (empty($item['value']) || empty($item['order'])) continue;
            $value = $item['value'];
            $order = $item['order'];
            if (!property_exists($className, $value)) continue;
            if (array_search(strtoupper($order), ['ASC', 'DESC']) === false) continue;
            $qb->addOrderBy('s.' . $item['value'], $item['order']);
        }

        $qb->select('s');

        $result = $qb->getQuery()->getArrayResult();
        return $result;
    }
}