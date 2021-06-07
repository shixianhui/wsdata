<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 多次调用的方法集成
 *
 */
class Advdbclass {	
	//获取友情链接
	public function getLink() {
		$CI = & get_instance();
		$CI->load->model('Link_model', '', TRUE);
		
	    return $CI->Link_model->gets('*', "display = 1 ");
	}
	
    //获取头部的栏目
	public function getHeadMenu() {
	    $CI = & get_instance();
		$CI->load->model('Menu_model', '', TRUE);
		$headWhere = "parent = 0 and hide = 0 and (position like 'head%' or position like '%,head' or position like '%,head,%')";
		
		return $CI->Menu_model->gets('id, menu_name, model, html_path, template, menu_type, url, cover_function, list_function, detail_function, en_menu_name', $headWhere);
	}
	
    //获取导航栏目的栏目
	public function getNavigationMenu() {
	    $CI = & get_instance();
		$CI->load->model('Menu_model', '', TRUE);
		
		return $CI->Menu_model->menuTree('id, menu_name, model, html_path, template, menu_type, url, cover_function, list_function, detail_function, en_menu_name', 0);
	}
	
	//获取版权的栏目
	public function getFooterMenu() {
	    $CI = & get_instance();
		$CI->load->model('Menu_model', '', TRUE);
		$footerWhere = "parent = 0 and hide = 0 and (position like 'footer%' or position like '%,footer' or position like '%,footer,%')";
		
		return $CI->Menu_model->gets('id, menu_name, model, html_path, template, menu_type, url, cover_function, list_function, detail_function, en_menu_name', $footerWhere);
	}
	
	//获取版权的栏目
	public function getMenuList($id = NULL) {
	    $CI = & get_instance();
	    $CI->load->model('Menu_model', '', TRUE);
	     
	    return $CI->Menu_model->gets('id, menu_name, model, html_path, template, menu_type, url, cover_function, list_function, detail_function, en_menu_name', array('parent'=>$id, 'hide'=>0));
	}
	
    /**
     * 获取分类,分类ID为已知
     * 
     * @param unknown_type $menuId 分类ID
     * @param unknown_type $is_all 1=显示所有；0=只显示显示的
     * @param unknown_type $num    数量
     */
	public function getMenuClass($menuId = NULL, $num = 100, $is_all = 1) {
	    $CI = & get_instance();
		$CI->load->model('Menu_model', '', TRUE);
		$parentId = $CI->Menu_model->getParentMenuId($menuId);

		return $CI->Menu_model->getChildMenuTree('id, menu_name, model, html_path, template, menu_type, url, cover_function, list_function, detail_function, en_menu_name', $parentId, $num, $is_all);
    }
    
    //获取产品分类,分类ID为已知
	public function getMenuInfo($menuId) {
	    $CI = & get_instance();
		$CI->load->model('Menu_model', '', TRUE);
		$parentId = $CI->Menu_model->getParentMenuId($menuId);

		return $CI->Menu_model->get('menu_name', array('id'=>$parentId));
    }
    
	/**
	 * 获取广告
	 * 
	 * @param unknown_type $id   分类ID
	 * @param unknown_type $num  数量
	 */
    public function getAd($id, $num = 10) {
        $CI = & get_instance();
        $CI->load->model('Ad_model', '', TRUE);
        
        return $CI->Ad_model->gets('path, url, ad_text', array('category_id'=>$id, 'ad_type'=>'image', 'enable'=>1), $num, 0);
	}
	
    /**
     * 栏目更多内容的写法
     * 
     * @param unknown_type $menuId
     * @param unknown_type $isHtml
     * @param unknown_type $client_index
     */
	public function getMenuUrl($menuId, $isHtml = false, $client_index = '') {
		$CI = & get_instance();
		$CI->load->model('Menu_model', '', TRUE);
		$url = '';
		
	    $menuInfo = $CI->Menu_model->get('id, html_path, menu_type, template', array('id'=>$menuId));
	    if ($menuInfo) {
		    if ($menuInfo['menu_type'] == '3') {
	    		$url = $menuInfo['url'];
	    	} else {
		    	if ($isHtml) {
				    $url = $menuInfo['html_path']."/index{$menuInfo['id']}.html";
				} else {
				    $url = $client_index;
		    	    $url .= $client_index?'/':'';
		    	    $url .= "{$menuInfo['template']}/index/{$menuInfo['id']}.html";
				}
	    	}
	    }
    	
    	return $url;
	}
	
    /**
     * 单页面内容
     * @param $menuId 分类ID
     * @param $num   数量
     */
    public function getPageList($menuId, $num = 1) {
		$CI = & get_instance();
		$CI->load->model('Menu_model', '', TRUE);
		$CI->load->model('Page_model', '', TRUE);
		$url = '';
		$strWhere = "page.display = 1 and page.category_id = {$menuId} ";
		
    	return $CI->Page_model->gets($strWhere, $num, 0);
	}
	
    //获取关键词
    public function getKeyword() {
	    $CI = & get_instance();
		$CI->load->model('System_model', '', TRUE);
		
		return $CI->System_model->get('globle_qq_service', array('id'=>1));
	}
	
    /**
     * 全局内容调用
     * 
     * @param $table     模型-news=新闻；cases=案例；teacher=优秀导师；team=优秀团队；download=软件下载；product=产品；video=视频；picture=图库；
     * @param $menuId    分类ID
     * @param $type      属性-头条[h]；推荐[c];特荐[a]；幻灯[f]；滚动[s]；加粗[b]；图片[p]；跳转[j] 
     * @param $is_image  1=仅调用图片
     * @param $num       数量
     */
     public function get_cus_list($table = 'news', $menuId, $type = 'c', $is_image = 0, $num = 10) {
		$CI = & get_instance();
		$tmp_obj = ucfirst($table).'_model';
		$CI->load->model('Menu_model', '', TRUE);
		$CI->load->model(ucfirst($table).'_model', $tmp_obj, TRUE);
		$url = '';
		$ids = $CI->Menu_model->getChildMenus($menuId);
		$strWhere = "{$table}.display = 1 and {$table}.category_id in ({$ids})";
        if ($type) {
		    $strWhere .= " and ({$table}.custom_attribute like '%{$type}' or {$table}.custom_attribute like '{$type}%' or {$table}.custom_attribute like '%,{$type},%') ";
		}
		if ($is_image) {
		    $strWhere .= " and {$table}.path <> ''  ";
		}
		
    	return $CI->$tmp_obj->gets($strWhere, $num, 0);
	}

	public function getPermissionsStr($adminGroupId = NULL) {
        $ret = false;
        $CI = & get_instance();
        $CI->load->model('Seller_group_model', '', TRUE);
        return $CI->Seller_group_model->get('permissions', array('id'=>$adminGroupId));
    }

    //获取权限
    public function getPermissions($adminGroupId = NULL, $permissionsItem = NULL) {
        $ret = false;
        $CI = & get_instance();
        $CI->load->model('Seller_group_model', '', TRUE);
        $admingroupInfo = $CI->Seller_group_model->get('permissions', array('id'=>$adminGroupId));
        if ($admingroupInfo) {
            $permissionArr = explode(',', $admingroupInfo['permissions']);
            if (in_array($permissionsItem, $permissionArr)) {
                $ret = true;
            }
        }

        return $ret;
    }

}
// END Validateloginclass class

/* End of file Validateloginclass.php */
/* Location: ./system/libraries/Validateloginclass.php */