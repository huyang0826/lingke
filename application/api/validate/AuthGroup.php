<?php
namespace app\admin\validate;
use think\Validate;
class AuthGroup extends Validate
{
    // 验证规则
    protected $rule = [
        ['title','require|min:5','必须设置用户组标题'],

        ['description','require|min:0|max:80','|描述最多80字符'],
    ];
}