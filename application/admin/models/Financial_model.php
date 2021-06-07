<?php
class Financial_model extends CI_Model {

	private $_tableName = 'financial';

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

    public function gets($strWhere = NULL, $limit = NULL, $offset = NULL) {
		$ret = array();
		$this->db->select("*");
		$this->db->order_by('id', 'DESC');
		$query = $this->db->get_where($this->_tableName, $strWhere, $limit, $offset);
		if ($query->num_rows() > 0){
			$ret = $query->result_array();
		}

		return $ret;
	}

    public function get($strWhere = NULL) {
		$ret = array();
		$this->db->select('*');
		$query = $this->db->get_where($this->_tableName, $strWhere);
		if ($query->num_rows() > 0){
			$ret = $query->result_array();
			return $ret[0];
		}

		return $ret;
	}

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

    public function get_total($strWhere = NULL) {
        $this->db->select("sum(price) as 'sum_price'");
        $query = $this->db->get_where($this->_tableName, $strWhere);
        if ($query->num_rows() > 0) {
            $ret = $query->result_array();
            return $ret[0]['sum_price'] ? $ret[0]['sum_price'] : 0;
        }
        return 0;
    }
}
/* End of file admin_model.php */
/* Location: ./application/admin/models/admin_model.php */