<?php
/**
 * Created by PhpStorm.
 * User: zhan
 * Date: 2016/11/28
 * Time: 18:24
 */

namespace App\Entities;

use App\Factory;

/**
 * 实体基础类，用于提供数据库操作
 * Class BaseEntity
 * @package App\Entities
 */
class BaseEntity
{
    /**
     * 获取调子类的类名
     * @return string   类名
     */
    public static function getClassName()
    {
        $class = get_called_class();
        $arr = explode('\\', $class);
        $className = $arr[count($arr) - 1];
        return $className;
    }

    /**
     * 查询数据库表 (主要用于分页场景中)
     * @param int   $filter             过滤后数据条数
     * @param array $where              查询条件
     * <pre>
     * 键值对数组形式，示例：
     * [
     *  'id' => 0,
     * ]
     * </pre>
     * @param array $orderBy            数据排序依据
     * <pre>
     * 二维数组形式，支持多个排序条件，在前的优先排序
     * [
     *  [
     *      'order' => 'id',
     *      'value' => 'DESC'   // DESC or ASC
     *  ]
     * ]
     * </pre>
     * @param int   $first              查询起始位置
     * @param int   $length             查询数据条数
     * @return array    返回二维数组
     */
    public static function getList(&$filter, $where = [], $orderBy = [], $first = 0, $length = 0)
    {
        $where = is_array($where)?$where:[];
        $orderBy = is_array($orderBy)?$orderBy:[];
        $repositoryClass = Factory::em()->getRepository(':' . self::getClassName());
        $qb = $repositoryClass->createQueryBuilder('s');
        $className = $repositoryClass->getClassName();
        // where
        foreach ($where as $key => $value) {
            if (!property_exists($className, $key)) continue;
            $qb->andWhere('s.'.$key . '=:'.$key);
            $qb->setParameter($key, $value);
        }
        // filter
        $filter = $qb->select("COUNT(s.id)")->getQuery()->getSingleScalarResult();

        // length first
        if (is_int($length)) {
            if ($length > 0) {
                $qb->setMaxResults($length);
            } elseif ($length == 0) {
                $qb->setMaxResults(Factory::getConfig('app', 'db_default_page'));
            }
        }
        if (is_int($first)) {
            if ($first >= 0) {
                $qb->setFirstResult($first);
            }
        }

        // orderBy

        if (empty($orderBy)) {
            $qb->addOrderBy('s.id', 'DESC');
        } else {
            foreach ($orderBy as $item) {
                if (empty($item['value']) || empty($item['order'])) continue;
                $value = $item['value'];
                $order = $item['order'];
                if (!property_exists($className, $value)) continue;
                if (array_search(strtoupper($order), ['ASC', 'DESC']) === false) continue;
                $qb->addOrderBy('s.' . $item['value'], $item['order']);
            }
        }

        $qb->select('s');
        $result = $qb->getQuery()->getArrayResult();
        return $result;
    }

    /**
     * 根据条件，修改数据
     * @param array $where  修改条件
     * @param array $data   修改的键值对
     * @return bool
     */
    public static function update($where, $data)
    {
        try {
            $repositoryClass = Factory::em()->getRepository(':' . self::getClassName());
            $qb = $repositoryClass->createQueryBuilder('s');
            $className = $repositoryClass->getClassName();
            $qb->update();
            // where
            foreach ($where as $key => $value) {
                if (!property_exists($className, $key)) continue;
                $qb->andWhere('s.'.$key . '=:'.$key);
                $qb->setParameter($key, $value);
            }
            // data
            $filterData = [];
            foreach ($data as $key => $val) {
                if (!property_exists($className, $key)) continue;
                $filterData[$key] = $val;
            }
            if (empty($filterData)) throw new \Exception('设置值错误');
            foreach ($filterData as $key => $val) {
                if (!property_exists($className, $key)) continue;
                $qb->set('s.'.$key, ':' . $key);
                $qb->setParameter($key, $val);
            }

            $qb->getQuery()->execute();
            return true;
        } catch (\Exception $e) {
            Factory::logger('error')->addError(__CLASS__, [__FUNCTION__, __LINE__, $e]);
            return false;
        }
    }

    public static function get($where, $shows = [], $hides = [])
    {
        $where = is_array($where)?$where:[];
        $hides = is_array($hides)?$hides:[];

        $repositoryClass = Factory::em()->getRepository(':' . self::getClassName());
        $qb = $repositoryClass->createQueryBuilder('s');
        $className = $repositoryClass->getClassName();
        // where
        foreach ($where as $key => $value) {
            if (!property_exists($className, $key)) continue;
            $qb->andWhere('s.'.$key . '=:'.$key);
            $qb->setParameter($key, $value);
        }
        // shows
        if (!empty($shows)) {
            if (is_array($shows)) {
                $selectArr= [];
                foreach ($shows as $show) {
                    if (!property_exists($className, $show)) continue;
                    $selectArr[] = 's.' . $show;
                }
                if (empty($selectArr)) {
                    $qb->select('s');
                } else {
                    $qb->select($selectArr);
                }
            } else {

            }
        } else {
            $qb->select('s');
        }
    }
}