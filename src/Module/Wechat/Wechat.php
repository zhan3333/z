<?php
namespace App\Module\Wechat;

use \App\Factory;
use \App\Util;

/**
 * Created by PhpStorm.
 * User: zhan
 * Date: 2016/11/9
 * Time: 13:00
 */
class Wechat
{
    const CODE_TYPE_ANCHOR = 'anchor';  // 主播二维码

    /* @var $wechat \EasyWeChat\Foundation\Application*/
    private $wechat = null;

    public function __construct()
    {
        $this->wechat = Factory::wechat();
    }

    /**
     * 根据主播id，获取一个永久的二维码
     * @param $anchorId integer     主播id
     * @return array
     * <pre>
     * [
     *  'ticket' => '',
     *  'url' => ''
     * ]
     * </pre>
     */
    public function getAnchorQRCode($anchorId)
    {
        $param = [
            'anchorId' => $anchorId,
            'type' => self::CODE_TYPE_ANCHOR
        ];
        $qrcode = $this->wechat->qrcode->forever(json_encode($param));
        return Util::obj2Arr($qrcode);
    }

    /**
     * 解析扫二维码传递来的数据
     * @param $content string
     * @return mixed
     */
    public function analyticScanCode($content)
    {
        $param = json_decode($content, true);
        return $param;
    }


}