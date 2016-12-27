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
use App\Module\Wechat\MyPayment;
use App\Util;
use EasyWeChat\Message\Article;
use EasyWeChat\Message\News;
use EasyWeChat\Payment\Merchant;
use EasyWeChat\Payment\Order;
use Overtrue\Socialite\Providers\WeChatProvider;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class Wechat
 * @package App\Service
 * @default disable
 */
class Wechat extends Base
{
    // 微信消息相关操作

    /**
     * 接收微信服务器消息
     * @default enable
     */
    public static function response()
    {
        try {
            ob_start();
            $app = Factory::wechat();
            $server = $app->server;
            $server->setRequest(Factory::getRequestObj());
            $server->setMessageHandler(__CLASS__ . '::message');
            $server->serve()->send();
            $result = ob_get_clean();
            return $result;
        } catch (\Exception $e) {
            Factory::logger('error')->addError(__CLASS__, [__FUNCTION__, __LINE__, $e]);
            ob_clean();
            return false;
        }
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
            return 'hello world';
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
     * @return int
     */
    private static function eventDo($message, &$retMsg)
    {
        $event = strtolower($message->Event);       // 事件类型
        switch ($event) {
            case 'subscribe':
                self::eventSubscribe($message, $retMsg);
                break;
            case 'unsubscribe':
                self::eventUnsubscribe($message, $retMsg);
                break;
            case 'scan':
                self::eventScan($message, $retMsg);
                break;
            case 'location':
                self::eventLocation($message, $retMsg);
                break;
            case 'click':
                self::eventClick($message, $retMsg);
                break;
            case 'view':
                self::eventView($message, $retMsg);
                break;
            default:
                break;
        }
    }

    /**
     * 关注事件，包括直接点击关注按钮、未关注扫码关注
     * @param $message
     * @param $retMsg
     * @return array
     */
    private static function eventSubscribe($message, &$retMsg)
    {
    }

    /**
     * 取消关注事件
     * @param $message
     * @param $retMsg
     */
    private static function eventUnsubscribe($message, &$retMsg)
    {

    }

    /**
     * 已关注用户扫描二维码事件
     * @param $message
     * @param $retMsg
     */
    private static function eventScan($message, &$retMsg)
    {

    }

    /**
     * 用户上报地理事件
     * @param $message
     * @param $retMsg
     */
    private static function eventLocation($message, &$retMsg)
    {

    }

    /**
     * 用户点击公众号菜单拉取消息事件
     * @param $message
     * @param $retMsg
     */
    private static function eventClick($message, &$retMsg)
    {

    }

    /**
     * 用户点击按菜单跳转到自定义页面事件
     * @param $message
     * @param $retMsg
     */
    private static function eventView($message, &$retMsg)
    {

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
            return Err::setLastErr(E_SYS_ERROR); // 系统错误
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
            return Err::setLastErr(E_SYS_ERROR); // 系统错误
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
     * @default enable
     */
    public static function paymentNotify()
    {
        Factory::logger('zhan')->addInfo(__CLASS__. '_' . __FUNCTION__, [__LINE__,
            @$GLOBALS['php://input']
        ]);
        ob_start();
        $request = Factory::getRequestObj();
        $merchant = new Merchant(Factory::getConfig('wechat', 'payment'));
        $payment = new MyPayment($merchant, $request);
        $response = $payment->handleNotify(function ($notify, $successful) {
            Factory::logger('wxPay')->addInfo(__CLASS__. '_' . __FUNCTION__, [__LINE__,
                'notify' => $notify,
                'successful' => $successful
            ]);
            //支付成功操作
            $transaction_id = $notify->transaction_id;                  //微信支付订单号
            $out_trade_no = $notify->out_trade_no;                      //内部订单号
            $total_fee = $notify->total_fee / 100;                      //总额(单位：元)



            $ur = Factory::userRepository();
            //获取数据库中充值记录信息,获取充值用户的id
            $mrId = $ur->internalOrderNum2Id($out_trade_no);
            if (empty($mrId)) return 'Order is not exist';      // 订单不存在
            $moneyRecordInfo = $ur->getUserMoneyRecordInfoById($mrId);
            $userId = $moneyRecordInfo['userId'];

            if (!Factory::getConfig('debugSwitch', 'wechatRealPay')) {
                // 微信虚拟支付金额设置，以订单内为准
                $total_fee = $moneyRecordInfo['moneyValue'];
            }

            // 订单已处理过
            if($moneyRecordInfo['transactionStatus'] == UserMoneyRecord::MONEY_RECORD_TRANSACTION_STATUS_SUCCESS
                || !empty($moneyRecordInfo['externalOrderNum'])) {
                //报出异常，并且不接受这条数据
                Factory::logger('wxPay')->addInfo(__FUNCTION__, [__LINE__, '微信回调数据异常，停止处理，请及时进行检查, UserMoneyRecord中id为:' . $mrId]);
                return 'Order is handle';
            }



            //写入微信支付订单号
            $ur->setMoneyRecord($mrId, ['externalOrderNum' => $transaction_id]);

            //增加用户夺宝币
            $payRet = $ur->pay($userId, $total_fee, $mrId);

            // 积分返现操作
            if (Factory::getConfig('moduleSwitch', 'integral')) {
                if($payRet) {
                    //若充值成功，则增加邀请者的返利积分
                    $userInfo = $ur->getUserInfo($userId, ['fromId']);
                    if (!empty($userInfo['fromId'])) {
                        // 推荐人处理
                        $shareUser = $userInfo['fromId'];
                        if ($shareUser) {
                            //增加邀请者的邀请积分
                            User::inviteUserRecharge($shareUser, $total_fee, $userId);
                        }
                    }
                }
            }

            //记录到平台收益
            $opt = [
                'userId'             => 0,                                                          //用户id
                'type'               => UserMoneyRecord::MONEY_RECORD_TYPE_PLATFORM_BENEFITS,       //操作类型
                'moneyValue'         => $total_fee,                                                 //数额
                'inOutType'          => UserMoneyRecord::MONEY_RECORD_IN_OUT_TYPE_IN,               //收入
                'internalOrderNum'   => Util::generateOrderNum(),                                   //内部订单号
                'transactionStatus'  => UserMoneyRecord::MONEY_RECORD_TRANSACTION_STATUS_SUCCESS,   //状态为成功
                'remark'             => '用户微信充值 '.$total_fee.' 元',                             //备注
            ];
            $ur->generateMoneyRecord($opt);

            //记录到操作记录中
            $optOperationLog = [
                'userId'             => 0,                                                          //用户id
                'noticeType'         => UserMoneyRecord::MONEY_RECORD_TYPE_PLATFORM_BENEFITS,       //操作类型
                'moneyValue'         => $total_fee,                                                 //数额
                'noticeStatus'       => OperationLog::OPERATION_LOG_NOTICE_SUCCESS,   //状态为成功
                'remark'             => '用微信充值 '.$total_fee.' 元',                           //备注
            ];
            $ur = Factory::userRepository();
            $ur->generateOperationLog($optOperationLog);
            return true;
        });
        $response->send();
        $result = ob_get_clean();
        return $result;
    }

    /**
     * 获取微信支付订单
     * @default enable
     * @param $openid
     * @param $payPrice integer 支付金额，单位为分
     * @return array
     */
    public static function getPaymentOrder($openid, $payPrice)
    {
        $userId = self::getClientUserId();
        if (empty($userId)) return Err::setLastErr(E_NO_LOGIN);
        if (empty($openid)) return Err::setLastErr(E_NO_INCOMING_OPENID);   // 未传入openid
        if (Factory::getConfig('debugSwitch', 'wechatPayOpenidMapping')) {
            $testOpenid = $openid;
            $formalOpenid = self::testOpenid2FormalOpenid($testOpenid);
            $openid = $formalOpenid;
            if (empty($openid)) return Err::setLastErr(E_OPENID_MAPPING_NO_EXIST);  // openid映射不存在
        }
        $payPrice = filter_var($payPrice, FILTER_VALIDATE_INT);
        if (empty($payPrice) || $payPrice <= 0) return Err::setLastErr(E_PAY_PRICE_NOT_LEGITIMATE); // 支付金额不合法

        if (!Factory::getConfig('debugSwitch', 'wechatRealPay')) {
            // 微信虚拟支付金额设置
            $total_fee = 1;
        } else {
            $total_fee = $payPrice;
        }
        $out_trade_no = Util::generateOrderNum();
        $body = self::getPaymentOrderBody(['payPrice' => $payPrice]);
        $attributes = [
            'trade_type'       => 'JSAPI', // JSAPI，NATIVE，APP...
            'body'             => $body,
//            'detail'           => 'iPad mini 16G 白色',
            'out_trade_no'     => $out_trade_no,
            'total_fee'        => $total_fee,
//            'notify_url'       => 'http://zhan.ykxing.com:8000/Wechat_paymentNotify', // 支付结果通知网址，如果不设置则会使用配置里的默认地址
            'openid' => $openid
            // ...
        ];
        $order = new Order($attributes);
        $payment = Factory::wechat()->payment;
        $result = $payment->prepare($order);
        if ($result->return_code == 'SUCCESS' && $result->result_code == 'SUCCESS'){
            $prepayId = $result->prepay_id;
            $json = $payment->configForPayment($prepayId, false);
            $paymentInfo = [
                'userId'             => $userId,                                                //用户id
                'type'               => UserMoneyRecord::MONEY_RECORD_TYPE_WEI_PAY,             //操作类型
                'moneyValue'         => $payPrice / 100,                                        //数额,单位元
                'inOutType'          => UserMoneyRecord::MONEY_RECORD_IN_OUT_TYPE_IN,           //收入
                'internalOrderNum'   => $out_trade_no,                                          //内部订单号
                'transactionStatus'  => UserMoneyRecord::MONEY_RECORD_TRANSACTION_STATUS_ON,    //状态为进行中
                'remark'             => '用户ID:'.$userId.' 微信充值 '.($payPrice / 100).' 元购买夺宝币',         //备注
            ];
            $recordRet = self::recordPaymentOrder($paymentInfo);
            if (empty($recordRet)) return Err::setLastErr(E_WECHAT_PAYMENT_GET_ORDER_FAIL); // 生成微信支付订单失败
            return [
                'result' => $json
            ];
        } else {
            return Err::setLastErr(E_GET_PAY_ORDER_FAIL);   // 获取支付订单失败
        }
    }

    private static function getPaymentOrderBody($data)
    {
        return "YY夺宝充值 " . $data['payPrice']/100 . ' 幸运币';
    }

    /**
     * 记录生成的微信订单
     * @param $payInfo
     * @return bool
     */
    private static function recordPaymentOrder($payInfo)
    {
        //信息写入平台
        $ur = Factory::userRepository();
        //记录金钱操作
        $recordRet = $ur->generateMoneyRecord($payInfo);
        //判断是否生成记录成功，若生成记录失败，则返回错误信息
        if(empty($recordRet)) return false;
        return true;
    }

    // 二维码

    /**
     * 获取临时二维码
     * @default enable
     * @param $sceneId
     * @param null $expireSeconds
     * @return array
     */
    public static function getTemporaryQRCode($sceneId, $expireSeconds = null)
    {
        $qrcode = Factory::wechat()->qrcode;
        $result = $qrcode->temporary($sceneId, $expireSeconds);
        return [
            'result' => Util::obj2Arr($result)
        ];
    }

    /**
     * 获取永久二维码
     * @default enable
     * @param $content  mixed
     * @return array
     */
    public static function getForeverQRCode($content)
    {
        $qrcode = Factory::wechat()->qrcode;
        $result = $qrcode->forever(json_encode($content));
        return [
            'result' => Util::obj2Arr($result)
        ];
    }

}