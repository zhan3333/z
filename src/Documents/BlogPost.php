<?php
/**
 * Created by PhpStorm.
 * User: zhan
 * Date: 2016/11/23
 * Time: 15:45
 */

namespace App\Documents;

/**
 * Class BlogPost
 * @package App\Documents
 *
 * @Document
 */
class BlogPost
{
    /**
     * @Id
     * @var
     */
    private $id;

    /**
     * @String
     * @var
     */
    private $title;

    /**
     * @String
     * @var
     */
    private $body;

    /**
     * @Date
     * @var
     */
    private $createdAt;

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function setBody($body)
    {
        $this->body = $body;
    }

    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }
}