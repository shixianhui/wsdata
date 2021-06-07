<?php
class Page_model extends CI_Model {

	private $_tableName = 'page';
	private $_menuTName = 'menu';

	public function __construct() {
		parent::__construct ();
	}

	/**
	 * select data
	 *
	 * @param $strWhere is string
	 * @param $limit is int
	 * @param $offset is int
	 * @return array
	 */
	public function gets($strWhere = NULL, $limit = NULL, $offset = NULL) {
		$ret = array();
		$this->db->select("{$this->_tableName}.*");
		$this->db->order_by("{$this->_tableName}.sort", 'ASC');
		$this->db->order_by("{$this->_tableName}.id", 'DESC');
		$this->db->join($this->_menuTName, "{$this->_tableName}.category_id = {$this->_menuTName}.id");
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
}
/* End of file product_model.php */
/* Location: ./application/admin/models/product_model.php */