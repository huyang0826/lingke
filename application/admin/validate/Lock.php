<?php
namespace app\admin\validate;
use think\Validate;
class Lock extends Validate
{
    // 验证规则
    protected $rule = [
        'lock_id'  => ['require','min'=>5,'unique:lock'],
	    'lock_name'  => ['require']
    ];

    protected $message = [
        'lock_id.require' => '锁ID必须',
        'user_name.unique'     => '锁ID不能重复',
        'lock_name'        => '锁名称不能为空',   
    ];

    protected $scene = [
        'add'   =>  ['lock_id','lock_name'],
        'edit'  =>  ['lock_name'],
    ]; 
       
}