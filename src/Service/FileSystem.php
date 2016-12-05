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
        $rootPath = self::rootPath();
        $path = realpath($path);
        if (empty($path) || $path == realpath('')) {
            $path = $rootPath;
        }
        if (false === stripos($path, $rootPath)) {
            return Err::setLastErr(E_BLOCKED_PATH); // 禁止访问的目录
        }
        if (empty($path)) return Err::setLastErr(E_PATH_IS_ILLEGAL);    // 非法的路径
        try {
            $result = self::getFileList($path);
            // 按文件名排序
            $child = $result['child'];
            if (!empty($child)) {
                foreach ($child as $key => $value) {
                    $sizeArr[$key] = $value['basename'];
                }
                array_multisort($child, $sizeArr);
                $result['child'] = $child;
            }
            return [
                'result' => $result
            ];
        } catch (\Exception $e) {
            Factory::logger('error')->addError(__CLASS__, [__FUNCTION__, __LINE__, $e]);
            return Err::setLastErr(E_GET_FILE_LIST_FAIL);   // 获取文件列表失败
        }
    }

    private static function getFileList($path)
    {
        $fs = new \FilesystemIterator($path, \FilesystemIterator::KEY_AS_PATHNAME);
        $infoArr = [];
        foreach ($fs as $key => $item) {
            /**@var \SplFileInfo $item*/
            $info = [
                'dirname' => $item->getPath(),          // 所在文件夹
                'basename' => $item->getBasename(),     // 文件或文件夹名
                'type' => $item->getType(),             // 类型
                'perms' => substr(sprintf('%o', $item->getPerms()), -4),    // 权限
                'fileSize' => $item->getSize(),         // 大小
                'date' => $item->getATime()
            ];
            $infoArr[] = $info;
        }
        $pathInfo = pathinfo($path);
        $ret['info'] = [
            'dirname' => $pathInfo['dirname'],
            'basename' => $pathInfo['basename'],
            'rootPath' => self::rootPath()
        ];
        $ret['child'] = $infoArr;
        return $ret;
    }

    /**
     * 获取根目录路径
     * @return string
     */
    private static function rootPath()
    {
        return Factory::getConfig('fileSystem', 'rootPath');
    }

    /**
     * 判断path是否为合理的路径(根目录网上返回false)
     * @param $path
     * @return bool
     */
    private static function isRightPath($path)
    {
        if (empty($path)) return false;
        $path = realpath($path);
        if (empty($path)) return false;
        $rootPath = self::rootPath();
        if (false === stripos($path, $rootPath)) return false;
        return true;
    }

    /**
     * 获取项目根路径
     * @return array
     * <pre>
     * [
     *  'rootPath' => ''
     * ]
     * </pre>
     */
    public static function getRootPath()
    {
        $rootPath = self::rootPath();
        return [
            'rootPath' => $rootPath
        ];
    }

    /**
     * 创建一个文件夹
     * @default enable
     * @param $dirName
     * @param string $path
     * @return array|bool
     */
    public static function createDir($dirName, $path = '')
    {
        if (!self::isRightPath($path)) return Err::setLastErr(E_BLOCKED_PATH);      // 禁止访问的路径
        $fullPath = realpath($path);
        if (empty($fullPath)) return Err::setLastErr(E_PATH_IS_ILLEGAL);            // 非法路径
        if (!is_dir($fullPath)) return Err::setLastErr(E_FILE_NOT_EXIST);              // 文件夹不存在
        if (is_dir($fullPath . '/' . $dirName)) return Err::setLastErr(E_DIR_ALREADY_EXIST);    // 文件夹已存在
        $dirName = filter_var($dirName, FILTER_SANITIZE_STRING);
        if (empty($dirName)) return Err::setLastErr(E_FILE_DIR_NAME_ERROR);     // 文件夹名不合法
        $createRet = mkdir($fullPath . '/' . $dirName, 0777);
        return [
            'result' => $createRet
        ];
    }

    /**
     * 删除一个文件或文件夹
     * @default enable
     * @param $paths
     * @return bool
     */
    public static function remove($paths)
    {
        foreach ($paths as $path) {
            if (!self::isRightPath($path)) return Err::setLastErr(E_BLOCKED_PATH);      // 禁止访问的路径
        }
        foreach ($paths as $path) {
            $fullPath = realpath($path);
            if (empty($fullPath)) continue;
            self::delete($path);
        }
        return [];
    }

    /**
     * 执行删除文件或文件夹操作
     * @param $path
     * @return bool
     */
    private static function delete($path)
    {
        try {
            if (is_file($path)) {
                unlink($path);
            }
            if (is_dir($path)) {
                $scandir = scandir($path);
                if (count($scandir) > 2) {
                    foreach ($scandir as $item) {
                        if ($item !== '.' && $item !== '..') {
                            $childPath = $path . '/' . $item;
                            self::delete($childPath);
                        }
                    }
                }
                rmdir($path);
            }
            return true;
        } catch (\Exception $e) {
            Factory::logger('error')->addError(__CLASS__, [__FUNCTION__, __LINE__, $e]);
            return false;
        }
    }

    /**
     * 上传文件
     * @default enable
     * @param string $toPath
     * @return bool
     */
    public static function uploadFile($toPath = '')
    {
        if (!self::isRightPath($toPath)) return false;
        if (!empty($_FILES)) {
            foreach ($_FILES as $item) {
                move_uploaded_file($item['tmp_name'], $toPath . '/' . $item['name']);
            }
        }
        return true;
    }
}