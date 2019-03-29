<?php
namespace app\home\controller;
use think\Controller;
use think\Db;
use think\Request;
use think\Session;
class IndexController extends Controller
{
	public function index(){
       	$banner = Db::name('banner')->where('status',1)->order('sort asc')->select();
       	$this->assign('banner',$banner);

       	$type = Db::name('type')->where('type',1)->where('status',1)->order('sort asc')->select();
       	$this->assign('type',$type);
        return view();   
    }


}