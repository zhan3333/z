<?php
/**
 * Created by PhpStorm.
 * User: zhan
 * Date: 2016/12/26
 * Time: 17:50
 */

namespace App\Module\Juhe;


use App\Factory;

class NewsHeadlines
{
    const QUERY_URL = 'http://v.juhe.cn/toutiao/index';     // 请求地址
    public $AppKey = '';
    public $typeArr = [
        'top', 'shehui', 'guonei', 'guoji', 'yule', 'tiyu', 'junshi', 'keji', 'caijing', 'shishang'
    ];

    /**
     * NewsHeadlines constructor.
     * @param array $options
     * <pre>
     * [
     *  'appKey' => ''          // 聚合申请到的appkey
     * ]
     * </pre>
     * @throws \Exception
     */
    public function __construct($options)
    {
        if (empty($options['AppKey'])) throw new \Exception('AppKey必须设置');
        if (!is_string($options['AppKey'])) throw new \Exception('AppKey类型错误');
        $this->AppKey = $options['AppKey'];
    }

    /**
     * 进行一次新闻获取
     * @param string $type
     * @return \Requests_Response
     * @throws \Exception
     */
    public function getNews($type = 'top')
    {
        if (false === array_search($type, $this->typeArr)) throw new \Exception('不存在的新闻类型');
        $url = self::QUERY_URL . "?type=$type&key=$this->AppKey";
        $result = $this->query($url);
        return $result;
    }

    public function query($url)
    {
        $result = \Requests::get($url);
        return $result;
    }
}