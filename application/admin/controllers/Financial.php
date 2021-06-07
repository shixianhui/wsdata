<?php
class Financial extends CI_Controller {
	private $_title = '会员财务记录';
	private $_tool = '';
	private $_table = '';
	private $_payment_type_arr = array(
        'recharge_in' => '充值',
        'recharge_out' => '提现'
	);

	private $_pay_way_arr = array(
        '1'=>'预存款支付',
        '2'=>'支付宝支付',
        '3'=>'微信支付',
        '4'=>'网银支付'
    );

	public function __construct() {
		parent::__construct();
		//获取表名
		$this->_table = $this->uri->segment(1);
		//快捷方式
		$this->_tool = $this->load->view("element/financial_tool", NULL, TRUE);
		//获取表对象
		$this->load->model(ucfirst($this->_table).'_model', 'tableObject', TRUE);
		$this->load->model('User_model', '', TRUE);
	}

	public function index($userId = 0,$flush = 1, $page = 0) {
        if ($flush) {
            $flush = 0;
            $this->session->unset_userdata('search');
        }
		$this->session->set_userdata(array("{$this->_table}RefUrl"=>base_url().'admincp.php/'.uri_string()));
		$condition = "{$this->_table}.id > 0 ";
		if ($userId){
            $condition .= " and {$this->_table}.user_id = {$userId}";
        }
		$strWhere = $this->session->userdata('search')?$this->session->userdata('search'):$condition;

		if ($_POST) {
			$strWhere = $condition;
			$user_id = trim($this->input->post('user_id', TRUE));
			$username = trim($this->input->post('username', TRUE));
		    $startTime = $this->input->post('inputdate_start', TRUE);
		    $endTime = $this->input->post('inputdate_end', TRUE);

		    if (! empty($user_id) ) {
		    	$strWhere .= " and user_id = '{$user_id}'";
		    }
		    if (! empty($username) ) {
		        $strWhere .= " and username = '{$username}'";
		    }
		    if (! empty($startTime) && ! empty($endTime)) {
		    	$strWhere .= " and add_time > ".strtotime($startTime.' 00:00:00')." and add_time < ".strtotime($endTime.' 23:59:59').' ';
		    }
		    $this->session->set_userdata('search', $strWhere);
		}

		//分页
		$this->config->load('pagination_config', TRUE);
		$paginationCount = $this->tableObject->rowCount($strWhere);
    	$paginationConfig = $this->config->item('pagination_config');
    	$paginationConfig['base_url'] = base_url()."admincp.php/{$this->_table}/index/{$userId}/{$flush}";
    	$paginationConfig['total_rows'] = $paginationCount;
    	$paginationConfig['uri_segment'] = 5;
		$this->pagination->initialize($paginationConfig);
		$pagination = $this->pagination->create_links();

		$itemList = $this->tableObject->gets($strWhere, $paginationConfig['per_page'], $page);
		$count0 = 0;
		$count1 = 0;
		$sumprice0 = 0;
		$sumprice1 = 0;
		foreach ($itemList as $item) {
			//支付
		    if ($item['price'] < 0) {
		        $count1++;
		        $sumprice1+=$item['price'];
		    } else {
		        $count0++;
		        $sumprice0+=$item['price'];
		    }
		}

		$data = array(
		        'tool'      =>$this->_tool,
				'itemList'  =>$itemList,
		        'pagination'=>$pagination,
		        'paginationCount'=>$paginationCount,
		        'pageCount'=>ceil($paginationCount/$paginationConfig['per_page']),
		        'count0'=>$count0,
		        'count1'=>$count1,
		        'sumprice0'=>$sumprice0,
		        'sumprice1'=>$sumprice1,
				'payment_type_arr'=>$this->_payment_type_arr,
				'pay_way_arr'=>$this->_pay_way_arr,
		        'table'=>$this->_table
		        );

	    $layout = array(
			      'title'=>$this->_title,
				  'content'=>$this->load->view($this->_table.'/index', $data, TRUE)
			      );
	    $this->load->view('layout/default', $layout);
	}

