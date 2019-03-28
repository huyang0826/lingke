<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 朱亚杰 <zhuyajie@topthink.net>
// +----------------------------------------------------------------------

namespace app\admin\controller;
use admin\model\AuthRule;
use admin\model\AuthGroup;
use \think\Db;
/**
 * 播放视频
 */
class VideoController extends AdminController{
	/**
	 * [camera 摄像头]
	 * @param  string $id [摄像头id]
	 * @return [type]     [description]
	 */
    public function camera($id='')
    {
        $list = Db::name('camera')->find($id);
        // $list['Rtmp'] = 'rtmp://lssplay.tcgqxx.com/qxdq/57c01086ad3b4?auth_key=1503740934-389974ded9c624c3de37f352fb3d4fc3';
        // $list['Hls'] = 'http://lssplay.tcgqxx.com/qxdq/57c01086ad3b4.m3u8?auth_key=1503740934-677429cd33252ab536d6866781af69df';
        // $list['_id'] = '57c010887242fc0a6a1d3bb0';
        $this->assign('list',$list);
        return view();
    }
}
