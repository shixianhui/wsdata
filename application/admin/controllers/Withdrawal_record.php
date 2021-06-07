<?php
class Withdrawal_record extends CI_Controller {
	private $_title = '提现管理';
	private $_tool = '';
	private $_table = '';
    private $_status_arr = array(
        '0'=>'<font color="red">待审核</font>',
        '1'=>'审核未通过',
        '2'=>'审核通过',
    );
	public function __construct() {
		parent::__construct();
		//获取表名
		$this->_table = $this->uri->segment(1);
		//快捷方式
		$this->_tool = $this->load->view("element/save_list_tool", array('table' => $this->_table, 'parent_title' => '交易管理', 'title' => '提现申请', 'is_checked' => 1), TRUE);
		//获取表对象
		$this->load->model(ucfirst($this->_table).'_model', 'tableObject', TRUE);
		$this->load->model('Menu_model', '', TRUE);
		$this->load->model('Orders_model', '', TRUE);
		$this->load->model('Financial_model', '', TRUE);
		$this->load->model('User_model', '', TRUE);
		$this->load->model('Attachment_model', '', TRUE);
		$this->load->model('Admin_model', '', TRUE);
		$this->load->model('Pay_log_model', '', TRUE);
		$this->load->model('Orders_process_model', '', TRUE);
        $this->load->helper('download');
	}

	public function index($clear = 1, $page = 0) {
        checkPermission("{$this->_table}_index");
	   clearSession(array('search'));
		if ($clear) {
			$clear = 0;
		    $this->session->unset_userdata("search");
		}
		$uri_2 = $this->uri->segment(2)?'/'.$this->uri->segment(2):'/index';
		$uri_sg = base_url().'admincp.php/'.$this->uri->segment(1).$uri_2."/{$clear}/{$page}";
        $this->session->set_userdata(array("{$this->_table}RefUrl"=>$uri_sg));
        $strWhere = "{$this->_table}.withdrawal_type = 0";
		$strWhere = $this->session->userdata('search')?$this->session->userdata('search'):$strWhere;

		if ($_POST) {
            $username = $this->input->post('username');
            $status = $this->input->post('status');
		    $startTime = $this->input->post('inputdate_start');
		    $endTime = $this->input->post('inputdate_end');

		    if (! empty($username) ) {
		        $strWhere .= " and user.username like '%".$username."%'";
		    }
		    if ($status != "") {
		        $strWhere .= " and {$this->_table}.status={$status} ";
		    }
		    if (! empty($startTime) && ! empty($endTime)) {
		    	$strWhere .= " and {$this->_table}.create_time > '{$startTime} 00:00:00' and {$this->_table}.create_time < '{$endTime} 23:59:59'";
		    }
            $this->session->set_userdata('search', $strWhere);
            $page = 0;
		}

		//分页
		$this->config->load('pagination_config', TRUE);
		$paginationCount = $this->tableObject->count_join_user($strWhere);
    	$paginationConfig = $this->config->item('pagination_config');
    	$paginationConfig['base_url'] = base_url()."admincp.php/{$this->_table}/index/{$clear}/";
    	$paginationConfig['total_rows'] = $paginationCount;
    	$paginationConfig['uri_segment'] = 4;
		$this->pagination->initialize($paginationConfig);
		$pagination = $this->pagination->create_links();

		$item_list = $this->tableObject->gets_join_user("{$this->_table}.*, user.username", $strWhere, $paginationConfig['per_page'], $page);

		$data = array(
		        'tool'      =>$this->_tool,
				'item_list'  =>$item_list,
		        'pagination'=>$pagination,
		        'paginationCount'=>$paginationCount,
		        'pageCount'=>ceil($paginationCount/$paginationConfig['per_page']),
		        'table'=>$this->_table,
                'status_arr'=>$this->_status_arr,
				'clear'=>$clear
		        );

	    $layout = array(
			      'title'=>$this->_title,
				  'content'=>$this->load->view("{$this->_table}/index", $data, TRUE)
			      );
	    $this->load->view('layout/default', $layout);
	}


