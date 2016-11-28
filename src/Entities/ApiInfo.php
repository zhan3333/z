<?php
/**
 * Created by PhpStorm.
 * User: zhan
 * Date: 2016/11/25
 * Time: 12:45
 */

namespace App\Entities;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Entity;

/**
 * 记录提供的api接口信息
 * Class ApiInfo
 * @package App\Entities
 * @Table(name = "ApiInfo")
 * @Entity(repositoryClass = "ApiInfoRepository")
 */
class ApiInfo extends BaseEntity
{
    /**
     * @Id
     * @GeneratedValue
     * @Column(type="integer")
     * @var
     */
    protected $id;

    /**
     * @Column(type = "string", unique = true, options = {"comment": "api名称"})
     * @var
     */
    protected $apiName;

    /**
     * @Column(type = "datetimetz")
     * @var
     */
    protected $postTime;

    public function setPostTime($postTime)
    {
        $this->postTime = $postTime;
    }

    public function setApiName($apiName)
    {
        $this->apiName = $apiName;
    }

    public function getApiName()
    {
        return $this->apiName;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getPostTime()
    {
        return $this->postTime;
    }
}