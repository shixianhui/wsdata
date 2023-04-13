<?php
class Category_model extends CI_Model {

	private $_tableName = 'category';

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

    public function gets($select = '*',$strWhere = NULL, $limit = NULL, $offset = NULL, $by = 'id', $order = 'DESC', $sort_order = NULL) {
		$ret = array();
		$this->db->select($select);
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

    public function get($select = '*', $strWhere = NULL, $by = 'id', $order = 'DESC') {
		$ret = array();
		$this->db->select($select);
		$this->db->order_by($by, $order);
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

    public function sum($column = 'draw_number',$strWhere = NULL) {
        $count = 0;
        $this->db->select("IFNULL(sum($column),0) as 'count'", FALSE);
        $query = $this->db->get_where($this->_tableName, $strWhere);
        if ($query->num_rows() > 0) {
            $ret = $query->result_array();
            $count = $ret[0]['count'];
        }

        return $count;
    }

    public function ancestry($pid) {
        $data = $this->gets();
        $ancestry = array();

        while($pid > 0) {
            foreach($data as $v) {
                if($v['id'] == $pid) {
                    $ancestry[] = $v['name'];
                    $pid = $v['parent_id'];
                }
            }
        }

        $ancestry_ids = $ancestry ? implode(',',array_reverse($ancestry)) : '';
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
}
/* End of file advertising_model.php */
/* Location: ./application/admin/models/advertising_model.php */