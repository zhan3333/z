<?php
/**
 * 银行卡4要素验证
 * User: zhan
 * Date: 2016/12/16
 * Time: 10:05
 */

namespace App\Module\AliYun;


use App\Factory;
use App\Module\Foundation\Log;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class BankCardVerify4
{
    const TYPE_SIMPLE = 'TYPE_SIMPLE';
    const TYPE_SIGNATURE = 'TYPE_SIGNATURE';
    const LOG_ID = 'BankCardVerify4';

    const API_VERIFY = 'http://jisubank4.market.alicloudapi.com/bankcardverify4/verify';

    private $config = [];
    private $type = null;
    private $appcode = null;

    /**
     * BankCardVerify4 constructor.
     * @param string $type 创建类型
     * @param array $config 配置文件数组
     * @throws AliYunException
     */
    public function __construct($type, $config)
    {
        $this->config = $config;
        $this->initializeLogger();      // 初始化日志
        $this->addDebug('construct BankCardVerify4: ', ['type' => $type, 'config' => $config]);
        $this->type = $type;
        if (strtoupper($type) == self::TYPE_SIMPLE) {
            $appcode = empty($this->config['simple']['appcode'])?null:$this->config['simple']['appcode'];
            if (empty($appcode || !is_string($appcode)))
                throw new AliYunException('can not set appcode!', AliYunException::E_ALI_NOT_SET_APPCODE);
            $this->appcode = $appcode;
        } elseif (strtoupper($type) == self::TYPE_SIGNATURE) {
            throw new AliYunException('还没有写这种调用方式，望有识之士替我写一下~');
        } else {
            $this->addError('undefined type:', ['type' => $type, 'config' => $config]);
            throw new AliYunException('undefined type', AliYunException::E_ALI_UNDEFINED_TYPE);
        }
    }

    /**
     * 进行验证
     * @param $bankCard
     * @param $idCard
     * @param $mobile
     * @param $realName
     * @return array
     * <pre>
     * [
     *  'status' => 0,
     *  'msg' => 'ok',
     *  'result' => [
     *      'bankcard' => '',
     *      'realname' => '',
     *      'idcard' => '',
     *      'mobile' => '',
     *      'verifystatus' => '1',
     *      'verifymsg' => '抱歉，银行卡号校验不一致！'
     *  ]
     * ]
     * </pre>
     * @throws AliYunException
     */
    public function verify($bankCard, $idCard, $mobile, $realName)
    {
        if (empty($bankCard)) throw new AliYunException('bank card most be fill in');
        if (empty($idCard)) throw new AliYunException('id card most be fill in');
        if (empty($mobile)) throw new AliYunException('mobile most be fill in');
        if (empty($realName)) throw new AliYunException('real name most be fill in');
        $result = $this->send($bankCard, $idCard, $mobile, $realName);
        return $result;
    }

    /**
     * 发送验证
     * @param string    $bankCard       银行卡号
     * @param string    $idCard         身份证号
     * @param string    $mobile         手机号
     * @param string    $realName       真实姓名
     */
    private function sendOld($bankCard, $idCard, $mobile, $realName)
    {
        $this->addDebug('verity begin: ', func_get_args());
        $method = "GET";
        $headers = array();
        array_push($headers, "Authorization:APPCODE " . $this->appcode);
        $realName = urlencode($realName);
        $querys = "bankcard=$bankCard&idcard=$idCard&mobile=$mobile&realname=$realName";
        $bodys = "";
        $url = self::API_VERIFY . "?" . $querys;

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_FAILONERROR, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, true);
        if (1 == strpos("$".self::API_VERIFY, "https://"))
        {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        }
        $result = curl_exec($curl);

        $this->addDebug('verity result: ', [gettype($result), $result, json_decode($result, true)]);
    }

    /**
     * @param $bankCard
     * @param $idCard
     * @param $mobile
     * @param $realName
     * @return array
     * <pre>
     * [
     *  'status' => 0,
     *  'msg' => 'ok',
     *  'result' => [
     *      'bankcard' => '',
     *      'realname' => '',
     *      'idcard' => '',
     *      'mobile' => '',
     *      'verifystatus' => '1',
     *      'verifymsg' => '抱歉，银行卡号校验不一致！'
     *  ]
     * ]
     * </pre>
     * @throws AliYunException
     */
    private function send($bankCard, $idCard, $mobile, $realName)
    {
        $this->addDebug('verity begin: ', func_get_args());
        $realName = urlencode($realName);
        $url = self::API_VERIFY . '?' . "bankcard=$bankCard&idcard=$idCard&mobile=$mobile&realname=$realName";
        $options = [];
        $headers = ["Authorization" => "APPCODE $this->appcode"];
        $request = \Requests::get($url, $headers, $options);
        $this->addDebug('verity result: ', [gettype($request), $request]);
        return self::parseResult($request);
    }

    /**
     * @param \Requests_Response $result
     * @return array
     * <pre>
     * [
     *  'status' => 0,
     *  'msg' => 'ok',
     *  'result' => [
     *      'bankcard' => '',
     *      'realname' => '',
     *      'idcard' => '',
     *      'mobile' => '',
     *      'verifystatus' => '1',
     *      'verifymsg' => '抱歉，银行卡号校验不一致！'
     *  ]
     * ]
     * </pre>
     * @throws AliYunException
     *
     */
    private function parseResult($result)
    {
        $status_code = $result->status_code;
        if ($status_code >= 400 && $status_code < 500) {
            $this->addError('client error, send verify fail: ', ['response' => $result]);
            throw new AliYunException('network error: ', AliYunException::E_ALI_CLIENT_ERROR);      // 客户端错误
        } elseif ($status_code >= 500) {
            $this->addError('api server error, send verify fail: ', ['response' => $result]);
            throw new AliYunException('network error: ', AliYunException::E_ALI_NETWORK_ERROR);     // api提供商错误
        } else {
            return json_decode($result->body, true);
        }
    }


    /**
     * 日志打打印操作
     * @param string $msg
     * @param array     $content
     */
    private function addDebug($msg, $content = [])
    {
        Log::debug(self::LOG_ID, $msg, $content);
    }

    private function addError($msg, $content = [])
    {
        Log::error(self::LOG_ID, $msg, $content);
    }

    /**
     * Initialize logger.
     */
    private function initializeLogger()
    {
        $logId = self::LOG_ID;
        if (Log::hasLogger($logId)) {
            return;
        }
        $logger = new Logger($logId);
        if (!empty($this->config['log']['file'])) {
            $logFile = $this->config['log']['file'];
            $logger->pushHandler(new StreamHandler($logFile));
        }
        Log::setLogger($logId, $logger);
    }
}