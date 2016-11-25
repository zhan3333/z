<?php
/**
 * Created by PhpStorm.
 * User: zhan
 * Date: 2016/11/24
 * Time: 17:26
 */

namespace App\Entities;

/**
 * 用户主表
 * Class User
 * @package App\Entities
 *
 * @Entity(repositoryClass="UserRepository")
 * @Table(name="User")
 */
class User
{
    const USER_TYPE_NORMAL = 1;         // 普通用户
    const USER_TYPE_ADMIN = 2;          // 管理员
    const USER_TYPE_SUPER_ADMIN = 3;    // 超级管理员

    /**
     * @Id
     * @GeneratedValue
     * @Column(type="integer")
     * @var
     */
    protected $id;

    /**
     * @Column(type = "integer", nullable = true, options={"comment": "用户类型"})
     * @var
     */
    protected $userType;

    /**
     * @Column(type="datetimetz")
     * @var
     */
    protected $postTime;

    public function setPostTime($postTime)
    {
        $this->postTime = $postTime;
    }

    public function setUserType($userType)
    {
        $this->userType = $userType;
    }

    public function getUserType()
    {
        return $this->userType;
    }

    public function getPostTime()
    {
        return $this->postTime;
    }

    public function getId()
    {
        if (!empty($this->id)) {
            return $this->id;
        } else {
            return false;
        }
    }
}