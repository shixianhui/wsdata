<?php
class Users_model extends CI_Model {

	private $_tableName = 'users';

	public function __construct() {
		 parent::__construct();
	}

	public function save($data, $where = NULL) {
		if (! empty($where)) {
			$ret = $this->db->update($this->_tableName, $data, $where);
		} else {
			$this->db->insert($this->_tableName, $data);
			$ret = $this->db->insert_id();
		}
		return $ret > 0 ? $ret : FALSE;
	}

    public function save_batch($data) {
        $ret = $this->db->insert_batch($this->_tableName, $data);
        return $ret;
    }

    public function save_column($column, $data, $where = NULL) {
        $ret = 0;

        if (!empty($where)) {
            $this->db->set($column, $data, FALSE);
            $this->db->where($where);
            $ret = $this->db->update($this->_tableName);
        }

        return $ret > 0 ? $ret : FALSE;
    }

	public function delete($where = '') {
		return $this->db->delete($this->_tableName, $where) > 0 ? TRUE : FALSE;
	}

    public function gets($select = '*',$strWhere = NULL, $limit = NULL, $offset = NULL, $by = 'id', $order = 'DESC', $sort_order = NULL) {
		$ret = array();
		$this->db->select($select);
        if ($sort_order) {
            $this->db->order_by("sort", $sort_order);
        }
		$this->db->order_by($by, $order);
		$query = $this->db->get_where($this->_tableName, $strWhere, $limit, $offset);
		if ($query->num_rows() > 0) {
			$ret = $query->result_array();
		}
        return $ret;
	}

    public function get($select = '*', $strWhere = NULL, $by = 'id', $order = 'DESC') {
		$ret = array();
		$this->db->select($select);
		$this->db->order_by($by, $order);
		$query = $this->db->get_where($this->_tableName, $strWhere);
		if ($query->num_rows() > 0){
			$ret = $query->result_array();
			return $ret[0];
		}
		return $ret;
	}

    public function count($strWhere = NULL)
    {
        $count = 0;
        $this->db->select("count(*) as 'count'");
        $query = $this->db->get_where($this->_tableName, $strWhere);
        if ($query->num_rows() > 0) {
            $ret = $query->result_array();
            $count = $ret[0]['count'];
        }
        return $count;
    }

    public function sum($column = 'draw_number',$strWhere = NULL) {
        $count = 0;
        $this->db->select("IFNULL(sum($column),0) as 'count'", FALSE);
        $query = $this->db->get_where($this->_tableName, $strWhere);
        if ($query->num_rows() > 0) {
            $ret = $query->result_array();
            $count = $ret[0]['count'];
        }

        return $count;
    }

	public function validateUnique($username) {
		$adminInfo = $this->get('id',array("lower({$this->_tableName}.username)"=>strtolower($username)));
		if ($adminInfo) {
		    return true;
		}

		return false;
	}

	
	public function getPasswordSalt($username, $password) {
		$addTime = 0;
	    $this->db->select("{$this->_tableName}.create_time");
		$query = $this->db->get_where($this->_tableName, array('username'=>$username));
	    if ($query->num_rows() > 0) {
            $ret = $query->result_array();
            $addTime = strtotime($ret[0]['create_time']);

        }
        
        return md5(strtolower($username).$addTime.$password);
	}
}
/* End of file admin_model.php */
/* Location: ./application/admin/models/admin_model.php */