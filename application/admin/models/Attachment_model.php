<?php
class Attachment_model extends CI_Model {

	private $_tableName = 'attachment';

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

		return $ret > 0 ? $ret : FALSE;

	}

	public function delete($where = '') {
		return $this->db->delete($this->_tableName, $where) > 0 ? TRUE : FALSE;
	}

	public function get($select = '*', $strWhere = NULL) {
		$ret = array();
		$this->db->select($select);
		$query = $this->db->get_where($this->_tableName, $strWhere);
		if ($query->num_rows() > 0) {
			$ret = $query->result_array();
		}

		return $ret;
	}

	public function gets($select = '*', $strWhere = NULL) {
		$ret = array();
		$this->db->select($select);
		$query = $this->db->get_where($this->_tableName, $strWhere);
		if ($query->num_rows() > 0) {
			$ret = $query->result_array();
		}

		return $ret;
	}
	
	public function gets2($ids = '') {
	    $ret = array();
	    $query = $this->db->query("select * from {$this->_tableName} where id in ({$ids}) order by field(id, {$ids})");
	    if ($query->num_rows() > 0) {
	        $ret = $query->result_array();
	    }
	
	    return $ret;
	}

	public function getAttachmentId($path) {
		$AttachmentId = $this->get('id', array('path'=>$path));
		if ($AttachmentId) {
		    return $AttachmentId[0]['id'];
		}

		return false;
	}
}
/* End of file link_model.php */
/* Location: ./application/admin/models/link_model.php */