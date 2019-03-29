<?php
namespace app\home\controller;
use think\Controller;
use think\Db;
use think\Request;
use think\Session;
class PersontgController extends Controller
{

  //投稿人用户信息
	public function person_info(){
       

     	$mobilephone = session::get('web_member_mobilephone');
     	$uid = session::get('web_member_id');

      $info = Db::name('tgmember_info')->where('uid',$uid )->find();

      if($info){
        $this->assign('data',$info);
      }else{
        $info['name'] = '';
        $info['sex'] = '';
        $info['email'] = '';
        $info['weixin'] = '';
        $info['url'] = '';
        $info['scly'] = '';
        $info['zyys'] = '';
        $info['introduce'] = '';
        $info['sf_card'] = '';
        $info['sf_img_zheng'] = '';
        $info['sf_img_fan'] = '';
        $info['bank_card'] = '';
        $info['bank_card_img'] = '';
        
        $this->assign('data',$info);
      }


  		$this->assign('mobilephone',$mobilephone);
  		$this->assign('uid',$uid);

        return view();
       
		
    }
    //投稿人用户信息提交
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


    //普通用户信息
  public function person_info_pt(){
       

      $mobilephone = session::get('web_member_mobilephone');
      $uid = session::get('web_member_id');


      $info = Db::name('member')->where('id',$uid )->find();

      $this->assign('data',$info);
      
      $this->assign('mobilephone',$mobilephone);
      $this->assign('uid',$uid);

        return view();
       
    
    }
    //普通用户信息提交
    public function person_info_pt_add(){

      $data  = input("post.");
       
      $res = Db::name('member')->where('id',$data['id'])->update($data);
      if($res){
        return json(['status'=>1]);
      }else{
        return json(['status'=>0]);
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