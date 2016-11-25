<?php
/**
 * Created by PhpStorm.
 * User: zhan
 * Date: 2016/11/24
 * Time: 17:29
 */

namespace App\Entities;

/**
 * 普通用户信息存储表
 * Class NormalAccount
 * @package App\Entities
 * @Table(name="NormalAccount")
 * @Entity(repositoryClass = "NormalAccountRepository")
 */
class NormalAccount
{
    /**
     * @Id
     * @GeneratedValue
     * @Column(type="integer")
     * @var
     */
    protected $id;

    /**
     * @Column(type="integer", unique = true, options = {"comment": "用户id"})
     * @var
     */
    protected $userId;

    /**
     * @Column(type="string", unique = true, options = {"comment": "登陆名"})
     * @var
     */
    protected $login;

    /**
     * @Column(type="string", options = {"comment": "用户密码"})
     * @var
     */
    protected $passwd;

    /**
     * @Column(type="datetimetz")
     * @var
     */
    protected $postTime;

    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    public function setLogin($login)
    {
        $this->login = $login;
    }

    public function setPasswd($passwd)
    {
        $this->passwd = $passwd;
    }

    public function setPostTime($postTime)
    {
        $this->postTime = $postTime;
    }

    public function getId()
    {
        if (empty($this->id)) {
            return false;
        }
        return $this->id;
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function getLogin()
    {
        return $this->login;
    }
}