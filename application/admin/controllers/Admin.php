<?php
class Admin extends CI_Controller {
	private $_title = '管理员管理';
	private $_tool = '';
	private $_table = 'admin';
	private $_template = 'admin';

	public function __construct() {
		parent::__construct();

		//获取表对象
		$this->load->model(ucfirst($this->_template).'_model', 'tableObject', TRUE);
		$this->load->model('Admin_group_model', '', TRUE);
		$this->load->model('Systemloginlog_model', '', TRUE);
		$this->load->model('System_model', '', TRUE);
		$this->load->library('Securitysecoderclass');
	}

	public function index($refresh = 1, $page = 0) {
	    checkPermission("{$this->_template}_index");

		if ($refresh) {
            $refresh = 0;
            $this->session->unset_userdata("search");
        }

        $strWhere = NULL;
        $strWhere = $this->session->userdata($this->_template.'_search')?$this->session->userdata($this->_template.'_search'):$strWhere;

		if ($_POST) {
			$strWhere = "{$this->_table}.id > 0";
		    $username = $this->input->post('username', TRUE);
		    $admin_group_id = $this->input->post('admin_group_id', TRUE);

		    if (! empty($admin_group_id) ) {
		        $strWhere .= " and {$this->_table}.admin_group_id = {$admin_group_id} ";
		    }

			if (! empty($username) ) {
		        $strWhere .= " and {$this->_table}.username regexp '{$username}' ";
		    }

		    $this->session->set_userdata($this->_template.'_search', $strWhere);
		}

		//分页
		$this->config->load('pagination_config', TRUE);
		$paginationCount = $this->tableObject->count_join_admin_group($strWhere);
    	$paginationConfig = $this->config->item('pagination_config');
    	$paginationConfig['base_url'] = base_url()."admincp.php/{$this->_template}/index/{$refresh}/";
    	$paginationConfig['total_rows'] = $paginationCount;
    	$paginationConfig['uri_segment'] = 4;
    	$paginationConfig['per_page'] = 5;
		$this->pagination->initialize($paginationConfig);
		$pagination = $this->pagination->create_links();

		$item_list = $this->tableObject->gets_join_admin_group($strWhere, $paginationConfig['per_page'], $page);
		if ($item_list) {
			foreach ($item_list as $key => $value) {
				$item_list[$key]['create_time'] = date('Y-m-d H:i:s', $value['add_time']);
			}
		}
		$admin_group_list = $this->Admin_group_model->gets();

        $data = array(
		              'tool'=>$this->_tool,
		              'admin_group_list'=>$admin_group_list,
		              'item_list'=>json_encode($item_list),
					  'table_limit' => $paginationConfig['per_page'],
		              'pagination'=>$pagination,
		              'template'=>$this->_template,
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
			$username = $this->input->post('username', TRUE);

			$fields = array(
			          'admin_group_id'=>$this->input->post('admin_group_id'),
			          'username'=>      $username,
			          'real_name'=>     $this->input->post('real_name', TRUE),
			          'mobile'=>     $this->input->post('mobile', TRUE),
			          );
		    $password = $this->input->post('password', TRUE);
			if ($password) {
				  $addTime = time();
				  $fields['add_time'] = $addTime;
			      $fields['password'] = $this->createPasswordSALT($username, $addTime, $password);
			}
			if (empty($id)) {
			    if ($this->tableObject->validateUnique($this->input->post('username', TRUE))) {
			        printAjaxError('fail', "用户名已经存在，请换个用户名！");
			    }
			}

		    if ($this->tableObject->save($fields, $id?array('id'=>$id):$id)) {
		    	printAjaxSuccess('close_layer','保存成功');
			} else {
				printAjaxError('fail', "操作失败！");
			}
		}

		$admin_group_list = $this->Admin_group_model->gets();
		$item_info = $this->tableObject->get('*', array('id'=>$id));
	    $data = array(
		        'tool'=>$this->_tool,
	            'item_info'=>$item_info,
	            'item_info_json'=>json_encode($item_info),
	            'admin_group_list'=>$admin_group_list,
	            'template'=>$this->_template,
	            'prfUrl'=>$prfUrl
		        );
		$layout = array(
		          'title'=>$this->_title,
				  'content'=>$this->load->view($this->_template.'/save', $data, TRUE)
		          );
		$this->load->view('layout/default', $layout);
	}

