<?php
class News extends CI_Controller {
	private $_title = '普通文章';
	private $_tool = '';
	private $_table = '';
	private $_template = '';
	public function __construct() {
		parent::__construct();
		//获取表名
		$this->_table = $this->uri->segment(1);
		//模型名
		$this->_template = $this->uri->segment(1);
		//快捷方式
		$this->_tool = $this->load->view("element/content_tool", array('template'=>$this->_template), TRUE);
		//获取表对象
		$this->load->model(ucfirst($this->_template).'_model', 'tableObject', TRUE);
		$this->load->model('Menu_model', '', TRUE);
		$this->load->model('System_model', '', TRUE);
    	$this->load->helper(array('url', 'my_fileoperate', 'file'));
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
			$title = $this->input->post('title');
			$categoryId = $this->input->post('select_category_id');
			$display = $this->input->post('display');
		    $startTime = $this->input->post('inputdate_start');
		    $endTime = $this->input->post('inputdate_end');

		    if (! empty($categoryId) ) {
		        $strWhere .= " and {$this->_table}.category_id = {$categoryId} ";
		    }
		    if (! empty($title) ) {
		        $strWhere .= " and {$this->_table}.title like '%".$title."%'";
		    }
		    if ($display != "") {
		        $strWhere .= " and {$this->_table}.display={$display} ";
		    }
		    if (! empty($startTime) && ! empty($endTime)) {
		    	$strWhere .= " and {$this->_table}.add_time > ".strtotime($startTime.' 00:00:00')." and {$this->_table}.add_time < ".strtotime($endTime." 23:59:59")." ";
		    }
		    $this->session->set_userdata('search', $strWhere);
            $page = 0;
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

		$menuList = $this->Menu_model->menuTree('id, menu_name', $this->_template);
		$itemList = $this->tableObject->gets($strWhere, $paginationConfig['per_page'], $page);
		foreach ($itemList as $key=>$value) {
		    $itemList[$key]['title'] = $value['title'].'&nbsp;&nbsp;'.$this->tableObject->attribute($value['custom_attribute']);
		}

		$data = array(
		        'tool'      =>$this->_tool,
				'itemList'  =>$itemList,
		        'pagination'=>$pagination,
		        'paginationCount'=>$paginationCount,
		        'pageCount'=>ceil($paginationCount/$paginationConfig['per_page']),
		        'template'=>$this->_template,
		        'menuList'=>$menuList
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
		$prfUrl = $this->session->userdata($this->_template.'RefUrl')?$this->session->userdata($this->_template.'RefUrl'):base_url()."admincp.php/{$this->_template}/index/";
		if ($_POST) {
			$custom_attribute = $this->input->post('custom_attribute', TRUE);
			if (! empty($custom_attribute)) {
			    $custom_attribute = implode($custom_attribute, ',');
			} else {
				$custom_attribute = '';
			}

			$fields = array(
			          'category_id'=>     $this->input->post('category_id', TRUE),
			          'title'=>           $this->input->post('title', TRUE),
			          'seo_title'=>       $this->input->post('seo_title', TRUE),
			          'title_color'=>     $this->input->post('title_color', TRUE),
			          'custom_attribute'=>$custom_attribute,
			          'author'=>          $this->input->post('author', TRUE),
			          'source'=>          $this->input->post('source', TRUE),
			          'relation'=>        $this->input->post('relation', TRUE),
			          'keyword'=>         $this->input->post('keyword', TRUE),
			          'abstract'=>        $this->input->post('abstract', TRUE),
			          'content'=>         unhtml($this->input->post('content')),
			          'hits'=>            $this->input->post('hits', TRUE),
			          'batch_path_ids'=>            $this->input->post('batch_path_ids', TRUE),
			          'add_time'=>        strtotime($this->input->post('add_time', TRUE)),
			          'path'=>            $this->input->post('path', TRUE)
			          );
		    if ($this->tableObject->save($fields, $id?array('id'=>$id):$id)) {
				printAjaxSuccess($prfUrl);
			} else {
				printAjaxError('fail', "操作失败！");
			}
		}

		$itemInfo = $this->tableObject->get('*', array('id'=>$id));
		$menuList = $this->Menu_model->menuTree('id, menu_name', $this->_template);

		$data = array(
		        'tool'=>$this->_tool,
		        'menuList'=>$menuList,
		        'template'=>$this->_template,
		        'itemInfo'=>$itemInfo,
		        'prfUrl'=>$prfUrl
		        );
		$layout = array(
		          'title'=>$this->_title,
				  'content'=>$this->load->view($this->_template.'/save', $data, TRUE)
		          );
		$this->load->view('layout/default', $layout);
	}

	public function getKeycode() {
		$this->load->library('Splitwordclass');
		$title = $this->input->post('title', TRUE);
		if ($title) {
			$splitword = new Splitwordclass();
			$keycode = $splitword->SplitRMM(iconv("UTF-8", "gbk", $title));
			$splitword->Clear();
			$keycode = iconv("gbk","UTF-8", $keycode);
			printAjaxData(array('keycode'=>$keycode));
		}
	}

	public function sort() {
	    checkPermissionAjax("{$this->_template}_edit");

		$ids = $this->input->post('ids', TRUE);
		$sorts = $this->input->post('sorts', TRUE);

		if (! empty($ids) && ! empty($sorts)) {
			$ids = explode(',', $ids);
			$sorts = explode(',', $sorts);

			foreach ($ids as $key=>$value) {
				$this->tableObject->save(
				                   array('sort'=>$sorts[$key]),
				                   array('id'=>$value));
			}
			printAjaxSuccess('', '排序成功！');
		}

		printAjaxError('fail', '排序失败！');
	}

	public function category() {
	    checkPermissionAjax("{$this->_template}_edit");

	    $ids = $this->input->post('ids', TRUE);
		$categoryId = $this->input->post('categoryId', TRUE);

		if (! empty($ids) && ! empty($categoryId)) {
			if($this->tableObject->save(array('category_id'=>$categoryId), 'id in ('.$ids.')')) {
			    printAjaxSuccess('', '修改栏目成功！');
			}
		}

		printAjaxError('fail', '修改栏目失败！');
	}

    public function display() {
        checkPermissionAjax("{$this->_template}_edit");

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
        checkPermissionAjax("{$this->_template}_edit");

	    $ids = $this->input->post('ids', TRUE);
		$customAttribute = $this->input->post('custom_attribute', TRUE);

		if (! empty($ids) && ! empty($customAttribute)) {
			if ($customAttribute == 'clear'){
			    $customAttribute = '';
			}
			if($this->tableObject->save(array('custom_attribute'=>$customAttribute), 'id in ('.$ids.')')) {
			    printAjaxSuccess('', '属性修改成功！');
			}
		}

		printAjaxError('fail', '属性修改失败！');
	}

    public function delete() {
        checkPermissionAjax("{$this->_template}_delete");

	    $ids = $this->input->post('ids', TRUE);

	    if (! empty($ids)) {
	        $itemList = $this->tableObject->gets($this->_table.".id in ($ids)");
	        foreach ($itemList as $key=>$value) {
	        	$filePath = "./{$value['html_path']}/{$value['id']}.html";
	        	if (file_exists($filePath)) {
	        		@unlink($filePath);
	        	}
	        }
	        if ($this->tableObject->delete('id in ('.$ids.')')) {
	            printAjaxData(array('ids'=>explode(',', $ids)));
	        }
	    }

	    printAjaxError('fail', '删除失败！');
	}

    public function html($page = 0) {
        checkPermission("{$this->_template}_html");

	    if (! $this->uri->segment(2)) {
		    $this->session->unset_userdata("search");
		}
		$this->session->set_userdata(array("{$this->_template}RefUrl"=>base_url().'admincp.php/'.uri_string()));
		$strWhere = $this->session->userdata('search')?$this->session->userdata('search'):NULL;

		if ($_POST) {
			$strWhere = "{$this->_table}.id > 0";
			$title = $this->input->post('title');
			$categoryId = $this->input->post('select_category_id');
			$display = $this->input->post('display');
		    $startTime = $this->input->post('inputdate_start');
		    $endTime = $this->input->post('inputdate_end');

		    if (! empty($categoryId) ) {
		        $strWhere .= " and {$this->_table}.category_id = {$categoryId} ";
		    }
		    if (! empty($title) ) {
		        $strWhere .= " and {$this->_table}.title like '%".$title."%'";
		    }
		    if ($display != "") {
		        $strWhere .= " and {$this->_table}.display={$display} ";
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
    	$paginationConfig['base_url'] = base_url()."admincp.php/{$this->_template}/html/";
    	$paginationConfig['total_rows'] = $paginationCount;
    	$paginationConfig['uri_segment'] = 3;
		$this->pagination->initialize($paginationConfig);
		$pagination = $this->pagination->create_links();

		$menuList = $this->Menu_model->menuTree('id, menu_name', $this->_template);
		$itemList = $this->tableObject->gets($strWhere, $paginationConfig['per_page'], $page);
		foreach ($itemList as $key=>$value) {
			if (file_exists("./".$value['html_path']."/{$value['id']}.html")) {
				$itemList[$key]['display'] = 1;
			} else {
			    $itemList[$key]['display'] = 0;
			}
		}

		$data = array(
		        'tool'      =>$this->_tool,
				'itemList'  =>$itemList,
		        'pagination'=>$pagination,
		        'paginationCount'=>$paginationCount,
		        'pageCount'=>ceil($paginationCount/$paginationConfig['per_page']),
		        'template'=>$this->_template,
		        'menuList'=>$menuList
		        );

	    $layout = array(
			      'title'=>$this->_title,
				  'content'=>$this->load->view($this->_template.'/html', $data, TRUE)
			      );
	    $this->load->view('layout/default', $layout);
	}

    public function htmlUpdate() {
        checkPermissionAjax("{$this->_template}_htmlUpdate");

        $systemInfo = $this->System_model->get('*', array('id'=>1));
		if ($systemInfo['html'] == 0) {
			printAjaxError('fail', '请到"系统设置 > 基本设置"开启静态！');
		}
	    $ids = $this->input->post('ids', TRUE);
	    if (! empty($ids)) {
	    	$itemList = $this->tableObject->gets($this->_table.".id in ($ids)");
	        foreach ($itemList as $key=>$value) {
	            if ($systemInfo['client_index']) {
			    	$url = base_url()."{$systemInfo['client_index']}/{$value['template']}/detail/{$value['id']}";
			    } else {
			    	$url = base_url()."{$systemInfo['client_index']}{$value['template']}/detail/{$value['id']}";
			    }
			    $content = file_get_contents ($url);
			    //在这里要对页面内容进入过滤
			    $filePath = "./{$value['html_path']}/";
		        createDirs($filePath);
				@write_file($filePath."/{$value['id']}.html", $content);
	        }
	    }
	    printAjaxSuccess('', '更新成功！');
	}

    public function htmlDelete() {
        checkPermissionAjax("{$this->_template}_htmlDelete");

	    $ids = $this->input->post('ids', TRUE);
	    if (! empty($ids)) {
	    	$itemList = $this->tableObject->gets($this->_table.".id in ($ids)");
	        foreach ($itemList as $key=>$value) {
	        	$filePath = "./{$value['html_path']}/{$value['id']}.html";
	        	if (file_exists($filePath)) {
	        		@unlink($filePath);
	        	}
	        }
	    }
	    printAjaxSuccess('', '删除成功！');
	}
}
/* End of file news.php */
/* Location: ./application/admin/controllers/news.php */