    /**
     * 商家提现申请
     */
	public function store_index($clear = 1, $page = 0) {
        checkPermission("{$this->_table}_index");
	   clearSession(array('search'));
		if ($clear) {
			$clear = 0;
		    $this->session->unset_userdata("search");
		}
		$uri_2 = $this->uri->segment(2)?'/'.$this->uri->segment(2):'/index';
		$uri_sg = base_url().'admincp.php/'.$this->uri->segment(1).$uri_2."/{$clear}/{$page}";
        $this->session->set_userdata(array("{$this->_table}RefUrl"=>$uri_sg));
        $strWhere = "{$this->_table}.withdrawal_type = 1";
		$strWhere = $this->session->userdata('search')?$this->session->userdata('search'):$strWhere;

		if ($_POST) {
            $username = $this->input->post('username');
            $status = $this->input->post('status');
		    $startTime = $this->input->post('inputdate_start');
		    $endTime = $this->input->post('inputdate_end');

		    if (! empty($username) ) {
		        $strWhere .= " and stores.store_name like '%".$username."%'";
		    }
		    if ($status != "") {
		        $strWhere .= " and {$this->_table}.status={$status} ";
		    }
		    if (! empty($startTime) && ! empty($endTime)) {
		    	$strWhere .= " and {$this->_table}.create_time > '{$startTime} 00:00:00' and {$this->_table}.create_time < '{$endTime} 23:59:59'";
		    }
            $this->session->set_userdata('search', $strWhere);
            $page = 0;
		}

		//分页
		$this->config->load('pagination_config', TRUE);
		$paginationCount = $this->tableObject->count_join_user($strWhere);
    	$paginationConfig = $this->config->item('pagination_config');
    	$paginationConfig['base_url'] = base_url()."admincp.php/{$this->_table}/index/{$clear}/";
    	$paginationConfig['total_rows'] = $paginationCount;
    	$paginationConfig['uri_segment'] = 4;
		$this->pagination->initialize($paginationConfig);
		$pagination = $this->pagination->create_links();

		$item_list = $this->tableObject->gets_join_stores("{$this->_table}.*, stores.store_name", $strWhere, $paginationConfig['per_page'], $page);

		$data = array(
		        'tool'      =>$this->load->view("element/save_list_tool", array('table' => $this->_table, 'parent_title' => '交易管理', 'title' => '提现申请', 'is_checked' => 1, 'index_method'=>'store_index'), TRUE),
				'item_list'  =>$item_list,
		        'pagination'=>$pagination,
		        'paginationCount'=>$paginationCount,
		        'pageCount'=>ceil($paginationCount/$paginationConfig['per_page']),
		        'table'=>$this->_table,
                'status_arr'=>$this->_status_arr,
				'clear'=>$clear
		        );

	    $layout = array(
			      'title'=>$this->_title,
				  'content'=>$this->load->view("{$this->_table}/store_index", $data, TRUE)
			      );
	    $this->load->view('layout/default', $layout);
    }
    
	public function download($clear = 0, $page = 0) {
	   clearSession(array('search'));
		if ($clear) {
			$clear = 0;
		    $this->session->unset_userdata("search");
		}
		$uri_2 = $this->uri->segment(2)?'/'.$this->uri->segment(2):'/index';
		$uri_sg = base_url().'admincp.php/'.$this->uri->segment(1).$uri_2."/{$clear}/{$page}";
		$this->session->set_userdata(array("{$this->_table}RefUrl"=>$uri_sg));
		$strWhere = $this->session->userdata('search')?$this->session->userdata('search'):NULL;

		if ($_POST) {
			$strWhere = "{$this->_table}.id > 0";
            $username = $this->input->post('username');
            $status = $this->input->post('status');
		    $startTime = $this->input->post('inputdate_start');
		    $endTime = $this->input->post('inputdate_end');

		    if (! empty($username) ) {
		        $strWhere .= " and {$this->_table}.username like '%".$username."%'";
		    }
		    if ($status != "") {
		        $strWhere .= " and {$this->_table}.status={$status} ";
		    }
		    if (! empty($startTime) && ! empty($endTime)) {
		    	$strWhere .= ' and add_time > '.strtotime($startTime.' 00:00:00').' and add_time < '.strtotime($endTime.' 23:59:59').' ';
		    }
		    $this->session->set_userdata('search', $strWhere);
		}

		//分页
		$this->config->load('pagination_config', TRUE);
		$paginationCount = $this->tableObject->rowCount($strWhere);
    	$paginationConfig = $this->config->item('pagination_config');
    	$paginationConfig['base_url'] = base_url()."admincp.php/{$this->_table}/index/{$clear}/";
    	$paginationConfig['total_rows'] = $paginationCount;
    	$paginationConfig['uri_segment'] = 4;
		$this->pagination->initialize($paginationConfig);
		$pagination = $this->pagination->create_links();

		$item_list = $this->tableObject->gets('*', $strWhere, $paginationConfig['per_page'], $page);

        $data2 = [];
        $head = array (
            '会员信息',
            '账号类型',
            '开户名',
            '提现金额',
            '申请时间',
            '处理时间',
            '状态'
        );
        $data2[]=implode(',',$head);

        foreach ($item_list as $key => $item){
            $data1 = [];
            $data1 [] ="\t".$item['username']."\t";
            $data1 [] =$item['type'];
            $data1 [] =$item['realname'];
            $data1 [] =$item['money'];
            $data1 [] = date('Y-m-d H:i:s',$item['add_time']);
            $data1 [] = $item['read_time']? date('Y-m-d H:i:s',$item['read_time']):'';
            $data1 [] =$this->_status_arr[$item['status']];

            $data2[]=implode(',',$data1);
        }
        $Date = date("YmdHis");
        $Filename = $Date.".csv";
        $data=implode("\n",$data2);
        $data=iconv("UTF-8","GBK//IGNORE",$data);
        force_download($Filename, $data);
	}

