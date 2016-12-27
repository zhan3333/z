<?php
/**
 * 聚合配置文件
 * User: zhan
 * Date: 2016/11/9
 * Time: 15:32
 */
$options = [
    // 短信配置
    'sms' => [
        'AppKey' => '',
        'template' => [
            'winningTips' => [
                'tpl_id' => '',
                'tpl_value' => [
                    '',
                    ''
                ]
            ]
        ]
    ],
    // 头条配置
    'newsHeadlines' => [
        'AppKey' => '',
    ],
    /**
     * 笑话大全配置
     * @doc https://www.juhe.cn/docs/api/id/95
     */
    'joke' => [
        'AppKey' => ''
    ]
];
return $options;