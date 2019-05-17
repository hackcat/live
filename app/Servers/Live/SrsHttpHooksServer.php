<?php
/**
 * Created by PhpStorm.
 * User: Tenry
 * Date: 2019-05-17
 * Time: 15:18
 */

namespace App\Servers\Live;


use App\Handler\SrsHttpHookHandler;
use App\Lib\Util;
use Swoole\Http\Response;
use Swoole\Process;

class SrsHttpHooksServer
{
    public static function start($process)
    {
        $http = new \swoole_http_server('127.0.0.1','8085');
        $http->set(['worker_num' => 4, 'reactor_num' =>1, 'daemonize' => 0]);
        $handler=new SrsHttpHookHandler();
        $http->on('request', function ($request,$response)use($handler){
            if ($request->server['request_method']!='POST') { self::responseHttp($response,SRS_ERROR);return false;}
            $data=json_decode($request->rawContent(),true);
            Util::info($data);
            if(empty($data)){self::responseHttp($response,SRS_ERROR); return false;}
            $action=substr($request->server['request_uri'],1);
            $this->responseHttp($response, $handler->$action($data));
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