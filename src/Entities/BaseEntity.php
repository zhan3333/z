<?php
/**
 * Created by PhpStorm.
 * User: zhan
 * Date: 2016/11/28
 * Time: 18:24
 */

namespace App\Entities;

use App\Factory;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\QueryBuilder;

/**
 * 实体基础类，用于提供数据库操作
 * Class BaseEntity
 * @package App\Entities
 */
class BaseEntity
{
    // 缓存

    // 类元数据缓存
    private static $classMetadataCache = [];
    // 实体字段映射缓存
    private static $fieldMappingsCache = [];
    // 实体主键名称缓存
    private static $identifierFieldNameCache = [];
    // 字段类型缓存
    private static $fieldTypeCache = [];
    // 查询qb缓存
    private static $qbCache = [];
    // repository类缓存
    private static $repositoryClassCache = [];

    const ABBR = 's';

    /**
     * 获取调子类的类名
     * 若不在类中调用，则返回false
     * @return string   类名(包含命名空间)
     */
    private static function getClassName()
    {
        $class = get_called_class();
        if (!$class) return false;
//        $arr = explode('\\', $class);
//        $className = $arr[count($arr) - 1];
        return $class;
    }

    /**
     * 获取类中的主键名称
     * @param string $className 包含命名空间的类名
     * @return bool|string  成功时返回主键名称，失败时返回false
     */
    private static function getIdentifierFieldName($className = '')
    {
        if (empty($className)) $className = self::getClassName();
        if (empty($className)) return false;
        if (empty(self::$identifierFieldNameCache[$className])) {
            $metadata = self::getClassMetadata($className);
            $identifierFieldNames = $metadata->getIdentifierFieldNames();
            $identifier = array_shift($identifierFieldNames);       // 主键名称
            self::$identifierFieldNameCache[$className] = $identifier;
        }
        return self::$identifierFieldNameCache[$className];
    }

    /**
     * 获取类元数据
     * @param string $className
     * @return bool| ClassMetadata
     */
    private static function getClassMetadata($className = '')
    {
        if (empty($className)) $className = self::getClassName();
        if (empty($className)) return false;
        if (empty(self::$classMetadataCache[$className])) {
            $classMetadata = Factory::em()->getClassMetadata($className);
            self::$classMetadataCache[$className] = $classMetadata;
        }
        return self::$classMetadataCache[$className];
    }

    /**
     * 获取实体映射关系
     * @param string $className
     * @return bool|array
     */
    private static function getFieldMappings($className = '')
    {
        if (empty($className)) $className = self::getClassName();
        if (empty($className)) return false;
        if (empty(self::$fieldMappingsCache[$className])) {
            $fieldMappings = self::getClassMetadata($className)->fieldMappings;
            self::$fieldMappingsCache[$className] = $fieldMappings;
        }
        return self::$fieldMappingsCache[$className];
    }

    /**
     * 获取字段类型
     * @param $fieldName
     * @param string $className
     * @return bool|string
     */
    private static function getFieldType($fieldName, $className = '')
    {
        if (empty($className)) $className = self::getClassName();
        if (empty($className)) return false;
        $fieldMappings = self::getFieldMappings($className);
        if (empty(self::$fieldTypeCache[$className][$fieldName])) {
            self::$fieldTypeCache[$className][$fieldName] = $fieldMappings[$fieldName]['type'];
        }
        return self::$fieldTypeCache[$className][$fieldName];
    }

    /**
     * @param string $className
     * @return QueryBuilder
     */
    private static function qb($className = '')
    {
        if (empty($className)) $className = self::getClassName();
        if (empty($className)) return false;
        if (empty(self::$repositoryClassCache[$className])) {
            $repositoryClass = Factory::em()->getRepository($className);
            self::$repositoryClassCache[$className] = $repositoryClass;
        }
        /**@var EntityRepository $repositoryClass*/
        $repositoryClass = self::$repositoryClassCache[$className];
        $qb = $repositoryClass->createQueryBuilder(self::ABBR);
        return $qb;
    }

