<?php
/**
 * Created by PhpStorm.
 * User: 39096
 * Date: 2016/9/1
 * Time: 23:38
 */

namespace App\Service;


use App\Documents\BlogPost;
use App\Documents\User;
use App\Entities\ApiInfo;
use App\Entities\Student;
use App\Factory;
use App\RepositoryClass;
use App\Util;
use FilesystemIterator;

class Test
{
    /**
     * @default enable
     */
    public static function getApiList()
    {
        $ret = array();
        $itFile = new \FilesystemIterator(__DIR__, \FilesystemIterator::KEY_AS_FILENAME);
        foreach ($itFile as $fileName) {
            $pathInfo = pathinfo($fileName);
            if ('php' != $pathInfo['extension']) continue;
            if('Base' == $pathInfo['filename']) continue;
            $className = "App\\Service\\". $pathInfo['filename'];
            if (!class_exists($className)) continue;

            $refObj = new \ReflectionClass($className);
            $refDoc = $refObj->getDocComment();
            $authMatches = [];
            preg_match('/@default\s+(enable|disable|)/i', $refDoc, $authMatches);
            if (!empty($authMatches[1]) && ('disable' == strtolower($authMatches[1]))) continue;

            $refObjMethod = $refObj->getMethods(\ReflectionMethod::IS_STATIC);
            if (count($refObjMethod ) > 0) {
                $classData = &$ret[$pathInfo['filename'] ];
                foreach ($refObjMethod as $methodInfo) {
                    if (!$methodInfo->isPublic()) continue;
                    if (!$methodInfo->isStatic()) continue;

                    $methodDoc = $methodInfo->getDocComment();

                    $authMatches = [];

                    preg_match('/@default\s+(enable|disable|)/i', $methodDoc, $authMatches);
                    if(!empty($authMatches[1]) && ('disable' == strtolower($authMatches[1]) ) ) continue;

                    $methodData = &$classData[$methodInfo->name];
                    $methodParam = $methodInfo->getParameters();
                    foreach ($methodParam as $paramInfo) {
                        $argMatches = [];
                        if(preg_match('/@param\s+(\w+)\s+\$'.$paramInfo->name.'/i',$methodDoc, $argMatches) ) {
                            $methodData[$paramInfo->name] = $argMatches[1];
                        }
                        else {
                            $methodData[$paramInfo->name] = $paramInfo->name;
                        }
                    }
                }
                if(!empty($classData) ) {
                    ksort($classData);
                }
                else {
                    unset($ret[$pathInfo['filename'] ]);
                }
            }

        }
        ksort($ret);
        return $ret;
    }

    /**
     * 测试返回数据
     * @default enable
     * @param string $a
     * @return array
     */
    public static function testReturn($a = '')
    {
        $result = [
            'string' => 'abcdef',
            'int' => 123456,
            'array' => [
                1,
                'a',
                [2, 'b'],
                ['c' => 1, 'd' => 2]
            ],
            'object' => [
                'a' => [1, 2, 3, 4],
                'b' => 'c'
            ]
        ];
        if (!empty($a)) {
            $result['a'] = $a;
        }
        return [
            'result' => $result
        ];
    }

    /**
     * @default enable
     * @return array
     */
    public static function testRedis()
    {
        $redis = Factory::redis();
        $size = $redis->dbSize();
        $set = $redis->set('a', 1);
        $get = $redis->get('a');
        return [
            'size' => $size,
            'set' => $set,
            'get' => $get
        ];
    }

    /**
     * 测试em数据库操作
     */
    public static function testEm()
    {
        try {
            $em = Factory::em();
            $ret2 = $em->getRepository(':Student')->createQueryBuilder('s')
                ->getQuery()->getArrayResult();
            $ret1 = $em->createQueryBuilder()->from(':Student', 's')
                ->select('s')->getQuery()->getArrayResult();
            return [
                'result2' => $ret2,
                'result1' => $ret1
            ];
        } catch (\Exception $e) {
            Factory::logger('zhan')->addInfo(__CLASS__. '_' . __FUNCTION__, [__LINE__,
                $e
            ]);
        }
    }

