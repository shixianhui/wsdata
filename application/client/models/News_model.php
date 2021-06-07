<?php
class News_model extends CI_Model {

	private $_tableName = 'news';
	private $_menuTName = 'menu';

	public function __construct() {
		parent::__construct();
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
		$this->db->select("{$this->_tableName}.id, {$this->_tableName}.title, {$this->_tableName}.sort, {$this->_tableName}.hits, {$this->_tableName}.abstract, {$this->_tableName}.hits, {$this->_tableName}.author, {$this->_tableName}.path, {$this->_tableName}.add_time, {$this->_tableName}.category_id,{$this->_tableName}.batch_path_ids, {$this->_menuTName}.menu_name, {$this->_menuTName}.html_path");
		$this->db->order_by("{$this->_tableName}.sort", 'DESC');
		$this->db->order_by("{$this->_tableName}.id", 'DESC');
		$this->db->join($this->_menuTName, "{$this->_tableName}.category_id = {$this->_menuTName}.id");
		$query = $this->db->get_where($this->_tableName, $strWhere, $limit, $offset);
		if ($query->num_rows() > 0) {
			$ret = $query->result_array();
		}

		return $ret;
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
	
	public function getPrv($currentId, $menuId) {
		$this->db->select("{$this->_tableName}.id, {$this->_tableName}.title, {$this->_menuTName}.html_path");
		$this->db->order_by("{$this->_tableName}.id", 'ASC');
		$this->db->join($this->_menuTName, "{$this->_tableName}.category_id = {$this->_menuTName}.id");
		$query = $this->db->get_where($this->_tableName, "{$this->_tableName}.id > {$currentId} and {$this->_tableName}.category_id in ({$menuId}) and {$this->_tableName}.display=1 ");
		if ($query->num_rows() > 0) {
			$ret = $query->result_array();
			return $ret[0];
		}

		return array();
	}
	
	public function getNext($currentId, $menuId) {
		$this->db->select("{$this->_tableName}.id, {$this->_tableName}.title, {$this->_menuTName}.html_path");
		$this->db->order_by('id', 'DESC');
		$this->db->join($this->_menuTName, "{$this->_tableName}.category_id = {$this->_menuTName}.id");
		$query = $this->db->get_where($this->_tableName, "{$this->_tableName}.id < {$currentId} and {$this->_tableName}.category_id in ({$menuId}) and {$this->_tableName}.display=1 ");
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
/* End of file news_model.php */
/* Location: ./application/admin/models/news_model.php */