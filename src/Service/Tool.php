<?php
/**
 * Created by PhpStorm.
 * User: 39096
 * Date: 2016/10/16
 * Time: 0:05
 */

namespace App\Service;


use App\Factory;
use App\Util;

class Tool
{
    /**
     * @default enable
     * @return array
     */
    public static function ip()
    {
        return Util::getClientIp();
    }
}