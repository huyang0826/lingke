<?php
namespace app\admin\validate;
use think\Validate;
class User extends Validate
{
    // 验证规则
    protected $rule = [
        'user_name'  => ['require','min'=>5,'unique:user'],
	    'password'  => ['require','min'=>6],
	    'phone'  => ['require','min'=>11],
	    'email'  => ['require','min'=>6],
	    'role_id'  => ['require']
    ];

    protected $message = [
        'user_name.require' => '名称必须',
        'user_name.mix'     => '用户名不能短于5个字符',
        'user_name.unique'     => '用户名不能重复',
        'password'   => '密码长度不小于6',
        'email'        => '邮箱格式错误',    
        'phone'        => '手机号小于11位',    
        'role_id'        => '角色不能为空',   
    ];

    protected $scene = [
        'add'   =>  ['user_name','password','phone','email','role_id'],
        'edit'  =>  ['email'],
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