<?php
class Menu_model extends CI_Model {

	private $_tableName = 'menu';

    public function __construct() {
		parent::__construct();
	}

	public function gets($select = '*', $strWhere = NULL, $limit = NULL, $offset = NULL) {
		$ret = array();
		$this->db->select($select);
		$this->db->order_by('sort', 'ASC');
		$query = $this->db->get_where($this->_tableName, $strWhere, $limit, $offset);
		if ($query->num_rows() > 0) {
			$ret = $query->result_array();
		}

		return $ret;
	}

	public function get($select = '*', $strWhere = NULL) {
		$ret = array();
		$this->db->select($select);
		$query = $this->db->get_where($this->_tableName, $strWhere);
		if ($query->num_rows() > 0){
			$ret = $query->result_array();
			return $ret[0];
		}

		return $ret;
	}

	/**
	 *
	 * 获取所有的menu id
	 * @param int $id 上级id
	 * return string
	 */
	public function getChildMenus($id) {
		$ids = $id.',';
		$menuList = $this->gets('id', array('parent'=>$id));
		if ($menuList) {
		    foreach ($menuList as $menu) {
		    	$ids .= $menu['id'].',';
		    	$subMenuList = $this->gets('id', array('parent'=>$menu['id']));
		    	if ($subMenuList) {
		    		foreach ($subMenuList as $subMenu) {
		    			$ids .= $subMenu['id'].',';
		    		}
		    	}
		    }
		}

		return substr($ids, 0, -1);
	}

	/**
	 * 获取一级栏目id
	 *
	 * @param int $childId 子栏目
	 * return id int
	 */
	public function getParentMenuId($childId) {
		$id = $childId;
		while (true) {
			$menuInfo = $this->get('id, parent', array('id'=>$id));
			if ($menuInfo) {
				if ($menuInfo['parent'] == 0) {
					return $menuInfo['id'];
				} else {
					$id = $menuInfo['parent'];
				}
			} else {
				return NULL;
			}
		}
	}

	//获取导航栏树结构
    public function menuTree($select = '*', $parentId = NULL) {
    	$strWhere = "hide = 0 and (position like 'navigation%' or position like '%,navigation' or position like '%,navigation,%')";
	    $menuList = $this->gets($select, $strWhere." and parent = {$parentId}");
	    foreach ($menuList as $key=>$value) {
	        $subMenuList = $this->gets($select, $strWhere." and parent = {$value['id']}");
	        foreach ($subMenuList as $sKey=>$sValue) {
	            $sSubMenuList = $this->gets($select, $strWhere." and parent = {$sValue['id']}");
	            $subMenuList[$sKey]['subMenuList'] = $sSubMenuList;
	        }
	        $menuList[$key]['subMenuList'] = $subMenuList;
	    }

	    return $menuList;
	}

    //获取网站地图
    public function getSitemap($select = '*', $parentId = NULL) {
    	$menuList = $this->gets($select, array('parent'=>$parentId));
	    foreach ($menuList as $key=>$value) {
	        $subMenuList = $this->gets($select, array('parent'=>$value['id']));
	        foreach ($subMenuList as $sKey=>$sValue) {
	            $sSubMenuList = $this->gets($select, array('parent'=>$sValue['id']));
	            $subMenuList[$sKey]['subMenuList'] = $sSubMenuList;
	        }
	        $menuList[$key]['subMenuList'] = $subMenuList;
	    }

	    return $menuList;
	}

    //获取子级栏目树结构,不包括父级
    public function getChildMenuTree($select = '*', $parentId = NULL, $num = 100, $is_all = 1) {
    	$retList = array();
        $whereArray['id'] = $parentId;
        if (!$is_all) {
            $whereArray['hide'] = 0;
        }
	    $menuList = $this->gets($select, $whereArray, $num, 0);
	    foreach ($menuList as $key=>$value) {
	    	$subWhereArray = array('parent'=>$value['id']);
	    	if (!$is_all) {
	    	    $subWhereArray['hide'] = 0;
	    	}
	        $subMenuList = $this->gets($select, $subWhereArray, $num, 0);
	        foreach ($subMenuList as $sKey=>$sValue) {
	        	$sSubWhereArray = array('parent'=>$sValue['id']);
	        	if (!$is_all) {
	        	    $sSubWhereArray['hide'] = 0;
	        	}
	            $sSubMenuList = $this->gets($select, $sSubWhereArray, $num, 0);
	            $subMenuList[$sKey]['subMenuList'] = $sSubMenuList;
	        }
	        $retList = $subMenuList;
	    }

	    return $retList;
	}

	public function getLocation($id = NULL, $html = false, $url = '') {
		$str = '';
	    if ($id) {
	        $info = $this->get('id, parent, menu_name, html_path, template', array('id'=>$id));
	        if (! $info) {
	            return $str;
	        }
	        if ($html) {
	        	$str = "<a href='{$info['html_path']}/index{$info['id']}.html'>{$info['menu_name']}</a>&nbsp;&gt;&nbsp;";
	        } else {
	            $str = "<a href='{$url}{$info['template']}/index/{$info['id']}.html'>{$info['menu_name']}</a>&nbsp;&gt;&nbsp;";
	        }
	        if ($info['parent'] == 0) {
	            return $str;
	        }
	        $info = $this->get('id, parent, menu_name, html_path, template', array('id'=>$info['parent']));
		    if ($html) {
		        $str = "<a href='{$info['html_path']}/index{$info['id']}.html'>{$info['menu_name']}</a>&nbsp;&gt;&nbsp;".$str;
		    } else {
		        $str = "<a href='{$url}{$info['template']}/index/{$info['id']}.html'>{$info['menu_name']}</a>&nbsp;&gt;&nbsp;".$str;
		    }
	        if ($info['parent'] == 0) {
	            return $str;
	        }
	        //三级
	        $info = $this->get('id, parent, menu_name, html_path, template', array('id'=>$info['parent']));
		    if ($html) {
		        $str = "<a href='{$info['html_path']}/index{$info['id']}.html'>{$info['menu_name']}</a>&nbsp;&gt;&nbsp;".$str;
		    } else {
		        $str = "<a href='{$url}{$info['template']}/index/{$info['id']}.html'>{$info['menu_name']}</a>&nbsp;&gt;&nbsp;".$str;
		    }
	    }

	    return $str;
	}
}
/* End of file menu_model.php */
/* Location: ./application/admin/models/menu_model.php */