	public function save($id = NULL) {
        $prfUrl = $this->session->userdata($this->_table.'RefUrl')?$this->session->userdata($this->_table.'RefUrl'):base_url()."admincp.php/{$this->_table}/index/";
        $this->session->set_userdata(array("ordersRefUrl"=>base_url().'admincp.php/'.uri_string()));
        $this->session->set_userdata(array("userRefUrl"=>base_url().'admincp.php/'.uri_string()));
        $item_info = $this->tableObject->get('*', array('id'=>$id));
        $user_info = $this->User_model->getInfo('username', ['id'=>$item_info['user_id']]);
        //凭证图片
//        $attachment_list = NULL;
//        if ($item_info && $item_info['batch_path_ids']) {
//            $tmp_atm_ids = preg_replace(array('/^_/', '/_$/', '/_/'), array('', '', ','), $item_info['batch_path_ids']);
//            $attachment_list = $this->Attachment_model->gets2($tmp_atm_ids);
//        }
//        $payment_title = '';
//        if ($item_info) {
//            $orders_info = $this->Orders_model->get('payment_id, payment_title', array('id'=>$item_info['orders_id']));
//            if ($orders_info) {
//                $payment_title = $orders_info['payment_title'];
//            }
//            $item_info['payment_title'] = $payment_title;
//        }


        $data = array(
            'tool'=>$this->_tool,
            'table'=>$this->_table,
            'item_info'=>$item_info,
            'user_info'=>$user_info,
            'status_arr'=>$this->_status_arr,
            'prfUrl'=>$prfUrl
        );
        $layout = array(
            'title'=>$this->_title,
            'content'=>$this->load->view("{$this->_table}/save", $data, TRUE)
        );
        $this->load->view('layout/default', $layout);
	}

	public function save_path(){
	    if($_POST){
	        $id = $this->input->post('id',TRUE);
	        $filed = array(
	          'path'=>$this->input->post('path',TRUE),
            );
	        if($this->tableObject->save($filed,array('id'=>$id))){
	            printAjaxSuccess('success','保存成功');
            }else{
	            printAjaxError('fail','保存失败');
            }
        }
    }

    public function delete() {
	    $ids = $this->input->post('ids', TRUE);

	    if (! empty($ids)) {
	        if ($this->tableObject->delete('id in ('.$ids.')')) {
	            printAjaxData(array('ids'=>explode(',', $ids)));
	        }
	    }

	    printAjaxError('删除失败！');
	}

    public function change_check() {
        if($_POST) {
            $id = $this->input->post('id', TRUE);
            $status = $this->input->post('status', TRUE);
            $client_remark = $this->input->post('client_remark', TRUE);
            $admin_remark = $this->input->post('admin_remark', TRUE);

            if (!$id) {
                printAjaxError('fail', '操作异常');
            }
            $item_info = $this->tableObject->get('*', array('id'=>$id));
            if (!$item_info) {
                printAjaxError('fail', '此提现信息不存在');
            }
//            if ($item_info['status'] != 0) {
//                printAjaxError('fail', '此退款状态异常');
//            }
            if (!$status) {
                printAjaxError('fail', '请选择审核状态');
            }
            if ($status == 1) {
                if (!$client_remark) {
                    printAjaxError('client_remark', '备注不能为空');
                }
                if (!$admin_remark) {
                    printAjaxError('admin_remark', '备注不能为空');
                }
            }
            $fields = array(
                'status'=>$status,
                'client_remark'=>$client_remark,
                'admin_remark'=>$admin_remark,
            );
            if (!$this->tableObject->save($fields, array('id'=>$item_info['id']))) {
                printAjaxError('fail', '操作失败');
            }
            printAjaxSuccess('success', '操作成功');
        }
    }

