<?php
class Category extends CI_Controller {
	private $_title = '标题';
	private $_tool = '';
	private $_table = '';
	private $_controller = '';
	public function __construct() {
		parent::__construct();
		//获取表名
		$this->_table = $this->uri->segment(1);
		$this->_controller = $this->uri->segment(1);
		//快捷方式
		$this->_tool = $this->load->view("element/save_list_tool", array('table'=>$this->_controller, 'parent_title'=>'父级标题', 'title'=>$this->_title), TRUE);
		//获取表对象
		$this->load->model(ucfirst($this->_table).'_model', 'tableObject', TRUE);

    }

	public function index($clear = 1,$page = 0) {
        checkPermission("{$this->_controller}_index");
        if ($clear) {
            $clear = 0;
            $this->session->unset_userdata('search');
			$this->session->unset_userdata('category');
            $this->session->unset_userdata('keyword');
        }
        $this->session->set_userdata(array("{$this->_controller}RefUrl"=>base_url().'admincp.php/'.uri_string()));
        $condition = "{$this->_table}.id > 0";
        $strWhere = $this->session->userdata('search')?$this->session->userdata('search'):$condition;
	    if ($_POST) {
			$strWhere = $condition;
			$keyword = $this->input->post('keyword');
			$parent_id = $this->input->post('parent_id');
			$display = $this->input->post('display');
		    $startTime = $this->input->post('inputdate_start');
		    $endTime = $this->input->post('inputdate_end');
		    if (! empty($keyword) ) {
		        $strWhere .= " and {$this->_table}.name REGEXP '{$keyword}'";
				$this->session->set_userdata('keyword', $keyword);
		    }
		    if ($parent_id) {
		        $strWhere .= " and {$this->_table}.parent_id = {$parent_id} ";
				$this->session->set_userdata('category', $parent_id);
		    }
		    // if (! empty($startTime) && ! empty($endTime)) {
		    // 	$strWhere .= " and {$this->_table}.add_time > ".strtotime($startTime.' 00:00:00')." and {$this->_table}.add_time < ".strtotime($endTime." 23:59:59")." ";
		    // }
			if (! empty($startTime) && ! empty($endTime)) {
		    	$strWhere .= " and {$this->_table}.create_time > '".$startTime." 00:00:00' and {$this->_table}.create_time < '".$endTime." 23:59:59'";
		    }
            $this->session->set_userdata('search', $strWhere);
		}

        //分页
        $this->config->load('pagination_config', TRUE);
        $paginationCount = $this->tableObject->count($strWhere);
        $paginationConfig = $this->config->item('pagination_config');
        $paginationConfig['base_url'] = base_url() . "admincp.php/{$this->_controller}/index/{$clear}/";
        $paginationConfig['total_rows'] = $paginationCount;
        $paginationConfig['uri_segment'] = 4;
        $this->pagination->initialize($paginationConfig);
        $pagination = $this->pagination->create_links();

		$item_list = $this->tableObject->gets('*', $strWhere, $paginationConfig['per_page'], $page);
		foreach ($item_list as $key=>$value) {
			$parent_name = '';
			if ($value['parent_id']) {
				$parent_name = $this->tableObject->ancestry($value['parent_id']);
			}
			$item_list[$key]['parent_name'] = $parent_name;
		}

		$datas = $this->tableObject->gets('*', NULL, NULL, NULL, 'id', 'ASC');
		$menus_list = $this->tableObject->generateTree($datas);

		//筛选条件
		$category_name = '';
		$category = $this->session->userdata('category') ?: 0;
		if ($category) {
			$category_info = $this->tableObject->get('*', ['id'=>$category]);
			$category_name = $category_info ? $category_info['name'] : '';
		}
		$keyword = $this->session->userdata('keyword') ?: '';

		$filter = compact('category', 'category_name', 'keyword');

		$data = array(
		        'tool'=>$this->_tool,
		        'table'=>$this->_table,
		        'template'=>$this->_table,
		        'item_list'=>json_encode($item_list),
				'table_limit' => $paginationConfig['per_page'],
	            'menus_list'=>json_encode($menus_list),
                'pagination'=>$pagination,
                'paginationCount'=>$paginationCount,
                'pageCount'=>ceil($paginationCount/$paginationConfig['per_page']),
				'filter' => json_encode($filter)
		        );
	    $layout = array(
			      'title'=>$this->_title,
				  'content'=>$this->load->view("{$this->_controller}/index", $data, TRUE)
			      );
	    $this->load->view('layout/default', $layout);
	}


