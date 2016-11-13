<?php

/**
 * Created by PhpStorm.
 * User: zhan
 * Date: 28/10/2016
 * Time: 10:24
 *
 * @Table(name = "Student")
 * @Entity(repositoryClass = "StudentRepository")
 *
 */
class Student
{
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue
     * @var
     */
    protected $id;
}