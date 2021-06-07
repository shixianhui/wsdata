<?php
class Area_model extends CI_Model {

	private $_tableName = 'area';

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

    public function gets($select = '*', $strWhere = NULL, $limit = NULL, $offset = NULL) {
		$ret = array();
		$this->db->select($select);		
		$this->db->order_by("{$this->_tableName}.sort", 'ASC');
		$this->db->order_by("{$this->_tableName}.id", 'ASC');
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
		$query = $this->db->get_where($this->_tableName, $strWhere);
	    if ($query->num_rows() > 0) {
			$ret = $query->result_array();
			$count = $ret[0]['count'];
		}

		return $count;
	}
	
    public function getLocation($id = 0, $controller) {
		$str = "";
	    if ($id) {
	    	//三级
	        $info = $this->get('*', array('id'=>$id));
	        if ($info) {
	           $str = "<a href='admincp.php/{$controller}/index/{$id}'>{$info['name']}</a>&nbsp;&gt;&nbsp;";
	           //二级
		        if ($info['parent_id'] != 0) {
			        //上级
			        $info2 = $this->get('*', array('id'=>$info['parent_id']));
			        if ($info2) {
			           $str = "<a href='admincp.php/{$controller}/index/{$info['parent_id']}'>{$info2['name']}</a>&nbsp;&gt;&nbsp;".$str;
			           if ($info2['parent_id'] != 0) {
			               //上级
			               $info3 = $this->get('*', array('id'=>$info2['parent_id']));
			               if ($info3) {
			                   $str = "<a href='admincp.php/{$controller}/index/{$info2['parent_id']}'>{$info3['name']}</a>&nbsp;&gt;&nbsp;".$str;
			               }
			           }
			        }
		        }	
	        }     
	    }
	    //一级
	    $str = "<a href='admincp.php/{$controller}/index/0'>一级分类</a>&nbsp;&gt;&nbsp;".$str;
	    
	    return $str;
	}
	
    //查子级
	public function getChildTreeCount($ids) {
		return $this->rowCount("parent_id in ({$ids})");
	}
}
/* End of file advertising_model.php */
/* Location: ./application/admin/models/advertising_model.php */