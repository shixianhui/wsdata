<?php
class Guestbook extends CI_Controller {
	private $_title = '留言管理';
	private $_tool = '';
	private $_table = '';
	private $_template = '';
	private $_status_arr = array('0'=>'<font color="red">待处理</font>', '1'=>'处理中', '2'=>'已处理');

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
			$strWhere = 'id > 0';
			$status = $this->input->post('status');
			$title = $this->input->post('title');
		    $startTime = $this->input->post('inputdate_start');
		    $endTime = $this->input->post('inputdate_end');

		    if ( isset($status) ) {
		        $strWhere .= " and status = '{$status}' ";
		    }
		    if (! empty($title) ) {
		        $strWhere .= " and (contact_name like '%".$title."%' or phone = '".$title."' or mobile = '".$title."' or qq = '".$title."'  or email = '".$title."')";
		    }
		    if (! empty($startTime) && ! empty($endTime)) {
		    	$strWhere .= ' and add_time > '.strtotime($startTime.' 00:00:00').' and add_time < '.strtotime($endTime.' 23:59:59').' ';
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

		$itemList = $this->tableObject->gets('*', $strWhere, $paginationConfig['per_page'], $page);
		$data = array(
		        'tool'      =>$this->_tool,
				'itemList'  =>$itemList,
		        'pagination'=>$pagination,
		        'paginationCount'=>$paginationCount,
		        'pageCount'=>ceil($paginationCount/$paginationConfig['per_page']),
		        'status_arr'=>$this->_status_arr,
		        'template'=>$this->_template
		        );

	    $layout = array(
			      'title'=>$this->_title,
				  'content'=>$this->load->view($this->_template.'/index', $data, TRUE)
			      );
	    $this->load->view('layout/default', $layout);
	}

	public function save($id = NULL) {
	    checkPermission("{$this->_template}_edit");
		$prfUrl = $this->session->userdata($this->_template.'RefUrl')?$this->session->userdata($this->_template.'RefUrl'):base_url()."admincp.php/{$this->_template}/index/";
		if ($_POST) {
			$custom_attribute = $this->input->post('custom_attribute', TRUE);
			if (! empty($custom_attribute)) {
			    $custom_attribute = implode($custom_attribute, ',');
			} else {
				$custom_attribute = '';
			}

			$fields = array(
			          'status'=>$this->input->post('status', TRUE),
			          'remark'=> $this->input->post('remark', TRUE),
			          'reply_time'=> strtotime($this->input->post('reply_time', TRUE))
			          );
		    if ($this->tableObject->save($fields, $id?array('id'=>$id):$id)) {
				printAjaxSuccess($prfUrl);
			} else {
				printAjaxError('fail', "操作失败！");
			}
		}

		$itemInfo = $this->tableObject->get('*', array('id'=>$id));

		$data = array(
		        'tool'=>$this->_tool,
		        'template'=>$this->_template,
		        'itemInfo'=>$itemInfo,
		        'status_arr'=>$this->_status_arr,
		        'prfUrl'=>$prfUrl
		        );
		$layout = array(
		          'title'=>$this->_title,
				  'content'=>$this->load->view($this->_template.'/save', $data, TRUE)
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
}
/* End of file news.php */
/* Location: ./application/admin/controllers/news.php */