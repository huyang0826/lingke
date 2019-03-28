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
class RoleController extends AdminController{

    /**
     * [role_list 角色列表]
     * @return [type]        [description]
     */
    public function role_list()
    {
        return view();
    }
    /**
     * [role_ajax 获取列表]
     * @return [type] [description]
     */
    public function role_ajax()
    {
        $AuthGroup = Model('AuthGroup');
        $where['status'] = 1; 
        $data = $AuthGroup->getGroups(array('cuid'=>session('user_id')));
        $count = count($data);
        return json(['total'=>$count,'rows'=>$data]);
    }
    /**
     * [role_add 添加角色]
     * @return [type] [description]
     */
    public function role_add()
    {
        if($_POST){
            if(isset($_POST['rules'])){
            sort($_POST['rules']);
                $_POST['rules']  = implode( ',' , array_unique($_POST['rules']));
            }
            
            //记录添加人uid
            $uid  = session('user_id');
            $arr['cuid'] = $uid;
            $arr['title'] =  input('title');
            $arr['description'] =  input('description');
            $arr['module'] =  'admin';

            $AuthGroup       =  Model('AuthGroup');
            $arr['type']   =  $AuthGroup::TYPE_ADMIN;
            $rs = $AuthGroup->insert($arr);
            if($rs===false){
                $this->error('操作失败'.$AuthGroup->getError());
            } else{
                $this->success('操作成功!',url('role_list'));
            }
        }else{
            return view();
        }
    }
    /**
     * [role_add 添加角色]
     * @return [type] [description]
     */
    public function role_edit()
    {
        if($_POST){
            if(isset($_POST['rules'])){
            sort($_POST['rules']);
                $_POST['rules']  = implode( ',' , array_unique($_POST['rules']));
            }
            $id = input('id');
            //记录添加人uid
            $uid  = session('user_id');
            $arr['cuid'] = $uid;
            $arr['title'] =  input('title');
            $arr['description'] =  input('description');
            $arr['module'] =  'admin';

            $AuthGroup       =  Model('AuthGroup');
            $arr['type']   =  $AuthGroup::TYPE_ADMIN;
            $rs = $AuthGroup->where(array('id'=>$id))->update($arr);
            if($rs===false){
                $this->error('操作失败'.$AuthGroup->getError());
            } else{
                $this->success('操作成功!',url('role_list'));
            }
        }else{
            $id = input('id');
            $AuthGroup =  Model('AuthGroup');
            $data = $AuthGroup->find($id);
            $this->assign('data',$data);
            return view();
        }
    }
    /**
     * 后台节点配置的url作为规则存入auth_rule
     * 执行新节点的插入,已有节点的更新,无效规则的删除三项任务
     * @author 朱亚杰 <zhuyajie@topthink.net>
     */
    public function updateRules(){
        //需要新增的节点必然位于$nodes
        $nodes    = $this->returnNodes(false);

        $AuthRule = Db::name('AuthRule');
        $map      = array('module'=>'admin','type'=>array('in','1,2'));//status全部取出,以进行更新
        //需要更新和删除的节点必然位于$rules
        $rules    = $AuthRule->where($map)->order('name')->select();

        //构建insert数据
        $data     = array();//保存需要插入和更新的新节点
        foreach ($nodes as $value){
            $temp['name']   = $value['url'];
            $temp['title']  = $value['title'];
            $temp['module'] = 'admin';
            $AuthRule = Model('AuthRule');
            if($value['pid'] >0){
                $temp['type'] = $AuthRule::RULE_URL;
            }else{
                $temp['type'] = $AuthRule::RULE_MAIN;
            }
            $temp['status']   = 1;
            $data[strtolower($temp['name'].$temp['module'].$temp['type'])] = $temp;//去除重复项
        }

        $update = array();//保存需要更新的节点
        $ids    = array();//保存需要删除的节点的id
        foreach ($rules as $index=>$rule){
            $key = strtolower($rule['name'].$rule['module'].$rule['type']);
            if ( isset($data[$key]) ) {//如果数据库中的规则与配置的节点匹配,说明是需要更新的节点
                $data[$key]['id'] = $rule['id'];//为需要更新的节点补充id值
                $update[] = $data[$key];
                unset($data[$key]);
                unset($rules[$index]);
                unset($rule['condition']);
                $diff[$rule['id']]=$rule;
            }elseif($rule['status']==1){

                $ids[] = $rule['id'];
            }
        }
        if ( count($update) ) {
            foreach ($update as $k=>$row){
                if ( $row!=$diff[$row['id']] ) {
                    $AuthRule->where(array('id'=>$row['id']))->update($row);
                }
            }
        }
        if ( count($ids) ) {
            $AuthRule->where( array( 'id'=>array('IN',implode(',',$ids)) ) )->update(array('status'=>-1));
            //删除规则是否需要从每个用户组的访问授权表中移除该规则?
        }
        if( count($data) ){
            $AuthRule->insertAll(array_values($data));
        }

        if ( $AuthRule->getError() ) {
            trace('['.__METHOD__.']:'.$AuthRule->getError());
            return false;
        }else{
            return true;
        }
    }


