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
use App\Module\AliYun\BankCardVerify4;
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

    /**
     * 测试四元素检测模块
     * 建议缓冲查询结果
     * @doc https://market.aliyun.com/products/57000002/cmapi011455.html#sku=yuncode545500005
     * @param string    $bankCard   // 银行卡号 ：6216600800000770000
     * @param string    $idCard     // 身份证号 ：420222199210101054
     * @param string    $mobile     // 手机号   ：13517210601
     * @param string    $realName   // 真实姓名 ：小詹
     * @return array    查询结果
     * <pre>
     * [
     *  'status' => 0, // 第一错误码 201：银行卡号为空，202：真实姓名为空，203：银行卡号不正确，204：真实姓名包含特殊字符，205：身份证不正确，210：没有消息
     *  'msg' => 'ok',
     *  'result' => [
     *      'bankcard' => '6216600800000770000',             // 银行卡号
     *      'realname' => '小詹',                            // 真实姓名
     *      'idcard' => '420222199210101054',               // 身份证号
     *      'mobile' => '13517210601',                      // 手机号
     *      'verifystatus' => '1',                          // 验证结果  0：一致， 1：不一致
     *      'verifymsg' => '抱歉，银行卡号校验不一致！'        // 验证结果消息
     *  ]
     * ]
     * </pre>
     */
    public static function testBankCardVerify4($bankCard = '', $idCard = '', $mobile = '', $realName = '')
    {
        try {
            $B = Factory::BankCardVerify4();
            if (empty($bankCard)) $bankCard = '6210985200013406610';
            if (empty($idCard)) $idCard = '420923199209286357';
            if (empty($mobile)) $mobile = '15102778299';
            if (empty($realName)) $realName = '李圣文';
            $result = $B->verify($bankCard, $idCard, $mobile, $realName);
            return [
                'result' => $result
            ];
        } catch (\Exception $e) {
            Factory::logger('zhan')->addInfo(__CLASS__. '_' . __FUNCTION__, [__LINE__,
                $e
            ]);
            return Err::setLastErr(E_SYS_ERROR);    // 系统错误
        }
    }

    /**
     * 测试utf8_encode 函数
     * @param $name
     * @return string
     */
    public static function test_urlencode($name)
    {
        return urlencode($name);
    }

    public static function testRequests()
    {
        $headers = ['Accept' => 'application/json'];
        $options = [];
        $request = \Requests::get('http://www.baidu.com', $headers, $options);
        Factory::logger('zhan')->addInfo(__CLASS__. '_' . __FUNCTION__, [__LINE__,
            $request->status_code, $request->body, $request->headers
        ]);

    }

    public static function testExplode()
    {
        $str = 'qrscene_{\"anchorId\":\"25\",\"type\":\"anchor\"}';
        $str2 = '{\"anchorId\":\"25\",\"type\":\"anchor\"}';
        $ret = explode('qrscene_', $str);
        $ret2 = explode('qrscene_', $str2);
        $ret3 = stripos($str, 'qrscene_');
        return [
            'ret1' => $ret,
            'ret2' => $ret2,
            'ret3' => $ret3
        ];
    }

    /**
     * 获取新闻信息
     * @param string $type
     * @return array
     */
    public static function testGetNews($type = 'top')
    {
        try {
            $News = Factory::JuheNewsHeadlines();
            $result = $News->getNews($type);
            $body = $result->body;
            $body = json_decode($body, true);
            if ($body['error_code'] == 0) {
                // 请求成功
                $data = $body['result'];
            } else {
                $data = [];
            }
            return [
                'news' => $data
            ];
        } catch (\Exception $e) {
            Factory::logger('error')->addError(__CLASS__, [__FUNCTION__, __LINE__, $e]);
            return [];
        }
    }

    /**
     * 获取随机笑话
     * @param string $type
     * @return array
     */
    public static function getRandJoke($type = '')
    {
        try {
            $Joke = Factory::JuheJoke();
            $result = $Joke->getRandJoke($type);
            $result = json_decode($result->body, true);
            return [
                'result' => $result
            ];
        } catch (\Exception $e) {
            Factory::logger('error')->addError(__CLASS__, [__FUNCTION__, __LINE__, $e]);
            return Err::setLastErr(E_SYS_ERROR);
        }
    }

    /**
     * 获取最新图片笑话
     * @param int $page
     * @param int $pagesize
     * @return array
     */
    public static function getNewImgJoke($page = 1, $pagesize = 1)
    {
        $Joke = Factory::JuheJoke();
        $result = $Joke->getNewImgJoke($page, $pagesize);
        $result = json_decode($result->body, true);
        return [
            'result' => $result
        ];
    }

    /**
     * 按照更新时间来获取图片笑话
     * @param int $page
     * @param int $pagesize
     * @param string $sort
     * @param int $time
     * @return array
     */
    public static function getListImgJoke($page = 1, $pagesize = 1, $sort = 'desc', $time = 0)
    {
        $Joke = Factory::JuheJoke();
        $result = $Joke->getListImgJoke($page, $pagesize, $sort, $time);
        $result = json_decode($result->body, true);

        return [
            'result' => $result
        ];
    }

    /**
     * 获取最新文字笑话
     * @param int $page
     * @param int $pagesize
     * @return array
     */
    public static function getNewTextJoke($page = 1, $pagesize = 1)
    {
        $Joke = Factory::JuheJoke();
        $result = $Joke->getNewTextJoke($page, $pagesize);
        $result = json_decode($result->body, true);

        return [
            'result' => $result
        ];
    }

    /**
     * 按照更新时间获取文字笑话
     * @param int $page
     * @param int $pagesize
     * @param string $sort
     * @param int $time
     * @return array
     */
    public static function getListTextJoke($page = 1, $pagesize = 1, $sort = 'desc', $time = 0)
    {
        $Joke = Factory::JuheJoke();
        $result = $Joke->getListTextJoke($page, $pagesize, $sort, $time);
        $result = json_decode($result->body, true);

        return [
            'result' => $result
        ];
    }

}