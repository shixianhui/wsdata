<?php

class Activity_model extends CI_Model {

    private $_tableName = 'activity';
    private $_userTName = 'user';

    public function __construct() {
        parent::__construct();
    }

    public function gets($strWhere = NULL, $limit = NULL, $offset = NULL) {
        $ret = array();
        $this->db->select("{$this->_tableName}.*, {$this->_userTName}.real_name, {$this->_userTName}.nickname");
        $this->db->join($this->_userTName, "{$this->_userTName}.id = {$this->_tableName}.user_id");
        $this->db->order_by("{$this->_tableName}.id", 'DESC');
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
        if ($query->num_rows() > 0) {
            $ret = $query->result_array();
            return $ret[0];
        }

        return $ret;
    }

    public function get2($strWhere = NULL, $limit = NULL, $offset = NULL) {
        $ret = array();
        $this->db->select("{$this->_tableName}.*, {$this->_userTName}.real_name, {$this->_userTName}.nickname, {$this->_userTName}.is_id_card_auth");
        $this->db->join($this->_userTName, "{$this->_userTName}.id = {$this->_tableName}.user_id");
        $this->db->order_by("{$this->_tableName}.id", 'DESC');
        $query = $this->db->get_where($this->_tableName, $strWhere, $limit, $offset);
        if ($query->num_rows() > 0) {
            $ret = $query->result_array();
            return $ret[0];
        }

        return $ret;
    }

    /**
     * save data
     *
     * @param $data is array
     * @param $where is array or string
     * @return boolean
     */
    public function save($data, $where = NULL) {
        $ret = 0;
        if (!empty($where)) {
            $ret = $this->db->update($this->_tableName, $data, $where);
        } else {
            $this->db->insert($this->_tableName, $data);
            $ret = $this->db->insert_id();
        }

        return $ret > 0 ? $ret : FALSE;
    }

    public function save2($column, $data, $where = NULL) {
        $ret = 0;

        if (!empty($where)) {
            $this->db->set($column, $data, FALSE);
            $this->db->where($where);
            $ret = $this->db->update($this->_tableName);
        }

        return $ret > 0 ? $ret : FALSE;
    }

    public function get_max_id($strWhere = NULL) {
        $this->db->select("max({$this->_tableName}.id) as 'max_id'");
        $query = $this->db->get_where($this->_tableName, $strWhere);
        if ($query->num_rows() > 0) {
            $ret = $query->result_array();
            return $ret[0]['max_id'] ? $ret[0]['max_id'] : 0;
        }
        return 0;
    }

    public function rowCount($strWhere = NULL) {
        $count = 0;
        $this->db->select("count(*) as 'count'");
        $this->db->join($this->_userTName, "{$this->_userTName}.id = {$this->_tableName}.user_id");
        $query = $this->db->get_where($this->_tableName, $strWhere);
        if ($query->num_rows() > 0) {
            $ret = $query->result_array();
            $count = $ret[0]['count'];
        }

        return $count;
    }

    /**
     * delete data
     *
     * @param $where is array or string
     * @return boolean
     */
    public function delete($where = '') {
        return $this->db->delete($this->_tableName, $where) > 0 ? TRUE : FALSE;
    }

    public function get_Total($strWhere = NULL) {
    	$this->db->select("sum({$this->_tableName}.total) as 'sum_total'");
    	$query = $this->db->get_where($this->_tableName, $strWhere);
    	if ($query->num_rows() > 0) {
    		$ret = $query->result_array();
    		return $ret[0]['sum_total'] ? $ret[0]['sum_total'] : 0;
    	}
    	return 0;
    }
}

/* End of file advertising_model.php */
/* Location: ./application/admin/models/advertising_model.php */