<?php
/**
 * 启动入口文件
 * @package App
 * @author  carry
 * @date    2016/03/22 14:34
 */
date_default_timezone_set('UTC');
define('APP_PREFIX', 'z');
define('APPTIMEZONE', '-8');        // 校正东8区
define('WEBPATH', __DIR__);
define('APPPATH', __DIR__);
define('CONFPATH', APPPATH.'/src/Config');
define('ENVIRONMENT', 'DEVELOP');   // 生产环境定义
define('DAILY_SECOND', 86400);
require 'vendor/autoload.php';

use App\Factory;
use App\Err;


/**
 * Class main
 *
 * @method set(array $config)
 */
class main extends Hprose\Swoole\WebSocket\Server
{

    public function workerStart(swoole_server $server, $worker_id)
    {
        if ($worker_id >= $server->setting['worker_num']) {
            echo date('Y-m-d H:i:s'), ' Task ', $worker_id, ' Start', PHP_EOL;
            cli_set_process_title(APP_PREFIX.' task #'.$worker_id);
        } else {
            echo date('Y-m-d H:i:s'), ' Worker ', $worker_id, ' Start', PHP_EOL;
            cli_set_process_title(APP_PREFIX.' worker #'.$worker_id);
            //进程0执行的操作
            if (0 == $worker_id) {

            }
            $this->onBeforeInvoke = [$this, 'beforeCall'];
            $this->setGetEnabled(true);
            $this->setCrossDomainEnabled(true);             //是否允许跨域
            $this->setP3PEnabled(true);                     //向浏览器声明自己的隐私规则

            $itFile = new \FilesystemIterator(__DIR__.'/src/Service/', \FilesystemIterator::KEY_AS_FILENAME);
            foreach ($itFile as $fileName) {
                $pathInfo = pathinfo($fileName);
                if ('php' != $pathInfo['extension']) continue;
                if ('Base' == $pathInfo['filename']) continue;

                $className = "App\\Service\\".$pathInfo['filename'];
                if (!class_exists($className)) continue;

                $refObj = new \ReflectionClass($className);     //获取类的相关信息

                $refObjMethod = $refObj->getMethods(\ReflectionMethod::IS_STATIC);

                if (count($refObjMethod) > 0) {
                    foreach ($refObjMethod as $methodInfo) {
                        if (!$methodInfo->isPublic()) continue;
                        if (!$methodInfo->isStatic()) continue;

                        $methodDoc = $methodInfo->getDocComment();

                        $authMatches = [];
                        preg_match('/@default\s+(enable|disable|)/i', $methodDoc, $authMatches);
                        if (!empty($authMatches[1]) && ('disable' == strtolower($authMatches[1]))) continue;
                        if (false !== stripos($methodInfo, 'asyncRet')) continue;
                        $isAsync = (false !== stripos($methodInfo, 'async'));
                        $this->addFunction(
                            [__CLASS__, 'hproseProxy'],
//                            [$className, $methodInfo->name],
                            $pathInfo['filename'].'_'.$methodInfo->name,
                            [
                                'mode' => Hprose\ResultMode::Normal,
                                'simple' => null,
                                'async' => $isAsync
                            ]
                        );
                    }
                }
            }
        }
    }

    public function hproseProxy()
    {
        $call = $GLOBALS['real'];
        $delimiter = strpos($call, '_');
        $result = [];       //待返回的结果
        $class = 'App\Service\\'.strtolower(substr($call, 0, $delimiter));
        $method = strtolower(substr($call, $delimiter + 1));
        $realCall = [$class, $method];
        Factory::logger('action')->addDebug('input',[$method, func_get_args()]);


        Err::setLastErr(0);     //初始化error code
        if(Factory::getConfig('swoole', 'debug') ) $execStart = microtime(true) * 1000;

        if(empty($GLOBALS['hproseCall']) && !is_callable($realCall) ) {
            Err::setLastErr(109);
        } else {
            $result = call_user_func_array( $realCall, func_get_args() );
        }

        $lastError = Err::getLastErr(); //获取error code
        if ($lastError) {
            $result['ret'] = ['code' => $lastError, 'msg' => Err::getErrMsg()];
        } else {
            if (!is_scalar($result)) {
                $result['ret'] = ['code' => 0, 'msg' => Err::getErrMsg()];
                if(!empty($_REQUEST['back'])) $result['back'] = $_REQUEST['back'];
            }
        }
        if(!empty($execStart) && !is_scalar($result) ) {
            $execTime = ceil(microtime(true) * 1000 - $execStart);              //计算接口执行时间
            $result['debug']['call'] = [ 'name' => $call, 'time' => $execTime];
            $result['debug']['input'] = func_get_args();                        //返回输入的参数
        }
        unset($GLOBALS['real']);
        unset($GLOBALS['hproseCall']);
        $_REQUEST = [];
        Factory::logger('action')->addDebug('output', [$result]);
        return $result;
    }

    public function start()
    {
        $this->on('workerStart', [$this, 'workerStart']);
        parent::start();
    }

