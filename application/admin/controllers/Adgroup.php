<?php
class Adgroup extends CI_Controller {
	private $_title = '广告位管理';
	private $_tool = '';
	private $_table = 'ad_group';
	private $_template = '';

	public function __construct() {
		parent::__construct();
		//模型名
		$this->_template = $this->uri->segment(1);
		//获取表对象
		$this->load->model(ucfirst($this->_template).'_model', 'tableObject', TRUE);
		$this->_tool = $this->load->view('element/adgroup_tool', '', TRUE);
	}

	public function index() {
	    checkPermission("{$this->_template}_index");

		if (! $this->uri->segment(2)) {
		    $this->session->unset_userdata("search");
		}
		$this->session->set_userdata(array("{$this->_template}RefUrl"=>base_url().'admincp.php/'.uri_string()));
		$itemList = $this->tableObject->gets();

		$data = array(
		                'tool'=>$this->_tool,
		                'template'=>$this->_template,
		                'itemList'=>$itemList
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

		$prfUrl = $this->session->userdata("{$this->_template}RefUrl")?$this->session->userdata("{$this->_template}RefUrl"):base_url()."admincp.php/{$this->_template}/index";
		if ($_POST) {
		    $fields = array('group_name'=>$this->input->post('group_name', TRUE));
		    if ($this->tableObject->save($fields, $id?array('id'=>$id):$id)) {
		    	printAjaxSuccess($prfUrl);
			} else {
				printAjaxError('fail', "操作失败！");
			}
		}

		$itemInfo = $this->tableObject->get('*', array('id'=>$id));

	    $data = array(
		        'tool'=>$this->_tool,
	            'itemInfo'=>$itemInfo,
	            'template'=>$this->_template,
	            'prfUrl'=>$prfUrl
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