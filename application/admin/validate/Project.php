<?php
namespace app\admin\validate;
use think\Validate;
class Project extends Validate
{
    // 验证规则
    protected $rule = [
        'name'  => ['require'],
	    'progress'  => ['require'],
	    'bussiness'  => ['require'],
	   
    ];

    protected $message = [
        'name.require' => '名称必须',
        'progress'   => '项目进度必填',
        'bussiness'        => '商务对接人必填',    
    ];

    protected $scene = [
        'add'   =>  ['name','progress','bussiness'],
    ]; 
   
}