    /**
     * 接收hprose客户端消息
     * @param $name
     * @param $args
     * @param $byRef
     * @param $context
     */
    public function beforeCall($name, &$args, $byRef, $context)
    {
        $server = Factory::swoole();
        if (isset($context->fd)) {
            // websocket长连接
            $fd = $context->fd;
            $clientInfo = $server->getClientInfo($fd);
            // 客户端IP获取(兼容 $_SERVER)
            if (!empty($clientInfo)) $_SERVER['REMOTE_ADDR'] = $clientInfo['remote_ip'];
            $_SERVER['REQUEST_TIME'] = time();
        } else {
            /**
             * @var \swoole_http_request $request
             * ajax 跨域
             */
            $request = $context->request;
            $this->setGlobal($request);
        }
        if (!empty($args[0]) ) {
            $args = $args[0];
            $this->parseArgs($args);
        }
        $GLOBALS['real'] = $name;
        $GLOBALS['hproseCall'] = true;


        $delimiter = strpos($name, '_');
        $class = 'App\Service\\'.strtolower(substr($name, 0, $delimiter));
        $method = strtolower(substr($name, $delimiter + 1));
        $realCall = [$class, $method];
        $context->method = $realCall;
    }

    public function setGlobal(\swoole_http_request $request)
    {
        $_REQUEST = $_SESSION = $_COOKIE = $_FILES = $_POST = $_SERVER = $_GET = array();
        if (!empty($request->get) ) $_GET = $request->get;
        if (!empty($request->post) ) $_POST = $request->post;
        if (!empty($request->files) ) $_FILES = $request->files;
        if (!empty($request->cookie) ) $_COOKIE = $request->cookie;
        if (!empty($request->server) ) $_SERVER = array_change_key_case($request->server, CASE_UPPER);
        //获取非urlencode-form表单的POST原始数据，现用于接收微信支付的回调结果
        if (!empty($request->rawContent())) {
            $GLOBALS['php://input'] = $request->rawContent();
            $GLOBALS['HTTP_RAW_POST_DATA'] = $request->rawContent();        //微信支付返回数据 xml格式
        }
        $_REQUEST = array_merge($_GET, $_POST, $_COOKIE);

        /**
         * 将HTTP头信息赋值给$_SERVER超全局变量
         */
        foreach($request->header as $key => $value) $_SERVER['HTTP_'.strtoupper(str_replace('-', '_', $key))] = $value;
        if(empty($_SERVER['HTTP_X_REAL_IP'])) $_SERVER['HTTP_X_REAL_IP'] = $_SERVER['REMOTE_ADDR'];
    }

    private function parseArgs(&$args)
    {
        Factory::logger('zhan')->addInfo(__FUNCTION__, [__LINE__, 'parseArgs']);
        if(!empty($args['cookie'])) {
            if(!empty($args['cookie']['userId'])) $_REQUEST['userId'] = $args['cookie']['userId'];      //用户id
            if(!empty($args['cookie']['token'])) $_REQUEST['token'] = $args['cookie']['token'];         //token
        }
        if (!empty($args['threeApp'])) {
            if (!empty($args['threeApp']['openid'])) $_REQUEST['openid'] = $args['threeApp']['openid'];
            if (!empty($args['threeApp']['openkey'])) $_REQUEST['openkey'] = $args['threeApp']['openkey'];
            if (!empty($args['threeApp']['appid'])) $_REQUEST['appid'] = $args['threeApp']['appid'];
        }
        if(!empty($args['userId'])) $_REQUEST['userId'] = $args['userId'];
        if(!empty($args['token'])) $_REQUEST['token'] = $args['token'];
        if(!empty($args['back'])) $_REQUEST['back'] = $args['back'];                //这里的数据将会原样返回给客户端
        unset($args['cookie'],$args['back']);
        if(!empty($args['data']) ) $args = $args['data'];
    }

    public function handle(\swoole_http_request $request = null, \swoole_http_response $response = null)
    {
        if ((strlen($request->server['request_uri']) > 1)
            && ('POST' == $request->server['request_method'])
        ) {
            $request->server['request_method'] = 'GET';
        }
        $context = $this->createContext($request, $response);
        $GLOBALS['context'] = $context;     // 存储在全局中
        return parent::handle($request, $response);
    }

    public function doFunctionList()
    {
        $context = $GLOBALS['context'];
        $path = '/';
        $result = '';
        /**
         * @var swoole_http_request $request
         */
        $request = null;
        if(!empty($context->request) ) {
            $request = $context->request;
            $path = $request->server['request_uri'];
        }

        if (strlen($path) > 1) {
            $reqTag = trim($path, '/');
            $reqArg = [];
            if (!empty($request->post) ) $reqArg = $request->post;
            if (empty($reqArg) && !empty($request->get)) $reqArg = $request->get;
            $this->setGlobal($request);
            if (!empty($reqArg) ) $this->parseArgs($reqArg);
            $GLOBALS['real'] = $reqTag;
            $result = call_user_func_array([$this, 'hproseProxy'], $reqArg);
            if (!is_scalar($result)) $result = json_encode($result, JSON_UNESCAPED_UNICODE);
        } else {
            //$result = parent::doFunctionList($context);    取消掉直接访问接口页面时的接口名显示
        }
        return $result;
    }
}

$server = new main('ws://0.0.0.0:2000');
$server->setErrorTypes(E_ALL);
$server->setDebugEnabled();
$server->start();

