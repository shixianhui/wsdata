<?php 

class Byte_dance_api extends CI_Controller {
    private $byteAppId = 'tt18da6e6787e2d65c01';
    private $byteAppSecret = '9e20a6cf3dddeecc0d4721ab6f7f5f9b87405985';
    private $_app_id = '2021002154623187';
    private $_rsaPrivateKey = 'MIIEogIBAAKCAQEAk/O/88zEdsnviusc96QrEizJG7bsvzM1ogz7pOl6+Qdrk6LNfaiZ0EqMrAFmxX0QPbzYAD/oY/Vd4avzz8mWZ2009/+IuNWi+LbUUH8C7iK+8ketvzZkdO9oMWL/yiXDhfB9wnhkhVxhpYvlr9gAviKBLsNFRh3w92JMLxvx/NxkKXTk+mK1++E/7hV+JQLYNYvu1Oxz1PEjURbj8FUQEUTo0OfYTabzV5v13wGD+83eXbBirdhL511j3AwYjCfcqyg/OLHCfGVubF34i/gDJVhfbVgUm565elYWyLaoTUip5NB1ZPwtaAwxVFMp93f+dXAV011VUwsbLBqfQnT4SQIDAQABAoIBADtNn6o8cJ9gr+iR9yl4H1+Ing/C0cCN6TiMVa2zfHwnMkKaJh3xH74ys5IaebQ9Pe5RLI9lDmRKrJfRUcf++I96YZnqTkYwM8PXnOCrGGSVfs4kVwtm5PtOHwh0syy22FlunSn/EeO2tNkjayyBu0J7GCbDVJgXVkgCmaDkZanBmxPvndigZLG9FXs8bLKgmraVPtLu7zAKKmkY5bJqt0OxQbHAbNqtgP07EaTuLhVzs7E6OI3ESwDha2M0IiEOBPMUN8/JVx2KOFca60Jw29CBga93yfhxPGhM3tBIaa2SXwubpRMqccIpHZYspk2cowOEMwQkNdsRVkwv9NyYxpECgYEA5lFEfi30BrvGdiEx+E1UxZwzAuKYSybh8tJPVWRT+2RLZbfAmEQ3TnuHkgMLeEZazJ7RynE2AuYJH9JpdxTGmg8qqKhBZtsalnnKUjYsRmqD1Vtbs34nLzba6Q+e9mVinqRphXtOSNen5LtZda5oEuwpgVxbeD86aSyl/86AOqUCgYEApHM//Ih8UK4zI8QebxitqPeztwSahNx5DuuGTdq2lLIxbVE/CtYLpoJdFOmOvEI7if5t69yTYwYFtMF5zec3Qf6SmuGgMF8oRK7JJeXhHbE33KIG4+olY3FXCDY16mPR2HqGuEJT08bUlltr7y8A9vJPCx7jSGPmgvO6vXGU6dUCgYBkIFJhdILQ5pMpydaadqvy2KwIhg/lI/s4gBuDKQGlmX15s/+jcoyErwlJ/c7fs95HdIgJtVvguLagwe2dmkeYtB08gyEjoP8XCc1eHjTzS90SRQxBpQdN7FAQ3/yga4ULKLjDEc7/tdlEg/opQe/2wfptRYRyazJuhL2JzvFKDQKBgFxNuB+VuDlM3bV4kiCHeIn7pqrWcaibW3OtbS/r0El8D1QtozYA2H84cuiXA5/ViTe2UJpvr2aIbdF8O1MAMbrgGgfHFrOv5ZlPheW4tveEjjdP1pA8z4mWh2Q7kV2jc5iPhWiNCiI3WwGeBOI2vtLdRNKYHrh2Il9kUG+e/heVAoGAYUNG5QnlvdMUT53g6E6YcPv6pUmTUnsYrwM+BImdE19suMux9QMUaez9lHpsxMup9KEZpKM1OptobJTcBD/+RUBLGLTWLPz9TCP8GbLtxglDZRmtCMwi9hzznFRJgyzHkE48vpGw4Sw7ZVK8NE7X70hG4WI/mMZ00+oyVAm8zS4=';
    //支付宝公钥
    private $_alipayrsaPublicKey = 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAoyFd4GUg8c+Nh8ZWEPGQbJDuWaZQrP4W46Ijv4s5FGfPmrUp6nYzQ6auCCy4gUd+ockZcgTV4DSUjGRisc/JmTXZErWiNp/Es9n06VmX3/gcePAM3Ax4aKysiFzv49KVRbM3yEQGiI0ULZ+DWR6p8My05u+ql3ykwa504sQpPR7C6lqk5DvjmWUda1I1Gulf5YkHa9rjwd8CCD7q2x6FWfJhM4wgFFBAzyEYRjh69oW6HFBbRUycVKmMeJl8tzAMnIFXP/su8yM0s4b2bABx8nFbVUyRBqxmiJvLBK52c9+wJK6U38zTkb6H4vF/qZFGZV89EX9B4mqJ9SteKaNgAQIDAQAB';

