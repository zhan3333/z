<?php
/**
 * Created by PhpStorm.
 * User: zhan
 * Date: 01/11/2016
 * Time: 09:36
 */

namespace App\Service;


use App\Err;
use App\Factory;
use EasyWeChat\Message\News;
use EasyWeChat\Payment\Merchant;
use EasyWeChat\Payment\Order;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Overtrue\Socialite\Providers\WeChatProvider;



class Wechat
{
    // 微信消息相关操作

    /**
     * 接收微信服务器消息
     * @default enable
     */
    public static function response()
    {
        if (!empty($_GET['echostr'])) return $_GET['echostr'];  // 用于验证服务器
        ob_start();
        $app = Factory::wechat();
        $server = $app->server;
        $server->setRequest(Factory::getRequestObj());
        $server->setMessageHandler(__CLASS__ . '::message');
        $server->serve()->send();
        $result = ob_get_clean();
        Factory::logger('zhan')->addInfo(__CLASS__. '_' . __FUNCTION__, [__LINE__,
            'result' => $result
        ]);

        return $result;
    }

    /**
     * 处理微信消息事件
     * @param $message
     *
     * # 基本属性，所有消息中都会有一下4个属性
     * $message->ToUserName    接收方帐号（该公众号 ID）
     * $message->FromUserName  发送方帐号（OpenID, 代表用户的唯一标识）
     * $message->CreateTime    消息创建时间（时间戳）
     * $message->MsgId         消息 ID（64位整型）
     *
     * @return string
     */
    public static function message($message)
    {
        $retMsg = '';       // 待回复的消息
        switch ($message->MsgType) {
            case 'event':
                # 事件消息...
                self::eventDo($message, $retMsg);
                break;
            case 'text':
                # 文字消息...
                self::textDo($message, $retMsg);
                break;
            case 'image':
                self::imageDo($message, $retMsg);
                # 图片消息...
                break;
            case 'voice':
                # 语音消息...
                self::voiceDo($message, $retMsg);
                break;
            case 'video':
                # 视频消息...
                self::videoDo($message, $retMsg);
                break;
            case 'shortvideo':
                # 小视屏...
                self::shortvideoDo($message, $retMsg);
                break;
            case 'location':
                # 坐标消息...
                self::locationDo($message, $retMsg);
                break;
            case 'link':
                # 链接消息...
                self::linkDo($message, $retMsg);
                break;
            // ... 其它消息
            default:
                # code...
                self::defaultDo($message, $retMsg);
                break;
        }
        if (empty($retMsg)) {
            return 'hello world~';
        }
        return $retMsg;
    }

    /**
     * 处理事件类消息
     * @param $message
     *
     * $message->MsgType     event
     * $message->Event       事件类型 （如：subscribe(订阅)、unsubscribe(取消订阅) ...， CLICK 等）
     *
     * # 扫描带参数二维码事件
     * $message->EventKey    事件KEY值，比如：qrscene_123123，qrscene_为前缀，后面为二维码的参数值
     * $message->Ticket      二维码的 ticket，可用来换取二维码图片
     *
     * # 上报地理位置事件
     * $message->Latitude    23.137466   地理位置纬度
     * $message->Longitude   113.352425  地理位置经度
     * $message->Precision   119.385040  地理位置精度
     *
     * # 自定义菜单事件
     * $message->EventKey    事件KEY值，与自定义菜单接口中KEY值对应，如：CUSTOM_KEY_001, www.qq.com
     *
     * @param $retMsg
     */
    private static function eventDo($message, &$retMsg)
    {
        $retMsg = $message->MsgType;
    }

    /**
     * @param $message
     *
     * $message->MsgType  text
     * $message->Content  文本消息内容
     *
     * @param $retMsg
     */
    private static function textDo($message, &$retMsg)
    {
    }

    /**
     * @param $message
     *
     * $message->MsgType  image
     * $message->PicUrl   图片链接
     *
     * @param $retMsg
     * @return News
     */
    private static function imageDo($message, &$retMsg)
    {
        $retMsg = new News([
            'title' => '标题',
            'description' => '描述',
            'url' => 'http://www.baidu.com',
            'image' => 'http://h.hiphotos.baidu.com/zhidao/pic/item/6d81800a19d8bc3ed69473cb848ba61ea8d34516.jpg'
        ]);
    }

    /**
     * @param $message
     *
     * $message->MsgType        voice
     * $message->MediaId        语音消息媒体id，可以调用多媒体文件下载接口拉取数据。
     * $message->Format         语音格式，如 amr，speex 等
     * $message->Recognition * 开通语音识别后才有
     *
     * @param $retMsg
     */
    private static function voiceDo($message, &$retMsg)
    {
    }

