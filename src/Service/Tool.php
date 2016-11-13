<?php
/**
 * Created by PhpStorm.
 * User: 39096
 * Date: 2016/10/16
 * Time: 0:05
 */

namespace App\Service;


use App\Factory;

class Tool
{
    /**
     * @default enable
     * @return array
     */
    public static function ip()
    {
        return $_SERVER['HTTP_X_REAL_IP'];
    }
}