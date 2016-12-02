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
            $result = self::getFileList($path, true);
            foreach ($result['child'] as $key => $value) {
                $sizeArr[$key] = $value['info']['fileSize'];
            }
            array_multisort($sizeArr, SORT_DESC);
            return [
                'result' => $result
            ];
        } catch (\Exception $e) {
            Factory::logger('error')->addError(__CLASS__, [__FUNCTION__, __LINE__, $e]);
        }
        return [];
    }

    private static function getFileList($path, $deep = false)
    {
        if (!is_dir($path)) {
            $fs = new \SplFileInfo($path);
        } else {
            $fs = new \FilesystemIterator($path, \FilesystemIterator::KEY_AS_FILENAME);
        }
        $type = $fs->getType();
        if ($type == 'dir') {
            $infoArr= [];
            if ($deep) {
                foreach ($fs as $key => $item) {
                    /**@var $item \SplFileInfo*/
                    $infoArr[] = self::getFileList($item->getPathname());
                }
            }
            return [
                'info' => [
                    'dirname' => $fs->getPath(),
                    'basename' => substr($fs->getPath(), strrpos($fs->getPath(), '/') + 1),
                    'type' => $type,
                    'perms' => substr(sprintf('%o', $fs->getPerms()), -4),
                    'fileSize' => -1,
                    'date' => $fs->getATime()
                ],
                'child' => $infoArr
            ];
        } elseif ($type == 'link') {
            return [
                'info' => [
                    'type' => $type,
                    'dirname' => '..'
                ],
                'child' => []
            ];
        } elseif ($type == 'file') {
            Factory::logger('zhan')->addInfo(__CLASS__. '_' . __FUNCTION__, [__LINE__,
                'dirname' => $fs->getPath(),
                $type
            ]);

            return [
                'info' => [
                    'dirname' => $fs->getPath(),
                    'basename' => substr($fs->getPath(), strrpos($fs->getPath(), '/') + 1),
                    'type' => $type,
                    'perms' => substr(sprintf('%o', $fs->getPerms()), -4),
                    'fileSize' => $fs->getSize(),
                    'date' => $fs->getATime()
                ],
                'child' => []
            ];
        }
        return [];
    }
}