<?php
class Systemloginlog_model extends CI_Model {

	private $_tableName = 'system_login_log';
	private $_adminTName = 'admin';

	public function __construct() {
		parent::__construct ();
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
		if (! empty ( $where )) {
			$ret = $this->db->update ( $this->_tableName, $data, $where );
		} else {
			$this->db->insert ( $this->_tableName, $data );
			$ret = $this->db->insert_id ();
		}

		return $ret > 0 ? TRUE : FALSE;
	}

	/**
	 * select info
	 *
	 * @param $select is string
	 * @param $strWhere is string
	 * @return array
	 */
	public function get($select = '*', $strWhere = NULL) {
		$this->db->select($select);
		$query = $this->db->get_where($this->_tableName, $strWhere);
		if ($query->num_rows() > 0) {
			$ret = $query->result_array();
			return $ret[0];
		}

		return array();
	}
	
	public function gets($strWhere = NULL, $limit = NULL, $offset = NULL) {
	    $ret = array();
	    $this->db->select("{$this->_tableName}.*, {$this->_adminTName}.username");
	    $this->db->order_by("{$this->_tableName}.id", 'DESC');
	    $this->db->join($this->_adminTName, "{$this->_tableName}.admin_id = {$this->_adminTName}.id");
	    $query = $this->db->get_where($this->_tableName, $strWhere, $limit, $offset);
	    if ($query->num_rows() > 0) {
	        $ret = $query->result_array();
	    }
	
	    return $ret;
	}
	
	public function rowCount($strWhere = NULL) {
	    $count = 0;
	    $this->db->select("count(*) as 'count'");
	    $this->db->join($this->_adminTName, "{$this->_tableName}.admin_id = {$this->_adminTName}.id");
	    $query = $this->db->get_where($this->_tableName, $strWhere);
	    if ($query->num_rows() > 0) {
	        $ret = $query->result_array();
	        $count = $ret[0]['count'];
	    }
	
	    return $count;
	}
}
/* End of file product_model.php */
/* Location: ./application/admin/models/product_model.php */