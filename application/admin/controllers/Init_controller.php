<?php
class Init_controller extends CI_Controller {
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
        }
        $this->session->set_userdata(array("{$this->_controller}RefUrl"=>base_url().'admincp.php/'.uri_string()));
        $condition = "{$this->_table}.id > 0";
        $strWhere = $this->session->userdata('search')?$this->session->userdata('search'):$condition;
	    if ($_POST) {
			$strWhere = $condition;
			$title = $this->input->post('title');
			$display = $this->input->post('display');
		    $startTime = $this->input->post('inputdate_start');
		    $endTime = $this->input->post('inputdate_end');
		    if (! empty($title) ) {
		        $strWhere .= " and {$this->_table}.title REGEXP '{$title}'";
		    }
		    if ($display != "") {
		        $strWhere .= " and {$this->_table}.display = {$display} ";
		    }
		    // if (! empty($startTime) && ! empty($endTime)) {
		    // 	$strWhere .= " and {$this->_table}.add_time > ".strtotime($startTime.' 00:00:00')." and {$this->_table}.add_time < ".strtotime($endTime." 23:59:59")." ";
		    // }
			if (! empty($startTime) && ! empty($endTime)) {
		    	$strWhere .= " and {$this->_table}.create_time > '".$startTime." 00:00:00' and {$this->_table}.create_time < '".$endTime." 23:59:59'";
		    }
            $this->session->set_userdata('search', $strWhere);
            $page = 0;
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

		$data = array(
		        'tool'=>$this->_tool,
		        'table'=>$this->_table,
		        'item_list'=>$item_list,
                'pagination'=>$pagination,
                'paginationCount'=>$paginationCount,
                'pageCount'=>ceil($paginationCount/$paginationConfig['per_page']),
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
            $title = $this->input->post('title', TRUE);
            $abstract = $this->input->post('abstract', TRUE);
            $path = $this->input->post('path', TRUE);
            $content = unhtml($this->input->post('content', TRUE));
            $create_time = $this->input->post('create_time', TRUE);
            $fields = array(
                'title' => $title,
                'path' => $path,
                'abstract' => $abstract,
                'content' => $content,
                'create_time' => $create_time,
                
            );
            $ret = $this->tableObject->save($fields, $id ? array('id' => $id) : NULL);
            if ($ret) {
                printAjaxSuccess($prfUrl);
            } else {
                printAjaxError('fail',"操作失败！");
            }
        }


	    $data = array(
		        'tool'=>$this->_tool,
	            'item_info'=>$item_info,
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