	public function recharge($userId = NULL) {
		$prfUrl = $this->session->userdata($this->_table.'RefUrl')?$this->session->userdata($this->_table.'RefUrl'):base_url()."admincp.php/{$this->_table}/index/";
	    if ($_POST) {
			$username = $this->input->post('username', TRUE);
		    $price = $this->input->post('price', TRUE);
		    $remark = $this->input->post('remark', TRUE);

	        if (!$username) {
	            printAjaxError('fail', "充值用户名不能为空！");
	        }
	        if (!$price) {
	            printAjaxError('fail', "充值金额不能为空或零！");
	        }
	        if ($price <= 0) {
	            printAjaxError('fail', "充值金额必须大于零！");
	        }
	        $userInfo = $this->User_model->get(array('user.username'=>$username));
	        if (! $userInfo) {
	        	printAjaxError('fail', "充值用户名不存在！");
	        }
	        $fields = array('total'=>($price + $userInfo['total']));
		    if ($this->User_model->save($fields, array('id'=>$userInfo['id']))) {
		    	$fFields = array(
		    	           'cause'=>'充值成功-'.$remark,
		    	           'price'=>$price,
		    			   'balance'=>($price + $userInfo['total']),
					       'add_time'=>time(),
		    			   'user_id'=>$userInfo['id'],
					       'username'=>$userInfo['username'],
		    			   'type'=>'recharge_in'
		    	           );
		    	$this->tableObject->save($fFields);
				printAjaxSuccess($prfUrl, '充值成功！');
			} else {
				printAjaxError('fail', "操作失败！");
			}
		}
		$userInfo = array();
		if ($userId) {
		    $userInfo = $this->User_model->getInfo('username', array('id'=>$userId));
		}
	    $data = array(
		        'tool'      =>$this->load->view("element/rechange_tool", NULL, TRUE),
		        'table'=>$this->_table,
	            'prfUrl'=>$prfUrl,
	            'userInfo'=>$userInfo
		        );

	    $layout = array(
			      'title'=>$this->_title,
				  'content'=>$this->load->view($this->_table.'/save', $data, TRUE)
			      );
	    $this->load->view('layout/default', $layout);
	}

	public function debit($userId = NULL) {
		$prfUrl = $this->session->userdata($this->_table.'RefUrl')?$this->session->userdata($this->_table.'RefUrl'):base_url()."admincp.php/{$this->_table}/index/";
		if ($_POST) {
			$username = $this->input->post('username', TRUE);
			$price = $this->input->post('price', TRUE);
			$remark = $this->input->post('remark', TRUE);

			if (!$username) {
				printAjaxError('fail', "扣款用户名不能为空！");
			}
			if (!$price) {
				printAjaxError('fail', "扣款金额不能为空或零！");
			}
			if ($price <= 0) {
				printAjaxError('fail', "扣款金额必须大于零！");
			}
			$userInfo = $this->User_model->get(array('user.username'=>$username));
			if (! $userInfo) {
				printAjaxError('fail', "扣款用户名不存在！");
			}
			if ($userInfo['total'] - $price < 0) {
				printAjaxError('fail', "账户余额不足，扣款失败");
			}
			$fields = array('total'=>($userInfo['total'] - $price));
			if ($this->User_model->save($fields, array('id'=>$userInfo['id']))) {
				$fFields = array(
						'cause'=>'扣款成功-'.$remark,
						'price'=>-$price,
						'balance'=>($userInfo['total'] - $price),
						'add_time'=>time(),
						'user_id'=>$userInfo['id'],
						'username'=>$userInfo['username'],
						'type'=>'recharge_out'
				);
				$this->tableObject->save($fFields);
				printAjaxSuccess($prfUrl, '扣款成功！');
			} else {
				printAjaxError('fail', "操作失败！");
			}
		}
		$userInfo = array();
		if ($userId) {
			$userInfo = $this->User_model->getInfo('username', array('id'=>$userId));
		}
		$data = array(
				'tool'      =>$this->load->view("element/rechange_tool", NULL, TRUE),
				'table'=>$this->_table,
				'prfUrl'=>$prfUrl,
				'userInfo'=>$userInfo
		);

		$layout = array(
				'title'=>$this->_title,
				'content'=>$this->load->view($this->_table.'/debit', $data, TRUE)
		);
		$this->load->view('layout/default', $layout);
	}
}
/* End of file news.php */
/* Location: ./application/admin/controllers/news.php */