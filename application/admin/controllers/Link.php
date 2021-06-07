<?php
class Link extends CI_Controller {
	private $_title = '友情链接';
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
	}

	public function index($page = 0) {
	    checkPermission("{$this->_template}_index");
		if (! $this->uri->segment(2)) {
		    $this->session->unset_userdata("search");
		}
		$this->session->set_userdata(array("{$this->_template}RefUrl"=>base_url().'admincp.php/'.uri_string()));
		$strWhere = $this->session->userdata('search')?$this->session->userdata('search'):NULL;
		if ($_POST) {
		    $strWhere = "{$this->_table}.id is not NULL ";
		    $siteName = $this->input->post('site_name', TRUE);
		    $categoryId = $this->input->post('category_id');
		    $linkType = $this->input->post('link_type', TRUE);
		    $display = $this->input->post('display');

		    if ($siteName) {
		        $strWhere .= " and {$this->_table}.site_name like '%{$siteName}%' ";
		    }
		    if (! empty($categoryId) ) {
		        $strWhere .= " and {$this->_table}.category_id = {$categoryId} ";
		    }
		    if ($linkType) {
		        $strWhere .= " and {$this->_table}.link_type = '{$linkType}' ";
		    }
		    if ($display != "") {
		        $strWhere .= " and {$this->_table}.display={$display} ";
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

		$menuList = $this->Menu_model->menuTree('id, menu_name', $this->_table);
		$itemList = $this->tableObject->gets($strWhere, $paginationConfig['per_page'], $page);

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
		    $fields = array(
			          'site_name'=>  $this->input->post('site_name', TRUE),
			          'url'=>        $this->input->post('url', TRUE),
			          'sort'=>       $this->input->post('sort'),
			          'link_type'=>  $this->input->post('link_type', TRUE),
			          'description'=>$this->input->post('description', TRUE),
		              'qq'=>         $this->input->post('qq', TRUE),
		              'email'=>      $this->input->post('email', TRUE),
		              'category_id'=>$this->input->post('category_id'),
		              'path'=>       $this->input->post('path')
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

    public function category() {
        checkPermissionAjax("{$this->_template}_edit");
	    $ids = $this->input->post('ids', TRUE);
		$categoryId = $this->input->post('categoryId', TRUE);

		if (! empty($ids) && ! empty($categoryId)) {
			if($this->tableObject->save(array('category_id'=>$categoryId), 'id in ('.$ids.')')) {
			    printAjaxSuccess('', '修改分类成功！');
			}
		}

		printAjaxError('fail', '修改分类失败！');
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
}
/* End of file link.php */
/* Location: ./application/admin/controllers/link.php */