    public function __construct()
    {
        parent::__construct();
        $this->_beforeFilter();

    }


    private function _beforeFilter() {
        $sid = $this->input->get('sid');
        if ($sid) {
            $sid = preg_replace('/sid-/', '', $sid);
            if ($sid) {
                $ret = NULL;
                $this->db->select('timestamp');
                $query = $this->db->get_where(config_item('sess_save_path'), array('id'=>$sid));
                if ($query->num_rows() > 0) {
                    $ret = $query->result_array();
                    $ret = $ret[0];
                }
                if (!$ret) {
                    $this->session->sess_destroy();
                    return FALSE;
                }
                //大于默认时间，系统 会自动更新
                if (($ret['timestamp'] + config_item('sess_time_to_update')) >= time()) {
                    return FALSE;
                } else {
                    if (config_item('sess_use_database') == TRUE) {
                        $this->db->update(config_item('sess_save_path'), array('timestamp'=>time()), array('id'=>$sid));
                    }
                }
            }
        }
    }


    private function _check_login($is_check_store = false) {
        if (!$this->session->userdata("user_id")) {
            printAjaxError('login', '请登录');
        }
        $user_id = $this->session->userdata("user_id");
        session_write_close();
        $this->load->model('User_model', '', TRUE);
        $item_info = $this->User_model->get('*', array('id' => $user_id, 'display <>'=>3));
        if (!$item_info) {
            printAjaxError('fail', '账号不存在');
        }
        if ($item_info['display'] == 0) {
            printAjaxError('fail', '你的账户还未激活，请联系客服激活');
        } else if ($item_info['display'] == 2) {
            printAjaxError('fail', '你的账户已冻结，请联系客服');
        }

        if ($is_check_store) {
            $this->load->model('Stores_model', '', TRUE);
            $store_info = $this->Stores_model->get('id,status', ['user_id'=>$user_id]);
            if (!$store_info) {
                printAjaxError('fail', '请先申请成为商户');
            }
            if ($store_info['status'] == 0) {
                printAjaxError('fail', '商户正在审核，请耐心等待');
            } elseif ($store_info['status'] == 2) {
                printAjaxError('fail', '商户申请审核拒绝');
            } elseif ($store_info['status'] == 3) {
                printAjaxError('fail', '商户暂时关闭');
            }
            $item_info['store_id'] = $store_info['id'];
        }

        return $item_info;
    }

    //获取唯一的商户订单号
    private function _get_unique_out_trade_no() {
        //一秒钟一万件的量
        $randCode = '';
        while (true) {
            $randCode = getOrderNumber(6);
            $count = $this->Pay_log_model->count(array('out_trade_no' => $randCode));
            if ($count > 0) {
                $randCode = '';
                continue;
            } else {
                break;
            }
        }
        return $randCode;
    }
    
