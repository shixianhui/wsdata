<?php
class Users extends CI_Controller {
	private $_displayArr = array('1'=>'启用', '2'=>'禁用', '3'=>'注销');
	private $_sexArr = array('0'=>'未知', '1'=>'男', '2'=>'女');
	private $_typeArr = array('0'=>'普通会员');
	private $_title = '会员管理';
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
        $this->load->model('Attachment_model', '', TRUE);
        $this->load->model('Area_model', '', TRUE);
        $this->load->model('Admin_model', '', TRUE);
		$this->load->helper(array('url', 'my_fileoperate'));
	}

	public function index($refresh = 1, $page = 0) {
		checkPermission("{$this->_template}_index");
		if ($refresh) {
            $refresh = 0;
            $this->session->unset_userdata("search");
        }
		$this->session->set_userdata(array("{$this->_template}RefUrl"=>base_url().'admincp.php/'.uri_string()));
		$strWhere = "{$this->_table}.status > 0";
        $strWhere = $this->session->userdata($this->_template.'_search')?$this->session->userdata($this->_template.'_search'):$strWhere;
		if ($_POST) {
			$strWhere = "{$this->_table}.status > 0";

			$id = $this->input->post('id', TRUE);
		    $username = trim($this->input->post('username', TRUE));
		    $nick_name = trim($this->input->post('nick_name', TRUE));
		    $type = $this->input->post('type', TRUE);
		    $display = $this->input->post('display', TRUE);
		    $custom_attribute = $this->input->post('custom_attribute', TRUE);
		    $startTime = $this->input->post('inputdate_start');
		    $endTime = $this->input->post('inputdate_end');

		    if ($id) {
		        $strWhere .= " and {$this->_table}.id = {$id} ";
		    }
		    if ($username) {
		        $strWhere .= " and lower({$this->_table}.username) regexp '".strtolower($username)."' ";
		    }
		    if ($nick_name) {
		        $strWhere .= " and {$this->_table}.nickname = '{$nick_name}' ";
		    }
		    if (! empty($custom_attribute) ) {
		        $strWhere .= " and {$this->_table}.custom_attribute like '%".$custom_attribute."%'";
		    }
		    if ($type) {
		        $strWhere .= " and {$this->_table}.type = '{$type}' ";
		    }
		    if ($display != "") {
		        $strWhere .= " and {$this->_table}.display = '{$display}' ";
		    }
		    if (! empty($startTime) && ! empty($endTime)) {
		    	$strWhere .= " and {$this->_table}.add_time > ".strtotime($startTime.' 00:00:00')." and {$this->_table}.add_time < ".strtotime($endTime.' 23:59:59')." ";
		    }

		    $this->session->set_userdata('search', $strWhere);
		}

		//分页
		$this->config->load('pagination_config', TRUE);
		$paginationCount = $this->tableObject->count($strWhere);
    	$paginationConfig = $this->config->item('pagination_config');
    	$paginationConfig['base_url'] = base_url()."admincp.php/{$this->_template}/index/{$refresh}";
    	$paginationConfig['total_rows'] = $paginationCount;
    	$paginationConfig['uri_segment'] = 4;
		$this->pagination->initialize($paginationConfig);
		$pagination = $this->pagination->create_links();

		$item_list = $this->tableObject->gets('*', $strWhere, $paginationConfig['per_page'], $page);
		if ($item_list){
		    foreach ($item_list as $key=>$value){
				$inviter_name = '';
				if ($value['inviter_id']) {
					$inviter_info = $this->tableObject->getInfo('real_name,nickname',array('id'=>$value['inviter_id']));
					$inviter_name = $inviter_info ? ($inviter_info['real_name'] ? $inviter_info['real_name'] : $inviter_info['nickname']) : '';
				}
                $item_list[$key]['inviter_name'] = $inviter_name;
				$item_list[$key]['sex'] = $this->_sexArr[$value['sex']];
            }
        }

		$data = array(
		              'tool'=>$this->_tool,
		              'displayArr'=>$this->_displayArr,
		              'sexArr'=>$this->_sexArr,
		              'typeArr'=>$this->_typeArr,
		              'template'=>$this->_template,
		              'item_list'=>json_encode($item_list),
		              'pagination'=>$pagination,
		              'table_limit'=>$paginationConfig['per_page'],
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
        $item_info = $this->tableObject->get('*',array('id'=>$id));
        if ($_POST) {
			$username = trim($this->input->post('username', TRUE));
            $path = $this->input->post('path', TRUE);

			$fields = array(
                'username'=>      $username,
                'nickname'=>     $this->input->post('nickname', TRUE),
                'real_name'=>     $this->input->post('real_name', TRUE),
                'sex'=>           $this->input->post('sex', TRUE),
                'mobile'=>        $this->input->post('mobile', TRUE),
                'status'=>        $this->input->post('status', TRUE),
			);
			if ($path) {
				$fields['path'] = $path;
			}

		    $password = trim($this->input->post('password', TRUE));
			if ($password) {
				$addTime = $id ? strtotime($item_info['create_time']) : time();
				if (!$id) {
					$fields['create_time'] = date('Y-m-d H:i:s', $addTime);
				}
			    $fields['password'] = $this->_createPasswordSALT($username, $addTime, $password);
			}
			if (empty($id)) {
			    if ($this->tableObject->validateUnique($username)) {
			        printAjaxError('fail', "用户名已经存在，请换个用户名！");
			    }
			}

		    if ($this->tableObject->save($fields, $id?array('id'=>$id):$id)) {
		    	printAjaxSuccess('close_layer','添加成功');
			} else {
				printAjaxError('fail', "操作失败！");
			}
		}

        $area_list = $this->Area_model->gets('id, name', array('parent_id'=>1));
	    $data = array(
		        'tool'=>$this->_tool,
	            'item_info'=>$item_info,
	            'item_info_json'=>json_encode($item_info),
                'area_list'=>$area_list,
	            'template'=>$this->_template,
	            'typeArr'=>$this->_typeArr,
	            'prfUrl'=>$prfUrl
		        );
		$layout = array(
		          'title'=>$this->_title,
				  'content'=>$this->load->view($this->_template.'/save', $data, TRUE)
		          );
		$this->load->view('layout/default', $layout);
	}

    public function selector($page = 0) {
        if (! $this->uri->segment(2)) {
            $this->session->unset_userdata("search");
        }
        $this->session->set_userdata(array("{$this->_template}RefUrl"=>base_url().'admincp.php/'.uri_string()));
        $strWhere = $this->session->userdata('search')?$this->session->userdata('search'):NULL;
        if ($_POST) {
            $strWhere = "{$this->_table}.id > 0";

            $id = $this->input->post('id', TRUE);
            $username = trim($this->input->post('username', TRUE));
            $nick_name = trim($this->input->post('nick_name', TRUE));
            $type = $this->input->post('type', TRUE);
            $display = $this->input->post('display', TRUE);
            $custom_attribute = $this->input->post('custom_attribute', TRUE);
            $startTime = $this->input->post('inputdate_start');
            $endTime = $this->input->post('inputdate_end');

            if ($id) {
                $strWhere .= " and {$this->_table}.id = {$id} ";
            }
            if ($username) {
                $strWhere .= " and lower({$this->_table}.username) = '".strtolower($username)."' ";
            }
            if ($nick_name) {
                $strWhere .= " and {$this->_table}.nickname = '{$nick_name}' ";
            }
            if (! empty($custom_attribute) ) {
                $strWhere .= " and {$this->_table}.custom_attribute like '%".$custom_attribute."%'";
            }
            if ($type) {
                $strWhere .= " and {$this->_table}.type = '{$type}' ";
            }
            if ($display != "") {
                $strWhere .= " and {$this->_table}.display = '{$display}' ";
            }
            if (! empty($startTime) && ! empty($endTime)) {
                $strWhere .= " and {$this->_table}.add_time > ".strtotime($startTime.' 00:00:00')." and {$this->_table}.add_time < ".strtotime($endTime.' 23:59:59')." ";
            }

            $this->session->set_userdata('search', $strWhere);
            $page = 0;
        }

        //分页
        $this->config->load('pagination_config', TRUE);
        $paginationCount = $this->tableObject->count($strWhere);
        $paginationConfig = $this->config->item('pagination_config');
        $paginationConfig['base_url'] = base_url()."admincp.php/{$this->_template}/selector/";
        $paginationConfig['total_rows'] = $paginationCount;
        $paginationConfig['uri_segment'] = 3;
        $this->pagination->initialize($paginationConfig);
        $pagination = $this->pagination->create_links();

        $itemList = $this->tableObject->gets($strWhere, $paginationConfig['per_page'], $page);
        if ($itemList){
            foreach ($itemList as $key=>$value){
                $user_info = $this->tableObject->getInfo('real_name,nickname',array('id'=>$value['parent_id']));
                $itemList[$key]['parent_name'] = $user_info ? ($user_info['real_name'] ? $user_info['real_name'] : $user_info['nickname']) : '';
                $itemList[$key]['user_name'] = $value['real_name'] ? $value['real_name'] : $value['nickname'];
            }
        }

        $data = array(
            'tool'=>$this->_tool,
            'displayArr'=>$this->_displayArr,
            'sexArr'=>$this->_sexArr,
            'typeArr'=>$this->_typeArr,
            'template'=>$this->_template,
            'itemList'=>$itemList,
            'pagination'=>$pagination,
            'paginationCount'=>$paginationCount,
            'pageCount'=>ceil($paginationCount/$paginationConfig['per_page'])
        );
        $layout = array(
            'title'=>$this->_title,
            'content'=>$this->load->view($this->_template.'/selector', $data, TRUE)
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

    public function display() {
        checkPermissionAjax("{$this->_template}_edit");

	    $ids = $this->input->post('ids');
		$display = $this->input->post('display');

		if (! empty($ids) && $display != "") {
			if($this->tableObject->save(array('status'=>$display), 'id in ('.$ids.')')) {
			    printAjaxSuccess('', '修改状态成功！');
			}
		}

		printAjaxError('fail', '修改状态失败！');
	}



	//加盐算法
	private function _createPasswordSALT($user, $salt, $password) {

	    return md5(strtolower($user).$salt.$password);
	}
}
/* End of file admin.php */
/* Location: ./application/admin/controllers/admin.php */