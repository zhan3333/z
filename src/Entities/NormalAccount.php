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
 * @Entity(repositoryClass = "NormalAccount")
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
     * @Column(type="datetimetz")
     * @var
     */
    protected $postTime;
}