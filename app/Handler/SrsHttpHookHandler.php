<?php
/**
 * Created by PhpStorm.
 * User: Tenry
 * Date: 2019-05-17
 * Time: 15:26
 */

namespace App\Handler;


class SrsHttpHookHandler
{
    /**
     * 心跳
     */
    public function heartbeat()
    {
        return SRS_SUCCESS;
    }

    /**连接
     * @param
     */
    public function onConnect()
    {
        return SRS_SUCCESS;
    }

    /**开始播放
     * @param  $data
     */
    public function onPlay(&$data)
    {

    }

    /**发布流
     * @param  $data  {"action": "on_publish","client_id": 1985,"ip": "192.168.1.10", "vhost": "video.test.com", "app": "live","stream": "livestream" }
     */
    public function onPublish(&$data)
    {

    }

    /**停止发布流
     * @param $data {"action": "on_unpublish","client_id": 1985,"ip": "192.168.1.10", "vhost": "video.test.com", "app": "live","stream": "livestream"}
     */
    public function onUnpublish(&$data)
    {

    }

    /**关闭连接
     * @param $data {"action": "on_close", "client_id": 1985,"ip": "192.168.1.10", "vhost": "video.test.com", "app": "live","send_bytes": 10240, "recv_bytes": 10240}
     */
    public function onClose(&$data)
    {

    }

    /**写入观看表
     * @param $data
     */
    private function insertWatchLive($data){

    }


    /**踢掉指定客户端
     * @param $client_id
     */
    private function kickClient($client_id){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'http://127.0.0.1:1985/api/v1/clients/'.$client_id);//设置请求的URL
        #curl_setopt($curl, CURLOPT_HEADER, false);// 不要http header 加快效率
        curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);
        curl_setopt ($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_exec($curl);
        curl_close($curl);
        unset($curl);
    }

    public function  __call($name, $args) {
//        if(!method_exists($this, $name)) {return SRS_ERROR;}
        return SRS_SUCCESS;
    }
}