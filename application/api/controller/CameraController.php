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
use think\Db;
/**
 * 菜单管理控制器
 * @author kjcx
 */
class CameraController extends AdminController{
    public function _initialize($value='')
    {
        
    }
    public function camera_list($value='')
    {
        return view();
    }
    /**
     * [index_ajax 获取列表]
     * @param  string $region_id [description]
     * @return [type]      [description]
     */
    public function index_ajax($region_id='',$search='')
    {
        if(input('region')){
            $where['region_id'] = input('region_id');
        }else{
            $where = [];
        }
        if($search){
            $where['title|deviceid|user_name'] = array('like',"%$search%");
        }
        $count = Db::name('Camera')->where($where)->count();
        $offset = input('offset')?:1;
        $pagesize =input('limit')?:20;
        $data = Db::name('Camera')->where($where)->limit($offset,$pagesize)->select();
        return json(['rows'=>$data,'total'=>$count]);

    }
    public function daoru($value='')
    {
        $str = file_get_contents('./camera_info.txt');
        $arr = json_decode($str,true);
        foreach ($arr as $k => $v) {
            Db::name('camera')->insert($v);
        }


    }
}
