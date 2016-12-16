<?php
/**
 * Created by PhpStorm.
 * User: zhan
 * Date: 2016/12/9
 * Time: 11:03
 */

namespace App\Entities;

/**
 * Class Test
 * @package App\Entities
 *
 * @Table(name = "Test")
 * @Entity(repositoryClass = "TestRepository")
 */
class Test extends BaseEntity
{
    /**
     * @Id
     * @GeneratedValue
     * @Column(type = "integer")
     * @var
     */
    protected $id;

    /**
     * @Column(type = "json_array")
     * @var
     */
    protected $text;

    public function setText($text)
    {
        $this->text = $text;
    }

    public function getText()
    {
        return $this->text;
    }
}