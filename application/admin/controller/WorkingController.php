<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use think\Request;
use model\Progress;
class WorkingController extends AdminController
{
	/**
	 * [log_list 日志列表]
	 * @param  string $value [description]
	 * @return [type]        [description]
	 */
    public function log_list($value='')
    {
        
        $data = Db::name('working_log a')
            ->select();

        $this->assign("data",$data);

    	return view();
    }

    /**
     * [log_ajax ajax获取列表]
     * @param  string $value [description]
     * @return [type]        [description]
     */
    public function log_ajax($search='')
    {
    	$where['status'] = 1;
        if(!is_administrator()){
            $where['a.cuid'] = session('user_name');
        }
        $start = input('start');
        $end = input('end');
        if($start){
            if($end){
                $where['a.create_time'] = array('between',array(strtotime($start),strtotime($end)));
            }else{
                $where['a.create_time'] = array('egt',strtotime($start));
            }
        }else{
            if($end){
                $where['a.create_time'] = array('elt',strtotime($end));
            }
        }
        $count = Db::name('working_log a')->where($where)->count();
        if($search){
            $where['a.cuid|p.name'] = array('like',"%$search%");
        }
        $offset = input('offset')?:0;
        $pagesize =input('limit')?:20;

        $data = Db::name('working_log a')->field('a.*,p.name as project_name')
        ->join('project p','p.id = a.pro_id','left')
        ->order('a.update_time desc')
        ->where($where)->limit($offset,$pagesize)
        // ->fetchSql(true)
        ->select();
        return json(['rows'=>$data,'total'=>$count]);
    }
    /**
     * [project_add 添加项目]
     * @param  string $value [description]
     * @return [type]        [description]
     */
    public function log_add($value='')
    {
        $data = input('post.');
        if($this->request->post()){
            // 获取表单上传文件
            $file = $this->request->file('file');
            if (empty($file)) {
                //$this->error('请选择上传文件');
            }else{
                $file_names = explode('.', $file->getInfo()['name']);
                $info = $file->move(ROOT_PATH . 'public' . DS . 'Uploads'  . DS . 'oss', $file_names[0] .'-'. date('Y-m-d H:i:s'));
                // 移动到框架应用根目录/public/Uploads/ 目录下
                // ->validate(['ext' => 'jpg,png,zip,doc,'])
                
                if ($info) {
                    $data['file'] = config('ECS-OSS') . $info->getSaveName();
                    //$this->success('文件上传成功：' . $info->getRealPath());
                } else {
                    // 上传失败获取错误信息
                    $this->error($file->getError());
                }
            }
            $WorkingLog = Model('WorkingLog');
            $rs = $WorkingLog->add($data);
            if($rs['code']){
                $this->success('添加成功',url('log_list'));
            }else{
                $this->error('添加失败'.$rs['msg'],url('log_list'));
            }
            
        }else{
            $pro_data = Db::name('project')->select();
            $this->assign('pro_data',$pro_data);
            return view();
        }
    }
    /**
     * [log_del 删除工作内容]
     * @param  string $id [description]
     * @return [type]     [description]
     */
    public function log_del($id='')
    {
        $cuid = session('user_name');
        $rs = $working_log = Db::name('working_log')->where(['id'=>$id,'cuid'=>$cuid])->update(['status'=>0]);
        if($rs!==false){
            //删除成功
            $rs = Db::name('project_content')->where(['work_id'=>$id])->delete();
            $this->success('删除成功',url('log_list'));
        }
    }
}
