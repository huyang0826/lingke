<?php
namespace app\Home\model;

use think\Model;

class Member extends Model
{
	public function find_data($where)
	{
		$rs = $this->where($where)->find();
		if($rs){
			return $rs;
		}else{
			return $this->getError();
		}
	}

	public function add_data($data)
	{
		$rs = $this->insert($data);
		if($rs){
			return $rs;
		}else{
			return $this->getError();
		}
	}

	public function update_data($where,$data)
	{
		$rs = $this->where($where)->update($data);
		if($rs){
			return $rs;
		}else{
			return $this->getError();
		}
	}
}