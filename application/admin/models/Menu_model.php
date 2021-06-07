<?php
class Menu_model extends CI_Model {

	private $_tableName = 'menu';

    public function __construct() {
		parent::__construct();
	}

	public function save($data, $where = NULL) {
		$ret = 0;

		if (! empty($where)) {
			$ret = $this->db->update($this->_tableName, $data, $where);
		} else {
			$this->db->insert($this->_tableName, $data);
			$ret = $this->db->insert_id();
		}

		return $ret > 0 ? TRUE : FALSE;
	}

	public function delete($where = '') {
		return $this->db->delete($this->_tableName, $where) > 0 ? TRUE : FALSE;
	}

	public function gets($select = '*', $strWhere = NULL) {
		$ret = array();
		$this->db->select($select);
		$this->db->order_by('sort', 'ASC');
		$this->db->order_by('id', 'ASC');
		$query = $this->db->get_where($this->_tableName, $strWhere);
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

    public function menuTree($select = '*', $model = NULL) {
		//一级
		$whereArray1 = array('parent'=>0);
		if (! empty($model)) {
		    $whereArray1['model'] = $model;
		}
	    $menuList1 = $this->gets($select, $whereArray1);
	    foreach ($menuList1 as $key1=>$value1) {
	    	$whereArray2 = array('parent'=>$value1['id']);
	    	if (! empty($model)) {
	    	    $whereArray2['model'] = $model;
	    	}
	    	//二级
	        $menuList2 = $this->gets($select, $whereArray2);
	        foreach ($menuList2 as $key2=>$value2) {
	        	$whereArray3 = array('parent'=>$value2['id']);
		        if (! empty($model)) {
		    	    $whereArray3['model'] = $model;
		    	}
		    	//三级
	            $menuList3 = $this->gets($select, $whereArray3);
	            foreach ($menuList3 as $key3=>$value3) {
		            $whereArray4 = array('parent'=>$value3['id']);
			        if (! empty($model)) {
			    	    $whereArray4['model'] = $model;
			    	}
			    	//四级
			    	$menuList4 = $this->gets($select, $whereArray4);
			    	foreach ($menuList4  as $key4=>$value4) {
				    	$whereArray5 = array('parent'=>$value4['id']);
				        if (! empty($model)) {
				    	    $whereArray5['model'] = $model;
				    	}
				    	//五级
				    	$menuList5 = $this->gets($select, $whereArray5);
				    	$menuList4[$key4]['subMenuList'] = $menuList5;
			    	}
			    	$menuList3[$key3]['subMenuList'] = $menuList4;
	            }
	            $menuList2[$key2]['subMenuList'] = $menuList3;
	        }
	        $menuList1[$key1]['subMenuList'] = $menuList2;
	    }

	    return $menuList1;
	}

	//查子级
	public function getChildMenu($id) {
		$menuList = $this->gets('id', array('parent'=>$id));

		return $menuList?TRUE:FALSE;
	}
	
   /**
	 *
	 * 获取所有的menu id
	 * @param int $id 上级id
	 * return string
	 */
	public function getChildIds($id) {
		$ids = $id.',';
		$menuList5 = $this->gets('id', array('parent'=>$id));
		foreach ($menuList5 as $menu5) {
		    $ids .= $menu5['id'].',';
		    $menuList4 = $this->gets('id', array('parent'=>$menu5['id']));
		    foreach ($menuList4 as $menu4) {
		    	$ids .= $menu4['id'].',';
		    	$menuList3 = $this->gets('id', array('parent'=>$menu4['id']));
		    	foreach ($menuList3 as $menu3) {
		    	    $ids .= $menu3['id'].',';
		    	    $menuList2 = $this->gets('id', array('parent'=>$menu3['id']));
		    	    foreach ($menuList2 as $menu2) {
		    	        $ids .= $menu2['id'].',';
		    	    }
		    	}
		    }
		}

		return substr($ids, 0, -1);
	}
	
	//查是否有文章内容
	public function getArticle($id) {
		$tmp_ids = $this->getChildIds($id);
		$count = 0;
		$menuInfo = $this->get('model', array('id'=>$id));
		if ($menuInfo && $menuInfo['model']) {
			if ($menuInfo['model'] != 'guestbook' && $menuInfo['model'] != 'sitemap' && $menuInfo['model'] != 'link') {
				$this->db->select("count(*) as 'count'");				
				$query = $this->db->get_where($menuInfo['model'], "category_id in ({$tmp_ids})");
				if ($query->num_rows() > 0) {
					$ret = $query->result_array();
					$count = $ret[0]['count'];
				}
			}
		}
		
		return $count;
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
	
   /**
	 * select
	 *
	 * @param $strWhere is string
	 * @return int
	 */
	public function rowCount($strWhere = NULL) {
		$count = 0;
		$this->db->select("count(*) as 'count'");
		$query = $this->db->get_where($this->_tableName, $strWhere);
	    if ($query->num_rows() > 0) {
			$ret = $query->result_array();
			$count = $ret[0]['count'];
		}

		return $count;
	}
}
/* End of file menu_model.php */
/* Location: ./application/admin/models/menu_model.php */