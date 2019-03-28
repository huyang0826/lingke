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
class WoanfangController extends AdminController{
    public function _initialize($value='')
    {
        $hostdir= '.'.config('view_replace_str')['__IMG__'] . '/woanfang_icon';

        //获取本文件目录的文件夹地址

        $filesnames = scandir($hostdir);

        //获取也就是扫描文件夹内的文件及文件夹名存入数组 $filesnames
       
        foreach ($filesnames as $k => $v) {
            if($v == '.' || $v == '..'){
                unset($filesnames[$k]);
            }
        }
        $pid = input('pid');
        $this->assign('pid',$pid);
        $this->assign('filesnames',$filesnames);
    }
    public function menu_list()
    {
        return view();
    }
    public function menu_add()
    {
        if($_POST){
            //post添加
            $data = input('post.');
            $rs = Db::name('anfang_menu')->insert($data);
            if($rs!==false){
                $this->success('添加成功',url('menu_list'));
            }else{
                $this->error('添加失败',url('menu_list'));
            }   
        }else{
            if(input('pid')){
                $pid = input('pid');
            }else{
                $pid = 0;
            }
            $this->assign('pid',$pid);
            return view();
        }
    }
    public function menu_edit()
    {
        
        $id = input('id');
        if($_POST){
            $rs = Db::name('anfang_menu')->where(array('id'=>$id))->update($_POST);
            if($rs!==false){
                $this->success('修改成功',url('menu_list'));
            }else{
                $this->success('修改失败',url('menu_list'));
            }
        }else{
            if($id){
                $data = Db::name('anfang_menu')->find($id);
            }
            $this->assign('info',$data);
            return view();
        }
    }
    public function index_ajax($pid='')
    {
        if(input('pid')){
            $where['pid'] = input('pid');
        }else{
            $where['pid'] = 0;
        }
        // $data = Db::name('anfang_menu')->where(array('id'=>input('pid')))->find();
        // if($data['type'] == 2){
        //     //有摄像头
        //     $arr = Db::name('menu_camera')->where(array('menu_id'=>input('pid')))->find();
        // }
        $count = Db::name('anfang_menu')->where($where)->count();

        $data = Db::name('anfang_menu')->where($where)->select();
        return json(['rows'=>$data,'total'=>$count]);

    }
    /**
     * [menu_del 删除菜单]
     * @param  string $id [description]
     * @return [type]     [description]
     */
    public function menu_del($id='')
    {
        $where['pid'] = $id;
        $count = Db::name('anfang_menu')->where($where)->count();
        if($count){
            return json(array('status'=>0,'msg'=>'请先删除下级菜单'));
        }else{
            $rs = Db::name('anfang_menu')->where(array('id'=>$id))->delete();
            return json(array('status'=>1,'msg'=>'删除成功'));
        }
    }
    /**
     * [icon_list 图标列表]
     * @param  string $value [description]
     * @return [type]        [description]
     */
    public function icon_list($value='')
    {
        $hostdir= '.'.config('view_replace_str')['__IMG__'] . '/woanfang_icon';

        //获取本文件目录的文件夹地址

        $filesnames = scandir($hostdir);

        //获取也就是扫描文件夹内的文件及文件夹名存入数组 $filesnames
       
        foreach ($filesnames as $k => $v) {
            if($v == '.' || $v == '..'){
                unset($filesnames[$k]);
            }
        }
        $this->assign('filesnames',$filesnames);

        return view();
    }
    /**
     * [camera_add 添加摄像头]
     * @param  string $value [description]
     * @return [type]        [description]
     */
    public function camera_add()
    {
        if($_POST){
            $camera_id = input('cameraid');

            $camera_id = rtrim($camera_id,',');
            $id = input('id');
            $data['menu_id'] = $id;
            $data['camera_id'] = $camera_id;
            $rs = Db::name('menu_camera')->insert($data,true);
            if($rs!==false){
                $this->success('添加成功',url('menu_list'));
            }else{
                $this->error('添加失败',url('menu_list'));
            }
        }else{
            $id = input('id');
            $data = Db::name('menu_camera')->where(array('menu_id'=>$id))->find();
            if($data['camera_id']){
                $arr = explode(',' ,rtrim($data['camera_id'],','));
                foreach ($arr as $v) {
                    $new_arr[] = (int)$v; 
                }
            }else{
                $new_arr = [];
            }
            $this->assign('id',$id);
            $this->assign('arr',json_encode($new_arr));
            return view();
            
        }
    }
}