    /**
     * 权限管理首页
     * @author 朱亚杰 <zhuyajie@topthink.net>
     * auth : 聂俊勇
 	 * crea ：2015/4/22
 	 * edit ：2015/5/18
     */
    public function index(){
    	
    	//判断是否为 admin 登陆
    	if(is_administrator()){
    		
    		$pdo = M('ucenter_member');
    		//查询所有管理员id
    		$obj = $pdo->field('id')->where('type=1 or type=2')->select();
    		foreach($obj as $k=>$v){
    			$uid[] = $v['id'];
    		}
    		//var_dump($uid);
    		$where['cuid'] = array('in',$uid);
    		
    	}else{
    		$uid = $_SESSION["onethink_admin"]["user_auth"]['uid'];
	    	$where['cuid'] = $uid;
    	}
    	
    	$where['module'] = 'admin';
        $list = $this->lists('AuthGroup',$where,'id asc');
        // p($list);
        $list = int_to_string($list);
        $this->assign( '_list', $list );
        $this->assign( '_use_tip', true );
        $this->meta_title = '权限管理';
        $this->display();
    }

    /**
     * 创建管理员用户组
     * @author 朱亚杰 <zhuyajie@topthink.net>
     */
    public function createGroup(){
        if ( empty($this->auth_group) ) {
            $this->assign('auth_group',array('title'=>null,'id'=>null,'description'=>null,'rules'=>null,));//排除notice信息
        }
        $this->meta_title = '新增用户组';
        $this->display('editgroup');
    }

    /**
     * 编辑管理员用户组
     * @author 朱亚杰 <zhuyajie@topthink.net>
     */
    public function editGroup(){
        $auth_group = M('AuthGroup')->where( array('module'=>'admin','type'=>AuthGroupModel::TYPE_ADMIN) )
                                    ->find( (int)$_GET['id'] );
        $this->assign('auth_group',$auth_group);
        $this->meta_title = '编辑用户组';
        $this->display();
    }


    /**
     * 访问授权页面
     */
    public function access(){

    	$this->updateRules();
        $AuthRule = Model('AuthRule');
    	$AuthGroup = Model('AuthGroup');
    	//判断是否为 admin 登陆
    	if(is_administrator()){
	        $auth_group = Db::name('AuthGroup')->where( array('status'=>array('egt','0'),'module'=>'admin','type'=>$AuthGroup::TYPE_ADMIN) )
	                                    ->column('id,id,title,rules');
                               
    	}else{
            
    		$uid = $_SESSION["onethink_admin"]["user_auth"]["uid"];
	        $auth_group = Db::name('AuthGroup')->where( array('status'=>array('egt','0'),'module'=>'admin','cuid'=>$uid,'type'=>$AuthGroup::TYPE_ADMIN) )
	                                    ->column('id,id,title,rules');
    	}
    	
        $node_list   = $this->returnNodes();
        $map         = array('module'=>'admin','type'=>$AuthRule::RULE_MAIN,'status'=>1);
        $main_rules  = Db::name('AuthRule')->where($map)->column('name,id');
        $map         = array('module'=>'admin','type'=>$AuthRule::RULE_URL,'status'=>1);
        $child_rules = Db::name('AuthRule')->where($map)->column('name,id');
        $this->assign('main_rules', $main_rules);
        $this->assign('auth_rules', $child_rules);
        $this->assign('node_list',  $node_list);
        $this->assign('auth_group', $auth_group);
        $this->assign('this_group', $auth_group[(int)input('group_id')]);
        $this->meta_title = '访问授权';
        return view('managergroup');
    }

