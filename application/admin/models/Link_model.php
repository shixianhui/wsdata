<?php
class Link_model extends CI_Model {

	private $_tableName = 'link';
	private $_menuTName = 'menu';

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
		$this->db->select("{$this->_tableName}.*, {$this->_menuTName}.menu_name");
		$this->db->order_by("{$this->_tableName}.id", 'DESC');
		$this->db->join($this->_menuTName, "{$this->_menuTName}.id = {$this->_tableName}.category_id", 'left');
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
		if ($query->num_rows() > 0){
			$ret = $query->result_array();
			return $ret[0];
		}

		return $ret;
	}

    public function rowCount($strWhere = NULL) {
		$count = 0;
		$this->db->select("count(*) as 'count'");
		$this->db->join($this->_menuTName, "{$this->_menuTName}.id = {$this->_tableName}.category_id", 'left');
		$query = $this->db->get_where($this->_tableName, $strWhere);
	    if ($query->num_rows() > 0) {
			$ret = $query->result_array();
			$count = $ret[0]['count'];
		}

		return $count;
	}
}
/* End of file link_model.php */
/* Location: ./application/admin/models/link_model.php */