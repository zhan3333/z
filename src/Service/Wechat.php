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
        ob_start();
        $app = Factory::wechat();
        $server = $app->server;
        $server->setRequest(Factory::getRequestObj());
        $server->setMessageHandler(__CLASS__ . '::message');
        $server->serve()->send();
        $result = ob_get_clean();
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

    // 用户相关

    /**
     * 根据openid获取用户微信信息
     * @param $openid
     * @return array
     */
    private static function getUserInfoByOpenid($openid)
    {
        try {
            $user = Factory::wechat()->user;
            $userInfo = $user->get($openid);
            return $userInfo;
        } catch (\Exception $e) {
            Factory::logger('error')->addError(__CLASS__, [__FUNCTION__, __LINE__, $e]);
            return ['openid' => $openid];
        }
    }

    /**
     * 根据openid获取用户微信信息
     * @param $openid
     * @return array
     */
    public static function testGetUserInfoByOpenid($openid)
    {
        $user = Factory::wechat()->user;
        $userInfo = $user->get($openid);
        return [
            'result' => $userInfo
        ];
    }

    /**
     * 微信账号登陆
     * 当openId未注册时，自动调用注册，然后进行登陆操作
     * @default enable
     * @param string $openid   页面get到的openid
     * @param string $token    页面get到的token
     * @return array
     * <pre>
     * (1)成功时返回：
     * [
     *  'once' => [
     *      'token' => '',
     *      'userId' =>> ''
     *  ]
     * ]
     * (2)失败时返回:
     * [
     *  'once' => false
     * ]
     * </pre>
     *
     */
    public static function openidLogin($openid, $token)
    {
        $openid = filter_var($openid, FILTER_SANITIZE_STRING);
        if (empty($openid)) {
            return Err::setLastErr(E_NO_INCOMING_OPENID);   // 未传入openid
        }
        $ur = Factory::userRepository();
        $userId = $ur->openid2UserId($openid);
        if (empty($userId)) {
            // 用户openid未在平台注册, 进行注册操作
            $wechatInfo = self::getUserInfoByOpenid($openid);
            $userInfo = [];
            if (!empty($wechatInfo['nickname'])) {
                $userInfo['nickname'] = $wechatInfo['nickname'];
            }
            $userInfo['type'] = UserInfo::UT_NORMAL;
            $userInfo['accountType'] = UserInfo::ACCOUNT_WECHAT;
            $userId = $ur->wechatReg($wechatInfo, $userInfo);    // 注册
        }
        $token = parent::openidLogin($userId, $openid, $token);
        if (empty($token)) {
            return [
                'once' => false
            ];
        }
        return [
            'once' => [
                'token' => $token,
                'userId' => $userId
            ]
        ];
    }

    /**
     * 前端获取jsSign
     * @default enable
     * @param array $apis 配置中的jsApiList，必须填写
     * <pre>
     * [
     *  'onMenuShareQQ', 'onMenuShareWeibo'
     * ]
     * </pre>
     * @param string $url   设置当前url， 会在result中返回
     * <pre>http://www.baidu.com</pre>
     * @return array 返回jsSign信息
     * <pre>
     * [
     *  'debug' => true,
     *  'beta' => false,
     *  'appId' => '*****',
     *  'nonceStr' => '******',             // 随机字符串
     *  'timestamp' => 1445478454,          // 生成签名的时间戳
     *  'url' => 'http://www.baidu.com',    // 页面url, 当页面url变更时，需要重新获取jsSign
     *  'signature' => '*****',             // 签名
     *  'jsApiList' => [                    // 需要使用的js接口列表
     *      'onMenuShareQQ', 'onMenuShareWeibo'
     *  ]
     * ]
     * </pre>
     */
    public static function getJsSign($apis = [], $url = '')
    {
        $userId = self::getClientUserId();
        if (empty($userId)) return Err::setLastErr(E_NO_LOGIN); // 未登陆
        if (!is_array($apis) || !filter_var($url, FILTER_VALIDATE_URL)) return Err::setLastErr(E_PARAM_ERROR);  // 参数错误
        $js = Factory::wechat()->js;
        $js->setUrl($url);
        $result = $js->config($apis, true, false, false);
        return [
            'result' => $result
        ];
    }

    // 消息发送相关

    /**
     * 推送一条消息
     * @default enable
     * @param $messageType  string  发送消息类型
     * @param $message      string  消息表标识
     * @param $user         mixed   接收者
     * @return array
     */
    public static function sendBroadcast($messageType, $message, $user = null)
    {
        $typeList = ['news', 'text', 'voice', 'image', 'video', 'card'];
        if (false === array_search($messageType, $typeList)) return Err::setLastErr(E_WECHAT_MATERIAL_NOT_EXIST_TYPE);  // 不存在的类型
        $broadcast = Factory::wechat()->broadcast;
        $sendRet = $broadcast->send($messageType, $message, $user);
        return [
            'result' => Util::obj2Arr($sendRet)
        ];
    }

    // oauth 授权相关
    // 三种方式：
    // 1. (跳转4次)
    // startOauth
    // 转到wechat授权链接
    // 跳转到receiveOauth
    // 达到toUrl地址，完成操作
    // 2. (跳转3次)
    // 直接访问wechat授权链接
    // 跳转到receiveOauth
    // 达到toUrl地址，完成操作
    // 3. (跳转2次, 调用接口两次)
    // 直接访问wechat授权链接
    // 直接达到目的地址
    // 访问code2Openid，获取openid与token
    // 使用openid与token进行登陆操作
    // 4. (跳转2次，调用接口一次)
    // 访问oauth页面
    // 达到目的地址
    // 访问codeLogin接口进行登陆操作

    /**
     * 发起授权，供用户点击进入微信时使用。
     * 将设置回调地址为 receiveOauth 接口
     * @default enable
     */
    public static function startOauth()
    {
        /* @var $oauth WeChatProvider**/
        $oauth = Factory::wechat()->oauth;
        /* @var $response RedirectResponse**/
        $response = $oauth->redirect();
        $headers = $response->headers;
        foreach ($headers as $key => $header) {
            foreach ($header as $item) {
                echo $key . ':' . $item . PHP_EOL;
            }
        }
    }

    /**
     * 获取Oauth验证跳转链接
     * @default enable
     * @param null $url     Oauth验证后跳转地址
     * @return array
     */
    public static function getOauthUrl($url = null)
    {
        if (empty($url)) $url = null;
        /* @var $oauth WeChatProvider**/
        $oauth = Factory::wechat()->oauth;
        /* @var $response RedirectResponse**/
        $response = $oauth->redirect($url);
        $headers = $response->headers;
        $url = '';
        foreach ($headers as $key => $header) {
            foreach ($header as $item) {
                if ($key == 'location') {
                    $url = $item;
                }
            }
        }
        return [
            'result' => $url
        ];
    }

    /**
     * 接收授权的回调数据。
     * 正确处理时将进行页面跳转，跳转后的页面将GET到参数：=openid=***&token=****
     * @default enable
     */
    public static function receiveOauth()
    {
        if (!empty($_GET)) {
            try {
                /* @var $oauth WeChatProvider**/
                $oauth = Factory::wechat()->oauth;
                $oauth->setRequest(Factory::getRequestObj());
                $user = $oauth->user();
                $openid = $user->getId();
                // 生成openid登陆使用的token
                $token = self::getOpenidLoginToken($openid);
                Factory::logger('zhan')->addInfo(__CLASS__. '_' . __FUNCTION__, [__LINE__,
                    'openid' => $openid,
                    'token' => $token
                ]);
                $toUrl = 'http://proxy-pass.js.yiyuan.zhannnnn.top' . '?openid=' . $openid . '&token=' . $token;
                echo 'Cache-Control: no-cache' . PHP_EOL;
                echo 'Location: '. $toUrl . PHP_EOL;
            } catch (\Exception $e) {
                Factory::logger('error')->addError(__CLASS__, [__FUNCTION__, __LINE__, $e]);
            }

        }
    }

    /**
     * 获取自己的微信相关数据
     * @default enable
     * @return array
     * <pre>
     * [
     *  'result' => [
     *      'id' => 0,
     *      'userId' => 0,
     *      'openid' => '',
     *      'nickname' => '',       // 用户微信昵称
     *      'sex' => 0,             // 用户性别
     *      'city' => '',           // 城市
     *      'country' => '',        // 国家
     *      'province' => '',       // 省
     *      'headimgurl' => ''      // 头像，可自定义不同规格
     *  ]
     * ]
     * </pre>
     */
    public static function getSelfWechatInfo()
    {
        $userId = self::getClientUserId();
        if (empty($userId)) return Err::setLastErr(E_NO_LOGIN);
        $userInfo = Factory::userRepository()->getWechatInfoByUserId($userId);
        return [
            'result' => $userInfo
        ];
    }

    /**
     * 根据code码，获取用于登陆的openid和token
     * @default enable
     * @param $code
     * @return array
     * <pre>
     * [
     *  'token' => '',
     *  'openid' => ''
     * ]
     * </pre>
     */
    public static function code2Openid($code = '')
    {
        try {
            /* @var $oauth WeChatProvider**/
            $oauth = Factory::wechat()->oauth;
            $accessToken = $oauth->getAccessToken($code);
            $user = $oauth->user($accessToken);
            $openid = $user->getId();
            $token = self::getOpenidLoginToken($openid);
            return [
                'token' => $token,
                'openid' => $openid
            ];
        } catch (\Exception $e) {
            Factory::logger('error')->addError(__CLASS__, [__FUNCTION__, __LINE__, $e]);
            Factory::logger('zhan')->addInfo(__CLASS__. '_' . __FUNCTION__, [__LINE__,
                $e
            ]);
            return Err::setLastErr(E_WECHAT_INVALID_CODE);      // 无效的code
        }

    }

    /**
     * 使用code码进行登陆
     * @default enable
     * @param $code
     * @return array
     * <pre>
     * [
     *  'once' => [
     *      'token' => '',
     *      'userId' => 0
     *  ],
     *  'openid' => 0
     * ]
     * </pre>
     */
    public static function codeLogin($code)
    {
        try {
            $exchangeRet = self::code2Openid($code);
            if (empty($exchangeRet)) return Err::setLastErr(E_WECHAT_INVALID_CODE); // 无效的code码
            $loginRet = self::openidLogin($exchangeRet['openid'], $exchangeRet['token']);
            if (!empty($loginRet)) {
                $loginRet['openid'] = $exchangeRet['openid'];
            }
            return $loginRet;
        } catch (\Exception $e) {
            Factory::logger('error')->addError(__CLASS__, [__FUNCTION__, __LINE__, $e]);
            Factory::logger('zhan')->addInfo(__CLASS__. '_' . __FUNCTION__, [__LINE__,
                $e
            ]);
            return Err::setLastErr(E_WECHAT_CODE_LOGIN_FAIL);   // code码登陆失败
        }
    }

    // openid映射, 临时用

    /**
     * 测试openid映射正式openid
     * @default enable
     * @test
     * @param $testOpenid
     * @return string
     */
    public static function testOpenid2FormalOpenid($testOpenid)
    {
        $mapping = self::getMappingTable();
        $formalOpenid = array_search($testOpenid, $mapping);
        return $formalOpenid;
    }

    /**
     * 正式openid映射测试openid
     * @default enable
     * @test
     * @param $formalOpenid
     * @return string
     */
    public static function formalOpenid2TestOpenid($formalOpenid)
    {
        $mapping = self::getMappingTable();
        if (!empty($mapping[$formalOpenid])) return $mapping[$formalOpenid];
        return '';
    }

    /**
     * 获取映射列表  正式 =》 测试
     * @test
     */
    private static function getMappingTable()
    {
        $mapping = [
            'ojwDCvxTg3rjcah_03rbXQIX-aO8' => 'oy8ouxAIHp8Kukxp9c2b1nBHLDCE',           // carry
            'ojwDCv-BK_GBuHa9uqplprTCeMHk' => 'oy8ouxBqBt2QQCCj0NZNTXtN325M',           // zhan
            'ojwDCv1XBrOWF2pQppKKW0L9e-W8' => 'oy8ouxGPQnrbwdQaomgJ0K-cirjw',           // ccm
            'ojwDCv8jhmuQTDa-F-geJKSKaBn0' => 'oy8ouxE0OcEiVpUXOsQg9IyvyHeQ',        // test-carry
            'ojwDCv36enzQszQWQPtDvtoMTpSA' => 'oy8ouxMNIdMGX03uZlmQHh-nWgS0',        //　hbh
            'ojwDCvyjGpz3JNBDPDvsk_Oyqn0o' => 'oy8ouxFgB_iLyO9oSbSbjveM0-no',        // xtp
            'ojwDCv_qp_9A-jOEiSBtjcAhY-8M' => 'oy8ouxGzv9zAXsOT3za97orIjAD8',   // fenghe
            'ojwDCv8vud8ZQPmQ641dAoWlEBis' => 'oy8ouxKln1mdiYUQKc_wY5snpT4s',   // 邵丽
            'ojwDCvzuisKEentUHh0DIDEx2PrU' => 'oy8ouxCavVZ2mfnwQEddBv8r6oB8',     //
            'ojwDCv4DFlpPPaKFuxTX6OjzHwRk' => 'oy8ouxDo_klH1qCKb1sWdmVrCyb8',       // 紫阳
            'ojwDCv5A3it3717_tbeJtorYTMjA' => 'oy8ouxD0ZcxBu92VWwM8NJttDQn8',       // 胡猛
        ];
        return $mapping;
    }

    //素材管理

    /**
     * post方式上传永久素材中的图片，支持多张同时上传，限制：1M以下大小，支持 bmp/png/jpeg/jpg/gif 格式
     * @default enable
     */
    public static function materialUploadImage()
    {
        if (empty($_FILES)) return Err::setLastErr(E_WECHAT_NOT_UPLOAD_FILE);   // 未上传图片
        $material = Factory::wechat()->material;
        $saveRet = Util::saveFile($_FILES);
        $uploadRet = [];
        foreach ($saveRet as $item) {
            $fullPath = $item['fullPath'];
            $uploadRet[] = $material->uploadImage($fullPath);
        }
        return [
            'result' => Util::obj2Arr($uploadRet)
        ];
    }

    /**
     * 上传永久图文消息
     * @default enable
     * @param $opt  array
     * <pre>
     * [
     *  'thumb_media_id'    => '',  // 图文消息的蒙面图片素材id(必须时永久mediaId)
     *  'author'            => '',  // 作者
     *  'title'             => '',  // 标题
     *  'content'           => '',  // 图文消息的具体内容（支持html标记，必须少于2万字，小于1M，会去除JS）
     *  'digest'            => '',  // 图文消息摘要，仅有单图文消息才有摘要，多图文此处为空
     *  'show_cover'        => '',  // 是否显示封面，用1， 0 表示
     *  'source_url'        => '',  // 阅读原文后访问的url
     * ]
     * </pre>
     * @return array
     */
    public static function materialUploadArticle($opt)
    {
        $material = Factory::wechat()->material;
        $opt = array_filter(filter_var_array($opt, [
            'thumb_media_id' => FILTER_SANITIZE_STRING,
            'author' => FILTER_SANITIZE_STRING,
            'title' => FILTER_SANITIZE_STRING,
            'content' => FILTER_SANITIZE_STRING,
            'digest' => FILTER_SANITIZE_STRING,
            'show_cover' => FILTER_VALIDATE_INT,
            'source_url' => FILTER_VALIDATE_URL
        ]), function ($a) {return ($a===false || $a===null)?false:true;});
        Factory::logger('zhan')->addInfo(__CLASS__. '_' . __FUNCTION__, [__LINE__,
            $opt
        ]);

        $article = new Article($opt);
        $uploadRet = $material->uploadArticle($article);
        return [
            'result' => Util::obj2Arr($uploadRet)
        ];
    }

    /**
     * 获取永久素材
     * @default enable
     * @param $mediaId  string 素材id
     * @return array
     * <pre>
     * [
     *  'result' => [
     *      'news_item' => [
     *          [
     *              'title' => '',
     *              'thumb_media_id' => '',
     *              'show_cover_pic' => '',
     *              'author' => '',
     *              'digest' =>'',
     *              'content' => '',
     *              'url' => '',
     *              'content_source_rul' => ''
     *          ],
     *          ... //多图文消息有多篇文章
     *      ],
     *  ]
     * ]
     * </pre>
     */
    public static function materialGetResource($mediaId)
    {
        $material = Factory::wechat()->material;
        $resource = $material->get($mediaId);
        return [
            'result' => Util::obj2Arr($resource)
        ];
    }

    /**
     * 获取永久素材列表
     * @default enable
     * @param $type string      可选值：['image', 'video', 'voice', 'news']
     * @param int $offset
     * @param int $count
     * @return array
     * <pre>
     * 图片、语音、视频 等类型的返回如下
     * [
     *  'result' => [
     *      'total_count' => 0,
     *      'item_count' => 0,
     *      'item' => [
     *          [
     *              'media_id' => 0,
     *              'name' => '',
     *              'update_time' => 0,
     *              'url' => ''
     *          ],
     *          ...
     *      ]
     *  ]
     * ]
     * </pre>
     * 永久图文消息素材列表的响应如下：
     * [
     *  'result' => [
     *      'total_count' => 0,
     *      'item_count' => 0,
     *      'item' => [
     *          [
     *              'media_id' => 0,
     *              'content' => [
     *                  'news_item' => [
     *                      [
     *                          'title' => '',
     *                          'thumb_media_id' => 0,
     *                          'show_cover_pic' => 0,
     *                          'author' => '',
     *                          'digest' => '',
     *                          'content' => '',
     *                          'url' => '',
     *                          'content_source_url' => ''
     *                      ],
     *                      ... // 多图文消息中的多文章
     *                  ]
     *              ],
     *              'update_time' => 0
     *          ],
     *          ... // 可能有多个图文消息item结构
     *      ]
     *  ]
     * ]
     * <pre>
     * [
     *
     * ]
     * </pre>
     */
    public static function materialGetResourceList($type, $offset = 0, $count = 20)
    {
        $typeList = ['image', 'video', 'voice', 'news'];
        if (false === array_search($type, $typeList)) return Err::setLastErr(E_WECHAT_MATERIAL_NOT_EXIST_TYPE);  // 不存在的永久素材类型
        $material = Factory::wechat()->material;
        $lists = $material->lists($type, $offset, $count);
        return [
            'result' => Util::obj2Arr($lists)
        ];
    }

    /**
     * 获取素材计数
     * @default enable
     * <pre>
     * [
     *  'result' => [
     *      'voice_count' => 0,
     *      'video_count' => 0,
     *      'image_count' => 0,
     *      'news_count'  => 0
     *  ]
     * ]
     * </pre>
     */
    public static function materialGetStats()
    {
        $material = Factory::wechat()->material;
        $stats = $material->stats();
        return [
            'result' => Util::obj2Arr($stats)
        ];
    }

    /**
     * 删除永久素材
     * @default enable
     * @param $mediaId
     * @return array
     */
    public static function materialDelete($mediaId)
    {
        $material = Factory::wechat()->material;
        $delRet= $material->delete($mediaId);
        return [
            'result' => $delRet
        ];
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