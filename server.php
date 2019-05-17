<?php
/**
 * Created by PhpStorm.
 * User: Tenry
 * Date: 2019-05-17
 * Time: 12:03
 */
include_once __DIR__.'/vendor/autoload.php';
include_once __DIR__.'/config/defind.php';

class server
{

    public function __construct()
    {
        $this->checkEnv();
    }

    /**
     * 环境检测
     */
    private function checkEnv(){
        if(strpos(strtolower(PHP_OS), 'win') === 0) {$this->error('不支持windows系统');return false;}
        if(!extension_loaded('pcntl')) {$this->error('请安装 pcntl 扩展');return false;}
        if(!extension_loaded('posix')) {$this->error('请安装 posix 扩展');return false;}
        if(!extension_loaded('swoole')) {$this->error('请安装 swoole 扩展');return false;}
        if(!function_exists('exec')) {$this->error('请开启 exec 函数.');return false;}
        if(!function_exists('proc_open')) {$this->error('请开启 proc_open 函数.');return false;}
    }

    public function handle($param)
    {
        switch ($param){
            case 'start':
                //开主进程
                if (\App\Lib\Pid::isPid('','master')) {\App\Lib\Util::error('抱歉!服务正在运行');return false;}
                $master_pid=getmypid();
                \App\Lib\Pid::set('','master',$master_pid);

                $str=<<<eof
|     master     | $master_pid  |
eof;

                //遍历所有服务 创建进程树
                foreach (glob(app_path('/Servers/*/*Server.php')) as $className){
                    $groupName=trim(strrchr(dirname($className), '/'),'/');
                    $className=rtrim(basename($className),'.php');
                    $server = 'App\Servers\\'.ucfirst($groupName).'\\'.$className;
                    (new \swoole_process(function (\swoole_process $process)use($server,$groupName,$className){
                        \App\Lib\Pid::set($groupName,$className,$process->pid);
                        $str=<<<eof
| $groupName-$className  |  {$process->pid}
eof;
                        \App\Lib\Util::success($str);
                        $server::start($process);
                    }))->start();
                }
                \App\Lib\Util::success($str);
                $this->signalListen();
                break;
            case 'stop':
                \App\Lib\Pid::stopMaster();
                break;
            case 'install':
                \App\Servers\Live\SrsServer::install();
                break;
            case 'uninstall':
                \App\Servers\Live\SrsServer::unistall();
                break;
        }
    }

    /**
     * 信号监听 子进程死掉就拉起
     */
    private function signalListen(){
        \swoole_process::signal(SIGCHLD, function(){
            $status = \swoole_process::wait(false);
            var_dump($status);
        });
    }
}

if(!isset($argv[1])||!in_array($argv[1],['install','uninstall','start','stop'])){
    $str=<<<eof
install    首次运行请执行安装程序
uninstall  卸载程序
start      启动服务,后台运行 php server.php start &
stop       停止服务
eof;

    \App\Lib\Util::error($str);
}
$server=new server();
$server->handle($argv[1]);