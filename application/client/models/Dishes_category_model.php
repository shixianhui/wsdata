<?php
class Dishes_category_model extends CI_Model {

	private $_tableName = 'dishes_category';

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

    public function gets($select = NULL,$strWhere = NULL, $limit = NULL, $offset = NULL, $by = 'id', $order = 'DESC') {
		$ret = array();
		$this->db->select($select);
		$this->db->order_by($by, $order);
		$query = $this->db->get_where($this->_tableName, $strWhere, $limit, $offset);
		if ($query->num_rows() > 0) {
			$ret = $query->result_array();
		}
        return $ret;
	}


	public function menu_tree() {
        $whereArray = array('display' => 1);
        $menu_list = $this->gets('*', $whereArray);
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
                        $sub_list[$sKey]['subMenuList'] = $sub_list_2;
                    }
                }
                $parent_list[$key]['subMenuList'] = $sub_list;
            }
        }

        return $parent_list;
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
}
/* End of file advertising_model.php */
/* Location: ./application/admin/models/advertising_model.php */