<?php
class Orders extends CI_Controller
{
    private $_title = '订单管理';
    private $_tool = '';
    private $_table = '';
    private $_template = '';
    private $_status_arr = array(
        '0' => '<font color="#ff4200">未付款</font>',
        '1' => '<font color="#cc3333">已付款</font>',
        '2' => '<font color="#ff811f">已使用</font>',
        '3' => '<font color="#066601">交易成功</font>',
        '9' => '<font color="#066601">已退款</font>',
        '10' => '<font color="#a0a0a0">交易关闭</font>',
    );
    public function __construct()
    {
        parent::__construct();
        //获取表名
        $this->_table = $this->uri->segment(1);
        //模型名
        $this->_template = $this->uri->segment(1);
        //快捷方式
        $this->_tool = $this->load->view("element/save_list_tool", array(
            'table' => $this->_table, 
            'parent_title' => '交易管理', 
            'title' => '订单', 
            'is_list' => 1, 
            'navigation' => [
                ['func' => 'index/0', 'id'=>'index_0', 'title' => '未付款列表'],
                ['func' => 'index/1', 'id'=>'index_1', 'title' => '已付款列表'],
                ['func' => 'index/2', 'id'=>'index_2', 'title' => '已使用列表'],
                ['func' => 'index/3', 'id'=>'index_3', 'title' => '交易成功列表'],
                ['func' => 'index/10', 'id'=>'index_10', 'title' => '交易关闭列表'],
                ['func' => 'view', 'no_url'=>1, 'title' => '详情'],
                ]
        ), TRUE);
        //获取表对象
        $this->load->model(ucfirst($this->_template) . '_model', 'tableObject', TRUE);
        $this->load->model('Attachment_model', '', TRUE);
        $this->load->model('Orders_detail_model', '', TRUE);
        $this->load->model('Orders_process_model', '', TRUE);
        $this->load->model('Stores_model', '', TRUE);
        $this->load->model('User_model', '', TRUE);
    }

    public function index($status = 'all', $refresh = 1, $page = 0)
    {
        checkPermission("{$this->_template}_index");
        if ($refresh) {
            $refresh = 0;
            $this->session->unset_userdata("search");
        }
        $this->session->set_userdata(array("{$this->_template}RefUrl" => base_url() . 'admincp.php/' . uri_string()));
        $strWhere = $status != 'all' ? "{$this->_table}.status = {$status}" : NULL;
        $strWhere = $this->session->userdata('search') ? $this->session->userdata('search') : $strWhere;
        if ($_POST) {
            $strWhere = "{$this->_table}.id > 0";
            $order_number = trim($this->input->post('order_number', TRUE));
            $store_name = trim($this->input->post('store_name', TRUE));
            $is_reward = $this->input->post('is_reward');
            $product_type = $this->input->post('product_type');
            $startTime = $this->input->post('inputdate_start');
            $endTime = $this->input->post('inputdate_end');

            if ($order_number) {
                $strWhere .= " and {$this->_table}.order_number regexp '{$order_number}' ";
            }

            if ($store_name != "") {
                $strWhere .= " and {$this->_table}.store_id in (select id from stores where store_name regexp '{$store_name}')";
            }

            if ($is_reward == 1) {
                $strWhere .= " and {$this->_table}.id in (select order_id from orders_detail where reward > 0)";
            }

            if ($product_type !== '') {
                $strWhere .= " and {$this->_table}.id in (select order_id from orders_detail where item_type = {$product_type})";
            }
            if (!empty($startTime) && !empty($endTime)) {
                $strWhere .= " and {$this->_table}.create_time > " . strtotime($startTime . ' 00:00:00') . " and {$this->_table}.create_time < " . strtotime($endTime . ' 23:59:59') . " ";
            }

            $this->session->set_userdata('search', $strWhere);
            $page = 0;
        }
        //分页
        $this->config->load('pagination_config', TRUE);
        $paginationCount = $this->tableObject->count($strWhere);
        $paginationConfig = $this->config->item('pagination_config');
        $paginationConfig['base_url'] = base_url() . "admincp.php/{$this->_template}/index/{$status}/{$refresh}/";
        $paginationConfig['total_rows'] = $paginationCount;
        $paginationConfig['uri_segment'] = 5;
        $this->pagination->initialize($paginationConfig);
        $pagination = $this->pagination->create_links();

        $item_list = $this->tableObject->gets('*', $strWhere, $paginationConfig['per_page'], $page);
        if ($item_list) {
            foreach ($item_list as $key => $value) {
                $order_detail_list = $this->Orders_detail_model->gets('*', array('order_id' => $value['id']));
                $item_list[$key]['order_detail_list'] = $order_detail_list;
                $store_info = $this->Stores_model->get('store_name', ['id'=>$value['store_id']]);
                $item_list[$key]['store_name'] = $store_info ? $store_info['store_name'] : '';
            }
        }

        $data = array(
            'tool' => $this->_tool,
            'template' => $this->_template,
            'pagination' => $pagination,
            'paginationCount' => $paginationCount,
            'pageCount' => ceil($paginationCount / $paginationConfig['per_page']),
            'status_arr' => $this->_status_arr,
            'item_list' => $item_list
        );
        $layout = array(
            'title' => $this->_title,
            'content' => $this->load->view($this->_template . '/index', $data, TRUE)
        );
        $this->load->view('layout/default', $layout);
    }


