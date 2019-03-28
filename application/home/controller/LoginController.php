<?php
namespace app\home\controller;
use think\Controller;
use think\Db;
use think\Request;
use think\Session;
class LoginController extends Controller
{
	//登录
	public function login(){

    	if($_POST){
    		$data['mobilephone'] = trim(input('mobilephone')) ;
    		$data['password'] = md5(input('password'));

    		//验证用户名密码
    		$Member = Model('member');

    		$rs = $Member->find_data($data);
    		if($rs){
    			session::set('web_member_id',$rs['id']);
                session::set('web_member_mobilephone',$rs['mobilephone']);
    			$data = [
    				"status"=>1
    			];
    		}else{
    			$data = [
    				"status"=>0
    			];
    		}
    		return json($data);
    	}else{
    		return view();
    	}
    }
    //注册
    public function regist(){

    	if($_POST){

    		$data['mobilephone'] = trim(input('mobilephone')) ;
    		$data['password'] = md5(input('password'));
    		$data['type'] = input('type');
    		$data['addtime'] = date('Y-m-d H:i:s',time());

    		$code_yz = Db::name("msg")->where(['code'=>$_POST['code'],'mobilephone'=>$data['mobilephone']])->find();

    		if($code_yz){//短信验证码正确

    			$Member = Model('member');

    			$find = $Member->find_data(['mobilephone'=>$data['mobilephone'],'type'=>$data['type']]);
    			
    			if($find){//已经注册
    				$res = [
	    				"status"=>3
	    			];
	    			return json($res);
    			}else{
    				$insert = $Member->add_data($data);

		    		if($insert){
		    			$res = [
		    				"status"=>1
		    			];
		    		}else{
		    			$res = [
		    				"status"=>0
		    			];
		    		}
    			}

	    		
    		}else{
    			$res = [
    				"status"=>2
    			];
    		}
    		
    		return json($res);
    	}else{
    		return view();
    	}
    }
  	//验证图片码
    function check_verify($verify,$mobilephone){
	   
        if (!captcha_check($verify)){//错误
            $res = [
				"status"=>0
			];
        }else{
           $res = [
				"status"=>1
			];

			//向用户发送短信验证码
			$data = [
				'mobilephone'=>$mobilephone,
				'code'=>1111,
				'addtime'=>date('Y-m-d H:i:s',time())

			];
			Db::name('msg')->insert($data,true);
        }
        return json($res);
	}
	
	function find_password(){
	   
        if($_POST){

    		$data['mobilephone'] = trim(input('mobilephone')) ;
    		$data['password'] = md5(input('password'));

    		$code_yz = Db::name("msg")->where(['code'=>$_POST['code'],'mobilephone'=>$data['mobilephone']])->find();

    		if($code_yz){//短信验证码正确

    			$Member = Model('member');

    			$find = $Member->find_data(['mobilephone'=>$data['mobilephone']]);
    			
    			if(!$find){
    				$res = [
	    				"status"=>3
	    			];
	    			return json($res);
    			}else{
    				$update = $Member->update_data(['mobilephone'=>$data['mobilephone']],['password'=>$data['password']]);

		    		if($update){
		    			$res = [
		    				"status"=>1
		    			];
		    		}else{
		    			$res = [
		    				"status"=>0
		    			];
		    		}
    			}

	    		
    		}else{
    			$res = [
    				"status"=>2
    			];
    		}
    		
    		return json($res);
    	}else{
    		return view();
    	}
	}
}