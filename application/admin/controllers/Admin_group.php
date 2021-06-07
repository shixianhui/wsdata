<?php
class Admin_group extends CI_Controller {
	private $_title = '管理组管理';
	private $_tool = '';
	private $_table = 'admin_group';
	private $_template = '';
	
	public function __construct() {
		parent::__construct();
		//模型名
		$this->_template = $this->uri->segment(1);
		//获取表对象
		$this->load->model(ucfirst($this->_template).'_model', 'tableObject', TRUE);
		$this->load->model('Menus_model', '', TRUE);
	}

	public function index() {
	    checkPermission("{$this->_template}_index");

		$item_list = $this->tableObject->gets();

		$data = array(
		        'tool'=>$this->_tool,
		        'template'=>$this->_template,
		        'item_list'=>json_encode($item_list)
		        );
	    $layout = array(
			      'title'=>$this->_title,
				  'content'=>$this->load->view($this->_template.'/index', $data, TRUE)
			      );
	    $this->load->view('layout/default', $layout);
	}

	public function save($id = NULL) {
	    if ($id) {
	        checkPermission("{$this->_template}_edit");
	    } else {
	        checkPermission("{$this->_template}_create");
	    }
		if ($_POST) {
			$permissions_arr = $this->input->post('permissions_arr', TRUE);
			$permissions_arr = json_decode($permissions_arr, true);
			$permissions = '';
			$permission_ids = [];
			if ($permissions_arr) {
				$one_arr = $this->Menus_model->tree_to_one($permissions_arr);
				foreach ($one_arr as $key => $value) {
					if ($value['component']) {
						$permissions .= ($value['permissions'] ? $value['component'].'_'.$value['permissions'] : $value['component']).',';
					}
					$permission_ids[] = $value['id'];
					if (in_array($value['parent_id'], $permission_ids)) {
						$k = array_search($value['parent_id'], $permission_ids);
						array_splice($permission_ids,$k,1);
					}
				}
			}
			$permissions = $permissions ? substr($permissions, 0, -1) : '';
			$permission_ids = implode(',', $permission_ids);
			
		    $fields = array(
		        'group_name'=>$this->input->post('group_name', TRUE),
		        'permissions'=>$permissions,
		        'permission_ids'=>$permission_ids,
			);
		    if ($this->tableObject->save($fields, $id?array('id'=>$id):$id)) {
		    	printAjaxSuccess('close_layer','保存成功');
			} else {
				printAjaxError('fail', "操作失败！");
			}
		}

		$item_info = $this->tableObject->get('*', array('id'=>$id));

		$datas = $this->Menus_model->gets('id,parent_id,title,component,permissions', NULL, NULL, NULL, 'id', 'ASC', 'ASC');
		foreach ($datas as $key=>$value) {
			$datas[$key]['spread'] = $value['parent_id'] == 0 ? true : false;
		}
		$menus_list = $this->Menus_model->generateTree($datas);
		
	    $data = array(
		        'tool'=>$this->_tool,
	            'item_info'=>$item_info,
	            'item_info_json'=>json_encode($item_info),
	            'menus_list'=>json_encode($menus_list),
	            'template'=>$this->_template,
		        );
		$layout = array(
		          'title'=>$this->_title,
				  'content'=>$this->load->view($this->_template.'/save', $data, TRUE)
		          );
		$this->load->view('layout/default', $layout);
	}

    public function delete() {
        checkPermissionAjax("{$this->_template}_delete");
        
	    $ids = $this->input->post('ids', TRUE);
        if (in_array('1',explode(',',$ids))){
            printAjaxError('fail', '超管不能删除！');
        }
	    if (! empty($ids)) {
	        if ($this->tableObject->delete('id in ('.$ids.')')) {
	            printAjaxData(array('ids'=>explode(',', $ids)));
	        }
	    }

	    printAjaxError('fail', '删除失败！');
	}
}
/* End of file admingroup.php */
/* Location: ./application/admin/controllers/admingroup.php */