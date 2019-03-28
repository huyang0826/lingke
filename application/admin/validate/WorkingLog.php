<?php
namespace app\admin\validate;
use think\Validate;
class WorkingLog extends Validate
{
    // 验证规则
    protected $rule = [
        'pro_id'  => ['require'],
	    'today_content'  => ['require'],
	    'tomorrow_content'  => ['require'],
	   
    ];

    protected $message = [
        'pro_id.require' => '项目名称',
        'today_content'   => '今日工作内容必填',
        'tomorrow_content'        => '明日工作内容必填',    
    ];

    protected $scene = [
        'add'   =>  ['pro_id','today_content','tomorrow_content'],
    ]; 
   
}