    public function view($id = NULL) {
        checkPermission("{$this->_table}_view");
         $prfUrl = $this->session->userdata("{$this->_table}RefUrl") ? $this->session->userdata("{$this->_table}RefUrl") : base_url() . "admincp.php/{$this->_table}/index/1";
         $item_info = $this->tableObject->get('*', array('id' => $id));
         $orders_detail_list = NULL;
         $user_info = NULL;
         $orders_process_list = NULL;
         if ($item_info) {
             $user_info = $this->User_model->get(array('user.id' => $item_info['user_id']));
             $orders_detail_list = $this->Orders_detail_model->gets('*', array('order_id' => $id));
             $orders_process_list = $this->Orders_process_model->gets('*', array('order_id' => $id));
         }
 
         $data = array(
             'tool' => $this->_tool,
             'table'=>$this->_table,
             'status_arr' => $this->_status_arr,
             'item_info' => $item_info,
             'user_info' => $user_info,
             'orders_detail_list'=>$orders_detail_list,
             'orders_process_list'=>$orders_process_list,
             'prfUrl' => $prfUrl
         );
         $layout = array(
             'title' => $this->_title,
             'content' => $this->load->view("{$this->_table}/view", $data, TRUE)
         );
         $this->load->view('layout/default', $layout);
    }

    public function delete()
    {
        checkPermissionAjax("{$this->_template}_delete");

        $ids = $this->input->post('ids', TRUE);

        if (!empty($ids)) {
            $itemList = $this->tableObject->gets($this->_table . ".id in ($ids)");
            foreach ($itemList as $key => $value) {
                $filePath = "./{$value['html_path']}/{$value['id']}.html";
                if (file_exists($filePath)) {
                    @unlink($filePath);
                }
            }
            if ($this->tableObject->delete('id in (' . $ids . ')')) {
                printAjaxData(array('ids' => explode(',', $ids)));
            }
        }

        printAjaxError('fail', '删除失败！');
    }

    //修改价格
    public function change_price() {
        checkPermissionAjax("{$this->_table}_edit");
        if ($_POST) {
            $id = $this->input->post('id', TRUE);
            $order_total = $this->input->post('order_total', TRUE);

            if (!$id) {
                printAjaxError('fail', '操作异常');
            }
            if (!$this->form_validation->required($order_total)) {
                printAjaxError('order_total', '修改金额不能为空');
            }
            if (!$this->form_validation->numeric($order_total)) {
                printAjaxError('order_total', '请输入正确的订单金额');
            }
            $item_info = $this->tableObject->get('id, total, status', array('id' => $id));
            if (!$item_info) {
                printAjaxError('fail', '此订单信息不存在');
            }
            if ($item_info['status'] != 0) {
                printAjaxError('fail', '当前状态不能修改订单金额');
            }

            $fields = array(
                    'total' => $order_total
            );
 
            if ($this->tableObject->save($fields, array('id' => $item_info['id'], 'status'=>0))) {
                $fields = array(
                        'content' => "修改订单金额成功-[订单金额由“{$item_info['total']}”元修改为“{$order_total}”元]",
                        'order_id' => $item_info['id'],
                        'order_status'=>0,
                        'change_status'=>0
                );
                $this->Orders_process_model->save($fields);
                printAjaxSuccess('success', '订单金额修改成功');
            } else {
                printAjaxError('fail', "订单金额修改失败");
            }
        }
    }
    