    public function refund_to_balance() {
        if ($_POST) {
            $id = $this->input->post('id', TRUE);
            if(!$id) {
                printAjaxError('fail', '操作异常');
            }
            $item_info = $this->tableObject->get('*', array('id'=>$id));
            if (!$item_info) {
                printAjaxError('fail', '此退款信息不存在');
            }
            if ($item_info['status'] != 2) {
                printAjaxError('fail', '状态异常');
            }
            $orders_info = $this->Orders_model->get('*', array('id'=>$item_info['orders_id']));
            if (!$orders_info) {
                printAjaxError('fail', '订单信息不存在');
            }
            //打款或面付
            if($orders_info['payment_id'] == 0){
                printAjaxError('fail', '此订单面付或打款，无法退款');
            }
            //预存款支付
            if ($orders_info['payment_id'] == 1) {
                $financial_info = $this->Financial_model->get(array('type'=>'order_out', 'ret_id'=>$orders_info['id']));
                if (!$financial_info) {
                    printAjaxError('fail', '支付记录不存在，退款失败');
                }
                $user_info = $this->User_model->get(array('user.id'=>$orders_info['user_id']));
                if (!$user_info) {
                    printAjaxError('fail', '买家信息不存在，退款失败');
                }
                $this->_balance_trade_refund(NULL, $item_info, $orders_info, $user_info, 3);
            }
            //支付宝
            else if ($orders_info['payment_id'] == 2) {
                $pay_log_info = $this->Pay_log_model->get(array('pay_log.out_trade_no'=>$orders_info['out_trade_no'], 'pay_log.payment_type'=>'alipay', 'pay_log.order_type'=>'orders'));
                if (!$pay_log_info) {
                    printAjaxError('fail', '支付记录不存在，退款失败');
                }
                if ($pay_log_info['trade_status'] != 'TRADE_SUCCESS' && $pay_log_info['trade_status'] != 'TRADE_FINISHED') {
                    printAjaxError('fail', '订单未付款，退款失败');
                }
                $user_info = $this->User_model->get(array('user.id'=>$orders_info['user_id']));
                if (!$user_info) {
                    printAjaxError('fail', '买家信息不存在，退款失败');
                }
                $this->_balance_trade_refund(NULL, $item_info, $orders_info, $user_info, 3);
            }
            //微信
            else if ($orders_info['payment_id'] == 3) {
                $pay_log_info = $this->Pay_log_model->get(array('pay_log.out_trade_no'=>$orders_info['out_trade_no'], 'pay_log.payment_type'=>'weixin', 'pay_log.order_type'=>'orders'));
                if (!$pay_log_info) {
                    printAjaxError('fail', '支付记录不存在，退款失败');
                }
                if ($pay_log_info['trade_status'] != 'TRADE_SUCCESS' && $pay_log_info['trade_status'] != 'TRADE_FINISHED') {
                    printAjaxError('fail', '订单未付款，退款失败');
                }
                $user_info = $this->User_model->get(array('user.id'=>$orders_info['user_id']));
                if (!$user_info) {
                    printAjaxError('fail', '买家信息不存在，退款失败');
                }
                $this->_balance_trade_refund(NULL, $item_info, $orders_info, $user_info, 3);
            }
            //网银
            else if ($orders_info['payment_id'] == 4) {

            }
        }
    }

    private function _balance_trade_refund($pay_log_info, $item_info, $orders_info, $user_info, $status = '4') {
        $fields = array(
            'total'=>$user_info['total'] + $item_info['price']
        );
        if (!$this->User_model->save($fields, array('id'=>$orders_info['user_id']))) {
            printAjaxError('fail', '退款操作失败');
        }
        $admin_id = get_cookie('admin_id');
        $operator = $this->Admin_model->get3('username',array('id'=>$admin_id));
        $fields = array(
            'cause'=>"退款成功-[订单号：{$orders_info['order_number']}]【{$operator['username']}】",
            'price'=>$item_info['price'],
            'balance'=>$user_info['total'] + $item_info['price'],
            'add_time'=>time(),
            'user_id'=>$user_info['id'],
            'username'=>$user_info['username'],
            'type' =>  'order_in',
            'pay_way'=>'1',
            'ret_id'=>$orders_info['id'],
            'from_user_id'=>$user_info['id']
        );
        $this->Financial_model->save($fields);
        //操作订单
        $fields = array(
            'cancel_cause'=> '交易关闭-[买家申请退款成功]',
            'status'=> 4
        );
        if ($this->Orders_model->save($fields, array('id' => $orders_info['id']))) {
            $admin_id = get_cookie('admin_id');
            $operator = $this->Admin_model->get3('username',array('id'=>$admin_id));
            $fields = array(
                'add_time' => time(),
                'content' => "交易关闭成功-[买家申请退款成功]【{$operator['username']}】",
                'order_id' => $orders_info['id'],
                'order_status'=>$orders_info['status'],
                'change_status'=>4
            );
            $this->Orders_process_model->save($fields);
        }
        //操作退款申请状态
        $this->tableObject->save(array('status'=>$status, 'update_time'=>time()), array('id'=>$item_info['id']));
        printAjaxSuccess('success', '退款成功');

    }
}
/* End of file news.php */
/* Location: ./application/admin/controllers/news.php */