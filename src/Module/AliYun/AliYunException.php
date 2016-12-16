<?php
/**
 * Created by PhpStorm.
 * User: zhan
 * Date: 2016/12/16
 * Time: 10:13
 */

namespace App\Module\AliYun;


class AliYunException extends \Exception
{
    const E_ALI_UNDEFINED_TYPE = 1001;      // 未知的类型
    const E_ALI_NOT_SET_APPCODE = 1002;     // 未设置appcode
    const E_ALI_NETWORK_ERROR = 1003;       // 网络错误
    const E_ALI_CLIENT_ERROR = 1004;        // 客户端错误

    public function __construct($message, $code = 0)
    {
        parent::__construct($message, $code);
    }

    public function __toString() {
        return __CLASS__.':['.$this->code.']:'.$this->message.'\n';
    }
}