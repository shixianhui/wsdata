<?php
class System extends CI_Controller {
	private $_title = '系统设置';
	private $_tool = '';
	private $_table = '';
	private $_template = '';
	
	public function __construct() {
		parent::__construct();
		//获取表名
		$this->_table = $this->uri->segment(1);
		//模型名
		$this->_template = $this->uri->segment(1);
		//获取表对象
		$this->load->model(ucfirst($this->_template).'_model', 'tableObject', TRUE);
	}


	public function save() {
	    checkPermission("{$this->_template}_save");
		if ($_POST) {
			$fields = array(
					'site_name'=>       $this->input->post('site_name', TRUE),
			        'index_site_name'=> $this->input->post('index_site_name', TRUE),
					'client_index'=>    $this->input->post('client_index', TRUE),			
					'site_copyright'=>  $this->input->post('site_copyright', TRUE),
					'site_keywords'=>    $this->input->post('site_keywords', TRUE),
					'site_description'=>$this->input->post('site_description', TRUE),
		            'icp_code'=>        $this->input->post('icp_code', TRUE),
					'upload_file_size'=>           $this->input->post('upload_file_size'),
					'cache_time'=>      $this->input->post('cache_time')
			        );
		    if ($this->tableObject->save($fields, array('id'=>1))) {
		    	printAjaxSuccess('reload', '修改成功');
			} else {
				printAjaxError('fail', "修改失败！");
			}
		}
		$item_info = $this->tableObject->get('*', array('id'=>1));
		$item_info_json = json_encode($item_info);
		$data = array(
		        'tool'=>$this->_tool,
				'item_info'=>$item_info,
				'item_info_json'=>$item_info_json,
		        );
	    $layout = array(
			      'title'=>$this->_title,
				  'content'=>$this->load->view($this->_template.'/save', $data, TRUE)
			      );
	    $this->load->view('layout/default', $layout);
	}
}
/* End of file news.php */
/* Location: ./application/admin/controllers/news.php */