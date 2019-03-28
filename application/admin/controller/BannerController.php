<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use think\Request;
use think\Validate;
class BannerController extends AdminController
{
    
    public function banner_list()
    {
      
         return view();
    }
   
    public function banner_ajax($search='')
    {
        
        $where = [];
        if($search){
            $where['name|mobilephone'] = array('like',"%$search%");
        }
     
        $count = Db::name('member')->where($where)->count();
        $offset = input('offset')?:0;
        $pagesize =input('limit')?:20;

        $data = Db::name('member')->where($where)->field("id,mobilephone,name,addtime")
        ->limit($offset,$pagesize)->select();


        return json(['rows'=>$data,'total'=>$count]);
    }
    public function banner_add(Request $request)
    {
        if($_POST||$_FILES){
            $data = input('post.');
            $file = $request->file('pic_url');
            if($file){

                $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads'. DS . 'banner');
                $data['pic_url'] = '/uploads/'. DS . 'banner/'.$info->getSaveName();
               
            }

            $rs = Db::name('banner')->insert($data);

            if($rs){
                $this->success('添加成功',url('banner_list'));
            }else{
                $this->success('添加失败',url('banner_list'));
            }
            
        }else{
            return view();
        }
    }
    public function banner_edit($id='')
    {
        if($_POST){
            $data = input('post.');
            $data['addtime'] = date('Y-m-d H:i:s',time());
            $check = validate('memberedit') -> check($data);
            if($check){
                $rs = Db::table('th_member')->where('id',$id)->update($data);
                if($rs){
                    $this->success('修改成功',url('user_list_pt'));
                }else{
                    $this->error('修改失败',url('user_list_pt'));
                }
            }else{
                $this->error('修改失败',url('user_list_pt'));
            }
            
        }else{
            $data = Db::name('member')->where('id',$id)->find();
            $this->assign('data',$data);
            return view();
        }
    }
  
    public function banner_del($id='')
    {
        $rs = Db::name('member')->where('id',$id)->delete();
        if($rs){
            return json(['status'=>1]);
        }
        
    }

   
}