    /**
     * @param QueryBuilder  $qb
     * @param string        $entityAbbr
     * @param string        $prop           字段名称
     * @param mixed         $val            字段值设置
     * 'type' => 'range' | 'like' | 'notLike' | 'isNull' | 'notNull' | 'in' | 'notIn'
     * <pre>
     * // range
     * [
     *  'type' => 'range',
     *  'value' => [
     *      'max' => 10,        prop < 10
     *      'min' => 0,         prop > 0
     *      'maxEq' => 10,      prop <= 10
     *      'minEq' => 2        prop >= 10
     *  ]
     * ]
     * // like
     * [
     *  'type' => 'like',
     *  'value' => ''           // 正则字符串
     * ]
     * // noLike
     * [
     *  'type' => 'notLike',
     *  'value' => '',          // 正则字符串
     * ]
     * // isNull
     * [
     *  'type' => 'isNull',
     * ]
     * // notNull
     * [
     *  'type' => 'notNull'
     * ]
     * // in
     * [
     *  'type' => 'in',
     *  'value' => [],      // 查询字段允许的值组成的数组
     * ],
     * // notIn
     * [
     *  'type' => 'notIn',
     *  'value' => [],      // 查询字段不允许的值组成的数组
     * ]
     * </pre>
     * @param string|null   $type           字段类型
     */
    private static function parseWhere(QueryBuilder $qb,  $prop, $val, $type = null, $entityAbbr = self::ABBR)
    {
        if (is_array($val)) {
            $valueType = empty($val['type'])?'':$val['type'];
            $value = empty($val['value'])?[]:$val['value'];
            switch ($valueType) {
                case 'range':
                    if (isset($value['max'])) {
                        $qb->andWhere("$entityAbbr.$prop < :{$prop}_max");  // prop < max
                        $qb->setParameter($prop.'_max', $value['max'], $type);
                    }
                    if (isset($value['min'])) {
                        $qb->andWhere("$entityAbbr.$prop > :{$prop}_min");  // prop > min
                        $qb->setParameter($prop.'_min', $value['min'], $type);
                    }
                    if (isset($value['maxEq'])) {
                        $qb->andWhere("$entityAbbr.$prop <= :{$prop}_maxEq");  // prop <= maxEq
                        $qb->setParameter($prop.'_maxEq', $value['maxEq'], $type);
                    }
                    if (isset($value['minEq'])) {
                        $qb->andWhere("$entityAbbr.$prop >= :{$prop}_minEq");  // prop >= minEq
                        $qb->setParameter($prop.'_minEq', $value['minEq'], $type);
                    }
                    break;
                case 'like':
                    if (!empty($value)) {
                        $qb->andWhere("$entityAbbr.$prop LIKE :{$prop}_like");
                        $qb->setParameter($prop.'_like', $value, $type);
                    }
                    break;
                case 'notLike':
                    if (!empty($value)) {
                        $qb->andWhere("$entityAbbr.$prop NOT LIKE :{$prop}_notLike");
                        $qb->setParameter($prop.'_notLike', $value, $type);
                    }
                    break;
                case 'isNull':
                    if (!empty($value)) {
                        $qb->andWhere("$entityAbbr.$prop IS NULL");
                    }
                    break;
                case 'notNull':
                    if (!empty($value)) {
                        $qb->andWhere("$entityAbbr.$prop IS NOT NULL");
                    }
                    break;
                case 'in':
                    if (!empty($value) & is_array($value)) {
                        $implodeValue = implode(',', $value);
                        $qb->andWhere("$entityAbbr.$prop IN ($implodeValue)");
                    }
                    break;
                case 'notIn':
                    if (!empty($value) & is_array($value)) {
                        $implodeValue = implode(',', $value);
                        $qb->andWhere("$entityAbbr.$prop NOT IN ($implodeValue)");
                    }
                    break;
                default:
                    break;
            }
        } else {
            $qb->andWhere("$entityAbbr.$prop = :$prop");
            $qb->setParameter($entityAbbr, $val, $type);
        }
    }

