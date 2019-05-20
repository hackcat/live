<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-09-10
 * Time: 19:57
 */
namespace App\Servers;


abstract class ServerAbstract
{

     private static $listen='0.0.0.0';
    private static $port;

    /**启动服务
     * @param \swoole_process $process
     * @return mixed
     */
    abstract public static function start(\swoole_process $process);
}