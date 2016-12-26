<?php
namespace App\Module\Juhe;
use App\Err;
use App\Factory;
use App\Util;

/**
 * 聚合模块
 * User: zhan
 * Date: 2016/11/9
 * Time: 11:48
 */
class Juhe
{
    const SMS_SEND_URL = 'http://v.juhe.cn/sms/send';   // 短信接口的url
    public $options = [];
    public $AppKey = null;
    public $mobile = null;
    public $tpl_id = null;
    public $tpl_value = [];
    public function __construct($options)
    {
        $this->options = $options;
    }

    /**
     * 属性重载
     * @param $name
     * @param $value
     * @return $this
     */
    public function __set($name, $value)
    {
        $this->tpl_value[$name] = $value;
        return $this;
    }

    public function __get($name)
    {
        if (array_key_exists($name, $this->tpl_value)) {
            return $this->tpl_value[$name];
        }
        return null;
    }

    public function __isset($name)
    {
        return isset($this->tpl_value[$name]);
    }

    public function __unset($name)
    {
        unset($this->tpl_value[$name]);
    }

    public function sms($mobile = null, $tpl_id = null, array $tpl_value = [])
    {
        if (empty($this->options['sms']['AppKey'])) throw new \Exception('AppKey not config');
        if (empty($this->options['sms']['template'])) throw new \Exception('template not config');

        if (!empty($mobile) && !Util::checkMobile($mobile)) throw new \Exception('mobile is not legitimate');
        if (!empty($tpl_id) && false === array_key_exists($tpl_id, $this->options['sms']['template'])) throw new \Exception('tpl_id not config');

        $this->AppKey = $this->options['sms']['AppKey'];
        if (!empty($tpl_id)) $this->tpl_id = $tpl_id;
        if (!empty($mobile)) $this->mobile = $mobile;
        if (!empty($tpl_value)) $this->tpl_value = $tpl_value;
        return $this;
    }

    /**
     * @param bool $isPost
     * @throws \Exception
     * @return bool  是否发送成功
     */
    public function sendSms($isPost = true)
    {
        if (empty($this->tpl_id)) throw new \Exception('tpl_id not allow empty');
        if (empty($this->mobile)) throw new \Exception('mobile not allow empty');
        $content = $this->juhecurl(self::SMS_SEND_URL, $this->getSmsConf(), $isPost);
        if($content){
            $result = json_decode($content,true);
            $error_code = $result['error_code'];
            if($error_code == 0){
                //状态为0，说明短信发送成功
                Factory::logger('juheSms')->addInfo(__CLASS__. '_' . __FUNCTION__, [__LINE__,
                    "短信发送成功,短信ID：".$result['result']['sid']
                ]);
                return true;
            }else{
                //状态非0，说明失败
                $msg = $result['reason'];
                Factory::logger('juheSms')->addInfo(__CLASS__. '_' . __FUNCTION__, [__LINE__,
                    "短信发送失败(".$error_code.")：".$msg
                ]);
                return Err::setLastErr($error_code);
            }
        }else{
            // 网络异常
            Factory::logger('juheSms')->addInfo(__CLASS__. '_' . __FUNCTION__, [__LINE__,
                "短信发送异常: $content"
            ]);
            return Err::setLastErr(E_NETWORK_ANOMALY);  // 网络异常
        }
    }

    /**
     * 获取短信发送的设置
     */
    public function getSmsConf()
    {
        $tpl_value = '';
        foreach ($this->tpl_value as $key => $value) {
            if (!empty($tpl_value)) {
                $tpl_value .= '&';
            }
            $tpl_value .= "#{$key}#={$value}";
        }
        $conf = [
            'key'           => $this->AppKey,
            'tpl_id'        => $this->tpl_id,
            'tpl_value'     => $tpl_value,
            'mobile'        => $this->mobile
        ];
        Factory::logger('juheSms')->addInfo(__CLASS__. '_' . __FUNCTION__, [__LINE__,
            "发送短信配置：", $conf
        ]);

        return $conf;
    }

    /**
     * 发送数据到聚合url
     * @param string $url   请求的url地址
     * @param mixed $params 请求的参数
     * @param bool $isPost  是否使用post方式
     * @return bool|mixed
     */
    public function juhecurl($url, $params = false, $isPost = false)
    {
        $httpInfo = array();
        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_HTTP_VERSION , CURL_HTTP_VERSION_1_1 );
        curl_setopt( $ch, CURLOPT_USERAGENT , 'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.22 (KHTML, like Gecko) Chrome/25.0.1364.172 Safari/537.22' );
        curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT , 30 );
        curl_setopt( $ch, CURLOPT_TIMEOUT , 30);
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER , true );
        if( $isPost )
        {
            curl_setopt( $ch , CURLOPT_POST , true );
            curl_setopt( $ch , CURLOPT_POSTFIELDS , $params );
            curl_setopt( $ch , CURLOPT_URL , $url );
        }
        else
        {
            if($params){
                curl_setopt( $ch , CURLOPT_URL , $url.'?'.$params );
            }else{
                curl_setopt( $ch , CURLOPT_URL , $url);
            }
        }
        $response = curl_exec( $ch );
        if ($response === FALSE) {
            //echo "cURL Error: " . curl_error($ch);
            return false;
        }
        $httpCode = curl_getinfo( $ch , CURLINFO_HTTP_CODE );
        $httpInfo = array_merge( $httpInfo , curl_getinfo( $ch ) );
        curl_close( $ch );
        return $response;
    }

}