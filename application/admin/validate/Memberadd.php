<?php
namespace app\admin\validate;
use think\Validate;
class Memberadd extends Validate
{
    // 验证规则
    protected $rule = [
        'user_name'  => ['require','min'=>4,'unique:member'],
	    'password'  => ['min'=>6],
	    'mobilephone'  => ['require','min'=>11],
        'mobilephone'  => ['require','max'=>11],

    ];

    protected $message = [
        'user_name.require' => '名称必须',
        'user_name.mix'     => '用户名不能短于5个字符',
        'user_name.unique'     => '用户名不能重复',
        'password.min'   => '密码长度不小于6',
        'mobilephone.min'        => '手机号需11位',    
        'mobilephone.max'        => '手机号需11位',  
    ];

    protected $scene = [
        'add'   =>  ['user_name','password','mobilephone'],
        'edit'  =>  [''],
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