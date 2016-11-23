<?php
/**
 * Created by PhpStorm.
 * User: zhan
 * Date: 2016/11/23
 * Time: 15:44
 */

namespace App\Documents;

/**
 * Class User
 * @package App\Documents
 *
 * @Document
 *
 */
class User
{
    /**
     * @Id
     * @var
     */
    private $id;

    /**
     * @String
     */
    private $name;

    /**
     * @String
     */
    private $email;

    /**
     * @ReferenceMany(targetDocument="BlogPost", cascade="all")
     */
    private $posts = array();

    public function setName($value)
    {
        $this->name = $value;
    }

    public function setEmail($value)
    {
        $this->email = $value;
    }

    public function setPosts($value)
    {
        $this->posts = $value;
    }
}