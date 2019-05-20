<?php
/**
 * http Api模块
 * User: Tenry
 * Date: 2019-05-20
 * Time: 11:14
 */

namespace App\Servers\Live;


use App\Handler\Live\ApiHandler;
use App\Lib\Util;
use App\Servers\ServerAbstract;

class ApiServer extends ServerAbstract
{
    private static $listen='0.0.0.0';
    private static $port='1224';

    /**
     * @param \swoole_process $process
     * @return mixed|void
     */
    public static function start(\swoole_process $process)
    {
        $http = new \swoole_http_server(self::$listen,self::$port);
        $http->set(['worker_num' => 1, 'reactor_num' =>0, 'daemonize' => 0]);
        $handler=new ApiHandler();
        $http->on('request', function ($request,$response)use($handler){
            Util::info("收到来自IP:{$request->server['remote_addr']}的{$request->server['request_method']}请求,DATA:{$request->rawContent()}");
            if ($request->server['request_method']!='POST') { return self::responseHttp($response,SRS_ERROR,[],'失败');}
            $data=json_decode($request->rawContent(),true);
            if(!empty($data)){ Util::info($data);}
            $action=substr(strrchr($request->server['request_uri'],'/'),1);
            if(!method_exists($handler,$action)){
                return self::responseHttp($response,SRS_ERROR,[],'未定义'.$action.'方法！');
            }
            return self::responseHttp($response, $handler->$action($data));
        });
        $http->start();
    }

    /**响应json数据
     * @param $response
     * @param int $code
     * @param array $data
     * @param string $msg
     */
    public static function responseHttp($response,$code=SRS_SUCCESS,$data=[],$msg='成功')
    {
        $data=json_encode(['code'=>$code, 'data'=>$data, 'msg'=>$msg]);
        $response->header('Content-type','text/html');
        $response->header('Connection','keep-alive');
        $response->header('Content-length',strlen($data));
        $response->write($data);
        $response->end();
    }
}