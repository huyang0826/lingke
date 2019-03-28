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
class MenuController extends AdminController{
    /**
     * 后台菜单首页
     * @return none
     */
    public function index($value='')
    {   
        return view();
    }
    public function index_ajax(){
        $pid  = input('pid',0);
        if($pid){
            $data = Db::name('Menu')->where("id={$pid}")->field(true)->find();
            $this->assign('data',$data);
        }else{
            $this->assign('data',['title'=>'']);
        }
        $title      =   trim(input('get.title'));
        $type       =   config('CONFIG_GROUP_LIST');
        $all_menu   =   Db::name('Menu')->column('id,title');
        $map['pid'] =   $pid;
        if($title)
            $map['title'] = array('like',"%{$title}%");
        $list       =   Db::name('Menu')->where($map)->field(true)->order('sort asc,id asc')->select();
        $count       =   Db::name('Menu')->where($map)->field(true)->count();
        int_to_string($list,array('hide'=>array(1=>'是',0=>'否'),'is_dev'=>array(1=>'是',0=>'否')));
        if($list) {
            foreach($list as &$key){
                if($key['pid']){
                    $key['up_title'] = $all_menu[$key['pid']];
                }
            }
            // p($list  );
            return json(['total'=>$count,'rows'=>$list]);
        }else{
            return json(['total'=>0,'rows'=>[]]);
        }
        
    }

    /**
     * 新增菜单
     * @author yangweijie <yangweijiester@gmail.com>
     */
    public function add(){
        if($_POST){
            $Menu = Db::name('Menu');
            $data = input('post.');
            if($data){
                $id = $Menu->insert($data);
                if($id){
                    // S('DB_CONFIG_DATA',null);
                    //记录行为
                    action_log('update_menu', 'Menu', $id, session('user_id'));
                    $this->success('新增成功', url('menu/index'));
                } else {
                    $this->error('新增失败');
                }
            } else {
                $this->error($Menu->getError());
            }
        } else {
            $this->assign('info',array('pid'=>input('pid')));
            $menus = Db::name('Menu')->field(true)->select();
            $menus = Model('Common/Tree')->toFormatTree($menus);
            $menus = array_merge(array(0=>array('id'=>0,'title_show'=>'顶级菜单')), $menus);
            $this->assign('Menus', $menus);
            $this->meta_title = '新增菜单';
            return view('edit');
        }
    }

    /**
     * 编辑配置
     * @author kjcx
     */
    public function edit($id = 0){
        if($_POST){
            $Menu = Db::name('menu');
            $data = input('post.');
            if($data){
                if($Menu->update($data)!== false){
                    // S('DB_CONFIG_DATA',null);
                    //记录行为
                    action_log('update_menu', 'Menu', $data['id'], session('user_id'));
                    $this->success('更新成功',url('menu/index'));
                } else {
                    $this->error('更新失败');
                }
            } else {
                $this->error($Menu->getError());
            }

        } else {
            $info = array();
            /* 获取数据 */
            $info = Db::name('menu')->field(true)->find($id);
            $menus = Db::name('menu')->field(true)->select();
            $menus = Model('Common/Tree')->toFormatTree($menus);

            $menus = array_merge(array(0=>array('id'=>0,'title_show'=>'顶级菜单')), $menus);
            $this->assign('Menus', $menus);

            if(false === $info){
                $this->error('获取后台菜单信息错误');
            }
            $this->assign('info', $info);
            $this->meta_title = '编辑后台菜单';
            return view();
        }
    }

    /**
     * 删除后台菜单
     * @author yangweijie <yangweijiester@gmail.com>
     */
    public function del(){
        $id = array_unique((array)input('id',0));

        if ( empty($id) ) {
            $this->error('请选择要操作的数据!');
        }

        $map = array('id' => array('in', $id) );
        if(Db::name('Menu')->where($map)->delete()){
            // S('DB_CONFIG_DATA',null);
            //记录行为
            action_log('update_menu', 'Menu', $id, session('user_id'));
            return json(array('status'=>1,'msg'=>'删除成功'));
        } else {
            return json(array('status'=>0,'msg'=>'删除失败'));
        }
    }
}
