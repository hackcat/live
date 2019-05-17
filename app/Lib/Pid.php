<?php
/**
 * Created by PhpStorm.
 * User: Tenry
 * Date: 2019-05-17
 * Time: 13:44
 */
namespace App\Lib;

class Pid
{
    /**设置pid
     * @param string $group_name
     * @param $server_name
     * @param $pid
     */
    public static function set($group_name='',$server_name,$pid)
    {
        $pidDir=storage_path('/tem/pids'.($group_name?'/'.$group_name.'/':'/'));
        $path=$pidDir.$server_name;
        is_dir($pidDir) OR mkdir($pidDir, 0777, true);
        file_put_contents($path.'.pid',$pid);
    }

    /**读取pid
     * @param $group_name
     * @param $server_name
     * @return bool|false|string
     */
    public static function get($group_name='',$server_name)
    {
        $path=storage_path('/tem/pids'.($group_name?'/'.$group_name.'/':'/').$server_name.'.pid');

        if (is_file($path)) {
            return (int)file_get_contents($path);
        }else{
            return '';
        }
    }

    /**检查进程是否正在运行
     * @param $group_name
     * @param $server_name
     * @return bool|false|string pid或false
     */
    public static function isPid($group_name='',$server_name)
    {
        $pid=self::get($group_name,$server_name);
        if (\swoole_process::kill($pid, 0)) {return $pid;}
        return false;
    }

    /**
     * 结束主进程
     */
    public static function stopMaster()
    {
        $masterPid=self::isPid('','master');
        if(empty($masterPid)){Util::error("进程结束失败,没有找到该进程");return false;}
        //根据主进程号 递归结束所有子进程
        //pstree -p pid
        exec('pstree -p '.$masterPid,$pids);
        $pid_arr=[];
        foreach ($pids as  $item){
            preg_match_all('/\d+/',$item,$arr);
            $arr=array_shift($arr);
            $pid_arr=array_merge($pid_arr,$arr);
        }
        sort($pid_arr);
        //结束所有进程
        foreach ($pid_arr as $pid){
            self::stopProcess($pid);
        }
    }

    /**结束进程
     * @param $pid
     * @return bool
     */
    public static function stopProcess($pid)
    {
        if (\swoole_process::kill($pid, 0)) {
            if (!\swoole_process::kill($pid, SIGTERM)) {
                Util::error(" PID [{$pid}] 停止失败");
                return false;
            }
        }
        return true;
    }
}