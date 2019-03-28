<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use think\Request;

class BannerController extends AdminController
{
    public function index($value='')
    {

    	if(is_null($this->user_info['header_img'])){
    		$header_img = '__IMG__/profile_small.jpg';
    	}else{
    		$header_img = $this->user_info['header_img'];
    	}
    	$this->assign('header_img',$header_img);
    	return view();
    }

    public function header_update($value='')
    {
    	if($_FILES){
    		
    		if (!file_exists("uploads/head/" )) {
                    mkdir("uploads/head/",0777,true);
                }
            move_uploaded_file($_FILES['header_img']['tmp_name'],"uploads/head/".time().$_FILES['header_img']['name']);

            $url ="/uploads/head/".time().$_FILES['header_img']['name'];
			$per = Db::name('user')->where('id',$this->user_id)->update(['header_img'=>$url]);

			if($per){
				$this->redirect('admin/index/index');
			}
    	}else{
    		$data = $this->user_info;
    		$this->assign('data',$data);
			return view();
    	}
    	
    	
    }
    public function index_v1($value='')
    {
    	return view('index_v1');
    }
}