    /**
     * 管理员用户组数据写入/更新
     * @author 朱亚杰 <zhuyajie@topthink.net>
     */
    public function writeGroup(){
        if(isset($_POST['rules'])){
            sort($_POST['rules']);
            $_POST['rules']  = implode( ',' , array_unique($_POST['rules']));
        }
        
        //记录添加人uid
    	$uid  = session('user_id');
	    $_POST['cuid'] = $uid;
        
        $_POST['module'] =  'admin';
        $AuthGroup = Model('AuthGroup');
        $_POST['type']   =  $AuthGroup::TYPE_ADMIN;
       
        $rs = $AuthGroup->update($_POST);
         if($rs===false){
                $this->error('操作失败'.$AuthGroup->getError());
        } else{
            $this->success('操作成功!',url('Role/role_list'));
        }
        
    }

    /**
     * 状态修改
     * @author 朱亚杰 <zhuyajie@topthink.net>
     */
    public function changeStatus($method=null){
        if ( empty($_REQUEST['id']) ) {
            $this->error('请选择要操作的数据!');
        }
        switch ( strtolower($method) ){
            case 'forbidgroup':
                $this->forbid('AuthGroup');
                break;
            case 'resumegroup':
                $this->resume('AuthGroup');
                break;
            case 'deletegroup':
                $this->delete('AuthGroup');
                break;
            default:
                $this->error($method.'参数非法');
        }
    }

    /**
     * 用户组授权用户列表
     * @author 朱亚杰 <zhuyajie@topthink.net>
     * 
     * edit : 聂俊勇
	 * date ：2015/4/22
     */
    public function user($group_id){
        if(empty($group_id)){
            $this->error('参数错误');
        }
        
    	//判断是否为 admin 登陆
        //$username = getusername();
        if(!is_administrator_manage()){
            $uid  = $_SESSION["onethink_admin"]["user_auth"]['uid'];
	    	$where['cuid'] = $uid;
	        $group_where['cuid'] = $_SESSION["onethink_admin"]["user_auth"]['uid'];
        }
        
        $where['status'] = 1;
        //查询账户
        $ucenter_member = M('ucenter_member','','DB_CONFIG1');
        $user = $ucenter_member->field('id,username')->order('update_time desc')->where($where)->select();
        
        //查询分组
        $group_where['module'] = 'admin';
        $group_where['status'] = array('egt','0');
        $group_where['type'] = AuthGroupModel::TYPE_ADMIN;
        
        $auth_group = M('AuthGroup')->where($group_where)->getfield('id,id,title,rules');
        //$auth_group = M('AuthGroup')->where( array('status'=>array('egt','0'),'module'=>'admin','cuid'=>10,'type'=>AuthGroupModel::TYPE_ADMIN) )
         //   ->getfield('id,id,title,rules');
        $prefix   = C('DB_PREFIX');
        // $l_table  = $prefix.(AuthGroupModel::MEMBER);//查询memeer
        $l_table  = $prefix.'ucenter_member';//查询ucenter_memeer

        $r_table  = $prefix.(AuthGroupModel::AUTH_GROUP_ACCESS);
        $model    = M()->table( $l_table.' m' )->join ( $r_table.' a ON m.id=a.uid' );
        $_REQUEST = array();
        $list = $this->lists($model,array('a.group_id'=>$group_id,'m.status'=>array('egt',0)),'m.id asc',null,'m.id,m.username,m.last_login_time,m.last_login_ip,m.status');
        int_to_string($list);
        $this->assign( '_list',     $list );
        $this->assign('auth_group', $auth_group);
        $this->assign('this_group', $auth_group[(int)$_GET['group_id']]);
        $this->meta_title = '成员授权';
        $this->assign('user', $user);//所有账户
        $this->display();
    }

    /**
     * 将分类添加到用户组的编辑页面
     * @author 朱亚杰 <zhuyajie@topthink.net>
     * edit ：聂俊勇
     * date : 2015/4/23
     */
    public function category(){
   	 	//判断是否为 admin 登陆
    	if(is_administrator_manage()){
	        $auth_group = M('AuthGroup')->where( array('status'=>array('egt','0'),'module'=>'admin','type'=>AuthGroupModel::TYPE_ADMIN) )
	                                    ->getfield('id,id,title,rules');
    	}else{
    		$uid = $_SESSION["onethink_admin"]["user_auth"]["uid"];
	        $auth_group = M('AuthGroup')->where( array('status'=>array('egt','0'),'module'=>'admin','cuid'=>$uid,'type'=>AuthGroupModel::TYPE_ADMIN) )
	                                    ->getfield('id,id,title,rules');
    	}
    	
        $group_list     =   D('Category')->getTree();
        $authed_group   =   AuthGroupModel::getCategoryOfGroup(I('group_id'));
        $this->assign('authed_group',   implode(',',(array)$authed_group));
        $this->assign('group_list',     $group_list);
        $this->assign('auth_group',     $auth_group);
        $this->assign('this_group',     $auth_group[(int)$_GET['group_id']]);
        $this->meta_title = '分类授权';
        $this->display();
    }

