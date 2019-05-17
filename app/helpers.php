<?php
/**
 * Created by PhpStorm.
 * User: Tenry
 * Date: 2019-05-17
 * Time: 12:50
 */

/**项目基础路径
 * @param string $path
 * @return string
 */
function base_path($path=''){
    return realpath(dirname(__FILE__).'/../').$path;
}

/**app路径
 * @param string $path
 * @return string
 */
function app_path($path=''){
    return base_path('/app').$path;
}

/**
 * @param string $path
 * @return string
 */
function storage_path($path=''){
    return base_path('/storage').$path;
}


/**获取配置文件
 * @param $key
 * @return mixed
 */
function config($key){
    $path=base_path('/config').'/'.$key.'.php';
    if (is_file($path)) {
        return include_once $path;
    }else{
        return null;
    }
}


/**
 * 解压zip文件到指定目录
 * $filepath： 文件路径
 * $extractTo: 解压路径
 */
function dr_unZip($filepath,$extractTo) {
    $zip = new ZipArchive;
    $res = $zip->open($filepath);
    if ($res === TRUE) {
        //解压缩到$extractTo指定的文件夹
        $zip->extractTo($extractTo);
        $zip->close();
        return true;
    } else {
        echo 'failed, code:' . $res;
        return false;
    }
}
