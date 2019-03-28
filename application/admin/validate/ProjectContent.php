<?php
namespace app\admin\validate;
use think\Validate;
class ProjectContent extends Validate
{
    // 验证规则
    protected $rule = [
	    'content'  => ['require'],
	    'pro_id'  => ['require'],
	   
    ];

    protected $message = [
        'content'   => '内容必须',
        'pro_id'        => '缺少项目id',    
    ];

    protected $scene = [
        'add'   =>  ['content','pro_id'],
    ]; 
   
}