    //交易关闭
    public function close_order() {
        checkPermissionAjax("{$this->_table}_edit");
        if ($_POST) {
            $id = $this->input->post('id', TRUE);
            $cancel_cause = $this->input->post('cancel_cause', TRUE);

            if (!$id) {
                printAjaxError('fail', '操作异常');
            }
            if (!$this->form_validation->required($cancel_cause)) {
                printAjaxError('cancel_cause', '请填写交易关闭的原因');
            }
            $item_info = $this->tableObject->get('id, user_id, order_number, status', array('id' => $id));
            if (!$item_info) {
                printAjaxError('fail', '此订单信息不存在');
            }
            if ($item_info['status'] != 0) {
                printAjaxError('fail', '当前状态不能关闭交易');
            }
            $user_info = $this->User_model->getInfo('*', array('id' => $item_info['user_id']));
            if (!$user_info) {
                printAjaxError('fail', "用户账号不存在或被管理员删除");
            }

            $fields = array(
                    'status'=> 10
            );
            if ($this->tableObject->save($fields, array('id' => $id, 'status'=>0))) {
                $fields = array(
                        'content' => "交易关闭-[平台关闭订单]",
                        'order_id' => $item_info['id'],
                        'order_status'=>$item_info['status'],
                        'change_status'=>10
                );
                $this->Orders_process_model->save($fields);
                $this->load->model('Combos_model', '', TRUE);
                $this->load->model('Share_goods_model', '', TRUE);
                $orders_detail_list = $this->Orders_detail_model->gets('item_id,item_type,buy_number', ['order_id'=>$id]);
                if ($orders_detail_list) {
                    foreach ($orders_detail_list as $key => $value) {
                        if ($value['item_type']) {
                            $this->Share_goods_model->save_column('stock', "stock+{$value['buy_number']}", ['id'=>$value['item_id']]);
                        } else {
                            $this->Combos_model->save_column('stock', "stock+{$value['buy_number']}", ['id'=>$value['item_id']]);
                        }
                    }
                }
                printAjaxSuccess('success', '交易关闭成功');
            } else {
                printAjaxError('fail', "交易关闭失败");
            }
        }
    }

    //修改状态(已付款)
    public function change_pay() {
    	checkPermissionAjax("{$this->_table}_edit");
    	if ($_POST) {
    		$id = $this->input->post('id', TRUE);
    		$remark = $this->input->post('remark', TRUE);

    		if (!$id) {
    			printAjaxError('fail', '操作异常');
    		}
    		if (!$remark) {
    			printAjaxError('remark', '备注不能为空');
    		}
    		$item_info = $this->tableObject->get('id, status, user_id, total, order_number', array('id' => $id));
    		if (!$item_info) {
    			printAjaxError('fail', '此订单信息不存在');
    		}
    		if ($item_info['status'] != 0) {
    			printAjaxError('fail', '当前状态不能将订单状态修改为已付款');
    		}

    		$fields = array(
    				'status' => 1
    		);
    		if ($this->tableObject->save($fields, array('id' => $id, 'status'=>0))) {
    			//订单跟踪记录
    			$fields = array(
    					'content' => "已付款状态修改成功[{$remark}]",
    					'order_id' => $item_info['id'],
    					'order_status'=>$item_info['status'],
    					'change_status'=>1
    			);
    			$this->Orders_process_model->save($fields);
    			printAjaxSuccess('success', '订单状态设置成功');
    		} else {
    			printAjaxError('fail', "订单状态设置失败");
    		}
    	}
    }

    //设为已使用
    public function delivery() {
    	checkPermissionAjax("{$this->_table}_edit");
    	if ($_POST) {
    		$id = $this->input->post('id', TRUE);
    		$remark = $this->input->post('remark', TRUE);

    		if (!$id) {
    			printAjaxError('fail', '操作异常');
    		}
    		if (!$remark) {
    			printAjaxError('remark', '备注不能为空');
    		}
    		$item_info = $this->tableObject->get('id, status, user_id, total, order_number', array('id' => $id));
    		if (!$item_info) {
    			printAjaxError('fail', '此订单信息不存在');
    		}
    		if ($item_info['status'] != 1) {
    			printAjaxError('fail', '当前状态不能将订单状态修改为已使用');
    		}

    		$fields = array(
    				'status' => 2
    		);
    		if ($this->tableObject->save($fields, array('id' => $id))) {
    			//订单跟踪记录
    			$fields = array(
    					'content' => "已使用状态修改成功[{$remark}]",
    					'order_id' => $item_info['id'],
    					'order_status'=>$item_info['status'],
    					'change_status'=>2
    			);
    			$this->Orders_process_model->save($fields);
    			printAjaxSuccess('success', '订单状态设置成功');
    		} else {
    			printAjaxError('fail', "订单状态设置失败");
    		}
    	}
    }
}
