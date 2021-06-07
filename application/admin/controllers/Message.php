<?php
class Message extends CI_Controller {
	private $_title = '信息提示';
    public function __construct() {
		parent::__construct();
    }

	public function index() {
		$message = array(
		           'msg'=>$this->session->userdata('msg'),
		           'url'=>$this->session->userdata('url')
		           );
		$layout = array(
			      'title'=>$this->_title,
				  'content'=>$this->load->view('message/index', $message, TRUE)
			      );
	    $this->load->view('layout/default', $layout);
	    $this->_unSession();
	}

	private function _unSession() {
	    $data = array('msg','url');
        $this->session->unset_userdata($data);
	}
}
/* End of file message.php */
/* Location: ./application/admin/controllers/message.php */