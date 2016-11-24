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
    /**
     * @Id
     * @GeneratedValue
     * @Column(type="integer")
     * @var
     */
    protected $id;

    /**
     * @Column(type="datetimetz")
     * @var
     */
    protected $postTime;
}