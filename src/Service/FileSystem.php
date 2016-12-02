<?php
/**
 * Created by PhpStorm.
 * User: zhan
 * Date: 2016/12/2
 * Time: 17:18
 */

namespace App\Service;


use App\Factory;

/**
 * 用户文件管理
 * Class FileSystem
 * @package App\Service
 */
class FileSystem
{
    /**
     * 获取文件管理列表
     * @default enable
     * @param string $path
     * @return array
     */
    public static function getFileSystemList($path = '')
    {
        if (empty($path)) {
            $path = APPPATH . '/test';
        }
        try {
            $result = self::getFileList($path);
            return [
                'result' => $result
            ];
        } catch (\Exception $e) {
            Factory::logger('error')->addError(__CLASS__, [__FUNCTION__, __LINE__, $e]);
        }
        return [];
    }

    private static function getFileList($path)
    {
        if (!is_dir($path) && !is_file($path)) return [];
        if (is_file($path)) {
            $info = pathinfo($path);
            Factory::logger('zhan')->addInfo(__CLASS__. '_' . __FUNCTION__, [__LINE__,
                $info
            ]);

            return [
                'info' => [
                    'dirname' => $info['dirname'],
                    'basename' => $info['basename'],
                    'type' => 'file',
                    'perms' => '',
                    'fileSize' => '',
                    'date' => ''
                ],
                'child' => []
            ];
        }
        $fs = new \FilesystemIterator($path, \FilesystemIterator::KEY_AS_FILENAME);
        if ($fs->isDir()) {
            $infoArr= [];
            foreach ($fs as $key => $item) {
                /**@var $item \SplFileInfo*/
                if ($item->isDir()) {
//                    文件夹
                    $infoArr[] = self::getFileList($item->getPathname());
                } else {
//                    文件
                    $infoArr[] = [
                        'info' => [
                            'dirname' => $item->getPath(),
                            'basename' => $item->getBasename(),
                            'type' => 'file',
                            'perms' => substr(sprintf('%o', $item->getPerms()), -4),
                            'fileSize' => $item->getSize(),
                            'date' => $item->getATime()
                        ],
                        'child' => []
                    ];
                }
            }

            return [
                'info' => [
                    'dirname' => $fs->getPath(),
                    'basename' => substr($fs->getPath(), strrpos($fs->getPath(), '/') + 1),
                    'type' => 'dir',
                    'perms' => substr(sprintf('%o', $fs->getPerms()), -4),
                    'fileSize' => '-',
                    'date' => $fs->getATime()
                ],
                'child' => $infoArr
            ];
        }
        return [];
    }
}