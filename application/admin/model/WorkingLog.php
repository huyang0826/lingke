<?php
namespace app\admin\model;
use think\Model;
use think\Validate;
use think\Db;
class WorkingLog extends Model
{
	 
	
	// 新增用户数据
	public function add($data)
	{
		$data['create_time'] = time();
		$data['update_time'] = time();
		$data['cuid'] = session('user_name');
		$data['status'] = 1;
	    if ($this->validate(true)->save($data)) {
	    	$project_content = Model('ProjectContent');
	    	$project_content->add(['pro_id'=>$data['pro_id'],'content'=>$data['today_content'],'work_id'=>$this->id]);
	        return ['code'=>1];
	    } else {
	        return ['code'=>0,'msg'=>$this->getError()];
	    }
	}
	
}