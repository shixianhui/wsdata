<?php
class Ws_data_model extends CI_Model {

	private $_tableName = 'ws_data';

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

    public function gets_join_category($select = '*',$strWhere = NULL, $limit = NULL, $offset = NULL, $by = 'ws_data.id', $order = 'DESC', $sort_order = NULL) {
		$ret = array();
		$this->db->select($select);
		$this->db->join('category as a', "a.id = {$this->_tableName}.category", 'left');
		$this->db->join('category as b', "b.id = {$this->_tableName}.category_1", 'left');
		$this->db->join('category as c', "c.id = {$this->_tableName}.category_2", 'left');
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

    public function count_join_category($strWhere = NULL)
    {
        $count = 0;
        $this->db->select("count(*) as 'count'");
        $this->db->join('category as a', "a.id = {$this->_tableName}.category", 'left');
		$this->db->join('category as b', "b.id = {$this->_tableName}.category_1", 'left');
		$this->db->join('category as c', "c.id = {$this->_tableName}.category_2", 'left');
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