    public function category() {
        checkPermissionAjax("{$this->_template}_edit");

	    $ids = $this->input->post('ids', TRUE);
		$categoryId = $this->input->post('categoryId', TRUE);

		if (! empty($ids) && ! empty($categoryId)) {
			if($this->tableObject->save(array('admin_group_id'=>$categoryId), 'id in ('.$ids.')')) {
			    printAjaxSuccess('', '修改管理组成功！');
			}
		}

		printAjaxError('fail', '修改管理组失败！');
	}

    public function delete() {
        checkPermissionAjax("{$this->_template}_delete");

	    $ids = $this->input->post('ids', TRUE);
	    if (in_array('1',explode(',',$ids))){
            printAjaxError('fail', '超管账号不能删除！');
        }

	    if (! empty($ids)) {
	        if ($this->tableObject->delete('id in ('.$ids.')')) {
	            printAjaxData(array('ids'=>explode(',', $ids)));
	        }
	    }

	    printAjaxError('fail', '删除失败！');
	}

	public function login() {
		if ($_POST) {
			$username = $this->input->post('username', TRUE);
			$password = $this->input->post('password', TRUE);
			$code = $this->input->post('code', TRUE);
            $ip = $this->input->post('ip', TRUE);
            $area = $this->input->post('area', TRUE);
			if (!$username) {

                printAjaxError('fail','请输入用户名');
			}
			if (!$password) {

                printAjaxError('fail','请输入密码');

			}
			if (!$code) {

                printAjaxError('fail','请输入验证码');

			}
			$securitysecoder = new Securitysecoderclass();
			if (! $securitysecoder->check($code)) {

                printAjaxError('fail','验证码错误');
                
			}
			$adminInfo = $this->tableObject->login($username, $password);


		    if ($adminInfo) {
		        $fields = array(
		            'ip'=>     $ip,
                    'address'=>$area,
		            'admin_id'=>$adminInfo['id'],
                );
		        if ($this->Systemloginlog_model->save($fields)) {
		            $this->tableObject->save(array('ip'=>$ip, 'ip_address'=>$area), array('id'=>$adminInfo['id']));
		        }
		    	$this->_setCookie($adminInfo);
                printAjaxSuccess(base_url().'admincp.php/home');
		    } else {

                printAjaxError('fail','登录失败');

		    }
		}
		if ($this->session->userdata('username')) {
		    redirect('/menu');
		}
        $systemInfo = $this->System_model->get('*', array('id'=>1));

		$data2=array(
		    'site_name'=>$systemInfo['site_name']
        );

        $this->load->view('admin/login',$data2);
	}

	public function logout() {
	    $this->_deleteCookie();
	    redirect(base_url().'admincp.php');
	}

	public function validateUnique() {
		$username = $this->input->post('username', TRUE);
		if (! empty($username)) {
		    if ($this->tableObject->validateUnique($username)) {
		        printAjaxError('fail', '', '用户名已经存在，请换个用户名！');
		    } else {
		        printAjaxSuccess('', '用户名可使用！');
		    }
		}
	}


	//加盐算法
	public function createPasswordSALT($user, $salt, $password) {

	    return md5($user.$salt.$password);
	}

    private function _deleteCookie() {
		delete_cookie('admin_id');
		delete_cookie('admin_username');
		delete_cookie('admin_group_name');
		delete_cookie('admin_ip');
		delete_cookie('admin_ip_address');
	}

	private function _setCookie($data) {
	    $cookie1 = array(
                   'name'  =>'admin_id',
                   'value' =>$data['id'],
                   'expire'=>0
                   );
		set_cookie($cookie1);

		$cookie2 = array(
                   'name'  =>'admin_username',
                   'value' =>$data['username'],
                   'expire'=>0
                   );
		set_cookie($cookie2);

		$cookie3 = array(
                   'name'  =>'admin_group_name',
                   'value' =>$data['group_name'],
                   'expire'=>0
                   );
		set_cookie($cookie3);

		$cookie7 = array(
		    'name'  =>'admin_group_id',
		    'value' =>$data['admin_group_id'],
		    'expire'=>0
		);
		set_cookie($cookie7);

		$cookie5 = array(
		    'name'  =>'admin_ip',
		    'value' =>$data['ip'],
		    'expire'=>0
		);
		set_cookie($cookie5);

		$cookie6 = array(
		    'name'  =>'admin_ip_address',
		    'value' =>$data['ip_address'],
		    'expire'=>0
		);
		set_cookie($cookie6);
	}
}
/* End of file admin.php */
/* Location: ./application/admin/controllers/admin.php */