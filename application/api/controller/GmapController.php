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
 * 权限管理控制器
 * Class AuthManagerController
 * @author 朱亚杰 <zhuyajie@topthink.net>
 */
class GmapController extends AdminController{
    /**
     * [index 地图]
     * @param  string $value [description]
     * @return [type]        [description]
     */
    public function index($value='')
    {
     
        $arr = [
            [
            'title' => '摄像头1',
            'x'=>116.368904,'y'=>39.923423,
            'content' =>"<img onclick='play(1)' src='http://tpc.googlesyndication.com/simgad/5843493769827749134'>地址：北京市朝阳区阜通东大街6号院3号楼东北8.3公里",
            ] ,
            [
            'title' => '摄像头2',
            'x'=>116.382122,'y'=>39.921176,
            'content' =>"<img onclick='play(1)' src='http://tpc.googlesyndication.com/simgad/5843493769827749134'>地址：北京市朝阳区阜通东大街6号院3号楼东北8.3公里",
            ] ,
            [
            'title' => '摄像头3',
            'x'=>116.387271,'y'=>39.922501,
            'content' =>"<img onclick='play(1)' src='http://tpc.googlesyndication.com/simgad/5843493769827749134'>地址：北京市朝阳区阜通东大街6号院3号楼东北8.3公里",
            ] ,
            [
            'title' => '摄像头4',
            'x'=>116.398258,'y'=>39.914600,
            'content' =>"<img onclick='play(1)' src='http://tpc.googlesyndication.com/simgad/5843493769827749134'>地址：北京市朝阳区阜通东大街6号院3号楼东北8.3公里电话：010-64733333<a href='http://ditu.amap.com/detail/B000A8URXB?citycode=110105'>详细信息</a>",
            ] ,
        ];
        $this->assign('arr',json_encode($arr));
        return view();
    }
}
