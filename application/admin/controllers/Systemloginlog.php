<?php
class Systemloginlog extends CI_Controller {
	private $_title = '系统登录日志';
	private $_tool = '';
	private $_table = 'system_login_log';
	private $_template = 'systemloginlog';
	public function __construct() {
		parent::__construct();
		//快捷方式
		$this->_tool = $this->load->view('element/system_login_log_tool', array('template'=>$this->_template), TRUE);
		//获取表对象
		$this->load->model('Systemloginlog_model', 'tableObject', TRUE);
		$this->load->model('Menu_model', '', TRUE);
		$this->load->model('System_model', '', TRUE);
	}

	public function index($page = 0) {
	    checkPermission("{$this->_template}_index");
	    if (! $this->uri->segment(2)) {
		    $this->session->unset_userdata("search");
		}
		$this->session->set_userdata(array("{$this->_template}RefUrl"=>base_url().'admincp.php/'.uri_string()));
		$strWhere = $this->session->userdata('search')?$this->session->userdata('search'):NULL;

		if ($_POST) {
			$strWhere = "{$this->_table}.id > 0";

			$username = $this->input->post('username', TRUE);
			$ip = $this->input->post('ip', TRUE);
			$address = $this->input->post('address', TRUE);
		    $startTime = $this->input->post('inputdate_start', TRUE);
		    $endTime = $this->input->post('inputdate_end', TRUE);

		    if (! empty($username) ) {
		        $strWhere .= " and admin.username = '{$username}' ";
		    }
		    if (! empty($ip) ) {
		        $strWhere .= " and {$this->_table}.ip = '{$ip}' ";
		    }
		    if (! empty($address) ) {
		        $strWhere .= " and {$this->_table}.address like '%".$address."%'";
		    }
		    if (! empty($startTime) && ! empty($endTime)) {
		    	$strWhere .= " and {$this->_table}.add_time > ".strtotime($startTime.' 00:00:00')." and {$this->_table}.add_time < ".strtotime($endTime." 23:59:59")." ";
		    }
		    $this->session->set_userdata('search', $strWhere);
		}

		//分页
		$this->config->load('pagination_config', TRUE);
		$paginationCount = $this->tableObject->rowCount($strWhere);
    	$paginationConfig = $this->config->item('pagination_config');
    	$paginationConfig['base_url'] = base_url()."admincp.php/{$this->_template}/index/";
    	$paginationConfig['total_rows'] = $paginationCount;
    	$paginationConfig['uri_segment'] = 3;
		$this->pagination->initialize($paginationConfig);
		$pagination = $this->pagination->create_links();

		$itemList = $this->tableObject->gets($strWhere, $paginationConfig['per_page'], $page);

		$data = array(
		        'tool'      =>$this->_tool,
				'itemList'  =>$itemList,
		        'pagination'=>$pagination,
		        'paginationCount'=>$paginationCount,
		        'pageCount'=>ceil($paginationCount/$paginationConfig['per_page']),
		        'template'=>$this->_template
		        );

	    $layout = array(
			      'title'=>$this->_title,
				  'content'=>$this->load->view($this->_template.'/index', $data, TRUE)
			      );
	    $this->load->view('layout/default', $layout);
	}
}
/* End of file news.php */
/* Location: ./application/admin/controllers/news.php */