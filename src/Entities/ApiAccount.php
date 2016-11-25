<?php
/**
 * Created by PhpStorm.
 * User: zhan
 * Date: 2016/11/25
 * Time: 11:55
 */

namespace App\Entities;

/**
 * 存储api记录接入商账号与key信息
 * Class ApiAccount
 * @package App\Entities
 * @Table(name="ApiAccount")
 * @Entity(repositoryClass = "ApiAccountRepository")
 */
class ApiAccount
{
    /**
     * @Id
     * @GeneratedValue
     * @Column(type="integer")
     * @var
     */
    protected $id;

    /**
     * @Column(type="string", unique=true, options = {"comment":"用户使用的key"})
     * @var
     */
    protected $apiKey;

    /**
     * @Column(type="integer", options = {"comment": "注册api记录账号的用户id"})
     * @var
     */
    protected $userId;

    /**
     * @Column(type="string", options = {"comment": "使用接口名称"})
     * @var
     */
    protected $apiId;

    /**
     * @Column(type="datetimetz", options = {"comment": "注册时间"})
     * @var
     */
    protected $postTime;

    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    public function setApiId($apiId)
    {
        $this->apiId = $apiId;
    }

    public function setPostTime($postTime)
    {
        $this->postTime = $postTime;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function getApiId()
    {
        return $this->apiId;
    }

    public function getApiKey()
    {
        return $this->apiKey;
    }
}