    /**
     * 文件存储access_token
     */
    private function get_byte_access_token()
    {
        $this->load->driver('cache');
        $cache = $this->cache->file->get_metadata('byte_token');
        if ($cache && $cache['expire'] > time()) {
            $access_token = $this->cache->file->get('byte_token');
        } else {
            $access_token = '';
            $appid = $this->byteAppId;
            $appSecret = $this->byteAppSecret;
            $json = http_curl("https://developer.toutiao.com/api/apps/token?appid={$appid}&secret={$appSecret}&grant_type=client_credential");
            $obj = json_decode($json);
            if (!isset($obj->errmsg)) {
                $access_token = $obj->access_token;
                $expires_in = $obj->expires_in;
                $this->cache->file->save('byte_token', $access_token, $expires_in-60);
            }
        }

        return $access_token;
    }

    /**
     * 支付
     *
     * @param $order_id ID
     * @param $type 0=单付 1=合并付款
     * @return array
     */
    public function order_app_pay($order_id = NULL, $type = 0) {
        //判断是否登录
        $user_info = $this->_check_login();
        $this->load->model('Orders_model', '', TRUE);
        $this->load->model('Orders_form_model', '', TRUE);

        if (!$order_id) {
            printAjaxError('fail', '操作异常');
        }
        if ($type){
            $item_info = $this->Orders_form_model->get('order_ids,total,order_number',array('id'=>$order_id));
            $out_trade_no = $item_info['order_number'];
        }else{
            $item_info = $this->Orders_model->get('*', "id = {$order_id} and user_id = {$user_info['id']} and status = 0");
            $out_trade_no = $item_info['out_trade_no'] ? $item_info['out_trade_no'] : $item_info['order_number'];
        }
        if (!$item_info) {
            printAjaxError('fail','订单信息不存在');
        }

        if ($item_info['total'] <= 0) {
            printAjaxError('fail','支付金额异常');
        }

        $total_amount = $item_info['total'] * 100;
        //生成支付记录
        $this->load->model('Pay_log_model', '', TRUE);
        if (!$this->Pay_log_model->count(array('out_trade_no' => $out_trade_no, 'order_type' => 0))) {
            $fields = array(
                'user_id' => $user_info['id'],
                'total_fee' => $item_info['total'],
                'out_trade_no' => $out_trade_no,
                'order_number' => $item_info['order_number'],
                'trade_status' => 'WAIT_BUYER_PAY',
                'add_time' => time(),
                'order_type' => 0,
                'form_type' => $type
            );
            if (!$this->Pay_log_model->save($fields)) {
                printAjaxError('fail', '支付失败，请重试');
            }
        }

        $params = [
            'app_id' => $this->byteAppId,
            'out_order_no' => $out_trade_no,
            'total_amount' => $total_amount,
            'subject' => '凑活生活',
            'body' => '凑活生活团购',
            'valid_time' => 24*3600,
            'notify_url' => base_url()."index.php/api/byte_dance_api/order_pay_notify",
        ];
        $sign = $this->sign($params);
        $params['sign'] = $sign;
        
        $url = "https://developer.toutiao.com/api/apps/ecpay/v1/create_order";
        $result = json_decode(http_curl($url, json_encode($params)));
        if ($result->err_no == 0) {
            printAjaxData($result->data);
        }
        printAjaxError('fail', '参数异常，请重试');
    }

    public function order_query()
    {
        $out_trade_no = '210706114158609822';
        $params = [
            'app_id' => $this->byteAppId,
            'out_order_no' => $out_trade_no,
        ];
        $sign = $this->sign($params);
        $params['sign'] = $sign;
        $url = "https://developer.toutiao.com/api/apps/ecpay/v1/query_order";
        $result = json_decode(http_curl($url, json_encode($params)), true);
        var_dump($result);
    }