    /**
     * @param string $normalAccount
     * @return int
     */
    public static function testNormalAccount2UserId($normalAccount)
    {
        $userId = RepositoryClass::NormalAccount()->normalAccount2UserId($normalAccount);
        return $userId;
    }

    public static function testRandom($len, $ext = [])
    {
        return Util::random($len, $ext);
    }

    /**
     * @param $where
     * @param $orderBy
     * @param $length
     * @param $first
     * @return array
     */
    public static function testGetTable($where, $orderBy, $length, $first)
    {
        $filter = 0;
        $result = RepositoryClass::ApiInfo()->getApiInfoList($filter, $where, $orderBy, $length, $first);
        return [
            'result' => $result
        ];
    }

    public static function testGet($where, $shows = [], $hides = [])
    {
        $result = ApiInfo::get($where, $shows, $hides);
        return [
            'result' => $result
        ];
    }

    public static function testGetById($value, $shows = [], $hides = [])
    {
        $result = ApiInfo::getById($value, $shows, $hides);
        return [
            'result' => $result
        ];
    }

    public static function testArrayShift()
    {
        $arr = ['id'];
        $result = array_shift($arr);
        return [
            'result' => $result
        ];
    }

    public static function testDelete($id)
    {
        $result = ApiInfo::deleteById($id);
        return [
            'result' => $result
        ];
    }

    public static function testTime()
    {
        $amount = 1;
        $a = 'test';
        $b = ' time';
        $ret = '';
        $time1 = microtime(true);
        // 直接点运算符
        for($i = 0; $i < $amount; $i ++) {
            $ret = $a . $b;
        }
        $time2 = microtime(true);
        // 双引号解析
        for($i = 0; $i < $amount; $i ++) {
            $ret = "$a$b";
        }
        $time3 = microtime(true);
        // 双引号加花括号分别括起
        for($i = 0; $i < $amount; $i ++) {
            $ret = "{$a}{$b}";
        }
        $time4 = microtime(true);
        // 直接赋值:单引号
        for($i = 0; $i < $amount; $i ++) {
            $ret = 'test time';
        }
        $time5 = microtime(true);
        // 直接赋值：双引号
        for($i = 0; $i < $amount; $i ++) {
            $ret = "test time";
        }
        $time6 = microtime(true);
        $retArr = [
            '$ret = $a . $b' => $time2 - $time1,
            '$ret = "$a$b"' => $time3 - $time2,
            ' $ret = "{$a}{$b}"' => $time4 - $time3,
            '$ret = \'test time\'' => $time5 - $time4,
            '$ret = "test time"' => $time6 - $time5
        ];
        array_multisort($retArr);
        return [
            'result' => $retArr
        ];
    }

    public static function testResetDQLParts()
    {
        try {
            $qb = RepositoryClass::ApiInfo()->createQueryBuilder('s');
            Factory::logger('zhan')->addInfo(__CLASS__. '_' . __FUNCTION__, [__LINE__,
                $qb->getQuery()->getSQL()
            ]);

            $qb->where('s.id =:id')->setParameter('id', 11);
            Factory::logger('zhan')->addInfo(__CLASS__. '_' . __FUNCTION__, [__LINE__,
                $qb->getQuery()->getSQL()
            ]);
            $qb->getQuery()->getArrayResult();
            Factory::logger('zhan')->addInfo(__CLASS__. '_' . __FUNCTION__, [__LINE__,
                $qb->getQuery()->getSQL()
            ]);
            $qb->resetDQLParts();
            Factory::logger('zhan')->addInfo(__CLASS__. '_' . __FUNCTION__, [__LINE__,
                $qb->getQuery()->getSQL()
            ]);
        } catch (\Exception $e) {
            Factory::logger('error')->addError(__CLASS__, [__FUNCTION__, __LINE__, $e]);
        }

    }
}