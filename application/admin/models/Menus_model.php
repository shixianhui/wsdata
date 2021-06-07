<?php
class Menus_model extends CI_Model {

	private $_tableName = 'menus';

	public function __construct() {
		 parent::__construct();
	}

    public function save($data, $where = NULL) {
		if (! empty($where)) {
			$ret = $this->db->update($this->_tableName, $data, $where);
		} else {
			$this->db->insert($this->_tableName, $data);
			$ret = $this->db->insert_id();
		}
		return $ret > 0 ? $ret : FALSE;
	}

    public function save_batch($data) {
        $ret = $this->db->insert_batch($this->_tableName, $data);
        return $ret;
    }

    public function save_column($column, $data, $where = NULL) {
        $ret = 0;

        if (!empty($where)) {
            $this->db->set($column, $data, FALSE);
            $this->db->where($where);
            $ret = $this->db->update($this->_tableName);
        }

        return $ret > 0 ? $ret : FALSE;
    }

	public function delete($where = '') {
		return $this->db->delete($this->_tableName, $where) > 0 ? TRUE : FALSE;
	}

    public function gets($select = '*',$strWhere = NULL, $limit = NULL, $offset = NULL, $by = 'id', $order = 'ASC', $sort_order = NULL) {
		$ret = array();
		$this->db->select($select, false);
        if ($sort_order) {
            $this->db->order_by("sort", $sort_order);
        }
		$this->db->order_by($by, $order);
		$query = $this->db->get_where($this->_tableName, $strWhere, $limit, $offset);
		if ($query->num_rows() > 0) {
			$ret = $query->result_array();
		}
        return $ret;
	}

    public function get($select = '*', $strWhere = NULL) {
		$ret = array();
		$this->db->select($select);
        $this->db->order_by('id', 'DESC');
        $query = $this->db->get_where($this->_tableName, $strWhere);
		if ($query->num_rows() > 0){
			$ret = $query->result_array();
			return $ret[0];
		}
		return $ret;
	}

    public function count($strWhere = NULL)
    {
        $count = 0;
        $this->db->select("count(*) as 'count'");
        $query = $this->db->get_where($this->_tableName, $strWhere);
        if ($query->num_rows() > 0) {
            $ret = $query->result_array();
            $count = $ret[0]['count'];
        }
        return $count;
    }


    public function menu_tree($strWhere = NULL) {
        $menu_list = $this->gets($strWhere);
        $parent_list = array();
        foreach ($menu_list as $menu) {
            if ($menu['parent_id'] == 0){
                $parent_list[] = $menu;
            }
        }
        if ($parent_list){
            foreach ($parent_list as $key => $value){
                $sub_list = array();
                foreach ($menu_list as $menu){
                    if ($menu['parent_id'] == $value['id']){
                        $sub_list[] = $menu;
                    }
                }
                if ($sub_list){
                    foreach ($sub_list as $sKey => $sValue){
                        $sub_list_2 = array();
                        foreach ($menu_list as $menu){
                            if ($menu['parent_id'] == $sValue['id']){
                                $sub_list_2[] = $menu;
                            }
                        }
                        $sub_list[$sKey]['children'] = $sub_list_2;
                    }
                }
                $parent_list[$key]['children'] = $sub_list;
            }
        }

        return $parent_list;
    }

    //子孙树
    public function get_sub_tree($id = 0) {
        $data = $this->gets('*', NULL, NULL, NULL, 'id', 'ASC', 'ASC');
        $task = array($id);                          # 栈 任务表
        $son = array();

        if ($data) {
            //上级ids
            foreach ($data as $key => $value){
                $ancestry_ids = $this->ancestry($data,$value['id']);
                $data[$key]['ancestry_ids'] = $ancestry_ids;
                //是否有下级
                $data[$key]['is_ancestry'] = 1;
                $data[$key]['open'] = true;  // 默认展开
            }

            while(!empty($task)) {
                $flag = false;                           # 是否找到节点标志
                foreach($data as $k => $v) {

                    # 判断是否是子孙节点的条件 与 递归方式一致
                    if($v['parent_id'] == $id) {
                        $v['level'] = count($task);      # 节点等级
                        $son[] = $v;                     # 节点存入数组
                        array_push($task , $v['id']);    # 节点id入栈
                        $id = $v['id'];                  # 判断条件切换
                        unset($data[$k]);                # 删除节点
                        $flag = true;                    # 找到节点标志
                    }
                }

                # flag == false说明已经到了叶子节点 无子孙节点了
                if($flag == false) {
                    //添加无下级的标志
                    $son[count($son) - 1]['is_ancestry'] = 0;
                    array_pop($task);                    # 出栈
                    $id = end($task);                    # 寻找栈顶id的子节点
                }
            }
        }
        
        return $son;
    }

    //获取祖先ids(包含本身)
    public function ancestry($data , $pid) {
        $ancestry = array();

        while($pid > 0) {
            foreach($data as $v) {
                if($v['id'] == $pid) {
                    $ancestry[] = $v['id'];
                    $pid = $v['parent_id'];
                }
            }
        }

        $ancestry_ids = implode(',',array_reverse($ancestry));
        return $ancestry_ids;
    }

    //引用算法树状数据
    public function generateTree($array){
        //第一步 构造数据
        $items = array();
        foreach($array as $value){
            $items[$value['id']] = $value;
        }
        //第二部 遍历数据 生成树状结构
        $tree = array();
        foreach($items as $key => $value){
            if(isset($items[$value['parent_id']])){
                //注意 这里传递的是引用
                $items[$value['parent_id']]['children'][] = &$items[$key];
            }else{
                $tree[] = &$items[$key];
            }
        }
        return $tree;
    }

    public function getMenuList($array){
        //第一步 构造数据
        $items = array();
        foreach($array as $key=>$value){
            $value['icon'] = 'fa '.$value['icon'];
            $items[$value['id']] = $value;
        }
        //第二部 遍历数据 生成树状结构
        $tree = array();
        foreach($items as $key => $value){
            if(isset($items[$value['parent_id']])){
                $items[$key]['href'] = 'admincp.php/'.$value['href'];
                $items[$value['parent_id']]['href'] = '';
                //注意 这里传递的是引用
                $items[$value['parent_id']]['child'][] = &$items[$key];
            }else{
                $items[$key]['href'] = '';
                $tree[] = &$items[$key];
            }
        }
        return $tree;
    }

    /**
     * 树状数据转一维数组
     */
    public function tree_to_one($array) {
        if (!is_array($array)) {
            return [];
        }
        static $one_arr = [];
        foreach ($array as $key=>$value) {
            if (isset($value['children']) && is_array($value['children'])) {
                unset($array[$key]['children']);
                $one_arr[] = $array[$key];
                $this->tree_to_one($value['children']);
            } else {
                $one_arr[] = $value;
            }
        }
        return $one_arr;
    }

}
/* End of file advertising_model.php */
/* Location: ./application/admin/models/advertising_model.php */