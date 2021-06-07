<?php
class Ad extends CI_Controller {
	private $_title = '广告管理';
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

		$this->_tool = $this->load->view('element/ad_tool', '', TRUE);
		$this->load->model('Adgroup_model', '', TRUE);
		$this->load->library('pagination');
	}

	public function index($page = 0) {
	    checkPermission("{$this->_template}_index");

		if (! $this->uri->segment(2)) {
		    $this->session->unset_userdata("search");
		}
		$this->session->set_userdata(array("{$this->_template}RefUrl"=>base_url().'admincp.php/'.uri_string()));
		$adType = array('image'=>'图片广告', 'flash'=>'<font color="#077ac7">Flash广告</font>', 'html'=>'<font color="#ff0000">Html广告</font>', 'text'=>'<font color="#e76e24">文字广告</font>');
		$strWhere = $this->session->userdata('search')?$this->session->userdata('search'):NULL;
		if ($_POST) {
			$strWhere = "{$this->_table}.id > 0 ";
		    $categoryId = $this->input->post('category_id', TRUE);
		    $adTypes = $this->input->post('ad_type', TRUE);
		    if ($categoryId) {
		        $strWhere .= " and {$this->_table}.category_id = {$categoryId} ";
		    }
		    if ($adTypes) {
		        $strWhere .= " and {$this->_table}.ad_type = '{$adTypes}' ";
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

		$itemList = $this->tableObject->gets($strWhere, $paginationConfig['per_page'], $page);
		$adgroupList = $this->Adgroup_model->gets();

		$data = array(
		              'tool'=>$this->_tool,
		              'adType'=>$adType,
		              'template'=>$this->_template,
		              'adgroupList'=>$adgroupList,
		              'itemList'=>$itemList,
		              'pagination'=>$pagination,
		              'paginationCount'=>$paginationCount,
		              'pageCount'=>ceil($paginationCount/$paginationConfig['per_page'])
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
		    $fields = array(
			          'ad_type'=>    $this->input->post('ad_type'),
			          'content'=>    unhtml($this->input->post('content')),
			          'width'=>      $this->input->post('width'),
			          'height'=>     $this->input->post('height'),
			          'enable'=>     $this->input->post('enable'),
		              'category_id'=>$this->input->post('category_id'),
		              'path'=>       $this->input->post('path', TRUE),
		              'ad_text'=>    $this->input->post('ad_text', TRUE),
		              'url'=>        $this->input->post('url')
			          );
		    if ($this->tableObject->save($fields, $id?array('id'=>$id):$id)) {
		    	printAjaxSuccess($prfUrl);
			} else {
				printAjaxError('fail', "操作失败！");
			}
		}

		$itemInfo = $this->tableObject->get('*', array('id'=>$id));
		$adgroupList = $this->Adgroup_model->gets();

	    $data = array(
		        'tool'=>$this->_tool,
	            'itemInfo'=>$itemInfo,
	            'template'=>$this->_template,
	            'adgroupList'=>$adgroupList,
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
}
/* End of file link.php */
/* Location: ./application/admin/controllers/link.php */