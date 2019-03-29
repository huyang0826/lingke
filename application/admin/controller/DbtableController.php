<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use think\Request;
use think\Validate;
use \tp5er\Backup;
class DbtableController extends AdminController
{
    
    public function beifen()

        $config=array(
            'path'     => './Data/',//数据库备份路径
            'part'     => 20971520,//数据库备份卷大小
            'compress' => 0,//数据库备份文件是否启用压缩 0不压缩 1 压缩
            'level'    => 9 //数据库备份文件压缩级别 1普通 4 一般  9最高
        );
        $db= new Backup($config);
        $data1 = $db->getFile();
        $data = $db->dataList();
        $start= $db->setFile()->backup('th_user', 0);


        
    }
   
    

   
}
