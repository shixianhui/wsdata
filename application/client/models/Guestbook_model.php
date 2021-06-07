<?php
class Guestbook_model extends CI_Model {

	private $_tableName = 'guestbook';

	public function __construct() {
		parent::__construct();
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
		if (! empty($where)) {
			$ret = $this->db->update($this->_tableName, $data, $where);
		} else {
			$this->db->insert($this->_tableName, $data);
			$ret = $this->db->insert_id();
		}

		return $ret > 0 ? TRUE : FALSE;
	}

	/**
	 * select data
	 *
	 * @param $strWhere is string
	 * @param $limit is int
	 * @param $offset is int
	 * @return array
	 */
	public function gets($select = '*', $strWhere = NULL, $limit = NULL, $offset = NULL) {
		$ret = array();
		$this->db->select($select);
		$this->db->order_by('id', 'DESC');
		$query = $this->db->get_where($this->_tableName, $strWhere, $limit, $offset);
		if ($query->num_rows() > 0) {
			$ret = $query->result_array();
		}

		return $ret;
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
/* End of file news_model.php */
/* Location: ./application/admin/models/news_model.php */