<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use think\Request;
use think\Validate;
class UserController extends AdminController
{
	/**
	 * [user_list 用户列表]
	 * @param  string $value [description]
	 * @return [type]        [description]
	 */
    public function user_list_pt()
    {
      
    	 return view();
    }
    /**
     * [index_ajax ajax获取列表]
     * @param  string $value [description]
     * @return [type]        [description]
     */
    public function user_ajax_pt($search='')
    {
    	$where = ['type'=>1];
        if($search){
            $where['name|mobilephone'] = array('like',"%$search%");
        }
     
        $count = Db::name('member')->where($where)->count();
        $offset = input('offset')?:0;
        $pagesize =input('limit')?:20;

        $data = Db::name('member')->where($where)->field("id,mobilephone,name,addtime,case when status =1 then '正常' when status =2 then '禁用' end status")
        ->limit($offset,$pagesize)
        ->order('id desc')
        ->select();


        return json(['rows'=>$data,'total'=>$count]);
    }
    public function user_add_pt($value='')
    {
    	if($_POST){
    		$data = input('post.');
    		$data['password'] = MD5(input('password'));
            $data['addtime'] = date('Y-m-d H:i:s',time());
            $check = validate('memberadd') -> check($data);
            if($check){
                $rs = Db::table('th_member')->insert($data);
                if($rs){
                    $this->success('添加成功',url('user_list_pt'));
                }else{
                    $this->error('添加失败',url('user_list_pt'));
                }
            }else{
                $this->error('添加失败',url('user_list_pt'));
            }
			
    	}else{
    		return view();
    	}
    }
   public function user_edit_pt($id='')
    {
        if($_POST){
            $data = input('post.');
            $data['addtime'] = date('Y-m-d H:i:s',time());
            $check = validate('memberedit') -> check($data);
            if($check){
                $rs = Db::table('th_member')->where('id',$id)->update($data);
                if($rs){
                    $this->success('修改成功',url('user_list_pt'));
                }else{
                    $this->error('修改失败',url('user_list_pt'));
                }
            }else{
                $this->error('修改失败',url('user_list_pt'));
            }
            
        }else{
            $data = Db::name('member')->where('id',$id)->find();
            $this->assign('data',$data);
            return view();
        }
    }
      //删除用户
    /**
     * [ad_del 删除用户]
     * @param  string $id [用户id]
     * @return [type]     [description]
     */
    public function user_del_pt($id='')
    {
        $rs = Db::name('member')->where('id',$id)->delete();
        if($rs){
            return json(['status'=>1]);
        }
        
    }

     public function user_details_pt($id='')
    {
        $rs = Db::name('member')->where('id',$id)->find();

        $this->assign('data',$rs);
        return view();
        
    }

    public function user_list_tg()
    {
       if($_GET){
            $this->assign('sh',$_GET['sh']);
       }else{
            $this->assign('sh','');
       }
        return view();
    
       
    }
    /**
     * [index_ajax ajax获取列表]
     * @param  string $value [description]
     * @return [type]        [description]
     */
    public function user_ajax_tg($search='',$sh='')
    {
        $where = ['type'=>2];
        if($search){
            $where['mobilephone'] = array('like',"%$search%");
        }
        if($sh){
            $where['sh'] = array('eq',$sh);
        }
    
        $count = Db::name('member')->where($where)->count();
        $offset = input('offset')?:0;
        $pagesize =input('limit')?:20;
        $data = Db::name('member m')
        ->join('tgmember_info mt','m.id = mt.uid','left')
        ->where($where)
        ->field("m.id,m.mobilephone,mt.name,m.addtime,case when status =1 then '正常' when status =2 then '禁用' end status")
        ->limit($offset,$pagesize)
        ->order('id desc')
        ->select();

        return json(['rows'=>$data,'total'=>$count]);
    }

     public function user_add_tg($value='')
    {
        if($_POST){
            $data = input('post.');
            $data['password'] = MD5(input('password'));
            $data['addtime'] = date('Y-m-d H:i:s',time());
            $check = validate('memberadd') -> check($data);
            if($check){
                $rs = Db::table('th_member')->insert($data);
                if($rs){
                    $this->success('添加成功',url('user_list_tg'));
                }else{
                    $this->error('添加失败',url('user_list_tg'));
                }
            }else{
                $this->error('添加失败',url('user_list_tg'));
            }
            
        }else{
            return view();
        }
    }
   public function user_edit_tg($id='')
    {
        if($_POST){
            $data = input('post.');
            $data['addtime'] = date('Y-m-d H:i:s',time());
            $check = validate('memberedit') -> check($data);
            if($check){
                $rs = Db::table('th_member')->where('id',$id)->update($data);
                if($rs){
                    $this->success('修改成功',url('user_list_tg'));
                }else{
                    $this->error('修改失败',url('user_list_tg'));
                }
            }else{
                $this->error('修改失败',url('user_list_tg'));
            }
            
        }else{
            $data = Db::name('member')->where('id',$id)->find();
            $this->assign('data',$data);
            return view();
        }
    }
      //删除用户
    /**
     * [ad_del 删除用户]
     * @param  string $id [用户id]
     * @return [type]     [description]
     */
    public function user_del_tg($id='')
    {
        $rs = Db::name('member')->where('id',$id)->delete();
        if($rs){
            return json(['status'=>1]);
        }
        
    }

     public function user_details_tg($id='')
    {
        $rs = Db::name('member m')
        ->join('th_tgmember_info t','m.id = t.uid')
        ->where('m.id',$id)
        ->field('t.* ,m.mobilephone,m.status,m.addtime')
        ->find();

        $this->assign('data',$rs);
        return view();
        
    }
}
