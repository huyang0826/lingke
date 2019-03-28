<?php
namespace app\api\controller;
use think\Controller;
use think\Db;
use think\Request;
class WoanfangcameraController extends Controller
{
    public function camera_list($server_id='')
    {
        $where = [];
        if($server_id){
    	   $where['server_id'] = $server_id;
        }
        $data = Db::name('woanfang_camera a')->field("a.*,r.ip,r.port,r.name,c.pushrtmp,c.rtmp,c.hls")
        ->join('__RTSP2RTMPSERVER__ r', 'r.id = a.server_id','left')
        ->join('__CAMERA__ c', 'c.id = a.camera_id','left')
        ->where($where)
        ->select();

        return json($data);
    }
}
