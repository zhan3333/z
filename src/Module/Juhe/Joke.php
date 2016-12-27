<?php
/**
 * Created by PhpStorm.
 * User: zhan
 * Date: 2016/12/27
 * Time: 10:41
 */

namespace App\Module\Juhe;


class Joke
{
    private $AppKey = null;
    private $urlArr = [
        'randJoke' => 'http://v.juhe.cn/joke/randJoke.php',                     // 随机获取趣图/笑话
        'newImgJoke' => 'http://japi.juhe.cn/joke/img/text.from',            // 最新趣图
        'listImgJoke' => 'http://japi.juhe.cn/joke/img/list.from',            // 按更新时间查询趣图
        'newTextJoke' => 'http://japi.juhe.cn/joke/content/text.from',    // 最新笑话
        'listTextJoke' => 'http://japi.juhe.cn/joke/content/list.from'     // 按更新时间查询笑话
    ];

    /**
     * Joke constructor.
     * @param $options
     * @throws \Exception
     */
    public function __construct($options)
    {
        if (empty($options['AppKey'])) throw new \Exception('AppKey必须设置');
        if (!is_string($options['AppKey'])) throw new \Exception('AppKey类型错误');
        $this->AppKey = $options['AppKey'];
    }

    /**
     * 获取随机趣图/笑话
     * @param string $type 获取类型，默认为文字类型，传入pic时，获取趣图类型
     * @return array|\Requests_Response
     * @throws \Exception
     */
    public function getRandJoke($type = '')
    {
        $type = strtolower($type);
        if (!empty($type) && $type != 'pic') throw new \Exception('type 填写错误');
        $result = $this->query('randJoke', ['jokeType' => $type]);
        if (empty($result)) throw new \Exception('查询异常');
        return $result;
    }

    /**
     * 获取最新图片笑话
     * @param int $page 当前页数
     * @param int $pagesize 每次返回条数
     * @return array|\Requests_Response
     * @throws \Exception
     */
    public function getNewImgJoke($page = 1, $pagesize = 1)
    {
        if (!is_int($pagesize) || $pagesize < 1 || $pagesize > 20) throw new \Exception('pagesize 填写错误');
        if ($page < 1) throw new \Exception('page 填写错误');
        $result = $this->query('newImgJoke', ['page' => $page, 'pagesize' => $pagesize]);
        if (empty($result)) throw new \Exception('查询异常');
        return $result;
    }

    /**
     * @param int       $page           当前页数，默认为1
     * @param int       $pagesize       每次返回条数，默认为，最大20
     * @param string    $sort           类型，desc:指定时间之前发布的，asc:指定时间之后发布的
     * @param int       $time           时间戳（10位），如：1418816972
     * @return array|\Requests_Response
     * @throws \Exception
     */
    public function getListImgJoke($page = 1, $pagesize = 1, $sort = 'desc', $time = 0)
    {
        if (!is_int($pagesize) || $pagesize < 1 || $pagesize > 20) throw new \Exception('pagesize 填写错误');
        if ($page < 1) throw new \Exception('page 填写错误');
        if (empty($sort)) throw new \Exception('sort 不能为空');
        $sort = strtolower($sort);
        if (false === array_search($sort, ['desc', 'asc'])) throw new \Exception('sort 填写错误');
        if (empty($time)) $time = time();
        if (!is_int($time) || $time < 1) throw new \Exception('time 填写错误');
        $result = $this->query('newImgJoke', [
            'page' => $page,
            'pagesize' => $pagesize,
            'sort' => $sort,
            'time' => $time
        ]);
        if (empty($result)) throw new \Exception('查询异常');
        return $result;
    }

    public function getNewTextJoke($page = 1, $pagesize = 1)
    {
        if (!is_int($pagesize) || $pagesize < 1 || $pagesize > 20) throw new \Exception('pagesize 填写错误');
        if ($page < 1) throw new \Exception('page 填写错误');
        $result = $this->query('newTextJoke', ['page' => $page, 'pagesize' => $pagesize]);
        if (empty($result)) throw new \Exception('查询异常');
        return $result;
    }

    /**
     * @param int       $page           当前页数，默认为1
     * @param int       $pagesize       每次返回条数，默认为，最大20
     * @param string    $sort           类型，desc:指定时间之前发布的，asc:指定时间之后发布的
     * @param int       $time           时间戳（10位），如：1418816972
     * @return array|\Requests_Response
     * @throws \Exception
     */
    public function getListTextJoke($page = 1, $pagesize = 1, $sort = 'desc', $time = 0)
    {
        if (!is_int($pagesize) || $pagesize < 1 || $pagesize > 20) throw new \Exception('pagesize 填写错误');
        if ($page < 1) throw new \Exception('page 填写错误');
        if (empty($sort)) throw new \Exception('sort 不能为空');
        $sort = strtolower($sort);
        if (false === array_search($sort, ['desc', 'asc'])) throw new \Exception('sort 填写错误');
        if (empty($time)) $time = time();
        if (!is_int($time) || $time < 1) throw new \Exception('time 填写错误');
        $result = $this->query('listTextJoke', [
            'page' => $page,
            'pagesize' => $pagesize,
            'sort' => $sort,
            'time' => $time
        ]);
        if (empty($result)) throw new \Exception('查询异常');
        return $result;
    }

    /**
     * @param string $type 查询类型
     * @param array $opt 参数配置
     * @return array|\Requests_Response
     */
    public function query($type = 'randJoke', $opt = [])
    {
        switch($type) {
            case 'randJoke':
                $jokeType = empty($opt['jokeType'])?'':$opt['jokeType'];
                $url = $this->urlArr['randJoke'] . "?key=$this->AppKey";
                if (!empty($jokeType)) {
                    $url .= "&type=$jokeType";
                }
                break;
            case 'newImgJoke':
                $page = empty($opt['page'])?1:$opt['page'];
                $pagesize = empty($opt['pagesize'])?1:$opt['pagesize'];
                $url = $this->urlArr['newImgJoke'] . "?key=$this->AppKey&page=$page&pagesize=$pagesize";
                break;
            case 'listImgJoke':
                $sort = empty($opt['sort'])?'ASC':$opt['sort'];
                $page = empty($opt['page'])?1:$opt['page'];
                $pagesize = empty($opt['pagesize'])?1:$opt['pagesize'];
                $time = empty($opt['time'])?time():$opt['time'];
                $url = $this->urlArr['listImgJoke'] . "?key=$this->AppKey&page=$page&pagesize=$pagesize&sort=$sort&time=$time";
                break;
            case 'newTextJoke':
                $page = empty($opt['page'])?1:$opt['page'];
                $pagesize = empty($opt['pagesize'])?1:$opt['pagesize'];
                $url = $this->urlArr['newTextJoke'] . "?key=$this->AppKey&page=$page&pagesize=$pagesize";
                break;
            case 'listTextJoke':
                $sort = empty($opt['sort'])?'ASC':$opt['sort'];
                $page = empty($opt['page'])?1:$opt['page'];
                $pagesize = empty($opt['pagesize'])?1:$opt['pagesize'];
                $time = empty($opt['time'])?time():$opt['time'];
                $url = $this->urlArr['listTextJoke'] . "?key=$this->AppKey&page=$page&pagesize=$pagesize&sort=$sort&time=$time";
                break;
            default:
                break;
        }
        if (!empty($url)) return \Requests::get($url);
        return [];
    }
}