    public function tree($tree = null){
        $this->assign('tree', $tree);
        $this->display('tree');
    }

    /**
     * 将用户添加到用户组的编辑页面
     * @author 朱亚杰 <zhuyajie@topthink.net>
     */
    public function group(){
        $uid            =   I('uid');
        $auth_groups    =   D('AuthGroup')->getGroups();
        $user_groups    =   AuthGroupModel::getUserGroup($uid);
        $ids = array();
        foreach ($user_groups as $value){
            $ids[]      =   $value['group_id'];
        }
        $nickname       =   D('Member')->getNickName($uid);
        $this->assign('nickname',   $nickname);
        $this->assign('auth_groups',$auth_groups);
        $this->assign('user_groups',implode(',',$ids));
        $this->display();
    }

    /**
     * 将用户添加到用户组,入参uid,group_id
     * @author 朱亚杰 <zhuyajie@topthink.net>
     */
    public function addToGroup(){
        $uid = I('uid');
        $gid = I('group_id');
        if( empty($uid) ){
            $this->error('参数有误');
        }
        $AuthGroup = D('AuthGroup');
        if(is_numeric($uid)){
            if ( is_administratoristrator($uid) ) {
                $this->error('该用户为超级管理员');
            }
            if( !M('Member')->where(array('uid'=>$uid))->find() ){
                $this->error('管理员用户不存在');
            }
        }

        if( $gid && !$AuthGroup->checkGroupId($gid)){
            $this->error($AuthGroup->error);
        }
        if ( $AuthGroup->addToGroup($uid,$gid) ){
            $this->success('操作成功');
        }else{
            $this->error($AuthGroup->getError());
        }
    }

    /**
     * 将用户从用户组中移除  入参:uid,group_id
     * @author 朱亚杰 <zhuyajie@topthink.net>
     */
    public function removeFromGroup(){
        $uid = I('uid');
        $gid = I('group_id');
        if( $uid==UID ){
            $this->error('不允许解除自身授权');
        }
        if( empty($uid) || empty($gid) ){
            $this->error('参数有误');
        }
        $AuthGroup = D('AuthGroup');
        if( !$AuthGroup->find($gid)){
            $this->error('用户组不存在');
        }
        if ( $AuthGroup->removeFromGroup($uid,$gid) ){
            $this->success('操作成功');
        }else{
            $this->error('操作失败');
        }
    }

    /**
     * 将分类添加到用户组  入参:cid,group_id
     * @author 朱亚杰 <zhuyajie@topthink.net>
     */
    public function addToCategory(){
        $cid = I('cid');
        $gid = I('group_id');
        if( empty($gid) ){
            $this->error('参数有误');
        }
        $AuthGroup = D('AuthGroup');
        if( !$AuthGroup->find($gid)){
            $this->error('用户组不存在');
        }
        if( $cid && !$AuthGroup->checkCategoryId($cid)){
            $this->error($AuthGroup->error);
        }
        if ( $AuthGroup->addToCategory($gid,$cid) ){
            $this->success('操作成功');
        }else{
            $this->error('操作失败');
        }
    }

    /**
     * 将模型添加到用户组  入参:mid,group_id
     * @author 朱亚杰 <xcoolcc@gmail.com>
     */
    public function addToModel(){
        $mid = I('id');
        $gid = I('get.group_id');
        if( empty($gid) ){
            $this->error('参数有误');
        }
        $AuthGroup = D('AuthGroup');
        if( !$AuthGroup->find($gid)){
            $this->error('用户组不存在');
        }
        if( $mid && !$AuthGroup->checkModelId($mid)){
            $this->error($AuthGroup->error);
        }
        if ( $AuthGroup->addToModel($gid,$mid) ){
            $this->success('操作成功');
        }else{
            $this->error('操作失败');
        }
    }

}
