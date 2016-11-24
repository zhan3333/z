<?php
/**
 * Created by PhpStorm.
 * User: zhan
 * Date: 2016/11/24
 * Time: 17:57
 */

namespace App\Service;


class UserBase
{
    /**
     * 获取当前用户userId
     */
    public static function getClientUserId()
    {
        if (empty($GLOBALS['_uid'])) {

        }
        $uid = $GLOBALS['_uid'];
        return $uid;
    }
}