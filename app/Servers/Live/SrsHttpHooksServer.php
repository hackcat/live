<?php
/**
 * Created by PhpStorm.
 * User: Tenry
 * Date: 2019-05-17
 * Time: 15:18
 */

namespace App\Servers\Live;


use App\Handler\Live\SrsHttpHookHandler;
use App\Lib\Util;
use Swoole\Http\Response;
use App\Servers\ServerAbstract;

class SrsHttpHooksServer extends ServerAbstract
{
    private static $listen='0.0.0.0';
    private static $port='8085';

    /**启动服务
     * @param \swoole_process $process
     * @return mixed|void
     */
    public static function start(\swoole_process $process)
    {
        $http = new \swoole_http_server(self::$listen,self::$port);
        $http->set(['worker_num' => 4, 'reactor_num' =>1, 'daemonize' => 0]);
        $handler=new SrsHttpHookHandler();
        $http->on('request', function ($request,$response)use($handler){
            if ($request->server['request_method']!='POST') { return self::responseHttp($response,SRS_ERROR);}
            $data=json_decode($request->rawContent(),true);
            Util::info($data);
            if(empty($data)){return self::responseHttp($response,SRS_ERROR);}
            $action=substr($request->server['request_uri'],1);
            return self::responseHttp($response, $handler->$action($data));
        });
        $http->start();
    }

    /**返回http响应
     * @param $response
     * @param int $code 0成功 1失败
     */
    private static function responseHttp(Response $response,$code=SRS_SUCCESS){
//        $msg = "HTTP/1.1 200\n"
//            ."Content-type:text/html;charset=utf-8\n"
//            ."Content-length:".strlen($code)."\n"
//            ."Connection:keep-alive\r\n\r\n".$code;
        $code=$code?$code:0;
        $response->header('Content-type','text/html');
        $response->header('Content-length',strlen($code));
        $response->header('Connection','keep-alive');
        $response->write($code);
        $response->end();
    }
}