<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use think\Request;
class Login extends Controller
{
	public function login(Request $request){

    	if($_POST){
    		$data['user_name'] = input('username');
    		$data['password'] = md5(input('password'));
    		//验证用户名密码
    		$User = Model('User');

    		$rs = $User->check($data,$request->IP());
    		if($rs){
    			//设置session
    			session('user_name',$rs['user_name']);
    			session('type',$rs['type']);
    			session('user_id',$rs['id']);
    			$this->success('登录成功',url('admin/index/index'));
    		}else{
    			$this->error('用户名密码不对');
    		}
    	}else{
    		return view();
    	}
    }
    public function add()
    {
        $data = input('post.');
        // 数据验证
        $result = $this->validate($data,'User');
        if (true !== $result) {
            return $result;
        }
        $user = new UserModel;
        // 数据保存
        $user->allowField(true)->save($data);
        return '用户[ ' . $user->nickname . ':' . $user->id . ' ]新增成功';
    }
  	/* 退出登录 */
    public function logout(){
        if(is_login()){
            session('[destroy]');
            $this->success('退出成功！', url('/admin/login/login'));
        } else {
            $this->success('退出成功！', url('/admin/login/login'));
        }
    }

    public function verify(){
        $verify = new \Think\Verify();
        $verify->entry(1);
    }

}