<?php
class News_model extends CI_Model {

	private $_tableName = 'news';
	private $_menuTName = 'menu';

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
	 * delete data
	 *
	 * @param $where is array or string
	 * @return boolean
	 */
	public function delete($where = '') {
		return $this->db->delete($this->_tableName, $where) > 0 ? TRUE : FALSE;
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
		$this->db->select("{$this->_tableName}.*, {$this->_menuTName}.menu_name, {$this->_menuTName}.template, {$this->_menuTName}.html_path");
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

	public function attribute($attributeStr = 'h,c') {
		$strAttribute = '';
		$attribute = array(
		             'h'=>'<font color=#FF0000>头条</font>',
		             'c'=>'<font color=#FF0000>推荐</font>',
		             'a'=>'<font color=#FF0000>特荐</font>',
		             'f'=>'<font color=#FF0000>幻灯</font>',
		             's'=>'<font color=#FF0000>滚动</font>',
		             'b'=>'<font color=#FF0000>加粗</font>',
		             'p'=>'<font color=#FF0000>图片</font>',
		             'j'=>'<font color=#FF0000>跳转</font>'
		             );
		if (! empty($attributeStr)) {
			$attributeArray = explode(',', $attributeStr);
			$strAttribute = '[';
			foreach ($attributeArray as $key=>$value) {
			    $strAttribute .= '&nbsp;'.$attribute[$value];
			}
			$strAttribute .= ']';
		}
		return $strAttribute;
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
		$this->db->join($this->_menuTName, "{$this->_tableName}.category_id = {$this->_menuTName}.id");
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