    /**
     * @param $message
     *
     * $message->MsgType       video
     * $message->MediaId       视频消息媒体id，可以调用多媒体文件下载接口拉取数据。
     * $message->ThumbMediaId  视频消息缩略图的媒体id，可以调用多媒体文件下载接口拉取数据。
     *
     * @param $retMsg
     */
    private static function videoDo($message, &$retMsg)
    {
    }

    /**
     * @param $message
     *
     * $message->MsgType     shortvideo
     * $message->MediaId     视频消息媒体id，可以调用多媒体文件下载接口拉取数据。
     * $message->ThumbMediaId    视频消息缩略图的媒体id，可以调用多媒体文件下载接口拉取数据。
     *
     * @param $retMsg
     */
    private static function shortvideoDo($message, &$retMsg)
    {

    }

    /**
     * @param $message
     *
     * $message->MsgType     location
     * $message->Location_X  地理位置纬度
     * $message->Location_Y  地理位置经度
     * $message->Scale       地图缩放大小
     * $message->Label       地理位置信息
     *
     * @param $retMsg
     */
    private static function locationDo($message, &$retMsg)
    {
    }

    /**
     * @param $message
     *
     * $message->MsgType      link
     * $message->Title        消息标题
     * $message->Description  消息描述
     * $message->Url          消息链接
     *
     * @param $regMsg
     */
    private static function linkDo($message, &$regMsg)
    {
    }

    private static function defaultDo($message, &$regMsg)
    {
    }

    // 菜单相关操作

    /**
     * 添加一个菜单
     * @default enable
     * @param array $buttons
     * @return array
     */
    public static function addMenu($buttons = [])
    {
        $menu = Factory::wechat()->menu;
        $addRet = $menu->add($buttons);
        if (!isset($addRet['errcode'])) {
            // 未知错误
            return Err::setLastErr(E_SYSTEM_ERROR); // 系统错误
        } else {
            if ($addRet['errcode'] !== 0) return Err::setLastErr($addRet['errcode']);
        }
        return [
          'result' => true
        ];
    }

    /**
     * 查询菜单信息
     * @default enable
     */
    public static function queryMenu()
    {
        try {
            $menu = Factory::wechat()->menu;
            $menus = $menu->all();
            return [
                'result' => $menus
            ];
        } catch (\Exception $e) {
            return [
                'result' => []
            ];
        }


    }

    /**
     * 删除菜单配置/根据id删除一个按钮
     * @default enable
     * @param int $menuId
     * @return bool
     */
    public static function deleteMenu($menuId = 0)
    {
        $menu = Factory::wechat()->menu;
        if (empty($menuId)) {
            $deleteRet = $menu->destroy();
        } else {
            $deleteRet = $menu->destroy($menuId);
        }
        if (!isset($deleteRet['errcode'])) {
            // 未知错误
            return Err::setLastErr(E_SYSTEM_ERROR); // 系统错误
        } else {
            if ($deleteRet['errcode'] !== 0) return Err::setLastErr($deleteRet['errcode']);
        }
        return [
            'result' => true
        ];
    }

    // 微信支付相关
    /**
     * 接收微信支付回调信息
     * todo 未测试是否能够使用
     * @default enable
     */
    public static function paymentNotify()
    {
        Factory::logger('zhan')->addInfo(__CLASS__. '_' . __FUNCTION__, [__LINE__,
            @$GLOBALS['php://input']
        ]);
        ob_start();
        $request = Factory::getRequestObj();
        $merchant = new Merchant(Factory::getConfig('wechat'));
        $payment = new \MyPayment($merchant, $request);
        $response = $payment->handleNotify(function ($notify, $successful) {
            Factory::logger('zhan')->addInfo(__CLASS__. '_' . __FUNCTION__, [__LINE__,
                'notify' => $notify,
                'successful' => $successful
            ]);
            return true;
        });
        $response->send();
        $result = ob_get_clean();
        return $result;
    }

    /**
     * 获取微信支付订单
     * todo 创建订单还需要传入openId
     * @default enable
     */
    public static function getPaymentOrder()
    {
        $attributes = [
            'trade_type'       => 'JSAPI', // JSAPI，NATIVE，APP...
            'body'             => 'iPad mini 16G 白色',
            'detail'           => 'iPad mini 16G 白色',
            'out_trade_no'     => '1217752501201407033233368018',
            'total_fee'        => 5388,
            'notify_url'       => 'http://zhan.ykxing.com:8000/Wechat_paymentNotify', // 支付结果通知网址，如果不设置则会使用配置里的默认地址
            'spbill_create_ip' => $_SERVER['HTTP_X_REAL_IP']
            // ...
        ];
        $order = new Order($attributes);
        $payment = Factory::wechat()->payment;
        $result = $payment->prepare($order);
        return [
            'result' => $result
        ];
    }
}