<?php
/**
 * Created by PhpStorm.
 * User: Tenry
 * Date: 2019-05-17
 * Time: 13:15
 */
namespace App\Servers\Live;

use App\Lib\Util;

class SrsServer
{
    /**
     * 环境检测
     */
    private static function checkEnv(){
        if(!is_file(storage_path('/srs/objs/srs'))){echo "\n请先执行 php server.php install 安装\n";return false;}
        $ffmpegPath=storage_path('/srs/objs/ffmpeg/bin/ffmpeg');
        $ffprobePath=storage_path('/srs/objs/ffmpeg/bin/ffprobe');
        if(!is_file($ffmpegPath)){Util::error("未找到ffmpeg PATH:$ffmpegPath;");return false;}
        if(!is_file($ffprobePath)){Util::error("未找到ffprobe PATH:$ffmpegPath;");return false;}
    }

    /**
     * 加载srs配置文件
     */
    private static function loadSrsConf(){
        $srsConf=config('srs');
        if(empty($srsConf)){Util::error("未找能加载srs配置文件");return false;}
        $srsConf=json_encode($srsConf);
        $srsConf=substr($srsConf, 1);
        $srsConf=substr($srsConf, 0, -1);
        $srsConf=str_replace("\",\"","\n",$srsConf);//替换掉每个属性的逗号
        $srsConf=str_replace("\":\"",' ',$srsConf);
        $srsConf=str_replace("\"",'',$srsConf);
        $srsConf=str_replace("\\",'',$srsConf);//url等的http:\/
        $srsConf=str_replace(",",'',$srsConf);
        $srsConf=str_replace(":{"," { \n",$srsConf);//键值对的 冒号
        $srsConf=str_replace(";}"," ;} \n",$srsConf);//键值对的 冒号
        return $srsConf;
    }

    /**启动srs
     * @return bool
     */
    public static function start($process){
//        ps -ef | grep srs | grep -v "grep" | awk '{print $2}'
        if(exec('ps -A | grep srs')){return true;}
        $srsConfPath=storage_path('/srs/conf/my.conf');
        if (file_put_contents($srsConfPath,self::loadSrsConf())) {
            exec(storage_path('/srs/objs/srs').' -c '.$srsConfPath);
//            $process->exec(storage_path('/srs/objs/srs'), [' -c '.$srsConfPath]);
        }
    }

    /**安装程序
     * @return bool
     */
    public static function install()
    {
        self::checkEnv();
        $srsPath=storage_path('/srs');

        if(is_file(storage_path('/srs/objs/srs'))){Util::error("服务已安装 请先卸载 php server.php uninstall");return false;}
        is_dir($srsPath) OR mkdir($srsPath, 0777, true);
        Util::success('程序安装中,预计30分钟,请勿退出此界面');
        sleep(3);

        if (!is_file(storage_path('/setup.zip'))) {Util::error('没有检测到安装包 PATH:'.storage_path('/setup.zip'));return false;}
        if (!dr_unZip(storage_path('/setup.zip'),$srsPath)){Util::error('安装包解压失败');return false;}

        $parameter=' --full --prefix='.$srsPath;
        //安装
        echo "./configure {$parameter} && make --jobs=6\n\n";

        $cmd='cd '.$srsPath .' && ./configure '.$parameter.' && make --jobs=6';
        $descriptorspec=[
            0=>['pipe','r'],
            1=>["pipe", "w"],
            2=>["pipe", "w"]
        ];
        $process = proc_open($cmd, $descriptorspec, $pipes, '/bin/bash');
        if(is_resource($process)){
            while($ret=fgets($pipes[1])){echo ''.$ret;}
            while($ret=fgets($pipes[2])){echo ''.$ret;}
        }
        fclose($pipes[0]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        proc_close($process);
        Util::success('安装结束 请通过 php server.php start 启动服务');
    }

    /**
     * 卸载程序
     */
    public static function unistall()
    {
        Util::success("正在删除....");
        exec("rm -rf ".storage_path('/srs'));
        Util::success("删除成功");
    }



}