	public function save($id = 0) {
        if ($id) {
            checkPermission("{$this->_controller}_edit");
        } else {
            checkPermission("{$this->_controller}_create");
        }
		$prfUrl = $this->session->userdata("{$this->_controller}RefUrl")?:base_url()."admincp.php/{$this->_controller}/index";
        $item_info = $id ? $this->tableObject->get('*', array("id"=>$id)) : [];
        if ($_POST) {
            $name = $this->input->post('name', TRUE);
            $parent_id = $this->input->post('parent_id', TRUE);
			if ($item_info && $parent_id == $item_info['id']) {
                printAjaxError('fail',"上级分类不能与自身相同！");
			}
            $fields = array(
                'name' => $name,
                'parent_id' => $parent_id,
                
            );
            $ret = $this->tableObject->save($fields, $id ? array('id' => $id) : NULL);
            if ($ret) {
                printAjaxSuccess('close_layer','保存成功');
            } else {
                printAjaxError('fail',"操作失败！");
            }
        }

		if ($item_info) {
			$item_info['parent_menu'] = $this->tableObject->get('*', ['id'=>$item_info['parent_id']])['name'];
		}

		$datas = $this->tableObject->gets('*', NULL, NULL, NULL, 'id', 'ASC');
		$menus_list = $this->tableObject->generateTree($datas);

	    $data = array(
		        'tool'=>$this->_tool,
	            'item_info'=>$item_info,
	            'item_info_json'=>json_encode($item_info),
	            'menus_list'=>json_encode($menus_list),
	    		'table'=>$this->_table,
	            'prfUrl'=>$prfUrl
		        );
		$layout = array(
		          'title'=>$this->_title,
				  'content'=>$this->load->view("{$this->_controller}/save", $data, TRUE)
		          );
		$this->load->view('layout/default', $layout);
	}

    public function delete() {
        checkPermissionAjax("{$this->_controller}_delete");

        $ids = $this->input->post('ids', TRUE);

	    if (! empty($ids)) {
	        if ($this->tableObject->delete("id in ($ids)")) {
	            printAjaxData(array('ids'=>explode(',', $ids)));
	        }
	    }

	    printAjaxError('删除失败！');
	}

    public function sort() {
        checkPermissionAjax("{$this->_controller}_edit");

        $ids = $this->input->post('ids', TRUE);
		$sorts = $this->input->post('sorts', TRUE);

		if (! empty($ids) && ! empty($sorts)) {
			$ids = explode(',', $ids);
			$sorts = explode(',', $sorts);

			foreach ($ids as $key=>$value) {
				$this->tableObject->save(array('sort'=>$sorts[$key]),array('id'=>$value));
			}
			printAjaxSuccess('', '排序成功！');
		}
		printAjaxError('排序失败！');
	}

	public function display() {
        checkPermissionAjax("{$this->_controller}_edit");

        $ids = $this->input->post('ids');
		$display = $this->input->post('display');

		if (! empty($ids) && $display != "") {
			if($this->tableObject->save(array('display'=>$display), 'id in ('.$ids.')')) {
				printAjaxSuccess('', '修改状态成功！');
			}
		}

		printAjaxError('fail', '修改状态失败！');
	}

	public function attribute() {
        checkPermissionAjax("{$this->_controller}_edit");
        if ($_POST) {
        	$ids = $this->input->post('ids', TRUE);
        	$customAttribute = $this->input->post('custom_attribute', TRUE);

        	if (! empty($ids) && ! empty($customAttribute)) {
        		if ($customAttribute == 'clear'){
        			$customAttribute = '';
        		}
        		if($this->tableObject->save(array('custom_attribute'=>$customAttribute), 'id in ('.$ids.')')) {
        			printAjaxSuccess('success', '属性修改成功！');
        		}
        	}

        	printAjaxError('fail', '属性修改失败！');
        }
	}


}
/* End of file init_controller.php */
/* Location: ./application/admin/controllers/init_controller.php */