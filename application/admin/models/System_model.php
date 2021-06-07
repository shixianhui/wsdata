<?php
class System_model extends CI_Model {

	private $_tableName = 'system';

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
}
/* End of file product_model.php */
/* Location: ./application/admin/models/product_model.php */