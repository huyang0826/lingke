<?php
namespace app\common\model;
use think\Model;
use think\Validate;
use think\Db;
class Token extends Model
{
	public function create_token($data='')
	{
		$token = md5($data['user_name'].$data['password'].time().rand());
		$data['token'] = $token;
		$data['create_time'] = time();
		$data['update_time'] = time();
		$record = $this->where(array('user_id'=>$data['user_id']))->find();
		if($record){
			$rs = $this->allowField(true)->save($data,['user_id'=>$data['user_id']]);
		}else{
			$rs = $this->allowField(true)->save($data);
		}
		if($rs!==false){
			return $token;
		}else{
			return false;
		}
	}	
}