    // 查询接口

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
        $className = self::getClassName();
        $qb = self::qb($className);
        $identifier = self::getIdentifierFieldName($className);
        $abbr = self::ABBR;
        // where
        foreach ($where as $key => $value) {
            if (!property_exists($className, $key)) continue;
            self::parseWhere($qb, $key, $value, self::getFieldType($key, $className));
        }
        // filter
        $filter = $qb->select("COUNT($abbr.$identifier)")->getQuery()->getSingleScalarResult();

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
            $qb->addOrderBy("$abbr.$identifier", 'DESC');   // 默认id倒序排列
        } else {
            foreach ($orderBy as $item) {
                if (empty($item['value']) || empty($item['order'])) continue;
                $value = $item['value'];
                $order = $item['order'];
                if (!property_exists($className, $value)) continue;
                if (array_search(strtoupper($order), ['ASC', 'DESC']) === false) continue;
                $qb->addOrderBy("$abbr." . $item['value'], $item['order']);
            }
        }

        $qb->select($abbr);
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
            $className = self::getClassName();
            $abbr = self::ABBR;
            $qb = self::qb();
            $qb->update();
            // where
            foreach ($where as $key => $value) {
                if (!property_exists($className, $key)) continue;
                self::parseWhere($qb, $key, $value, self::getFieldType($key, $className));
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
                $qb->set("$abbr.$key", ":$key");
                $qb->setParameter($key, $val);
            }

            $qb->getQuery()->execute();
            return true;
        } catch (\Exception $e) {
            Factory::logger('error')->addError(__CLASS__, [__FUNCTION__, __LINE__, $e]);
            return false;
        }
    }

    /**
     * 根据条件，查询数据
     * @param $where
     * @param array $shows 要查询的字段，只接受数组类型
     * @param array $hides 要隐藏的字段，只接受数组类型
     * @param bool $scalar  将返回二维数组中的第一个值
     * @return array|mixed 查询到的结果，为二维数组
     */
    public static function get($where, $shows = [], $hides = [], $scalar = false)
    {
        $where = is_array($where)?$where:[];
        $hides = is_array($hides)?$hides:[];
        $shows = is_array($shows)?$shows:[];
        $className = self::getClassName();
        $abbr = self::ABBR;
        $qb = self::qb();
        // where
        foreach ($where as $key => $value) {
            if (!property_exists($className, $key)) continue;
            self::parseWhere($qb, $key, $value, self::getFieldType($key, $className));
        }
        // shows
        if (!empty($shows)) {
            // shows 是数组
            $selectArr= [];
            foreach ($shows as $show) {
                if (!property_exists($className, $show)) continue;
                $selectArr[] = "$abbr.$show";
            }
            if (empty($selectArr)) {
                $qb->select($abbr);
            } else {
                $qb->select($selectArr);
            }
            $result = $qb->getQuery()->getArrayResult();
        } else {
            // shows 为空
            $qb->select($abbr);
            $result = $qb->getQuery()->getArrayResult();
        }

        // result
        if (!empty($result)) {
            if (!empty($hides)) {
                foreach ($result as &$item) {
                    $item = array_diff_key($item, array_flip($hides));
                }
            }
            if ($scalar) {
                $result = array_shift($result);
            }
        }
        return $result;
    }

    /**
     * 根据主键查询一条数据
     * @param integer $value 主键值
     * @param array|string  $shows      目标字段，为数组时返回数组，为字符串时返回数组中的这个字段对应值
     * @param array         $hides      需隐藏字段，目标中将会被过滤掉；只接受数组类型
     * @return array|mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Exception
     */
    public static function getById($value, $shows = [], $hides = [])
    {
        $hides = is_array($hides)?$hides:[];
        $className = self::getClassName();
        $abbr = self::ABBR;
        $qb = self::qb();
        $identifier = self::getIdentifierFieldName($className);     // 主键名称
        // 设置查询条件
        $qb->where("$abbr.$identifier = :$identifier");
        $qb->setParameter($identifier, $value);
        $result = [];
        if (!empty($shows)) {
            if (is_array($shows)) {
                // shows 是数组
                $selectArr= [];
                foreach ($shows as $show) {
                    if (!property_exists($className, $show)) continue;
                    $selectArr[] = "$abbr.$show";
                }
                if (empty($selectArr)) {
                    $qb->select($abbr);
                } else {
                    $qb->select($selectArr);
                }
                $result = $qb->getQuery()->getOneOrNullResult();
            }
            if (is_scalar($shows)) {
                if (!property_exists($className, $shows)) throw new \Exception("shows $shows is scalar, but not exist in class $className");
                $result = $qb->select("$abbr.$shows")->getQuery()->getOneOrNullResult();
                $result = array_shift($result);
            }
        } else {
            $qb->select($abbr);
            $result = $qb->getQuery()->getOneOrNullResult();
        }
        if (!empty($result) && is_array($result)) {
            if (!empty($hides)) {
                Factory::logger('zhan')->addInfo(__CLASS__. '_' . __FUNCTION__, [__LINE__,
                    $hides, $result, gettype($result)
                ]);

                $result = array_diff_key($result, array_flip($hides));
            }
        }
        return $result;
    }

    /**
     * 同 getById
     * @param $id
     * @param array $shows
     * @param array $hides
     * @return array|mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Exception
     */
    public static function find($id, $shows = [], $hides = [])
    {
        $hides = is_array($hides)?$hides:[];
        $className = self::getClassName();
        $abbr = self::ABBR;
        $qb = self::qb();
        $identifier = self::getIdentifierFieldName($className);     // 主键名称
        // 设置查询条件
        $qb->where("$abbr.$identifier = :$identifier");
        $qb->setParameter($identifier, $id);
        $result = [];
        if (!empty($shows)) {
            if (is_array($shows)) {
                // shows 是数组
                $selectArr= [];
                foreach ($shows as $show) {
                    if (!property_exists($className, $show)) continue;
                    $selectArr[] = "$abbr.$show";
                }
                if (empty($selectArr)) {
                    $qb->select($abbr);
                } else {
                    $qb->select($selectArr);
                }
                $result = $qb->getQuery()->getOneOrNullResult();
            }
            if (is_scalar($shows)) {
                if (!property_exists($className, $shows)) throw new \Exception("shows $shows is scalar, but not exist in class $className");
                $result = $qb->select("$abbr.$shows")->getQuery()->getOneOrNullResult();
                $result = array_shift($result);
            }
        } else {
            $qb->select($abbr);
            $result = $qb->getQuery()->getOneOrNullResult();
        }
        if (!empty($result) && is_array($result)) {
            if (!empty($hides)) {
                $result = array_diff_key($result, array_flip($hides));
            }
        }
        return $result;
    }

    /**
     * 根据条件删除一条数据
     * @param array     $where
     * @return mixed
     * @throws \Exception
     */
    public static function delete($where)
    {
        $className = self::getClassName();
        $abbr = self::ABBR;
        $qb = self::qb();
        $qb->delete();
        $filterWhere = [];
        foreach ($where as $key => $value) {
            if (!property_exists($className, $key)) continue;
            $filterWhere[$key] = $value;
        }
        if (empty($where)) throw new \Exception('where invalid condition!');
        foreach ($filterWhere as $key => $value) {
            self::parseWhere($qb, $key, $value, self::getFieldType($key, $className));
        }
        $result = $qb->getQuery()->execute();
        return $result;
    }

    /**
     * 根据主键删除一条数据
     * @param integer $id   主键
     * @return mixed        查询结果
     */
    public static function deleteById($id)
    {
        $className = self::getClassName();
        $abbr = self::ABBR;
        $qb = self::qb();
        $identifier = self::getIdentifierFieldName($className);     // 主键名称
        $qb->delete();
        $qb->andWhere("$abbr.$identifier = :$identifier")->setParameter($identifier, $id);
        $result = $qb->getQuery()->execute();
        return $result;
    }
}