<?php
/**
 * Created by PhpStorm.
 * User: 39096
 * Date: 2016/10/30
 * Time: 16:41
 */

namespace App\Service;
use App\Err;
use App\Factory;

/**
 * 提供文件操作接口
 * 以项目根目录下的test文件夹作为文件操作的根目录
 * Class File
 * @package App\Service
 */
class File
{
    /**
     * @default enable
     * @param $path
     * @return bool
     */
    public static function getFileInfo($path = '')
    {
        // todo 文件访问权限问题未解决
        if ($path == '..') {
            return Err::setLastErr(E_NO_ACCESS);    // 禁止访问
        }
        if (empty($path)) $path = '/';
        $fullPath = self::resolvePath($path);
        if (empty($fullPath)) return Err::setLastErr(E_PATH_IS_ILLEGAL);    // 不合法的路径
        $fileInfoRet = [];
        if (is_dir($fullPath)) {
            // 为文件夹时，返回文件夹内信息
            $it = new \FilesystemIterator($fullPath, \FilesystemIterator::CURRENT_AS_FILEINFO);
            foreach ($it as $key => $item) {
                /**@var $item \SplFileInfo*/
                $fileInfo = [];
                $fileInfo['fileName'] = $item->getFilename();   // 文件或文件夹名
                $fileInfo['date'] = $item->getCTime();          // 文件创建时间
                $fileInfo['type'] = $item->getType();       // 文件类型
                $fileInfo['perms'] = substr(sprintf('%o', $item->getPerms()), -4);     //  文件权限
                $fileInfo['fileSize'] = $item->getSize();   // 文件大小
                $fileInfo['path'] = $path;
                Factory::logger('zhan')->addInfo(__FUNCTION__, [__LINE__, 'realPath' => $item->getRealPath(), 'path' => $item->getPath()]);
                array_push($fileInfoRet, $fileInfo);
            }
        } elseif (is_file($fullPath)) {
            return Err::setLastErr(E_NOT_IS_DIR);   // 非文件夹
        } else {
            return Err::setLastErr(E_FILE_OR_DIR_NOT_EXIST);    // 文件或文件夹不存在
        }
        return [
            'result' => $fileInfoRet
        ];
    }

    /**
     * 创建一个文件夹
     * @default enable
     * @param $dirName  string  文件名称
     * @param $path     string  要创建的文件夹的父文件夹路径，若父文件夹不存在，则返回一个错误
     * @return array
     */
    public static function createDir($dirName, $path = '')
    {
        $fullPath = self::resolvePath($path);
        if (empty($fullPath)) return Err::setLastErr(E_PATH_IS_ILLEGAL);    // 非法路径

        if (!is_dir($fullPath)) {
            return Err::setLastErr(E_DIR_NOT_EXIST);    // 父文件夹不存在
        }

        $createRet = mkdir($fullPath . '/' . $dirName, 0777);
        return [
            'result' => $createRet
        ];
    }

    /**
     * 删除一个空文件夹
     * @default enable
     * @param $path
     * @return bool
     */
    public static function removeDir($path)
    {
        $fullPath = self::resolvePath($path);
        if (!is_dir($fullPath)) return Err::setLastErr(E_NOT_IS_DIR);   // 不是文件夹
        if (count(scandir($fullPath)) > 2) return Err::setLastErr(E_DIR_NOT_AIR);  // 文件夹不为空，无法删除
        $removeRet = rmdir($fullPath);
        return [
            'result' => $removeRet
        ];
    }

    /**
     * 删除一个文件
     * @default enable
     * @param $path
     * @return bool
     */
    public static function removeFile($path)
    {
        $fullPath = self::resolvePath($path);
        if (!is_file($fullPath)) return Err::setLastErr(E_NOT_IS_FILE); // 非文件
        $removeRet = unlink($fullPath);
        return [
            'result' => $removeRet
        ];
    }

    /**
     * 上传文件到当前目录(post方式上传)
     * @default enable
     * @param string $path  文件保存路径
     * @return bool
     */
    public static function uploadFile($path = '')
    {
        if (empty($_FILES)) return Err::setLastErr(E_NO_FILE_UPLOAD);   // 无文件上传
        Factory::logger('zhan')->addInfo(__FUNCTION__, [__LINE__, $_FILES]);
        $fullPath = self::resolvePath($path);
        if (!is_dir($fullPath)) return Err::setLastErr(E_NOT_IS_DIR);   // 非文件夹
        $uploadRet = [];
        foreach ($_FILES as $key => $item) {
            $uploadRet[$key]['fileName'] = $item['name'];
            $uploadRet[$key]['ok'] = move_uploaded_file($item['tmp_name'], $fullPath . '/' . $item['name']); // 移动文件
        }
        return [
            'result' => $uploadRet
        ];
    }

    /**
     * 获取下载链接
     * @default enable
     */
    public static function getResUrl()
    {
        $url = self::getDownBasePath();
        return [
            'url' => $url
        ];
    }

    /**
     * 获取下载路径
     * @return string   'http://res.zhannnnn.top/...'   下载文件路径
     */
    private static function getDownBasePath()
    {
        return Factory::getConfig('fileSystem', 'downBaseUrl') . Factory::getConfig('fileSystem', 'uploadDir');
    }

    /**
     * 获取根目录的绝对路径
     */
    private static function getBasePath()
    {
        $basePath =  WEBPATH . '/' . Factory::getConfig('fileSystem', 'uploadDir');
        if (!is_dir($basePath)) {
            mkdir($basePath, 0777);
        }
        return $basePath;
    }

    /**
     * 对输入的路径进行过滤处理
     * @param $path
     * @return bool|string 当path为空时，返回根目录，当path非法时，返回false,当path合法时，返回文件完整路径
     */
    private static function resolvePath($path)
    {
        $basePath = self::getBasePath();
        if (empty($path)) {
            return $basePath;
        }
        $path = filter_var($path, FILTER_SANITIZE_STRING);
        if (empty($path)) {
            return false;
        }
        if ($path[0] == '/') {
            return  $basePath . $path;
        } else {
            return $basePath . '/' . $path;
        }
    }
    
}