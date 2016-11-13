<?php
/**
 * Created by PhpStorm.
 * User: 39096
 * Date: 2016/9/1
 * Time: 23:38
 */

namespace App\Service;


use App\Factory;
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
            $classDoc = $refObj->getDocComment();
            $authMatches = [];
            $aliasMatches = [];
            //if(!preg_match('/@default\s+(enable|disable|)/i',$classDoc, $authMatches) ) continue;
            //if('disable' == strtolower($authMatches[1]) ) continue;
            //if(!preg_match('/@alias\s+(\w+)/ui',$classDoc, $aliasMatches) ) continue;
            //if(empty($aliasMatches[1]) ) continue;

            $refObjMethod = $refObj->getMethods(\ReflectionMethod::IS_STATIC);
            if (count($refObjMethod ) > 0) {
                $classData = &$ret[$pathInfo['filename'] ];
                foreach ($refObjMethod as $methodInfo) {
                    if (!$methodInfo->isPublic()) continue;
                    if (!$methodInfo->isStatic()) continue;

                    $methodDoc = $methodInfo->getDocComment();

                    $authMatches = [];

                    preg_match('/@default\s+(enable|disable|)/i',$methodDoc, $authMatches);
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
}