    //支付异步通知
    public function order_pay_notify() {
        $callback = json_decode(file_get_contents("php://input"), true);
        // logs($callback);
        if($callback && $callback['type'] == 'payment') {
            $this->load->model('User_model', '', TRUE);
            $this->load->model('Orders_model', '', TRUE);
            $this->load->model('Orders_form_model', '', TRUE);
            $this->load->model('Orders_process_model', '', TRUE);
            $this->load->model('Pay_log_model', '', TRUE);

            $msg = json_decode($callback['msg'], true);
            //商户订单号
            $out_trade_no = $msg['cp_orderno'];
            $payment_type = $msg['way'] == 2 ? 1 : 0;
            $trade_no = $msg['payment_order_no'];
            $app_id = $msg['appid'];

            $pay_log_info = $this->Pay_log_model->get('*', array('out_trade_no'=>$out_trade_no, 'order_type'=>0));
            if ($pay_log_info && $app_id == $this->byteAppId) {
                //支付记录
                $fields = array(
                    'trade_no' => $trade_no,
                    'trade_status' => 'TRADE_SUCCESS',
                    'notify_time' => $callback['timestamp'],
                    'payment_type' => $payment_type
                );
                if ($this->Pay_log_model->save($fields, array('id' => $pay_log_info['id']))) {

                    if ($pay_log_info['form_type']) {
                        $orders_form_info = $this->Orders_form_model->get('order_ids,total,order_number', array('order_number' => $out_trade_no));
                        $order_id_arr = explode(',', $orders_form_info['order_ids']);
                    } else {
                        $order_info = $this->Orders_model->get('id', array("order_number" => $pay_log_info['order_number']));
                        $order_id_arr = [$order_info['id']];
                    }
                    $user_info = $this->User_model->get('id, total, username, nickname, mobile', array('id' => $pay_log_info['user_id']));
                    if ($order_id_arr && $user_info) {

                        $this->load->model('Orders_detail_model', '', TRUE);
                        $this->load->model('Combos_model', '', TRUE);
                        $this->load->model('System_model', '', TRUE);
                        $this->load->model('Share_goods_model', '', TRUE);
                        $this->load->model('Bonus_record_model', '', TRUE);


                        foreach ($order_id_arr as $id) {
                            $item_info = $this->Orders_model->get('*', array('id' => $id, 'status' => 0));
                            if ($item_info) {
                                
                                //修改订单状态
                                $fields = array(
                                    'status' => 1,
                                    'trade_no' => $trade_no,
                                    'payment_type' => $payment_type);
                                if ($this->Orders_model->save($fields, array('id' => $item_info['id']))) {
                                    //订单追踪记录
                                    $fields = array(
                                        'content' => "订单付款成功[字节跳动小程序支付]",
                                        'order_id' => $item_info['id'],
                                        'order_status' => $item_info['status'],
                                        'change_status' => 1
                                    );
                                    $this->Orders_process_model->save($fields);
                                    $order_detail_info = $this->Orders_detail_model->get('item_id,item_type,buy_number,reward,parent_id', ['order_id'=>$item_info['id']]);
                                    if ($order_detail_info) {
                                        //加销量
                                        if ($order_detail_info['item_type'] == 1) {
                                            $share_goods_info = $this->Share_goods_model->get('goods_id', ['id'=>$order_detail_info['item_id']]);
                                            $combos_info = $this->Combos_model->get('id,product_id,sales,custom_sales', ['id'=>$share_goods_info['goods_id']]);
                                            if ($combos_info) {
                                                $columns['sales'] = $combos_info['sales'] + $order_detail_info['buy_number'];
                                                if ($combos_info['custom_sales']) {
                                                    $columns['custom_sales'] = $combos_info['custom_sales'] + $order_detail_info['buy_number'];
                                                }
                                                $this->Combos_model->save($columns, ['id'=>$combos_info['id']]);
                                            }
                                        } else {
                                            $combos_info = $this->Combos_model->get('id,product_id,sales,custom_sales', ['id'=>$order_detail_info['item_id']]);
                                            if ($combos_info) {
                                                $columns['sales'] = $combos_info['sales'] + $order_detail_info['buy_number'];
                                                if ($combos_info['custom_sales']) {
                                                    $columns['custom_sales'] = $combos_info['custom_sales'] + $order_detail_info['buy_number'];
                                                }
                                                $this->Combos_model->save($columns, ['id'=>$combos_info['id']]);
                                            }
                                            
                                        }
                                        //创建惠生活订单
                                        if ($item_info['order_type'] == 1) {

                                            $biz_content = [
                                                'ProductID' => $combos_info['product_id'],
                                                'OrderList' => [['OrderNo'=>$item_info['order_number'],'CustName'=>$user_info['nickname'],'CustPhone'=>$user_info['mobile'],'Num'=>$order_detail_info['buy_number']]]
                                            ];
                                            $import_info = $this->hbdapiclass->hbd_batch_import_order($biz_content);
                                            if ($import_info) {
                                                $this->Orders_model->save(['hbd_code_no'=>$import_info['CodeNo'], 'hbd_order_no'=>$import_info['HBDOrderNo']], ['id'=>$item_info['id']]);
                                            }
                                        }
                                    }

                                    //平台奖金
                                    $system_info = $this->System_model->get('bonus_amount,platform_commission', ['id'=>1]);
                                    $amount = sprintf("%.2f",substr(sprintf("%.3f", $item_info['total'] * $system_info['platform_commission']), 0, -1));
                                    $balance = $system_info['bonus_amount'] + $amount;
                                    if ($this->System_model->save(['bonus_amount'=>$balance], ['id'=>1])) {
                                        $reg_data = [
                                            'amount' => $amount,
                                            'cause' => '用户购物',
                                            'balance' => $balance,
                                            'user_id' => $item_info['user_id'],
                                            'type' => 0,
                                            'ret_id' => $item_info['id']
                                        ];
                                        $this->Bonus_record_model->save($reg_data);
                                    }
                                    
                                    echo json_encode(['err_no'=>0, 'err_tips'=>"success"]);
                                }
                            }
                        }
                    }
                }
            }

            echo json_encode(['err_no'=>0, 'err_tips'=>"success"]);
        } else {
            echo "fail";
        }

    }


    
    //获取转发的小程序码
    public function get_byte_code($item_id = 0, $item_type = 0, $page = null, $appname = 'douyin')
    {
        $user_id = $this->session->userdata('user_id');
        //小程序码目录
        $save_dir='uploads/wxcode/';
        if (!file_exists($save_dir)) {
            mkdir($save_dir, 0777, true);
        }

        $file_name = '';
        if ($item_type == 0) {
            $file_name = $save_dir."combos_{$item_id}_{$user_id}.png";
        } else {
            $file_name = $save_dir."share_goods_{$item_id}_{$user_id}.png";
        }
        $page = urldecode($page);
        if (!$page) {
            $page = 'pages/index/index';
        }

        // $scene = $item_id.'_'.$user_id;
        $page .= "?id={$item_id}&parent_id={$user_id}";
        $result = $this->get_byte_qr_code($page, $appname);

        file_put_contents($file_name, $result);

        $tmp_image_arr = filter_image_path($file_name);
        $path = $tmp_image_arr['path'];
        printAjaxData(['path'=>$path]);

    }

