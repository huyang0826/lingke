<?php
namespace app\home\controller;
use think\Controller;
use think\Db;
use think\Request;
use think\Session;
class PersontgController extends Controller
{
	public function person_info(){
       

       	$mobilephone = session::get('web_member_mobilephone');
       	$uid = session::get('web_member_id');
		$this->assign('mobilephone',$mobilephone);
		$this->assign('uid',$uid);

        return view();
       
		
    }

    public function person_info_add(){

       $data  = input("post.");
       $find = Db::name('tgmember_info')->where('uid',$data['uid'])->find();

       if($find){
       		$res = Db::name('tgmember_info')->where('uid',$data['uid'])->update($data);
       		if($res){
       			return json(['status'=>1]);
       		}else{
       			return json(['status'=>0]);
       		}
       		
       }else{
       		$res =Db::name('tgmember_info')->where('uid',$data['uid'])->insert($data);

       		if($res){
       			return json(['status'=>1]);
       		}else{
       			return json(['status'=>0]);
       		}
       	 	
       }
		
    }

  


    public function picgo(Request $request){

    	$file = $request->file('file');
        if($file){

            $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads'. DS . 'sf');
            if($info){
                return '/uploads/'. DS . 'sf/'.$info->getSaveName();
                // 成功上传后 获取上传信息

            }
        }
        
        
    }
    

}