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
use \think\Db;
/**
 * 权限管理控制器
 * Class AuthManagerController
 * @author 朱亚杰 <zhuyajie@topthink.net>
 */
class HikController extends AdminController{

    public function video_list($value='')
    {
		$url = 'http://60.18.162.39/cms/services/ICommonService?wsdl';
		if(session('treelist') == ''){
    		$soap = new \SoapClient($url);
    	
    		$result = $soap->getAllResourceDetail(array('nodeIndexCode'=>'','resType'=>1000));  

    		$xmlobj = simplexml_load_string($result->return,'SimpleXMLElement',LIBXML_NOCDATA);

      		$res = json_decode(json_encode($xmlobj),true);
    		$row = $res['rows']['row'];
            foreach ($row as $k => $v) {
    			$vv = $v['@attributes'];
    			
    			$arr['i_id'] = $vv['i_id'];
    			$arr['parentid'] = $vv['parentid'];
    			$arr['c_org_name'] = $vv['c_org_name'];
    			$arr['c_index_code'] = $vv['c_index_code'];

    			$d[] = $arr;
    		}
            vendor('Tree');
            $tree=new \Tree("i_id","parentid","children");
    		$tree->load($d);
    		$treelist=$tree->DeepTree();//所有分类树结构
            session('treelist',$treelist);
        }else{
            $treelist = session('treelist');
        }
        
        $this->assign('tree',$treelist);
        return view();
    }
    /**
     * [getAllResourceDetailByOrg 获取组织监控点]
     * @param  string $value [description]
     * @return [type]        [description]
     */
    public function getAllResourceDetailByOrg($id='')
    {
    	
        $url = 'http://60.18.162.39/cms/services/ICommonService?wsdl';
    	
		$soap = new \SoapClient($url);
    	$result = $soap->getAllResourceDetailByOrg(array('nodeIndexCode'=>'','orgCode'=>$id,'resType'=>10000));  
	
		$xmlobj = simplexml_load_string($result->return,'SimpleXMLElement',LIBXML_NOCDATA);

		$res = json_decode(json_encode($xmlobj),true);

		if( $res['head']['result']['@attributes']['result_code'] == 0){
			$size = $res['head']['result']['@attributes']['size'];
    		$rtsp_server = Db::name('rtsp2rtmpserver')->where('status','=','1')->select();
    		$this->assign('rtsp_server',$rtsp_server);
    		$this->assign('size',$size);
            if(isset($res['rows']['row'])){
        		$row = $res['rows']['row'];
                $this->assign('row',$row);
                $check_radio = Db::name('woanfang_camera')->column('camera_id');
                $this->assign('check_radio',json_encode($check_radio));
            }else{
                echo '无数据';die;
            }
            
        }else{
            echo '无数据';die;
        }
		return view('camera_list');
    }
    /**
     * [rtsp_rtmp rtsp_rtmp]
     * @param  string $camera_id    [摄像头id]
     * @param  string $c_index_code [监控点id]
     * @param  string $server_id    [rtsp服务器ip]
     * @return [type]               [description]
     */
    public function rtsp_rtmp($camera_id='',$c_index_code='',$server_id='')
    {
    	$data['camera_id'] = $camera_id;
    	if($camera_id == ''){
    		$this->error('请选择摄像头');
    	}
    	$data['c_index_code'] = $c_index_code;
    	$data['server_id'] = $server_id;
    	$rs = Db::name('woanfang_camera')->insert($data,true);
    	if($rs!==false){
    		$this->success('添加成功');
    	}else{
    		$this->success('添加失败');
    	}

    }
    /**
     * [check_rtsp_rtmp 验证推流地址是否添加]
     * @param  string $c_index_code [资源id]
     * @return [type]               [description]
     */
    public function check_rtsp_rtmp($c_index_code='')
    {
        $rs = Db::name('woanfang_camera')->where(array('c_index_code'=>$c_index_code))->find();
        if($rs){
            return json(['status'=>1,'id'=>$rs['camera_id']]);
        }else{
            return json(['status'=>0,'id'=>0]);
        }
    }
}
