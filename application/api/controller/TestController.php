<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 朱亚杰 <zhuyajie@topthink.net>
// +----------------------------------------------------------------------

namespace app\admin\controller;
use admin\model\AuthRule;
use admin\model\AuthGroup;
use think\Db;
/**
 * 菜单管理控制器
 * @author kjcx
 */
class TestController extends AdminController{
    public function _initialize($value='')
    {
        
    }
    public function index($value='')
    {
        $cli = new \swoole_http_client('127.0.0.1', 80);

        $cli->setHeaders(['User-Agent' => "swoole"]);
        $cli->post('/dump.php', array("test" => '9999999'), function (swoole_http_client $cli)
        {
            var_dump($cli->body);
            echo "#{$cli->sock}\tPOST response Length: " . strlen($cli->body) . "\n";
            $cli->get('/index.php', function (swoole_http_client $cli)
            {
                echo "#{$cli->sock}\tGET response Length: " . strlen($cli->body) . "\n";
            });
        });
    }
}
