<?php
class Home extends CI_Controller {
	private $_title = '首页';
	private $_template = '';

    public function __construct() {
		parent::__construct();
		$this->_template = $this->uri->segment(1);
		//获取表对象
		$this->load->model('System_model', '', TRUE);
	}
    public function index()	{
        $systemInfo = $this->System_model->get('*', array('id'=>1));

		$data = [
			'username'  =>get_cookie('admin_username'),
			'group_name'=>get_cookie('admin_group_name'),
			'site_name'=>$systemInfo['site_name'],
		];
		$this->load->view($this->_template.'/index', $data);
	}

    public function homepage() {
		$this->load->view($this->_template.'/homepage');
	}

	public function get_init_data()
	{
		$this->load->model('Menus_model', '', TRUE);
		
		$init_info = [
			'homeInfo' => [
				'title'=>'物损数据库',
				'href'=>"admincp.php/ws_data/index.html"
			],
			"logoInfo" => [
				'title'=>'管理系统',
				'image'=>"images/logo.png",
				'href'=>"admincp.php/home.html",
			]
		];
		$datas = $this->Menus_model->gets('id,parent_id,title,icon,href,target', ['status'=>1, 'type <'=>2], NULL, NULL, 'id', 'ASC', 'ASC');
		$menus_list = $this->Menus_model->getMenuList($datas);
		$init_info['menuInfo'] = $menus_list;
		echo json_encode($init_info);
		exit(1);
	}


	public function clear_session($controller = NULL)
	{
		if ($controller) {
			$this->session->unset_userdata($controller."_search");
			$this->session->unset_userdata("search");
		}

		echo json_encode(['code'=>1, 'msg'=>'服务端清理缓存成功']);
	}

}

/* End of file Home.php */
/* Location: ./application/admin/controllers/Home.php */