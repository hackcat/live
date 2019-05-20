>基于[SRS](https://github.com/ossrs/srs) http_api模块开发的直播系统<br/>
>利用ffmpeg对摄像头进行拉流(或RTMP推流),转码,切片(hls)<br/>
>支持手机端,pc端,支持按需拉流(没人观看时,不拉取视频流,节约资源)

###1.安装

    composer install
    #务必先安装服务
    php server.php install 
    
###2.配置流列表
##### 2.1在storage/data/stream目录下新增 json 文件,文件名等于json里的stream项
示例: storage/data/stream/4.json

    {
    	"stream": 4,//视频流唯一编号
    	"process_pid": null,//必须为null
    	"address": "同福苑",//设备地址
    	"live_host": "rtsp://player:jskj20180116@dt.cnxnu.com:10554/Streaming/Channels/1302",//直播流地址
    	"coordinate": "104.222,30.5",//经纬度
    	"app": "live"//必须为live
    }
###3.命令

    //启动
    php server.php start 
    //后台运行
    php server.php start &
    //停止
    php server.php stop
    //卸载服务
    php server.php uninstall
