<?php

class Live_sale_api extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index(){
        //获得参数 signature nonce token timestamp echostr
        $nonce     = $_GET['nonce'];
        $token     = 'qianhuwanwu';
        $timestamp = $_GET['timestamp'];
        $echostr   = $_GET['echostr'];
        $signature = $_GET['signature'];
        //形成数组，然后按字典序排序
        $array = array();
        $array = array($nonce, $timestamp, $token);
        sort($array);
        //拼接成字符串,sha1加密 ，然后与signature进行校验
        $str = sha1( implode( $array ) );
        if( $str  == $signature && $echostr ) {
            //第一次接入weixin api接口的时候
            echo $echostr;
            exit;
        }else{
            $this->message_notify();
        }
    }

    public function test_cache()
    {
        $this->load->driver('cache');
        $info = $this->cache->file->get_metadata('foo');
        var_dump($info);die;
        $this->load->library('wxapiclass');
        $cat = $this->wxapiclass->get_shop_cat();
        var_dump($cat);die;
    }

    public function add_product()
    {
        $product_ids = $this->input->post('ids', TRUE);
        if (!$product_ids) {
            printAjaxError('fail', '参数异常');
        }
        $this->load->model('Combos_model', '', TRUE);
        $this->load->library('wxapiclass');
        foreach (explode(',',$product_ids) as $product_id) {
            $item_info = $this->Combos_model->get('*', ['id'=>$product_id]);
            $result = $this->wxapiclass->add_product($item_info);
            if ($result == false) {
                printAjaxError('fail','参数异常');
            }
            if ($result['errcode'] != 0) {
                printAjaxError('fail', '操作中断：'.$result['errmsg']);
            }
        }
        printAjaxSuccess('success','添加成功');
    }

    public function update_product($product_id)
    {
        $this->load->model('Combos_model', '', TRUE);
        $item_info = $this->Combos_model->get('*', ['id'=>$product_id]);
        $this->load->library('wxapiclass');
        $result = $this->wxapiclass->update_product($item_info);
        var_dump($result);
    }

    public function get_product($product_id)
    {
        // $product_id = 16755;
        $this->load->library('wxapiclass');
        $result = $this->wxapiclass->get_product($product_id);
        var_dump($result);
    }

    public function del_product()
    {
        $product_ids = $this->input->post('ids', TRUE);
        if (!$product_ids) {
            printAjaxError('fail', '参数异常');
        }
        $this->load->library('wxapiclass');
        foreach (explode(',',$product_ids) as $product_id) {
            $result = $this->wxapiclass->del_product($product_id);
            if ($result['errcode'] != 0) {
                printAjaxError('fail', '操作中断：'.$result['errmsg']);
            }
        }
        // printAjaxSuccess('success','删除成功');
        printAjaxData(array('ids' => explode(',', $product_ids)));
    }

    public function listing_product()
    {
        $product_ids = $this->input->post('ids', TRUE);
        if (!$product_ids) {
            printAjaxError('fail', '参数异常');
        }
        $this->load->library('wxapiclass');
        foreach (explode(',',$product_ids) as $product_id) {
            $result = $this->wxapiclass->listing_product($product_id);
            if ($result['errcode'] != 0) {
                printAjaxError('fail', '操作中断：'.$result['errmsg']);
            }
        }
        printAjaxSuccess('success','上架成功');
    }

    public function delisting_product()
    {
        $product_ids = $this->input->post('ids', TRUE);
        if (!$product_ids) {
            printAjaxError('fail', '参数异常');
        }
        $this->load->library('wxapiclass');
        foreach (explode(',',$product_ids) as $product_id) {
            $result = $this->wxapiclass->delisting_product($product_id);
            if ($result['errcode'] != 0) {
                printAjaxError('fail', '操作中断：'.$result['errmsg']);
            }
        }
        printAjaxSuccess('success','下架成功');
    }

    public function get_product_list()
    {
        $json = file_get_contents('php://input');
        $params = json_decode($json, true);
        $this->load->library('wxapiclass');
        $result = $this->wxapiclass->get_product_list($params);
        echo json_encode($result);
    }

    public function upload_img()
    {
        $this->load->library('wxapiclass');
        $result = $this->wxapiclass->upload_img();
        var_dump($result);
    }

    public function get_category_list()
    {
        $this->load->library('wxapiclass');
        $result = $this->wxapiclass->get_category_list();
        var_dump($result);
    }

    public function audit_category()
    {
        $this->load->library('wxapiclass');
        $result = $this->wxapiclass->audit_category();
        var_dump($result);
    }

    public function audit_result()
    {
        $this->load->library('wxapiclass');
        $result = $this->wxapiclass->audit_result();
        var_dump($result);
    }

    public function order_get()
    {
        $order_info['order_number'] = "210719094140936965";
        $order_info['openid'] = "o6D9R5RKVhJMlNjIWdMZ4BorP-8g";
        $this->load->library('wxapiclass');
        $return_order_info = $this->wxapiclass->order_get($order_info);
    }

    public function aftersale_add($order_id = NULL, $type = 0)
    {
        $this->load->model('Orders_model', '', TRUE);
        $this->load->model('Orders_detail_model', '', TRUE);
        $this->load->model('Orders_form_model', '', TRUE);
        if ($type){
            $item_info = $this->Orders_form_model->get('order_ids,total,order_number',array('id'=>$order_id));
            $out_trade_no = $item_info['order_number'];
            $order_detail_list = [];
            foreach (explode(',', $item_info['order_ids']) as $order_id) {
                $order_detail = $this->Orders_detail_model->gets('*', ['order_id'=>$order_id]);
                $order_detail_list = array_merge($order_detail_list, $order_detail);
            }
            $item_info['order_detail_list'] = $order_detail_list;
            $item_info['type'] = $type;
        }else{
            $item_info = $this->Orders_model->get('*', "id = {$order_id}");
            $out_trade_no = $item_info['out_trade_no'] ? $item_info['out_trade_no'] : $item_info['order_number'];
            $order_detail_list = $this->Orders_detail_model->gets('*', ['order_id'=>$order_id]);
            $item_info['order_detail_list'] = $order_detail_list;
            $item_info['type'] = $type;
        }
        $order_info = $item_info;
        $order_info['openid'] = "o6D9R5RKVhJMlNjIWdMZ4BorP-8g";
        $this->load->library('wxapiclass');
        $return_order_info = $this->wxapiclass->aftersale_add($order_info);
    }

    public function aftersale_get()
    {
        $order_info['order_number'] = "210717172512708397";
        $order_info['openid'] = "o6D9R5RKVhJMlNjIWdMZ4BorP-8g";
        $this->load->library('wxapiclass');
        $return_order_info = $this->wxapiclass->aftersale_get($order_info);
    }

    public function aftersale_update()
    {
        $order_info['order_number'] = "210717172512708397";
        $order_info['openid'] = "o6D9R5RKVhJMlNjIWdMZ4BorP-8g";
        $order_info['out_aftersale_id'] = "210717172512708397";
        $this->load->library('wxapiclass');
        $return_order_info = $this->wxapiclass->aftersale_update($order_info);
    }

    public function message_notify()
    {
        // $json = $GLOBALS['HTTP_RAW_POST_DATA'];
        $json = file_get_contents('php://input');
        $post_data = json_decode($json, true);
        logs($post_data);
    }
}