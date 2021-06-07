<?php
class Watermark extends CI_Controller {
	private $_title = '图片水印设置';
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
		$this->_tool = $this->load->view('element/system_tool', '', TRUE);
	}

	public function save() {
	    checkPermission("{$this->_template}_save");
		if ($_POST) {
			$fields = array(
					'is_open'=>     $this->input->post('is_open'),
					'path'=>  		$this->input->post('path', TRUE),
					'location'=>    $this->input->post('location', TRUE)
			        );
		    if ($this->tableObject->save($fields, array('id'=>1))) {
		    	printAjaxSuccess(base_url()."admincp.php/{$this->_template}/save");
			} else {
				printAjaxError('fail', "修改失败！");
			}
		}
		
		$itemInfo = $this->tableObject->get('*', array('id'=>1));
		
		$data = array(
		        'tool'=>$this->_tool,
				'itemInfo'=>$itemInfo
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