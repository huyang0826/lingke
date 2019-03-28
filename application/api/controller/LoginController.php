<?php
namespace app\api\controller;
use think\Controller;
use think\Db;
use think\Request;
use app\common\model\LockUser;
class LoginController extends Controller
{
	public function login(){
        if(Request::instance()->isPost()){

            $json = Request::instance()->getInput();
            // p(input('post.'));
            $arr = json_decode($json,true);
            $LockUser = Model('LockUser');
            $rs = $LockUser->check_login($arr);
            return json($rs);
        }else{
            return json();
            
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
            $this->success('退出成功！', url('login/login'));
        } else {
            $this->redirect('login/login');
        }
    }

    public function verify(){
        $verify = new \Think\Verify();
        $verify->entry(1);
    }

}