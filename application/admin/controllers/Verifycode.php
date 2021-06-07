<?php
class Verifycode extends CI_Controller {
	public function __construct() {
		parent::__construct();
		$this->load->library('Securitysecoderclass');
	}

	public function index($push = 0) {
		$securitysecoder = new Securitysecoderclass(false, true, 16);
        $securitysecoder->entry();
	}

    public function index2($push = 0) {
		$securitysecoder = new Securitysecoderclass(false, true, 16);
        $securitysecoder->entry2();
	}
}
/* End of file news.php */
/* Location: ./application/admin/controllers/user.php */