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
     
        $count = Db::name('banner')->where($where)->count();
        $offset = input('offset')?:0;
        $pagesize =input('limit')?:20;

        $data = Db::name('banner')->where($where)->field("id,url,pic_url,sort,case when status =1 then '显示' when status =2 then '隐藏' end status ")
        ->limit($offset,$pagesize)->order('sort asc')->select();


        return json(['rows'=>$data,'total'=>$count]);
    }
    public function banner_add(Request $request)
    {
        if($_POST||$_FILES){
            $data = input('post.');
            $file = $request->file('pic_url');
            if($file){

                $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads'. DS . 'banner');
                $data['pic_url'] = '/uploads'. DS . 'banner/'.$info->getSaveName();
               
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
    public function banner_edit(Request $request,$id='')
    {
        if($_POST||$_FILES){
            $data = input('post.');

            $file = $request->file('pic_url');

            if($file){

                $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads'. DS . 'banner');
                $data['pic_url'] = '/uploads'. DS . 'banner/'.$info->getSaveName();
               
            }

           
            $rs = Db::table('th_banner')->where('id',$id)->update($data);
            if($rs){
                $this->success('修改成功',url('banner_list'));
            }else{
                $this->error('修改失败',url('banner_list'));
            }
           
            
        }else{
            $data = Db::name('banner')->where('id',$id)->find();
            $this->assign('data',$data);
            return view();
        }
    }
  
    public function banner_del($id='')
    {
        $rs = Db::name('banner')->where('id',$id)->delete();
        if($rs){
            return json(['status'=>1]);
        }
        
    }

   
}
