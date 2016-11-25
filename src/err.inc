<?php
const E_FILE_OR_DIR_NOT_EXIST = 1000;
const E_FILE_NOT_EXIST = 1001;
const E_DIR_NOT_EXIST = 1002;
const E_PATH_IS_ILLEGAL = 1003;
const E_NOT_IS_DIR = 1004;
const E_NO_ACCESS = 1005;
const E_NO_FILE_UPLOAD = 1006;
const E_DIR_NOT_AIR = 1007;
const E_NOT_IS_FILE = 1008;

// User相关错误 2000~3000
const E_USER_CREATE_FAIL = 2000;
const E_USER_ID_NOT_ALLOW_AIR = 2001;
const E_USER_ACCOUNT_NOT_ALLOW_AIR = 2002;
const E_USER_PASSWD_NOT_ALLOW_AIR = 2003;
const E_USER_CREATE_NORMAL_ACCOUNT_FAIL = 2004;
const E_USER_NOT_EXIST = 2005;

$err = [
    0 => '执行成功',

    // 文件相关错误
    E_FILE_OR_DIR_NOT_EXIST => '文件或文件夹不存在',
    E_FILE_NOT_EXIST => '文件不存在',
    E_DIR_NOT_EXIST => '文件夹不存在',
    E_PATH_IS_ILLEGAL => '非法路径',
    E_NOT_IS_DIR => '非文件夹',
    E_NO_ACCESS => '禁止访问',
    E_NO_FILE_UPLOAD => '无文件上传',
    E_DIR_NOT_AIR => '文件夹不为空，无法删除',
    E_NOT_IS_FILE => '非文件',

    // User相关错误 2000~3000
    E_USER_CREATE_FAIL => '创建用户失败',
    E_USER_ID_NOT_ALLOW_AIR => '用户id不允许为空',
    E_USER_ACCOUNT_NOT_ALLOW_AIR => '用户账号不允许为空',
    E_USER_PASSWD_NOT_ALLOW_AIR => '用户密码不允许为空',
    E_USER_CREATE_NORMAL_ACCOUNT_FAIL => '创建用户普通账号失败',
    E_USER_NOT_EXIST => '用户不存在',
];
return $err;