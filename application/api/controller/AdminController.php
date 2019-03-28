<?php
namespace app\Admin\controller;
use think\Controller;
use think\Db;
use think\Request;
use admin\model\AuthRule;
use admin\model\Auth;
use admin\model\Menu;
use admin\model\AuthGroup;
class AdminController extends Controller
{
	public function _initialize()
	{
		if(!session('user_id')){
            $this->redirect(url('admin/login/login'));
        }
  
		define('UID', session('user_id'));
		define('IS_ROOT',   is_administrator());
		 // 检测访问权限
        $access =   $this->accessControl();
        if ( $access === false ) {
            $this->error('403:禁止访问',url('/admin/login/login'));
        }elseif( $access === null ){
            $dynamic        =   $this->checkDynamic();//检测分类栏目有关的各项动态权限
            if( $dynamic === null ){
                //检测非动态权限
                $rule  = strtolower('/'.request()->module().'/'.request()->controller().'/'.request()->action());
                if ( !$this->checkRule($rule,array('in','1,2')) ){
                    $this->error('未授权访问!',url('/admin/login/login'));
                }
            }elseif( $dynamic === false ){
                $this->error('未授权访问!',url('/admin/login/login'));
            }
        }
        $menu = $this->getMenus();
        $this->assign('menu', $menu);


		$controller = request()->controller();
		$menu = $this->getMenus($controller);
		
        
		$this->assign('menu',$menu);
	}
	/**
	 * [admin_list 管理员列表]
	 * @return [type]        [description]
	 */
	public function admin_list()
	{
		return view();
	}
	/**
	 * [ajax_admin ajax获取列表]
	 * @return [type] [description]
	 */
	public function admin_ajax()
	{
		$User = Model('User');
		$data = $User->lists();
		return json($data);
	}
	/**
	 * [admin_add 添加管理员]
	 * @return [type]        [description]
	 */
	public function admin_add()
	{
		if($_POST){
			$data = input('post.');
			$data['password'] = MD5(input('password'));
			$User = Model('User');
			$rs = $User->add($data);
			if($rs){
				$this->success('添加成功',url('admin_list'));
			}else{
				$this->error($rs['msg']);
			}
		}else{
			$get_role_list = get_role_list();
			$this->assign('get_role_list',$get_role_list);
			return view();
		}
	}
	/**
	 * [admin_edit 编辑管理员]
	 * @param  string $value [description]
	 * @return [type]        [description]
	 */
	public function admin_edit($value='')
	{
		if($_POST){
			$data = input('post.');

			unset($data['user_name']);
			if(input('password')){
				$data['password'] = MD5(input('password'));
			}else{
				unset($data['password']);
			}
	
			$User = Model('User');
			$AuthGroup = Model('AuthGroup');
			$gid = input('role_id');
	        if( $gid && !$AuthGroup->checkGroupId($gid)){
	            $this->error($AuthGroup->error);
	        }
			$rs = $User->where(array('user_name'=>input('user_name'),'id'=>input('id')))->update($data);
			
			if($rs){
				$this->success('编辑成功',url('admin_list'));
			}else{
				$this->error($rs['msg']);
			}
		}else{
			$id = input('id');
			$User = Model('User');
			$data = $User->find($id);
			$this->assign('data',$data);
			$get_role_list = get_role_list();
			$this->assign('get_role_list',$get_role_list);
			return view();
		}
	}
	/**
     * 返回后台节点数据
     * @param boolean $tree    是否返回多维数组结构(生成菜单时用到),为false返回一维数组(生成权限节点时用到)
     * @retrun array
     *
     * 注意,返回的主菜单节点数组中有'controller'元素,以供区分子节点和主节点
     *
     */
    final protected function returnNodes($tree = true){
        static $tree_nodes = array();
        if ( $tree && !empty($tree_nodes[(int)$tree]) ) {
            return $tree_nodes[$tree];
        }
        if((int)$tree){
            
            //判断是否为 admin 登陆
            if(session("user_name")==='admin'){
                 $list = Db::name('Menu')->field('id,pid,title,url,tip,hide')->order('sort asc')->select();
            }else{
                //查询当前人所属的分组------拥有的权限
                $uid = session('user_id');
                $group_pdo = Db::name('');
                $group_obj = $group_pdo->query("select c.rules from zk_user as a left join zk_auth_group_access as b on a.id=b.uid left join zk_auth_group as c on b.group_id=c.id where a.id=$uid");
                
                $in = explode(',', $group_obj[0]['rules']);
                foreach($in as $k=>&$v){
                    $v = (int)$v;
                }
                //查询出权限name
                $where['id'] = array('in',$in);
                $list1 = Db::name('Auth_rule')->where($where)->field('name')->select();
                $list2 = [];
                foreach($list1 as $k=>$v){
                    $name = explode('/', $v['name']);
                    $list2[] = $name[1].'/'.$name[2];
                }
                
                $where = null;
                $where['url'] = array('in',$list2);
                
                //查询权限名称
                $list = Db::name('Menu')->where($where)->field('id,pid,title,url,tip,hide')->order('sort asc')->select();
            }
            foreach ($list as $key => $value) {
                if( stripos($value['url'],request()->module())!==0 ){
                    $list[$key]['url'] = request()->module().'/'.$value['url'];
                    $list[$key]['url'] = $value['url'];
                }
            }
            $nodes = list_to_tree($list,$pk='id',$pid='pid',$child='operator',$root=0);
            foreach ($nodes as $key => $value) {
                if(!empty($value['operator'])){
                    $nodes[$key]['child'] = $value['operator'];
                    unset($nodes[$key]['operator']);
                }
            }
        }else{
            $nodes = Db::name('Menu')->field('title,url,tip,pid')->order('sort asc')->select();

            foreach ($nodes as $key => $value) {
                if( stripos($value['url'],request()->module())!==0 ){
                    // $nodes[$key]['url'] = request()->module().'/'.$value['url'];
                    $nodes[$key]['url'] = $value['url'];
                }
            }
        }
        $tree_nodes[(int)$tree]   = $nodes;
        return $nodes;
    }
     /**
     * 获取控制器菜单数组,二级菜单元素位于一级菜单的'_child'元素中
     * @author 朱亚杰  <xcoolcc@gmail.com>
     */
    final public function getMenus($controller=''){
		//define('IS_ROOT',   is_administrator());
        if(config('ADMIN_MENU_LIST')){
            $menus  =   session('ADMIN_MENU_LIST'.$controller);//取菜单session
        }else{
            $menus = '';
        }
        if(empty($menus)){
            // 获取主菜单
            $where['pid']   =   0;
            $where['hide']  =   0;
            if(!config('DEVELOP_MODE')){ // 是否开发者模式
                $where['is_dev']    =   0;
            }
            $menus['main']  =   Db::name('Menu')->where($where)->order('sort asc')->select();
            foreach ($menus['main'] as $k => $v) {
            	$where['pid'] = $v['id'];
            	$menus['main'][$k]['child'] = Db::name('Menu')->where($where)->order('sort asc')->select();
            }
            // return $menus;
            $menus['child'] = array(); //设置子节点
            $AuthRule = Model('AuthRule');
            //高亮主菜单
            $current = Db::name('Menu')->where("url like '%{$controller}/".request()->action()."%'")->field('id')->find();
            if($current){
                $nav = Model('Menu')->getPath($current['id']);
                $nav_first_title = $nav[0]['title'];
                foreach ($menus['main'] as $key => $item) {

                    if (!is_array($item) || empty($item['title']) || empty($item['url']) ) {
                        $this->error('控制器基类$menus属性元素配置有误');
                    }
                    // if( stripos($item['url'],request()->module())!==0 ){
                    //     $item['url'] = request()->module().'/'.$item['url'];
                        
                    // }

                    // 判断主菜单权限
                    if ( !IS_ROOT && !$this->checkRule($item['url'],$AuthRule::RULE_MAIN,null) ) {

                        unset($menus['main'][$key]);
                        continue;//继续循环
                    }
                    // p($nav_first_title);
                    // 获取当前主菜单的子菜单项
                    if($item['title'] == $nav_first_title){
                        $menus['main'][$key]['class']='current';
                        //生成child树
                        $groups = Db::name('Menu')->where("pid = {$item['id']}")->distinct(true)->field("`group`")->select();
                        if($groups){
                            $groups = array_column($groups, 'group');
                        }else{
                            $groups =   array();
                        }

                        //获取二级分类的合法url
                        $where          =   array();
                        $where['pid']   =   $item['id'];
                        $where['hide']  =   0;
                        if(!config('DEVELOP_MODE')){ // 是否开发者模式
                            $where['is_dev']    =   0;
                        }
                        $second_urls = Db::name('Menu')->where($where)->column('id,url');
                        if(!IS_ROOT){
                            // 检测菜单权限
                            $to_check_urls = array();
                            foreach ($second_urls as $key=>$to_check_url) {
                                if( stripos($to_check_url,request()->module())!==0 ){
                                    $rule = request()->module().'/'.$to_check_url;
                                }else{
                                    $rule = $to_check_url;
                                }
                                if($this->checkRule($rule, $AuthRule::RULE_URL,null))
                                    $to_check_urls[] = $to_check_url;
                            }
                        }
                        // 按照分组生成子菜单树
                        foreach ($groups as $g) {
                            $map = array('group'=>$g);
                            if(isset($to_check_urls)){
                                if(empty($to_check_urls)){
                                    // 没有任何权限
                                    continue;
                                }else{
                                    $map['url'] = array('in', $to_check_urls);
                                }
                            }
                            $map['pid'] =   $item['id'];
                            $map['hide']    =   0;
                            if(!config('DEVELOP_MODE')){ // 是否开发者模式
                                $map['is_dev']  =   0;
                            }
                            $menuList = Db::name('Menu')->where($map)->field('id,pid,title,url,tip')->order('sort asc')->select();
                           
                            $menus['child'][$g] = list_to_tree($menuList, 'id', 'pid', 'operater', $item['id']);
                        }
                        if($menus['child'] === array()){
                            //$this->error('主菜单下缺少子菜单，请去系统=》后台菜单管理里添加');
                        }
                    }
                }
            }
            session('ADMIN_MENU_LIST'.$controller,$menus);//设计菜单session
        }
       
        return $menus;
    }
	/**
     * action访问控制,在 **登陆成功** 后执行的第一项权限检测任务
     *
     * @return boolean|null  返回值必须使用 `===` 进行判断
     *
     *   返回 **false**, 不允许任何人访问(超管除外)
     *   返回 **true**, 允许任何管理员访问,无需执行节点权限检测
     *   返回 **null**, 需要继续执行节点权限检测决定是否允许访问
     * @author 朱亚杰  <xcoolcc@gmail.com>
     */
    final protected function accessControl(){
        if(IS_ROOT){
            return true;//管理员允许访问任何页面
        }
        $allow = config('ALLOW_VISIT');
        $deny  = config('DENY_VISIT');
        $check = strtolower(request()->controller().'/'.request()->action());
        if ( !empty($deny)  && in_array_case($check,$deny) ) {
            return false;//非超管禁止访问deny中的方法
        }
        if ( !empty($allow) && in_array_case($check,$allow) ) {
            return true;
        }
        return null;//需要检测节点权限
    }
    /**
     * 检测是否是需要动态判断的权限
     * @return boolean|null
     *      返回true则表示当前访问有权限
     *      返回false则表示当前访问无权限
     *      返回null，则会进入checkRule根据节点授权判断权限
     *
     * @author 朱亚杰  <xcoolcc@gmail.com>
     */
    protected function checkDynamic(){
        if(IS_ROOT){
            return true;//管理员允许访问任何页面
        }
        return null;//不明,需checkRule
    }
     /**
     * 权限检测
     * @param string  $rule    检测的规则
     * @param string  $mode    check模式
     * @return boolean
     * @author 朱亚杰  <xcoolcc@gmail.com>
     */
    final protected function checkRule($rule, $type=AuthRule::RULE_URL, $mode='url'){
        
        if(IS_ROOT){
            return true;//管理员允许访问任何页面
        }
        static $Auth    =   null;
        if (!$Auth) {
            $Auth       =   Model('Auth');
        }
        if(!$Auth->check($rule,UID,$type,$mode)){
            return false;
        }
        return true;
    }
	// /**
	//  * [role_list 角色列表]
	//  * @return [type]        [description]
	//  */
	// public function role_list()
	// {
	// 	return view();
	// }
	// /**
	//  * [role_add 添加角色]
	//  * @return [type] [description]
	//  */
	// public function role_add()
	// {
	// 	if($_POST){
	// 		if(isset($_POST['rules'])){
 //            sort($_POST['rules']);
	//             $_POST['rules']  = implode( ',' , array_unique($_POST['rules']));
	//         }
	        
	//         //记录添加人uid
	//     	$uid  = session('user_id');
	// 	    $arr['cuid'] = $uid;
	        
	//         $arr['module'] =  'admin';

	//         $AuthGroup       =  Model('AuthGroup');
	//         $data['type']   =  $AuthGroup::TYPE_ADMIN;
	//         $data = $AuthGroup->create();
	//         if ( $data ) {
	//             if ( empty($data['id']) ) {
	//                 $r = $AuthGroup->add();
	//             }else{
	//                 $r = $AuthGroup->save();
	//             }
	//             if($r===false){
	//                 $this->error('操作失败'.$AuthGroup->getError());
	//             } else{
	//                 $this->success('操作成功!',U('index'));
	//             }
	//         }else{
	//             $this->error('操作失败'.$AuthGroup->getError());
	//         }
	// 	}else{
	// 		return view();
	// 	}
	// }
}