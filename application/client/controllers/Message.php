<?php
class Message extends CI_Controller {
	public function __construct() {
		parent::__construct();
		$this->load->model('System_model', '', TRUE);
	}

	public function index() {
		$systemInfo = $this->System_model->get('*', array('id'=>1));
		$data = array(
				'site_name'=>$systemInfo['site_name'],
				'index_name'=>$systemInfo['index_name'],
		        'client_index'=>$systemInfo['client_index'],
				'title'=>'信息提示',
		        'keywords'=>'信息提示',
		        'description'=>'信息提示',
				'site_copyright'=>$systemInfo['site_copyright'],
				'icp_code'=>$systemInfo['icp_code'],
		        'html'=>$systemInfo['html'],
				'parent_id'=>0,
		        'msg'=>$this->session->userdata('user_msg'),
		        'url'=>$this->session->userdata('user_url')
		        );
	    $layout = array(
				  'content'=>$this->load->view('message/index', $data, TRUE)
			      );
	    $this->load->view('layout/default_layout', $layout);
	    $this->_unSession();
	}

    private function _unSession() {
	    $data = array(
		        'user_msg'=>'',
		        'user_url'=>''
		        );
        $this->session->unset_userdata($data);
	}
}
/* End of file main.php */
/* Location: ./application/client/controllers/main.php */