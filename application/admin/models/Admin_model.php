<?php
class Admin_model extends CI_Model {

	private $_tableName = 'admin';
	private $_admingropTName = 'admin_group';

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

	public function gets($select = '*',$strWhere = NULL, $limit = NULL, $offset = NULL, $by = 'id', $order = 'ASC', $sort_order = NULL) {
		$ret = array();
		$this->db->select($select, false);
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

	public function gets_join_admin_group($strWhere = NULL, $limit = NULL, $offset = NULL) {
		$ret = array();
		$this->db->select("{$this->_tableName}.*, IFNULL({$this->_admingropTName}.group_name, '') as group_name");
		$this->db->order_by("{$this->_tableName}.id", 'DESC');
		$this->db->join($this->_admingropTName, "{$this->_admingropTName}.id = {$this->_tableName}.admin_group_id", 'left');
		$query = $this->db->get_where($this->_tableName, $strWhere, $limit, $offset);
	    if ($query->num_rows() > 0) {
            $ret = $query->result_array();
        }

        return $ret;
	}

    public function get_join_admin_group($strWhere = NULL) {
		$this->db->select("{$this->_tableName}.*, {$this->_admingropTName}.group_name, {$this->_tableName}.admin_group_id");
		$this->db->join($this->_admingropTName, "{$this->_admingropTName}.id = {$this->_tableName}.admin_group_id");
		$query = $this->db->get_where($this->_tableName, $strWhere);
	    if ($query->num_rows() > 0) {
            $ret = $query->result_array();
            return $ret[0];
        }

        return array();
	}

	public function get($select = '*', $strWhere = NULL) {
		$ret = array();
		$this->db->select($select);
        $this->db->order_by('id', 'DESC');
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

	public function count_join_admin_group($strWhere = NULL) {
		$count = 0;
		$this->db->select("count(*) as 'count'");
		$this->db->join($this->_admingropTName, "{$this->_admingropTName}.id = {$this->_tableName}.admin_group_id", 'left');
		$query = $this->db->get_where($this->_tableName, $strWhere);
	    if ($query->num_rows() > 0) {
			$ret = $query->result_array();
			$count = $ret[0]['count'];
		}

		return $count;
	}

	public function validateUnique($username) {
		$adminInfo = $this->get_join_admin_group(array("{$this->_tableName}.username"=>$username));
		if ($adminInfo) {
		    return true;
		}

		return false;
	}


	public function login($username, $password) {
	    $adminInfo = $this->get_join_admin_group(array("{$this->_tableName}.username"=>$username));
	    if ($adminInfo) {
	        if ($adminInfo['password'] == $this->getPasswordSalt($username, $password)) {
	            return $adminInfo;
	        }
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
        
        return md5($username.$addTime.$password);
	}
}
/* End of file admin_model.php */
/* Location: ./application/admin/models/admin_model.php */