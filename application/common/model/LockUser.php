<?php
namespace app\common\model;
use think\Model;
use think\Validate;
use think\Db;
use common\model\token;
class LockUser extends Model
{
	 
	
	// 新增用户数据
	public function add($data)
	{
	    if ($this->validate(true)->save($data)) {
	        return ['code'=>1];
	    } else {
	        return ['code'=>0,'msg'=>$this->getError()];
	    }
	}
	/**
	 * [check_login 验证登录]
	 * @param  string $data [登录数据]
	 * @return [type]       [description]
	 */
	public function check_login($data='')
	{
		$where['user_name'] = $data['user_name'];
		$where['password'] = md5($data['password']);
		$rs = $this->where($where)->find();
		if($rs){	
			//创建token
			// $arr = $this->login_token($data);
			$token = Model('token');
			$data['user_id'] = $rs['user_id'];
			$arr = $token->create_token($data);
			if($arr){
				return ['status'=>1,'user_name'=>$data['user_name'],'token'=>$token,'msg'=>'登录成功'];
			}else{
				return ['status'=>0,'user_name'=>$data['user_name'],'token'=>'','msg'=>'token创建失败'];
			}
		}else{
			return ['status'=>0,'user_name'=>$data['user_name'],'token'=>'','msg'=>'用户名密码不对'];
		}
	}
	
}