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
use App\Err;
use App\Factory;
use App\RepositoryClass;
use App\Util;
use FilesystemIterator;
use Hprose\Swoole\WebSocket\Client;

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

                    $methodDoc = $methodInfo->getDocComment();      // 接口文档

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
     * 新的获取调试api列表
     * @default enable
     * @return array
     * <pre>
     * [
     *  [
     *      'className' => [                    // 类名称
     *          'apiName' => [                  // 接口名称
     *              'params' => [               // 参数数组
     *                  'param1Name' => [       // 参数名称
     *                      'type' => '',       // 参数类型
     *                      'doc' => ''         // 参数注释
     *                  ],
     *                  ...
     *              ],
     *              'doc' => ''                 // 接口文档
     *          ],
     *          'doc' => '',                    // 类文档
     *      ]
     *  ]
     * ]
     * </pre>
     */
    public static function getApiListNew()
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

            $refObjMethods = $refObj->getMethods(\ReflectionMethod::IS_STATIC);
            if (count($refObjMethods ) > 0) {
                $classData = &$ret[$pathInfo['filename'] ];
                $classDoc = $refObj->getDocComment();               // 类文档
                foreach ($refObjMethods as $methodInfo) {
                    if (!$methodInfo->isPublic()) continue;
                    if (!$methodInfo->isStatic()) continue;

                    $methodDoc = $methodInfo->getDocComment();      // 接口文档

                    $authMatches = [];

                    preg_match('/@default\s+(enable|disable|)/i', $methodDoc, $authMatches);
                    if(!empty($authMatches[1]) && ('disable' == strtolower($authMatches[1]) ) ) continue;

                    $methodData = &$classData[$methodInfo->name];
                    $methodParam = $methodInfo->getParameters();
                    Factory::logger('zhan')->addInfo(__CLASS__. '_' . __FUNCTION__, [__LINE__,
                        $methodParam
                    ]);

                    foreach ($methodParam as $paramInfo) {
                        /** @var $paramInfo \ReflectionParameter*/
                        $argMatches = [];
                        if(preg_match('/@param\s+(\w+)\s+\$'.$paramInfo->name.'/i',$methodDoc, $argMatches) ) {
                            $methodData[$paramInfo->name] = $argMatches[1];     // 参数类型
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
     * 测试消息推送
     */
    public static function testPush()
    {
        try {
            $serv = Factory::swoole();
            $serv->publish('time');
            $serv->push('time', microtime(true));
        } catch (\Exception $e) {
            Factory::logger('zhan')->addInfo(__CLASS__. '_' . __FUNCTION__, [__LINE__,
                $e
            ]);
        }
    }

    public static function testPushClient()
    {
        $client = new Client("ws://127.0.0.1:2001");
        $count = 0;
        $client->subscribe('time', function($date) use ($client, &$count) {
            Factory::logger('zhan')->addInfo(__CLASS__. '_' . __FUNCTION__, [__LINE__,
                $date
            ]);

            if (++$count > 10) {
                $client->unsubscribe('time');
                swoole_event_exit();
            }
            else {
                var_dump($date);
            }
        });
    }

    public static function getTestById($id)
    {
        $result = RepositoryClass::Test()->find($id);
        return [
            'result' => $result
        ];
    }

    public static function addTest($text)
    {
        try {
            $Test = new \App\Entities\Test();
            $Test->setText($text);
            $em = Factory::em();
            $em->persist($Test);
            $em->flush();
        } catch (\Exception $e) {
            Factory::logger('zhan')->addInfo(__CLASS__. '_' . __FUNCTION__, [__LINE__,
                $e
            ]);

        }
    }

    public static function json_encode($text)
    {
        return [
            'result' => json_encode($text)
        ];
    }

    public static function json_decode($text)
    {
        return [
            'result' => json_decode($text)
        ];
    }

    public static function returnErr()
    {
        return Err::setLastErr(E_PATH_IS_ILLEGAL);
    }
}