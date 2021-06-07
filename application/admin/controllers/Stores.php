<?php
class Stores extends CI_Controller {
    private $_title = '商家管理';
    private $_tool = '';
    private $_table = '';
    private $_status_arr = ['<font color="#FF0000">待审核</font>','审核通过','审核拒绝','关闭'];
    private $_business_status_arr = ['暂未营业','正在营业','休息中'];
    private $_store_type_arr = ['美食','外卖','美食、外卖'];

    public function __construct() {
        parent::__construct();
        //获取表名
        $this->_table = $this->uri->segment(1);
        //快捷方式
        $this->_tool = $this->load->view("element/save_list_tool", array('table' => $this->_table, 'parent_title' => '商家管理', 'title' => '商家管理'), TRUE);
        //获取表对象
        $this->load->model(ucfirst($this->_table) . '_model', 'tableObject', TRUE);
        $this->load->model('Store_type_model', '', TRUE);
        $this->load->model('Attachment_model', '', TRUE);
        $this->load->model('User_model', '', TRUE);
        $this->load->model('Seller_group_model', '', TRUE);

    }

    public function index() {
    	checkPermission("{$this->_table}_index");
        $this->session->set_userdata(array("{$this->_table}RefUrl" => base_url() . 'admincp.php/' . uri_string()));

        $item_list = $this->tableObject->gets();
        if ($item_list) {
            foreach ($item_list as $key=>$value) {
                $type_info = $this->Store_type_model->get('name',array('id'=>$value['type_id']));
                $item_list[$key]['type_str'] = $type_info ? $type_info['name'] : '';
            }
        }
        $data = array(
            'tool' => $this->_tool,
            'table' => $this->_table,
            'status_arr' => $this->_status_arr,
            'business_status_arr' => $this->_business_status_arr,
            'store_type_arr' => $this->_store_type_arr,
            'item_list' => $item_list
        );
        $layout = array(
            'title' => $this->_title,
            'content' => $this->load->view("{$this->_table}/index", $data, TRUE)
        );
        $this->load->view('layout/default', $layout);
    }

    public function save($id = NULL) {
    	if ($id) {
    		checkPermission("{$this->_table}_edit");
    	} else {
    		checkPermission("{$this->_table}_create");
    	}
        $prfUrl = $this->session->userdata("{$this->_table}RefUrl") ? $this->session->userdata("{$this->_table}RefUrl") : base_url() . "admincp.php/{$this->_table}/index";
        $item_info = $this->tableObject->get('*', array('id' => $id));
        if ($_POST) {
            $status = $this->input->post('status', TRUE);
            $user_id = $this->input->post('user_id', TRUE);
            $fields = array(
                'user_id' => $user_id,
                'store_name' => $this->input->post('store_name', TRUE),
                'type_id' => $this->input->post('type_id', TRUE),
                'bank_card_number' => $this->input->post('bank_card_number', TRUE),
                'phone' => $this->input->post('phone', TRUE),
                'store_type' => $this->input->post('store_type', TRUE),
                'sort' => $this->input->post('sort', TRUE),
                'status' => $status,
            );
            if ($this->tableObject->save($fields, $id ? array('id' => $id) : NULL)) {
                //部门管理
                if ($status == 1){
                    $user_info = $this->User_model->getInfo('seller_group_id', array('id'=>$user_id));
                    if (empty($user_info['seller_group_id'])){
                        $permissions = 'seller_g,seller,seller_index,seller_add,useller_edit,seller_delete,dishes_g,dishes,dishes_index,dishes_add,dishes_edit,dishes_delete,dishes_category,dishes_category_index,dishes_category_add,dishes_category_edit,dishes_category_delete,combos,combos_index,combos_add,combos_edit,combos_delete,seller_group_g,seller_group,seller_group_index,seller_group_add,seller_group_edit,seller_group_delete,user,user_index,user_add,user_edit,user_delete';
                        $ret_id = $this->Seller_group_model->save(array('group_name'=>'超级管理员', 'permissions'=>$permissions,'user_id'=>$user_id));
                        if ($ret_id > 0){
                            $this->User_model->save(array('seller_group_id'=>$ret_id), array('id'=>$user_id));
                        }
                    }
                }
                printAjaxSuccess($prfUrl);
            } else {
                printAjaxError("操作失败！");
            }
        }
        $image_list = [];
        if ($item_info) {
            if ($item_info['image_ids']) {
                $ids = preg_replace(array('/^_/', '/_$/', '/_/'), array('', '', ','), $item_info['image_ids']);
                $image_list = $this->Attachment_model->gets2($ids);
            }
            $user_info = $this->User_model->getInfo('nickname', ['id'=>$item_info['user_id']]);
            $item_info['nickname'] = $user_info['nickname'];
        }
        $type_list = $this->Store_type_model->gets('*');

        $data = array(
            'tool' => $this->_tool,
        	'type_list'=>$type_list,
            'item_info' => $item_info,
            'image_list' => $image_list,
        	'table' => $this->_table,
        	'status_arr' => $this->_status_arr,
        	'store_type_arr' => $this->_store_type_arr,
            'prfUrl' => $prfUrl
        );
        $layout = array(
            'title' => $this->_title,
            'content' => $this->load->view("{$this->_table}/save", $data, TRUE)
        );
        $this->load->view('layout/default', $layout);
    }

    public function delete() {
    	checkPermissionAjax("{$this->_table}_delete");
    	if ($_POST) {
    		$id = $this->input->post('id', TRUE);

    		if (!$id) {
    			printAjaxError('fail', '参数错误');
    		}
    		if ($this->tableObject->delete("id in ($id)")) {
    			printAjaxData(array('id' => $id));
    		}
    		printAjaxError('fail', '删除失败！');
    	}
    }

    public function sort() {
    	checkPermissionAjax("{$this->_table}_edit");
    	if ($_POST) {
    		$ids = $this->input->post('ids', TRUE);
    		$sorts = $this->input->post('sorts', TRUE);

    		if (!empty($ids) && !empty($sorts)) {
    			$ids = explode(',', $ids);
    			$sorts = explode(',', $sorts);

    			foreach ($ids as $key => $value) {
    				$this->tableObject->save(
    						array('sort' => $sorts[$key]), array('id' => $value));
    			}
    			printAjaxSuccess('', '排序成功！');
    		}

    		printAjaxError('排序失败！');
    	}
    }

    public function display() {
    	checkPermissionAjax("{$this->_table}_edit");
    	if ($_POST) {
    		$ids = $this->input->post('ids');
    		$display = $this->input->post('display');
    		if (!empty($ids) && $display != "") {
    			if ($this->tableObject->save(array('status' => $display), 'id in (' . $ids . ')')) {
    				printAjaxSuccess('', '修改状态成功！');
    			}
    		}

    		printAjaxError('fail', '修改状态失败！');
    	}
    }
}

/* End of file admingroup.php */
/* Location: ./application/admin/controllers/admingroup.php */