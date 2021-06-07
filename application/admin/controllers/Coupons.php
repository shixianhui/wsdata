<?php
class Coupons extends CI_Controller {
	private $_title = '优惠券相关设置';
	private $_tool = '';
	private $_table = 'coupons';
	private $_template = 'coupons';
	private $_type_arr =  array('满减优惠券','无门槛优惠券');
	private $_way_arr =  ['正常发放', '新人券', '邀新券'];
	public function __construct() {
		parent::__construct();
         $this->load->model('System_model', '', TRUE);
         $this->load->model('User_coupons_model', '', TRUE);
         $this->load->model('User_model', '', TRUE);
		//获取表对象
		$this->load->model(ucfirst($this->_template).'_model', 'tableObject', TRUE);
        $this->_tool = $this->load->view("element/save_list_tool", array('table'=>$this->_table, 'parent_title'=>'交易管理', 'title'=>'优惠券'), TRUE);
	}

	public function index($clear = 1,$page = 0) {
        if ($clear) {
        	$clear = 0;
            $this->session->unset_userdata("search");
        }
        $uri_2 = $this->uri->segment(2)?'/'.$this->uri->segment(2):'/index';
        $uri_sg = base_url().'admincp.php/'.$this->uri->segment(1).$uri_2."/{$clear}/{$page}";
        $this->session->set_userdata(array("{$this->_template}RefUrl"=>$uri_sg));
            $condition = NULL;

        $strWhere = $this->session->userdata('search')?$this->session->userdata('search'):$condition;

        //分页
        $this->config->load('pagination_config', TRUE);
        $paginationConfig = $this->config->item('pagination_config');
        $paginationCount = $this->tableObject->count($strWhere);
        $paginationConfig['base_url'] = base_url()."admincp.php/{$this->_template}/index/{$clear}/";
        $paginationConfig['total_rows'] = $paginationCount;
        $paginationConfig['uri_segment'] = 4;
        $this->pagination->initialize($paginationConfig);
        $pagination = $this->pagination->create_links();

        $item_list = $this->tableObject->gets("*",$strWhere, $paginationConfig['per_page'], $page);

		$data = array(
		            'tool'=>$this->_tool,
					'template'=>       $this->_template,
					'type_arr'=>       $this->_type_arr,
					'way_arr'=>       $this->_way_arr,
					'pagination'=>     $pagination,
					'paginationCount'=>$paginationCount,
					'pageCount'=>      ceil($paginationCount/$paginationConfig['per_page']),
					'item_list' =>     $item_list
		              );
	    $layout = array(
			      'title'=>$this->_title,
                  'content'=>$this->load->view($this->_template.'/index', $data, TRUE),
			      );
	    $this->load->view('layout/default', $layout);
	}

	public function save($id = NULL) {
        $prfUrl = $this->session->userdata("{$this->_table}RefUrl") ? $this->session->userdata("{$this->_table}RefUrl") : base_url() . "admincp.php/{$this->_table}/index";
        if ($_POST) {

            $type = $this->input->post('type', TRUE);
            $fields = array(
                'title'          => $this->input->post('title', TRUE),
                'way'          => $this->input->post('way', TRUE),
                'type'           => $type,
                'achieve_amount' => $type ? 0 : $this->input->post('achieve_amount', TRUE),
                'used_amount'    => $this->input->post('used_amount', TRUE),
                'start_time'     => $this->input->post('start_time', TRUE) . ' 00:00:00',
                'end_time'       => $this->input->post('end_time', TRUE) . ' 23:59:59',
                'valid_days'         => $this->input->post('valid_days', TRUE),
                'status'         => $this->input->post('status', TRUE),
                'usable_goods_ids' => match_string($this->input->post('usable_goods_ids', TRUE)),
            );

            if ($this->tableObject->save($fields, $id ? array('id'=>$id) : NULL)) {
                printAjaxSuccess($prfUrl, '添加成功！');
            }
            printAjaxError('fail', '参数异常');
        }
        $item_info = $this->tableObject->get('*',array('id'=>$id));
        $data = array(
            'tool'     => $this->_tool,
            'template' => $this->_template,
            'type_arr' => $this->_type_arr,
            'way_arr' => $this->_way_arr,
            'item_info'   => $item_info,
            'prfUrl'   => $prfUrl,
        );
        $layout = array(
            'title'   => $this->_title,
            'content' => $this->load->view($this->_template . '/save', $data, TRUE)
        );
        $this->load->view('layout/default', $layout);
	}

    public function send($id = NULL) {
        $prfUrl = $this->session->userdata("{$this->_table}RefUrl") ? $this->session->userdata("{$this->_table}RefUrl") : base_url() . "admincp.php/{$this->_table}/index";
        if ($_POST) {
            $type = $this->input->post('type', TRUE);
            $user_ids = match_string($this->input->post('user_ids', TRUE));

            if ($type) {
                if (!$user_ids) {
                    printAjaxError('fail', '参数异常');
                }
                $data = [];
                foreach (explode(',', $user_ids) as $value) {
                    $data[] = [
                        'user_id' => $value,
                        'coupon_id' => $id
                    ];
                }
                if($this->User_coupons_model->save_batch($data)){
                    $number = count(explode(',', $user_ids));
                    $this->tableObject->save_column('get_number', 'get_number+'.$number, ['id'=>$id]);
                    printAjaxSuccess($prfUrl, '发放成功！');
                }
            } else {
                $data = [];
                $user_list = $this->User_model->gets();
                foreach ($user_list as $value) {
                    $data[] = [
                        'user_id' => $value['id'],
                        'coupon_id' => $id
                    ];
                }
                if($this->User_coupons_model->save_batch($data)){
                    $number = count(explode(',', $user_list));
                    $this->tableObject->save_column('get_number', 'get_number+'.$number, ['id'=>$id]);
                    printAjaxSuccess($prfUrl, '发放成功！');
                }
            }


            printAjaxError('fail', '参数异常');
        }

        $data = array(
            'tool'     => $this->_tool,
            'template' => $this->_template,
            'prfUrl'   => $prfUrl,
        );
        $layout = array(
            'title'   => $this->_title,
            'content' => $this->load->view($this->_template . '/send', $data, TRUE)
        );
        $this->load->view('layout/default', $layout);
	}

    public function delete() {
//        checkPermissionAjax("{$this->_template}_delete");

	    $ids = $this->input->post('ids', TRUE);

	    if (! empty($ids)) {
	        if ($this->tableObject->delete('id in ('.$ids.')')) {
	            printAjaxData(array('ids'=>explode(',', $ids)));
	        }
	    }

	    printAjaxError('fail', '删除失败！');
	}

    public function display() {
//        checkPermissionAjax("{$this->_table}_edit");
        if ($_POST) {
            $ids = $this->input->post('ids');
            $display = $this->input->post('display');

            if (! empty($ids) && $display != "") {
                if($this->tableObject->save(array('status'=>$display), 'id in ('.$ids.')')) {
                    printAjaxSuccess('success', '修改状态成功！');
                }
            }

            printAjaxError('fail', '修改状态失败！');
        }
    }

       //获取唯一的订单号
	private function _getRandnum() {
		//一秒钟一万件的量
		$randCode = '';
	    while (true) {
	    	$randCode = getRandCode(11);
	    	$count = $this->tableObject->rowCount(array('coupon_number'=>$randCode));
	    	if ($count > 0) {
	    		$randCode = '';
	    	    continue;
	    	} else {
	    		break;
	    	}
	    }

	    return $randCode;
	}
}
/* End of file admin.php */
/* Location: ./application/admin/controllers/admin.php */
