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
const E_USER_NO_LOGIN = 2006;       // 用户未登陆
const E_USER_ACCOUNT_ERROR = 2007;  // 账号填写错误
const E_USER_PASSWD_ERROR = 2008;       // 密码填写错误
const E_USER_NOT_IS_ADMIN = 2009;       // 非管理员无法执行此操作

// Api错误  3000~4000
const E_API_KEY_NOT_ALLOW_AIR = 3000;       // key不允许为空
const E_API_API_ID_NOT_ALLOW_AIR = 3001;    // apiId不允许为空
const E_API_USER_ID_NOT_ALLOW_AIR = 3002;   // userId不允许为空
const E_API_ID_NOT_EXIST = 3003;            // apiId不存在
const E_API_NAME_NOT_ALLOW_AIR = 3004;      // 接口名称不允许为空
const E_API_NAME_ALREADY_EXISTS = 3005;     // 接口名称已经存在
const E_API_CREATE_API_ACCOUNT_FAIL = 3006; // 创建apiAccount失败
const E_API_CREATE_API_INFO_FAIL = 3007;    // 创建apiInfo失败
const E_API_NAME_IS_ERROR = 3008;           // 接口名称错误
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
    E_USER_NO_LOGIN => '用户未登陆',
    E_USER_ACCOUNT_ERROR => '账号填写错误',
    E_USER_PASSWD_ERROR  => '密码填写错误',
    E_USER_NOT_IS_ADMIN => '非管理员无法执行此操作',

    // Api错误  3000~4000
    E_API_KEY_NOT_ALLOW_AIR => 'api key 不允许为空',
    E_API_API_ID_NOT_ALLOW_AIR => 'apiId不允许为空',
    E_API_USER_ID_NOT_ALLOW_AIR => 'userId不允许为空',
    E_API_ID_NOT_EXIST => 'apiId对应数据不存在',
    E_API_NAME_NOT_ALLOW_AIR => '接口名称不允许为空',
    E_API_NAME_ALREADY_EXISTS => '接口名称已存在',
    E_API_CREATE_API_ACCOUNT_FAIL => '创建apiAccount失败',
    E_API_CREATE_API_INFO_FAIL => '创建apiInfo失败',
    E_API_NAME_IS_ERROR => '接口名称错误',
];
return $err;