    //获取小程序码
    public function get_byte_qr_code($page, $appname = 'douyin')
    {
        $access_token = $this->get_byte_access_token();
        if (!$access_token){
            printAjaxError('fail', 'invalid appid!');
        }
        $url = "https://developer.toutiao.com/api/apps/qrcode";
        $data = array(
            'access_token' => $access_token,
            'appname' => $appname,
            'path'=>urlencode($page),
            'width'=>280,
            'set_icon' => true
        );
        $data=json_encode($data);
        $result = http_curl($url,$data,1);
        if (json_decode($result)){
            printAjaxError('fail', 'invalid result!');
        }
        return $result;
    }

    public function sign($map) {
        $rList = array();
        foreach($map as $k => $v) {
            if ($k == "other_settle_params" || $k == "app_id" || $k == "sign" || $k == "thirdparty_id")
                continue;
            $value = trim(strval($v));
            $len = strlen($value);
            if ($len > 1 && substr($value, 0,1)=="\"" && substr($value,$len, $len-1)=="\"")
                $value = substr($value,1, $len-1);
            $value = trim($value);
            if ($value == "" || $value == "null")
                continue;
            array_push($rList, $value);
        }
        array_push($rList, "SWFnlrmonFy0Y8RJA4V7yrrXw0nd2oV6awBoA2JP");
        sort($rList, 2);
        return md5(implode('&', $rList));
    }
}