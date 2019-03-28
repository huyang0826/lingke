<?php
namespace app\admin\validate;
use think\Validate;
class Cash extends Validate
{
    // 验证规则
    protected $rule = [
        'content'  => ['require','min'=>1],
	    'money'  => ['require'],
    ];

    protected $message = [
        'content.require' => '说明必填',
    
        'money'   => '金额必填',
    ];

    protected $scene = [
        'add'   =>  ['content','money'],
    ]; 
    // protected $rule = [
    //     'name'  =>  'require|max:25',
    //     'email' =>  'email',
    // ];

    // protected $message = [
    //     'name.require'  =>  '用户名不能短于5个字符',
    //     'email' =>  '邮箱格式错误',
    // ];

    // protected $scene = [
    //     'add'   =>  ['name','email'],
    //     'edit'  =>  ['email'],
    // ];    
}