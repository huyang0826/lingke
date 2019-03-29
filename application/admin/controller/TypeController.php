<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use think\Request;
use think\Validate;
class TypeController extends AdminController
{
    
    public function cytp_list()
    {
      
         return view();
    }
    public function bjtp_list()
    {
      
         return view();
    }
    public function cysp_list()
    {
      
         return view();
    }
   
    public function cytp_ajax($search='',$type='')
    {
        
        $where = ['type'=>$type];
        
        if($search){
            $where['name'] = array('like',"%$search%");
        }
     
        $count = Db::name('type')->where($where)->count();
        $offset = input('offset')?:0;
        $pagesize =input('limit')?:20;

        $data = Db::name('type')->where($where)->limit($offset,$pagesize)
        ->field("* ,case when status =1 then '显示' when status =2 then '隐藏' end status")
        ->order('sort asc')->select();


        return json(['rows'=>$data,'total'=>$count]);
    }
    public function cytp_add($type='')
    {
        if($_POST){
            $data = input('post.');
            
            $rs = Db::name('type')->insert($data);

            if($rs){
                if($data['type']==1){
                    $this->success('添加成功',url('cytp_list'));
                }else if($data['type']==2){
                    $this->success('添加成功',url('bjtp_list'));
                }else if($data['type']==3){
                    $this->success('添加成功',url('cysp_list'));
                }   
                
            }else{
                $this->success('添加失败',url('cytp_list'));
            }
            
        }else{
            $this->assign('type',$type);
            return view();
        }
    }
    public function cytp_edit($id='')
    {
        if($_POST){
            $data = input('post.');

            $rs = Db::name('type')->where('id',$data['id'])->update($data);
            if($rs){
                $type = Db::name('type')->where('id',$data['id'])->value("type");
                
                if($type==1){
                    $this->success('修改成功',url('cytp_list'));
                }else if($type==2){
                    $this->success('修改成功',url('bjtp_list'));
                }else if($type==3){
                    $this->success('修改成功',url('cysp_list'));
                }   
                
            }else{
                $this->error('修改失败',url('cytp_list'));
            }
           
            
        }else{
            $data = Db::name('type')->where('id',$id)->find();
            $this->assign('data',$data);
            return view();
        }
    }
  
    public function cytp_del($id='')
    {
        $rs = Db::name('type')->where('id',$id)->delete();
        if($rs){
            return json(['status'=>1]);
        }
        
    }

   
}
