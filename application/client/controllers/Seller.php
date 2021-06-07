<?php

class Seller extends CI_Controller {

    private $_status = array(
        '0' => '<font color="#ff4200">未付款</font>',
        '1' => '<font color="#cc3333">已付款</font>',
        '2' => '<font color="#ff811f">已使用</font>',
        '3' => '<font color="#066601">交易成功</font>',
        '10' => '<font color="#a0a0a0">交易关闭</font>',
    );
    private $_measurement = array(
        '1' => '件',
        '2' => 'kg',
        '3' => 'm³',
    );
    private $_exchange_reason_arr = array(
        '0'=>'无理由退货',
        '1'=>'不需要/不想的商品',
        '2'=>'其它'
    );
    private $_exchange_status_arr = array(
        '0'=>'<font color="red">待审核</font>',
        '1'=>'审核未通过',
        '2'=>'审核通过',
        '3'=>'退款到余额成功',
        '4'=>'原路返回退款成功'
    );

    private $_exchange_status2_arr = array(
        '0'=>'(退款审核中)',
        '1'=>'(退款审核拒绝)',
        '2'=>'(退款审核通过)',
        '3'=>'(退款成功)',
        '4'=>'(退款成功)'
    );
    private $_comment_status_arr = array(
        '0'=>'未回复',
        '1'=>'已回复'
    );
    private $_evaluate_arr = array(
        '1'=>'好评',
        '2'=>'中评',
        '3'=>'差评',
    );
    private $_table = 'user';
    private $_template = 'seller';
    private $_check_status_arr = array('<font color="red">审核中</font>','<font color="green">审核通过</font>','<font color="red">审核拒绝</font>');
    private $_dishes_type_arr = ['美食','外卖','美食、外卖'];
    private $_business_status_arr = ['暂未营业','正在营业','休息中'];

    public function __construct() {
        parent::__construct();
        $this->load->model('Menu_model', '', TRUE);
        $this->load->model('System_model', '', TRUE);
        $this->load->model('User_model', '', TRUE);
        $this->load->model('Stores_model', '', TRUE);
        $this->load->model('Seller_group_model', '', TRUE);
        $this->load->model('Area_model', '', TRUE);
        $this->load->model('Attachment_model', '', TRUE);
        $this->load->model('Dishes_category_model', '', TRUE);
        $this->load->model('Dishes_model', '', TRUE);
        $this->load->model('Combos_model', '', TRUE);
        $this->load->model('Share_goods_model', '', TRUE);
        $this->load->model('Store_type_model', '', TRUE);
        $this->load->model('Grasses_model', '', TRUE);
        $this->load->model('Orders_model', '', TRUE);
        $this->load->model('Orders_detail_model', '', TRUE);
        $this->load->model('Orders_process_model', '', TRUE);
        $this->load->model('Tags_model', '', TRUE);
        $this->load->library('Form_validation');
    }

    public function index() {
        $this->session->set_userdata(array("gloabPreUrl" => base_url() . 'index.php/' . uri_string()));
        //判断是否登录
        checkLogin(true);
        // checkPermission("seller_index");
        $systemInfo = $this->System_model->get('*', array('id' => 1));
//        $user_id = $this->session->userdata('user_id');
        $seller_group_info = $this->Seller_group_model->get('user_id',array('id' => get_cookie('seller_group_id')));
        $item_info = $this->Stores_model->get('*', array('user_id' => $seller_group_info['user_id']));
        $province_list = $this->Area_model->gets('*', array('parent_id' => 0));
        if ($_POST) {
            $path = $this->input->post('path', TRUE);
            // $store_name = $this->input->post('store_name', TRUE);
            $description = $this->input->post('description', TRUE);
            // $province_id = intval($this->input->post('province_id', TRUE));
            // $city_id = intval($this->input->post('city_id', TRUE));
            $address = $this->input->post('address', TRUE);
            $phone = $this->input->post('phone', TRUE);
            $business_status = $this->input->post('business_status', TRUE);
            $business_hours = $this->input->post('business_hours', TRUE);
            $per_amount = $this->input->post('per_amount', TRUE);
            // if (!$this->form_validation->required($store_name)) {
            //     printAjaxError('store_name', '店铺名称不能为空');
            // }
            // if (!$province_id || !$city_id || !$area_id) {
            //     printAjaxError('area', '省市区不能为空');
            // }
            if (!$this->form_validation->required($phone)) {
                printAjaxError('mobile', '手机号不能为空');
            }

            // $txt_address_str = '';
            // $area_info = $this->Area_model->get('name', array('id' => $province_id));
            // if ($area_info) {
            //     $txt_address_str .= $area_info['name'];
            // }
            // $area_info = $this->Area_model->get('name', array('id' => $city_id));
            // if ($area_info) {
            //     $txt_address_str .= ' ' . $area_info['name'];
            // }
            // $area_info = $this->Area_model->get('name', array('id' => $area_id));
            // if ($area_info) {
            //     $txt_address_str .= ' ' . $area_info['name'];
            // }
            $fields = array(
                'logo' => $path,
                'description' => $description,
                // 'province_id' => $province_id,
                // 'city_id' => $city_id,
                // 'area_id' => $area_id,
                'address' => $address,
                'phone' => $phone,
                'business_status' => $business_status,
                'business_hours' => $business_hours,
                'per_amount' => $per_amount
            );

            $result = $this->Stores_model->save($fields, array('id' => $item_info['id']));
            if ($result) {
                printAjaxSuccess($_SERVER['REQUEST_URI'], '保存成功');
            } else {
                printAjaxError('fail', '保存失败');
            }
        }
        $data = array(
            'site_name' => $systemInfo['site_name'],
            'index_name' => $systemInfo['index_name'],
            'client_index' => $systemInfo['client_index'],
            'title' => '我的店铺_商家中心' . $systemInfo['site_name'],
            'keywords' => $systemInfo['site_keywords'],
            'description' => $systemInfo['site_description'],
            'site_copyright' => $systemInfo['site_copyright'],
            'icp_code' => $systemInfo['icp_code'],
            'html' => $systemInfo['html'],
            'template' => $this->_template,
            'item_info' => $item_info,
            'province_list' => $province_list,
            'business_status_arr' => $this->_business_status_arr
        );
        $layout = array(
            'content' => $this->load->view("{$this->_template}/index", $data, TRUE)
        );
        $this->load->view('layout/seller_layout', $layout);
        //缓存
        if ($systemInfo['cache'] == 1) {
            $this->output->cache($systemInfo['cache_time']);
        }
    }

    //我要入驻
    public function my_join() {
        //判断是否登录
        checkLogin();
        $systemInfo = $this->System_model->get('*', array('id' => 1));
        $user_id = $this->session->userdata('user_id');
        $item_info = $this->Stores_model->get('*', array('user_id' => $user_id));
        $store_id = $item_info ? $item_info['id'] : 0;

        if ($_POST) {
            $store_name = $this->input->post('store_name', TRUE);
            $image_ids = $this->input->post('image_ids', TRUE);
            $license_image = $this->input->post('license_path', TRUE);
            $is_branch = $this->input->post('is_branch', TRUE);
            $type_id = $this->input->post('type_id', TRUE);
            $province_id = intval($this->input->post('province_id', TRUE));
            $city_id = intval($this->input->post('city_id', TRUE));
            $area_id = intval($this->input->post('area_id', TRUE));
            $address = $this->input->post('address', TRUE);
            $business_hours = $this->input->post('business_hours', TRUE);
            $phone = $this->input->post('phone', TRUE);
            $bank_card_number = $this->input->post('bank_card_number', TRUE);
            $remark = $this->input->post('remark', TRUE);
            $user_id = $this->session->userdata('user_id');
            //检测用户是否已经入住店铺
            if ($item_info) {
                if ($item_info['status'] == 0) {
                    printAjaxError('fail', '您已提交店铺申请，正在审核中...');
                } else if ($item_info['status'] == 1) {
                    printAjaxError('fail', '您已入驻，不用重复申请');
                } else if ($item_info['status'] == 3) {
                    printAjaxError('fail', '您的店铺已被关闭，请联系网站客服');
                }
            }
            $store_info = $this->Stores_model->get('id', "store_name = '{$store_name}' and user_id <> {$user_id} ");
            if ($store_info) {
                printAjaxError('fail', '此店铺名称已被注册，请换个试试');
            }
            if (!$image_ids) {
                printAjaxError('type_id', '请上传门头图');
            }
            if (!$license_image) {
                printAjaxError('type_id', '请上传门营业执照');
            }
            if (!$type_id) {
                printAjaxError('type_id', '请选择商户类型');
            }
            if (!$address) {
                printAjaxError('address', '请填写地址');
            }
            if (!$this->form_validation->required($store_name)) {
                printAjaxError('store_name', '店铺名称不能为空');
            }
            if (!$province_id || !$city_id) {
                printAjaxError('area', '请选择地区');
            }
            if (!$this->form_validation->valid_mobile($phone) && !$this->form_validation->valid_phone($phone)) {
                printAjaxError('mobile', '请填写正确的商家电话');
            }
            if (!preg_match('/^([1-9]{1})(\d{15}|\d{16}|\d{18})$/', $bank_card_number)){
                printAjaxError('bank_card_number', '银行卡号格式不正确');
            }
            // $txt_address_str = '';
            // $area_info = $this->Area_model->get('name', array('id' => $province_id));
            // if ($area_info) {
            //     $txt_address_str .= $area_info['name'];
            // }
            $city = '';
            $area_info = $this->Area_model->get('name', array('id' => $city_id));
            if ($area_info) {
                $city = $area_info['name'];
            }
            // $area_info = $this->Area_model->get('name', array('id' => $area_id));
            // if ($area_info) {
            //     $txt_address_str .= ' ' . $area_info['name'];
            // }

            $fields = array(
                'image_ids' => $image_ids,
                'license_image' => $license_image,
                'store_name' => $store_name,
                'is_branch' => $is_branch,
                'type_id' => $type_id,
                'province_id' => $province_id,
                'city_id' => $city_id,
                'address' => unhtml($address),
                'city' => $city,
                'business_hours' => $business_hours,
                'phone' => $phone,
                'bank_card_number' => $bank_card_number,
                'remark' => $remark,
                'user_id' => $user_id,
                'status' => 0,
            );
            $result = $this->Stores_model->save($fields, $store_id ? array('id' => $store_id, 'user_id' => $user_id) : NULL);
            if ($result) {
                printAjaxSuccess('success_store', '您的资料提交成功，我们会尽快处理您的申请');
            } else {
                printAjaxError('fail', '保存失败');
            }
        }

        $image_list = [];
        if ($item_info){
            if ($item_info['status'] != 2 || $item_info['id'] != $store_id){
                if ($item_info['status'] == 0){
                    alert_message('店铺审核中，请耐心等待',base_url());
                }else if ($item_info['status'] == 3){
                    alert_message('店铺已关闭，原因：'.$item_info['close_reason'],base_url());
                }else{
                    alert_message('操作异常',base_url());
                }
            }
            if ($item_info['image_ids']) {
                $tmp_atm_ids = preg_replace(array('/^_/', '/_$/', '/_/'), array('', '', ','), $item_info['image_ids']);
                $image_list = $this->Attachment_model->gets('path', "id in ({$tmp_atm_ids})");
            }

        }
        $province_list = $this->Area_model->gets('*', array('parent_id' => 1));
        $store_type_list = $this->Store_type_model->gets('*', ['display'=>1]);

        $data = array(
            'site_name' => $systemInfo['site_name'],
            'index_name' => $systemInfo['index_name'],
            
            'client_index' => $systemInfo['client_index'],
            'title' => '我要入驻_商家中心' . $systemInfo['site_name'],
            'keywords' => $systemInfo['site_keywords'],
            'description' => $systemInfo['site_description'],
            'site_copyright' => $systemInfo['site_copyright'],
            'icp_code' => $systemInfo['icp_code'],
            'html' => $systemInfo['html'],
            'template' => $this->_template,
            'item_info' => $item_info,
            'store_id' => $store_id,
            'province_list' => $province_list,
            'store_type_list' => $store_type_list,
            'image_list' => $image_list,
        );
        $layout = array(
            'content' => $this->load->view("{$this->_template}/my_join", $data, TRUE)
        );
        $this->load->view('layout/default_layout', $layout);
        //缓存
        if ($systemInfo['cache'] == 1) {
            $this->output->cache($systemInfo['cache_time']);
        }
    }

    //发布商品
    public function my_save_dishes($id = NULL) {
        //判断是否登录
        checkLogin(true);
        if ($id) {
            checkPermission("dishes_edit");
        } else {
            checkPermission("dishes_add");
        }
        $systemInfo = $this->System_model->get('*', array('id' => 1));
        $seller_group_info = $this->Seller_group_model->get('user_id',array('id' => get_cookie('seller_group_id')));
        $store_info = $this->Stores_model->get('id', array('user_id' => $seller_group_info['user_id']));
        //产品分类
        $dishes_category_list = $this->Dishes_category_model->gets('*', ['store_id' => $store_info['id']]);
        // if ($dishes_category_list){
        //     foreach ($dishes_category_list as $key=>$value){
        //         $dishes_category_list[$key]['name'] = str_repeat( '&nbsp;&nbsp;&nbsp;&nbsp;', $value['level'] - 1) . $value['name'];
        //     }
        // }
        //产品详细
        $item_info = $this->Dishes_model->get('*', array("id" => $id, 'store_id' => $store_info['id']));

        // $tmp_dishes_num = '';
        // $tmp_dishes_info = $this->dishes_model->get("max(id) as 'max_id'");
        // if ($tmp_dishes_info) {
        //     $tmp_dishes_num = sprintf("C%06d", $tmp_dishes_info['max_id'] + 1);
        // }

        if ($_POST) {
            $title = $this->input->post('title', TRUE);
            $batch_path_ids = $this->input->post('batch_path_ids', TRUE);
            $path = $this->input->post('path', TRUE);
            $content = $this->input->post('content');
            $stock = $this->input->post('stock', TRUE);
            $category_id = $this->input->post('category_id', TRUE);
            $price = $this->input->post('price', TRUE);
            $display = $this->input->post('display', TRUE);
            $type = $this->input->post('type', TRUE);
            $attribute = $this->input->post('attribute', TRUE);


            if (!$category_id && $type != 0) {
                printAjaxError('fail', '请选择商品分类');
            }
            if (!$this->form_validation->required($title)) {
                printAjaxError('title', '商品标题不能为空');
            }

            if (!$this->form_validation->required($path)) {
                if (!$this->form_validation->required($batch_path_ids)){
                    printAjaxError('path', '请上传封面图');
                }
                $path_info = $this->Attachment_model->get('path',array('id'=>$batch_path_ids[0]));
                if (!$path_info){
                    printAjaxError('path', '请上传封面图');
                }
                $path = $path_info['path'];
            }

            if($batch_path_ids){
                $batch_path_ids = implode('_', $batch_path_ids);
                $batch_path_ids .= '_';
            }

            $attribute = $attribute ? implode(',', $attribute) : '';

            $fields = array(
                'store_id' => $store_info['id'],
                'category_id' => $category_id,
                'name' => $title,
                'content' => unhtml($content),
                'cover_image' => $path,
                'stock' => $stock,
                'image_ids' => $batch_path_ids,
                'price' => $price,
                'display' => $display,
                'type' => $type,
                'attribute' => $attribute,
            );
            $retId = $this->Dishes_model->save($fields, $id ? array('id' => $id) : $id);
            if ($retId) {
                printAjaxSuccess(getBaseUrl(false, '', 'seller/my_get_dishes_list.html', $systemInfo['client_index']), '保存成功');
            } else {
                printAjaxError('fail', '保存失败');
            }
        }

        $attachment_list = array();
        if($item_info && $item_info['image_ids']){
            $tmp_atm_ids = preg_replace(array('/^_/', '/_$/', '/_/'), array('', '', ','), $item_info['image_ids']);
            $attachment_list = $this->Attachment_model->gets('id,path',"id in ($tmp_atm_ids)");
        }
        

        $data = array(
            'site_name' => $systemInfo['site_name'],
            'index_name' => $systemInfo['index_name'],
            'client_index' => $systemInfo['client_index'],
            'title' => '发布商品_商家中心' . $systemInfo['site_name'],
            'keywords' => $systemInfo['site_keywords'],
            'description' => $systemInfo['site_description'],
            'site_copyright' => $systemInfo['site_copyright'],
            'icp_code' => $systemInfo['icp_code'],
            'html' => $systemInfo['html'],
            'template' => $this->_template,
            'dishes_category_list' => $dishes_category_list,
            'item_info' => $item_info,
            'attachment_list' => $attachment_list,
        );
        $layout = array(
            'content' => $this->load->view("{$this->_template}/my_save_dishes", $data, TRUE)
        );
        $this->load->view('layout/seller_layout', $layout);
        //缓存
        if ($systemInfo['cache'] == 1) {
            $this->output->cache($systemInfo['cache_time']);
        }
    }

    //商品列表
    public function my_get_dishes_list($clear = 1, $page = 0) {
        //判断是否登录
        checkLogin(true);
        checkPermission("dishes_index");
        if ($clear) {
            $clear = 0;
            $this->session->unset_userdata('search');
        }
        $systemInfo = $this->System_model->get('*', array('id' => 1));
        $seller_group_info = $this->Seller_group_model->get('user_id',array('id' => get_cookie('seller_group_id')));
        $store_info = $this->Stores_model->get('id', array('user_id' => $seller_group_info['user_id']));
        $condition = "store_id = {$store_info['id']}";
        $strWhere = $this->session->userdata('search') ? $this->session->userdata('search') : $condition;
        if($_POST){
        	$strWhere = $condition;
            $title = $this->input->post('title',TRUE);
            $product_category_id = $this->input->post('product_category_id', TRUE);
            $sell_price_start = $this->input->post('sell_price_start',TRUE);
            $sell_price_end = $this->input->post('sell_price_end',TRUE);

            if($title){
                $strWhere .= " and name regexp '{$title}' ";
            }

            if($product_category_id){
                $strWhere .= " and category_id = {$product_category_id} ";
            }

            if(!empty($sell_price_start) && !empty($sell_price_end)){
                $strWhere .= " and (price >= '{$sell_price_start}' and price <= '{$sell_price_end}') ";
            }

            $this->session->set_userdata('search', $strWhere);
            $page = 0;
        }
        //分页
        $paginationCount = $this->Dishes_model->count($strWhere);
        $this->config->load('pagination_config', TRUE);
        $paginationConfig = $this->config->item('pagination_config');
        $paginationConfig['base_url'] = base_url() . "index.php/seller/my_get_dishes_list/{$clear}/";
        $paginationConfig['total_rows'] = $paginationCount;
        $paginationConfig['uri_segment'] = 4;
        $paginationConfig['per_page'] = 20;
        $this->pagination->initialize($paginationConfig);
        $pagination = $this->pagination->create_links();
        $item_list = $this->Dishes_model->gets('id,cover_image,sort,name,price,stock,category_id,display,type', $strWhere, $paginationConfig['per_page'], $page);
        foreach ($item_list as $key => $value) {
            //分类
            $dishes_category_info = $this->Dishes_category_model->get('name', ['id'=>$value['category_id']]);
            $item_list[$key]['category_str'] = $dishes_category_info ? $dishes_category_info['name'] : '';
            $item_list[$key]['type_str'] = $this->_dishes_type_arr[$value['type']];
        }
        $my_product_category_list = $this->Dishes_category_model->gets('*', ['store_id' => $store_info['id']]);
        $data = array(
            'site_name' => $systemInfo['site_name'],
            'index_name' => $systemInfo['index_name'],
            'client_index' => $systemInfo['client_index'],
            'title' => '商品管理_商家中心' . $systemInfo['site_name'],
            'keywords' => $systemInfo['site_keywords'],
            'description' => $systemInfo['site_description'],
            'site_copyright' => $systemInfo['site_copyright'],
            'icp_code' => $systemInfo['icp_code'],
            'html' => $systemInfo['html'],
            'template' => $this->_template,
            'item_list' => $item_list,
            'pagination' => $pagination,
            'my_product_category_list' => $my_product_category_list
        );
        $layout = array(
            'content' => $this->load->view("{$this->_template}/my_get_dishes_list", $data, TRUE)
        );
        $this->load->view('layout/seller_layout', $layout);
        //缓存
        if ($systemInfo['cache'] == 1) {
            $this->output->cache($systemInfo['cache_time']);
        }
    }

    public function my_get_dishes_selector($clear = '1', $page = 0) {
        //判断是否登录
        checkLogin(true);
        checkPermission("product_index");
        $systemInfo = $this->System_model->get('*', array('id' => 1));
        $seller_group_info = $this->Seller_group_model->get('user_id',array('id' => get_cookie('seller_group_id')));
        $store_info = $this->Stores_model->get('id', array('user_id' => $seller_group_info['user_id']));
        $my_product_category_list = $this->Product_category_model->menuTree($store_info['id']);
        $style_list = $this->Style_model->gets(array('store_id' => $store_info['id']));
        $brand_list = $this->Brand_model->gets('*', array('store_id' => $store_info['id']));
        if ($clear) {
            $clear = 0;
            $this->session->unset_userdata('search');
        }
        $condition = "store_id = {$store_info['id']}";
        $strWhere = $this->session->userdata('search') ? $this->session->userdata('search') : $condition;
        if($_POST){
            $strWhere = $condition;
            $title = $this->input->post('title',TRUE);
            $product_num = $this->input->post('product_num',TRUE);
            $brand_name = $this->input->post('brand_name',TRUE);
            $style_name = $this->input->post('style_name',TRUE);
            $product_category_ids = $this->input->post('product_category_id', TRUE);
            $recommend_to_store_index = $this->input->post('recommend_to_store_index', TRUE);
            $sell_price_start = $this->input->post('sell_price_start',TRUE);
            $sell_price_end = $this->input->post('sell_price_end',TRUE);

            if($title){
                $strWhere .= " and title regexp '{$title}' ";
            }
            if($product_num){
                $strWhere .= " and product_num = '{$product_num}' ";
            }
            if($brand_name){
                $strWhere .= " and brand_name = '{$brand_name}' ";
            }
            if(!empty($style_name)){
                $strWhere .= " and style_name = '{$style_name}' ";
            }
            if(!empty($recommend_to_store_index)){
                $strWhere .= " and recommend_to_store_index = '{$recommend_to_store_index}' ";
            }
            if(!empty($sell_price_start) && !empty($sell_price_end)){
                $strWhere .= " and (sell_price >= '{$sell_price_start}' and sell_price <= '{$sell_price_end}') ";
            }
            if (!empty($product_category_ids)){
                $parent_id = 0;
                $product_category_id = 0;
                $product_category_ids_arr = explode(',', $product_category_ids);
                if ($product_category_ids_arr) {
                    if (count($product_category_ids_arr) >= 1) {
                        $parent_id = $product_category_ids_arr[0];
                    }
                    if (count($product_category_ids_arr) >= 2) {
                        $product_category_id = $product_category_ids_arr[1];
                    }
                }
                $product_id_arr = $this->Product_category_ids_model->gets('product_id', array('parent_id' => $parent_id, 'product_category_id' => $product_category_id));
                $ids = -1;
                if ($product_id_arr) {
                    foreach ($product_id_arr as $value) {
                        $product_ids[] = $value['product_id'];
                    }
                    $ids = implode(',', $product_ids);
                }
                $strWhere .= " and id IN ({$ids})";
            }
            $this->session->set_userdata('search', $strWhere);
        }
        //分页
        $paginationCount = $this->Product_model->count($strWhere);
        $this->config->load('pagination_config', TRUE);
        $paginationConfig = $this->config->item('pagination_config');
        $paginationConfig['base_url'] = base_url() . "index.php/seller/my_get_product_selector/{$clear}/";
        $paginationConfig['total_rows'] = $paginationCount;
        $paginationConfig['uri_segment'] = 4;
        $paginationConfig['per_page'] = 10;
        $this->pagination->initialize($paginationConfig);
        $pagination = $this->pagination->create_links();
        $product_list = $this->Product_model->gets('id,path,title,brand_name,style_name,unclear_price,sell_price,stock,product_num,recommend_to_store_index', $strWhere, $paginationConfig['per_page'], $page);
        foreach ($product_list as $key => $value) {
            //分类
            $product_category_str = '';
            $p_c_i_list = $this->Product_category_ids_model->gets('*', array('product_id' => $value['id']));
            if ($p_c_i_list) {
                foreach ($p_c_i_list as $p_c_i_key => $p_c_i_value) {
                    $product_category_str .= $this->Product_category_model->getLocation($p_c_i_value['product_category_id']) . '<br/>';
                }
                if ($product_category_str) {
                    $product_category_str = substr($product_category_str, 0, -1);
                }
            }
            $product_list[$key]['product_category_str'] = $product_category_str;
        }
        $data = array(
            'site_name' => $systemInfo['site_name'],
            'index_name' => $systemInfo['index_name'],
            
            'client_index' => $systemInfo['client_index'],
            'title' => '商品管理_商家中心' . $systemInfo['site_name'],
            'keywords' => $systemInfo['site_keywords'],
            'description' => $systemInfo['site_description'],
            'site_copyright' => $systemInfo['site_copyright'],
            'icp_code' => $systemInfo['icp_code'],
            'html' => $systemInfo['html'],
            'template' => $this->_template,
            'product_list' => $product_list,
            'pagination' => $pagination,
            'my_product_category_list' => $my_product_category_list,
            'style_list' => $style_list,
            'brand_list' => $brand_list,
        );
        $this->load->view("{$this->_template}/my_get_product_selector", $data);
        //缓存
        if ($systemInfo['cache'] == 1) {
            $this->output->cache($systemInfo['cache_time']);
        }
    }

    public function my_delete_dishes() {
        checkPermission('dishes_delete');
        $ids = $this->input->post('ids', TRUE);
        if (!$this->form_validation->required($ids)) {
            printAjaxError('title', '请选择您要删除的项');
        }

        if (!empty($ids)) {
            if ($this->Dishes_model->delete('id in (' . $ids . ')')) {
            
                printAjaxData(array('ids' => explode(',', $ids)));
            }
        }
        printAjaxError('','删除失败！');
    }

    //商品排序
    public function my_sort_dishes() {
        checkPermissionAjax("dishes_edit");
        $ids = $this->input->post('ids', TRUE);
        $sorts = $this->input->post('sorts', TRUE);

        if (!empty($ids) && !empty($sorts)) {
            $ids = explode(',', $ids);
            $sorts = explode(',', $sorts);

            foreach ($ids as $key => $value) {
                $this->Dishes_model->save(array('sort' => $sorts[$key]), array('id' => $value));
            }
            printAjaxSuccess('排序成功！');
        }

        printAjaxError('排序失败！');
    }

  
    //菜品分类列表
    public function my_get_dishes_category_list() {
        //判断是否登录
        checkLogin(true);
        checkPermission('dishes_category_index');
        $systemInfo = $this->System_model->get('*', array('id' => 1));
        $seller_group_info = $this->Seller_group_model->get('user_id',array('id' => get_cookie('seller_group_id')));
        $store_info = $this->Stores_model->get('id', array('user_id' => $seller_group_info['user_id']));
        $item_list = $this->Dishes_category_model->gets('*', ['store_id'=>$store_info['id']]);

        $data = array(
            'site_name' => $systemInfo['site_name'],
            'index_name' => $systemInfo['index_name'],
            'client_index' => $systemInfo['client_index'],
            'title' => '菜品分类管理_商家中心' . $systemInfo['site_name'],
            'keywords' => $systemInfo['site_keywords'],
            'description' => $systemInfo['site_description'],
            'site_copyright' => $systemInfo['site_copyright'],
            'icp_code' => $systemInfo['icp_code'],
            'html' => $systemInfo['html'],
            'template' => $this->_template,
            'item_list' => $item_list,
        );
        $layout = array(
            'content' => $this->load->view("{$this->_template}/my_get_dishes_category_list", $data, TRUE)
        );
        $this->load->view('layout/seller_layout', $layout);
        //缓存
        if ($systemInfo['cache'] == 1) {
            $this->output->cache($systemInfo['cache_time']);
        }
    }

    //添加菜品分类
    public function my_save_dishes_category($tmp_parent_id = 0, $id = NULL) {
        //判断是否登录
        checkLogin(true);
        if ($id){
            checkPermission('dishes_category_edit');
        }else{
            checkPermission('dishes_category_add');
        }
        $systemInfo = $this->System_model->get('*', array('id' => 1));
        $seller_group_info = $this->Seller_group_model->get('user_id',array('id' => get_cookie('seller_group_id')));
        $store_info = $this->Stores_model->get('id', array('user_id' => $seller_group_info['user_id']));
        // $parent_category_list = $this->Dishes_category_model->gets(array('store_id' => $store_info['id'], 'parent_id' => 0));
        $item_info = $this->Dishes_category_model->get('*', array('store_id' => $store_info['id'], 'id' => $id));
        if ($_POST) {
            $parent_id = $this->input->post('parent_id', TRUE);
            $product_category_name = $this->input->post('product_category_name', TRUE);
            $sort = $this->input->post('sort', TRUE);
            $path = $this->input->post('path', TRUE);
            if (!$this->form_validation->required($product_category_name)) {
                printAjaxError('product_category_name', '分类名称不能为空');
            }
            if ($id) {
                if ($parent_id == $id) {
                    printAjaxError('parent_id', '自己不能是自己的上级分类');
                }
            }
            if ($id) {
                $fields = array(
                    // 'parent_id' => $parent_id,
                    'name' => $this->input->post('product_category_name', TRUE),
                    'display' => $this->input->post('display', TRUE),
                    'sort' => intval($sort),
                    // 'path' => $path,
                    'store_id' => $store_info['id'],
                );
                if ($this->Dishes_category_model->save($fields, array('id' => $id))) {
                    printAjaxSuccess(getBaseUrl(false, '', 'seller/my_get_dishes_category_list.html', $systemInfo['client_index']), '添加成功');
                } else {
                    printAjaxError("操作失败！");
                }
            } else {
                $i = 0;
                $title = preg_replace(array('/^\|+/', '/\|+$/', '/｜/'), array('', '', '|'), $product_category_name);
                $titleArr = explode("|", $title);
                foreach ($titleArr as $key => $title) {
                    $fields = array(
                        // 'parent_id' => $parent_id,
                        'sort' => $sort + $key,
                        'name' => trim($title),
                        // 'path' => $path,
                        'store_id' => $store_info['id'],
                    'display' => $this->input->post('display', TRUE),
                    );
                    if ($this->Dishes_category_model->save($fields)) {
                        $i++;
                    }
                }
                if (count($titleArr) == $i) {
                    printAjaxSuccess(getBaseUrl(false, '', 'seller/my_get_dishes_category_list.html', $systemInfo['client_index']), '添加成功');
                } else {
                    printAjaxError('fail', "操作失败！");
                }
            }
        }
        $data = array(
            'site_name' => $systemInfo['site_name'],
            'index_name' => $systemInfo['index_name'],
            
            'client_index' => $systemInfo['client_index'],
            'title' => '添加菜品分类_商家中心' . $systemInfo['site_name'],
            'keywords' => $systemInfo['site_keywords'],
            'description' => $systemInfo['site_description'],
            'site_copyright' => $systemInfo['site_copyright'],
            'icp_code' => $systemInfo['icp_code'],
            'html' => $systemInfo['html'],
            'template' => $this->_template,
            // 'parent_category_list' => $parent_category_list,
            'item_info' => $item_info,
            'tmp_parent_id' => $tmp_parent_id,
        );
        $layout = array(
            'content' => $this->load->view("{$this->_template}/my_save_dishes_category", $data, TRUE)
        );
        $this->load->view('layout/seller_layout', $layout);
        //缓存
        if ($systemInfo['cache'] == 1) {
            $this->output->cache($systemInfo['cache_time']);
        }
    }

    public function my_sort_dishes_category() {
        checkLogin(true);
        checkPermission('dishes_category_edit');
        if ($_POST) {
            $ids = $this->input->post('ids', TRUE);
            $sorts = $this->input->post('sorts', TRUE);
            if (!empty($ids) && !empty($sorts)) {
                $ids = explode(',', $ids);
                $sorts = explode(',', $sorts);
                foreach ($ids as $key => $value) {
                    $this->Dishes_category_model->save(array('sort' => $sorts[$key]), array('id' => $value));
                }
                printAjaxSuccess('success', '排序成功！');
            }
            printAjaxError('fail', '排序失败！');
        }
    }

    public function my_change_dishes_category_sort() {
        checkLoginAjax(true);
        checkPermissionAjax('dishes_category_edit');
        if ($_POST) {
            $id = $this->input->post('id', TRUE);
            $sort = $this->input->post('sort', TRUE);
            $result = $this->Dishes_category_model->save(array('sort' => intval($sort)), array('id' => intval($id)));
            if ($result) {
                printAjaxSuccess('success', '保存成功');
            } else {
                printAjaxError('fail', '保存失败');
            }
        }
    }

    public function my_delete_dishes_category() {
        checkLogin(true);
        checkPermission('dishes_category_delete');
        if ($_POST) {
            $seller_group_info = $this->Seller_group_model->get('user_id',array('id' => get_cookie('seller_group_id')));
            $store_info = $this->Stores_model->get('id', array('user_id' => $seller_group_info['user_id']));
            $id = $this->input->post('id', TRUE);
            if (!empty($id)) {
                $ids = $this->Dishes_category_model->getChildIds($id);
                if ($ids != $id) {
                    printAjaxError('fail', '删除失败，请先删除下级分类！');
                }
                if ($this->Dishes_category_model->delete("dishes_category.id in ({$id}) and store_id = {$store_info['id']}")) {
                    printAjaxData(array('id' => $id));
                }
            }
            printAjaxError('fail', '删除失败！');
        }
    }

    //品牌列表
    public function my_get_brand_list() {
        //判断是否登录
        checkLogin(true);
        checkPermission('brand_index');
        $systemInfo = $this->System_model->get('*', array('id' => 1));
        $seller_group_info = $this->Seller_group_model->get('user_id',array('id' => get_cookie('seller_group_id')));
        $store_info = $this->Stores_model->get('id', array('user_id' => $seller_group_info['user_id']));
        $item_list = $this->Brand_model->gets('*', "store_id = {$store_info['id']} or (store_id = 0 and display = 1)");
        $data = array(
            'site_name' => $systemInfo['site_name'],
            'index_name' => $systemInfo['index_name'],
            
            'client_index' => $systemInfo['client_index'],
            'title' => '品牌管理_商家中心' . $systemInfo['site_name'],
            'keywords' => $systemInfo['site_keywords'],
            'description' => $systemInfo['site_description'],
            'site_copyright' => $systemInfo['site_copyright'],
            'icp_code' => $systemInfo['icp_code'],
            'html' => $systemInfo['html'],
            'template' => $this->_template,
            'item_list' => $item_list,
        );
        $layout = array(
            'content' => $this->load->view("{$this->_template}/my_get_brand_list", $data, TRUE)
        );
        $this->load->view('layout/seller_layout', $layout);
        //缓存
        if ($systemInfo['cache'] == 1) {
            $this->output->cache($systemInfo['cache_time']);
        }
    }

    //添加品牌
    public function my_save_brand($id = NULL) {
        //判断是否登录
        checkLogin(true);
        if ($id){
            checkPermission('brand_edit');
        }else{
            checkPermission('brand_add');
        }
        $systemInfo = $this->System_model->get('*', array('id' => 1));
        $seller_group_info = $this->Seller_group_model->get('user_id',array('id' => get_cookie('seller_group_id')));
        $store_info = $this->Stores_model->get('id', array('user_id' => $seller_group_info['user_id']));
        $item_info = $this->Brand_model->get('*', array('id' => $id, 'store_id' => $store_info['id']));
        if ($_POST) {
            $brand_name = $this->input->post('brand_name', TRUE);
//            $tag = $this->input->post('tag', TRUE);
            $path = $this->input->post('path', TRUE);
            if (!$this->form_validation->required($brand_name)) {
                printAjaxError('brand_name', '品牌名称不能为空');
            }
            if (!$this->form_validation->max_length($brand_name, 100)) {
                printAjaxError('title', '品牌名称字数不能超过100字');
            }
            if ($id) {
                $fields = array(
                    'brand_name' => $brand_name,
//                    'tag' => $tag,
                    'path' => $path,
                    'store_id' => $store_info['id'],
                );
                if ($this->Brand_model->save($fields, array('id' => $id))) {
                    printAjaxSuccess(getBaseUrl(false, '', 'seller/my_get_brand_list.html', $systemInfo['client_index']), '修改成功');
                } else {
                    printAjaxError('fail', "操作失败！");
                }
            } else {
                $i = 0;
                $brand_name = preg_replace(array('/^\|+/', '/\|+$/', '/｜/'), array('', '', '|'), $brand_name);
                $titleArr = explode("|", $brand_name);
                foreach ($titleArr as $key => $title) {
                    $fields = array(
                        'brand_name' => $title,
//                        'tag' => $tag,
                        'path' => $path,
                        'store_id' => $store_info['id'],
                    );
                    if ($this->Brand_model->save($fields)) {
                        $i++;
                    }
                }
                if (count($titleArr) == $i) {
                    printAjaxSuccess(getBaseUrl(false, '', 'seller/my_get_brand_list.html', $systemInfo['client_index']), '添加成功');
                } else {
                    printAjaxError('fail', "操作失败！");
                }
            }
        }
        $data = array(
            'site_name' => $systemInfo['site_name'],
            'index_name' => $systemInfo['index_name'],
            
            'client_index' => $systemInfo['client_index'],
            'title' => '添加品牌_商家中心' . $systemInfo['site_name'],
            'keywords' => $systemInfo['site_keywords'],
            'description' => $systemInfo['site_description'],
            'site_copyright' => $systemInfo['site_copyright'],
            'icp_code' => $systemInfo['icp_code'],
            'html' => $systemInfo['html'],
            'template' => $this->_template,
            'item_info' => $item_info
        );
        $layout = array(
            'content' => $this->load->view("{$this->_template}/my_save_brand", $data, TRUE)
        );
        $this->load->view('layout/seller_layout', $layout);
        //缓存
        if ($systemInfo['cache'] == 1) {
            $this->output->cache($systemInfo['cache_time']);
        }
    }

    public function my_delete_brand() {
        checkLoginAjax(true);
        checkPermissionAjax('brand_delete');
        if ($_POST) {
            $seller_group_info = $this->Seller_group_model->get('user_id',array('id' => get_cookie('seller_group_id')));
            $store_info = $this->Stores_model->get('id', array('user_id' => $seller_group_info['user_id']));
            $id = $this->input->post('id', TRUE);
            if (!$this->form_validation->required($id)) {
                printAjaxError('title', 'id不能为空');
            }
            $result = $this->Brand_model->delete(array('id' => intval($id), 'store_id' => $store_info['id']));
            if ($result) {
                printAjaxSuccess('success', '删除成功');
            } else {
                printAjaxError('fail', '删除失败');
            }
        }
    }

    //风格列表
    public function my_get_style_list() {
        //判断是否登录
        checkLogin(true);
        checkPermission('style_index');
        $systemInfo = $this->System_model->get('*', array('id' => 1));
        $seller_group_info = $this->Seller_group_model->get('user_id',array('id' => get_cookie('seller_group_id')));
        $store_info = $this->Stores_model->get('id', array('user_id' => $seller_group_info['user_id']));
        $item_list = $this->Style_model->gets("store_id = {$store_info['id']} or (store_id = 0 and display = 1)");
        $data = array(
            'site_name' => $systemInfo['site_name'],
            'index_name' => $systemInfo['index_name'],
            
            'client_index' => $systemInfo['client_index'],
            'title' => '风格管理_商家中心' . $systemInfo['site_name'],
            'keywords' => $systemInfo['site_keywords'],
            'description' => $systemInfo['site_description'],
            'site_copyright' => $systemInfo['site_copyright'],
            'icp_code' => $systemInfo['icp_code'],
            'html' => $systemInfo['html'],
            'template' => $this->_template,
            'item_list' => $item_list
        );
        $layout = array(
            'content' => $this->load->view("{$this->_template}/my_get_style_list", $data, TRUE)
        );
        $this->load->view('layout/seller_layout', $layout);
        //缓存
        if ($systemInfo['cache'] == 1) {
            $this->output->cache($systemInfo['cache_time']);
        }
    }

    //编辑风格
    public function my_save_style($id = NULL) {
        //判断是否登录
        checkLogin(true);
        if ($id){
            checkPermission('style_edit');
        }else{
            checkPermission('style_add');
        }

        $systemInfo = $this->System_model->get('*', array('id' => 1));
        $seller_group_info = $this->Seller_group_model->get('user_id',array('id' => get_cookie('seller_group_id')));
        $store_info = $this->Stores_model->get('id', array('user_id' => $seller_group_info['user_id']));
        $item_info = $this->Style_model->get('*', array('id' => $id, 'store_id' => $store_info['id']));
        if ($_POST) {
            $style_name = $this->input->post('style_name', TRUE);
//            $tag = $this->input->post('tag', TRUE);
            if (!$this->form_validation->required($style_name)) {
                printAjaxError('style_name', '风格名称不能为空');
            }
            if (!$this->form_validation->max_length($style_name, 100)) {
                printAjaxError('style_name', '风格名称字数不能超过100字');
            }
            if ($id) {
                $fields = array(
                    'style_name' => $style_name,
//                    'tag' => $tag,
                    'store_id' => $store_info['id'],
                );
                if ($this->Style_model->save($fields, array('id' => $id))) {
                    printAjaxSuccess(getBaseUrl(false, '', 'seller/my_get_style_list.html', $systemInfo['client_index']), '修改成功');
                } else {
                    printAjaxError('fail', "操作失败！");
                }
            } else {
                $i = 0;
                $style_name = preg_replace(array('/^\|+/', '/\|+$/', '/｜/'), array('', '', '|'), $style_name);
                $titleArr = explode("|", $style_name);
                foreach ($titleArr as $key => $title) {
                    $fields = array(
                        'style_name' => $title,
//                        'tag' => $tag,
                        'store_id' => $store_info['id'],
                    );
                    if ($this->Style_model->save($fields)) {
                        $i++;
                    }
                }
                if (count($titleArr) == $i) {
                    printAjaxSuccess(getBaseUrl(false, '', 'seller/my_get_style_list.html', $systemInfo['client_index']), '添加成功');
                } else {
                    printAjaxError('fail', "操作失败！");
                }
            }
        }
        $data = array(
            'site_name' => $systemInfo['site_name'],
            'index_name' => $systemInfo['index_name'],
            
            'client_index' => $systemInfo['client_index'],
            'title' => '添加风格_商家中心' . $systemInfo['site_name'],
            'keywords' => $systemInfo['site_keywords'],
            'description' => $systemInfo['site_description'],
            'site_copyright' => $systemInfo['site_copyright'],
            'icp_code' => $systemInfo['icp_code'],
            'html' => $systemInfo['html'],
            'template' => $this->_template,
            'item_info' => $item_info,
        );
        $layout = array(
            'content' => $this->load->view("{$this->_template}/my_save_style", $data, TRUE)
        );
        $this->load->view('layout/seller_layout', $layout);
        //缓存
        if ($systemInfo['cache'] == 1) {
            $this->output->cache($systemInfo['cache_time']);
        }
    }

    public function my_delete_style() {
        checkLoginAjax(true);
        checkPermissionAjax('style_delete');
        if ($_POST) {
            $seller_group_info = $this->Seller_group_model->get('user_id',array('id' => get_cookie('seller_group_id')));
            $store_info = $this->Stores_model->get('id', array('user_id' => $seller_group_info['user_id']));
            $id = $this->input->post('id', TRUE);
            if (!$this->form_validation->required($id)) {
                printAjaxError('title', 'id不能为空');
            }
            $result = $this->Style_model->delete(array('id' => intval($id), 'store_id' => $store_info['id']));
            if ($result) {
                printAjaxSuccess('success', '删除成功');
            } else {
                printAjaxError('fail', '删除失败');
            }
        }
    }

    //材质列表
    public function my_get_material_list() {
        //判断是否登录
        checkLogin(true);
        checkPermission('material_index');
        $systemInfo = $this->System_model->get('*', array('id' => 1));
        $seller_group_info = $this->Seller_group_model->get('user_id',array('id' => get_cookie('seller_group_id')));
        $store_info = $this->Stores_model->get('id', array('user_id' => $seller_group_info['user_id']));
        $item_list = $this->Material_model->gets("store_id = {$store_info['id']} or (store_id = 0 and display = 1)");
        $data = array(
            'site_name' => $systemInfo['site_name'],
            'index_name' => $systemInfo['index_name'],
            
            'client_index' => $systemInfo['client_index'],
            'title' => '材质管理_商家中心' . $systemInfo['site_name'],
            'keywords' => $systemInfo['site_keywords'],
            'description' => $systemInfo['site_description'],
            'site_copyright' => $systemInfo['site_copyright'],
            'icp_code' => $systemInfo['icp_code'],
            'html' => $systemInfo['html'],
            'template' => $this->_template,
            'item_list' => $item_list
        );
        $layout = array(
            'content' => $this->load->view("{$this->_template}/my_get_material_list", $data, TRUE)
        );
        $this->load->view('layout/seller_layout', $layout);
        //缓存
        if ($systemInfo['cache'] == 1) {
            $this->output->cache($systemInfo['cache_time']);
        }
    }

    //编辑材质
    public function my_save_material($id = NULL) {
        //判断是否登录
        checkLogin(true);
        if ($id){
            checkPermission('material_edit');
        }else{
            checkPermission('material_add');
        }
        $systemInfo = $this->System_model->get('*', array('id' => 1));
        $seller_group_info = $this->Seller_group_model->get('user_id',array('id' => get_cookie('seller_group_id')));
        $store_info = $this->Stores_model->get('id', array('user_id' => $seller_group_info['user_id']));
        $item_info = $this->Material_model->get('*', array('id' => $id, 'store_id' => $store_info['id']));
        if ($_POST) {
            $material_name = $this->input->post('material_name', TRUE);
//            $tag = $this->input->post('tag', TRUE);
            if (!$this->form_validation->required($material_name)) {
                printAjaxError('brand_name', '材质名称不能为空');
            }
            if (!$this->form_validation->max_length($material_name, 100)) {
                printAjaxError('title', '材质名称字数不能超过100字');
            }

            if ($id) {
                $fields = array(
                    'material_name' => $material_name,
//                    'tag' => $tag,
                    'store_id' => $store_info['id'],
                );
                if ($this->Material_model->save($fields, array('id' => $id))) {
                    printAjaxSuccess(getBaseUrl(false, '', 'seller/my_get_material_list.html', $systemInfo['client_index']), '修改成功');
                } else {
                    printAjaxError('fail', "操作失败！");
                }
            } else {
                $i = 0;
                $material_name = preg_replace(array('/^\|+/', '/\|+$/', '/｜/'), array('', '', '|'), $material_name);
                $titleArr = explode("|", $material_name);
                foreach ($titleArr as $key => $title) {
                    $fields = array(
                        'material_name' => $title,
//                        'tag' => $tag,
                        'store_id' => $store_info['id'],
                    );
                    if ($this->Material_model->save($fields)) {
                        $i++;
                    }
                }
                if (count($titleArr) == $i) {
                    printAjaxSuccess(getBaseUrl(false, '', 'seller/my_get_material_list.html', $systemInfo['client_index']), '添加成功');
                } else {
                    printAjaxError('fail', "操作失败！");
                }
            }
        }
        $data = array(
            'site_name' => $systemInfo['site_name'],
            'index_name' => $systemInfo['index_name'],
            
            'client_index' => $systemInfo['client_index'],
            'title' => '添加材质_商家中心' . $systemInfo['site_name'],
            'keywords' => $systemInfo['site_keywords'],
            'description' => $systemInfo['site_description'],
            'site_copyright' => $systemInfo['site_copyright'],
            'icp_code' => $systemInfo['icp_code'],
            'html' => $systemInfo['html'],
            'template' => $this->_template,
            'item_info' => $item_info,
        );
        $layout = array(
            'content' => $this->load->view("{$this->_template}/my_save_material", $data, TRUE)
        );
        $this->load->view('layout/seller_layout', $layout);
        //缓存
        if ($systemInfo['cache'] == 1) {
            $this->output->cache($systemInfo['cache_time']);
        }
    }

    public function my_delete_material() {
        checkLoginAjax(true);
        checkPermissionAjax('material_delete');
        if ($_POST) {
            $seller_group_info = $this->Seller_group_model->get('user_id',array('id' => get_cookie('seller_group_id')));
            $store_info = $this->Stores_model->get('id', array('user_id' => $seller_group_info['user_id']));
            $id = $this->input->post('id', TRUE);
            if (!$this->form_validation->required($id)) {
                printAjaxError('fail', '请选择删除项');
            }
            $result = $this->Material_model->delete(array('id' => intval($id), 'store_id' => $store_info['id']));
            if ($result) {
                printAjaxSuccess('success', '删除成功');
            } else {
                printAjaxError('fail', '删除失败');
            }
        }
    }
    //面料列表
    public function my_get_fabric_list() {
        //判断是否登录
        checkLogin(true);
        checkPermission('fabric_index');
        $systemInfo = $this->System_model->get('*', array('id' => 1));
        $seller_group_info = $this->Seller_group_model->get('user_id',array('id' => get_cookie('seller_group_id')));
        $store_info = $this->Stores_model->get('id', array('user_id' => $seller_group_info['user_id']));
        $item_list = $this->Fabric_model->gets("store_id = {$store_info['id']} or (store_id = 0 and display = 1)");
        $data = array(
            'site_name' => $systemInfo['site_name'],
            'index_name' => $systemInfo['index_name'],
            
            'client_index' => $systemInfo['client_index'],
            'title' => '面料管理_商家中心' . $systemInfo['site_name'],
            'keywords' => $systemInfo['site_keywords'],
            'description' => $systemInfo['site_description'],
            'site_copyright' => $systemInfo['site_copyright'],
            'icp_code' => $systemInfo['icp_code'],
            'html' => $systemInfo['html'],
            'template' => $this->_template,
            'item_list' => $item_list
        );
        $layout = array(
            'content' => $this->load->view("{$this->_template}/my_get_fabric_list", $data, TRUE)
        );
        $this->load->view('layout/seller_layout', $layout);
        //缓存
        if ($systemInfo['cache'] == 1) {
            $this->output->cache($systemInfo['cache_time']);
        }
    }

    //编辑面料
    public function my_save_fabric($id = NULL) {
        //判断是否登录
        checkLogin(true);
        if ($id){
            checkPermission('fabric_edit');
        }else{
            checkPermission('fabric_add');
        }
        $systemInfo = $this->System_model->get('*', array('id' => 1));
        $seller_group_info = $this->Seller_group_model->get('user_id',array('id' => get_cookie('seller_group_id')));
        $store_info = $this->Stores_model->get('id', array('user_id' => $seller_group_info['user_id']));
        $item_info = $this->Fabric_model->get('*', array('id' => $id, 'store_id' => $store_info['id']));
        if ($_POST) {
            $fabric_name = $this->input->post('fabric_name', TRUE);
//            $tag = $this->input->post('tag', TRUE);
            if (!$this->form_validation->required($fabric_name)) {
                printAjaxError('brand_name', '面料名称不能为空');
            }
            if (!$this->form_validation->max_length($fabric_name, 100)) {
                printAjaxError('title', '面料名称字数不能超过100字');
            }

            if ($id) {
                $fields = array(
                    'fabric_name' => $fabric_name,
//                    'tag' => $tag,
                    'store_id' => $store_info['id'],
                );
                if ($this->Fabric_model->save($fields, array('id' => $id))) {
                    printAjaxSuccess(getBaseUrl(false, '', 'seller/my_get_fabric_list.html', $systemInfo['client_index']), '修改成功');
                } else {
                    printAjaxError('fail', "操作失败！");
                }
            } else {
                $i = 0;
                $fabric_name = preg_replace(array('/^\|+/', '/\|+$/', '/｜/'), array('', '', '|'), $fabric_name);
                $titleArr = explode("|", $fabric_name);
                foreach ($titleArr as $key => $title) {
                    $fields = array(
                        'fabric_name' => $title,
//                        'tag' => $tag,
                        'store_id' => $store_info['id'],
                    );
                    if ($this->Fabric_model->save($fields)) {
                        $i++;
                    }
                }
                if (count($titleArr) == $i) {
                    printAjaxSuccess(getBaseUrl(false, '', 'seller/my_get_fabric_list.html', $systemInfo['client_index']), '添加成功');
                } else {
                    printAjaxError('fail', "操作失败！");
                }
            }
        }
        $data = array(
            'site_name' => $systemInfo['site_name'],
            'index_name' => $systemInfo['index_name'],
            
            'client_index' => $systemInfo['client_index'],
            'title' => '添加材质_商家中心' . $systemInfo['site_name'],
            'keywords' => $systemInfo['site_keywords'],
            'description' => $systemInfo['site_description'],
            'site_copyright' => $systemInfo['site_copyright'],
            'icp_code' => $systemInfo['icp_code'],
            'html' => $systemInfo['html'],
            'template' => $this->_template,
            'item_info' => $item_info,
        );
        $layout = array(
            'content' => $this->load->view("{$this->_template}/my_save_fabric", $data, TRUE)
        );
        $this->load->view('layout/seller_layout', $layout);
        //缓存
        if ($systemInfo['cache'] == 1) {
            $this->output->cache($systemInfo['cache_time']);
        }
    }

    public function my_delete_fabric() {
        checkLoginAjax(true);
        checkPermissionAjax('fabric_delete');
        if ($_POST) {
            $seller_group_info = $this->Seller_group_model->get('user_id',array('id' => get_cookie('seller_group_id')));
            $store_info = $this->Stores_model->get('id', array('user_id' => $seller_group_info['user_id']));
            $id = $this->input->post('id', TRUE);
            if (!$this->form_validation->required($id)) {
                printAjaxError('fail', '请选择删除项');
            }
            $result = $this->Fabric_model->delete(array('id' => intval($id), 'store_id' => $store_info['id']));
            if ($result) {
                printAjaxSuccess('success', '删除成功');
            } else {
                printAjaxError('fail', '删除失败');
            }
        }
    }
    //皮革列表
    public function my_get_leather_list() {
        //判断是否登录
        checkLogin(true);
        checkPermission('leather_index');
        $systemInfo = $this->System_model->get('*', array('id' => 1));
        $seller_group_info = $this->Seller_group_model->get('user_id',array('id' => get_cookie('seller_group_id')));
        $store_info = $this->Stores_model->get('id', array('user_id' => $seller_group_info['user_id']));
        $item_list = $this->Leather_model->gets("store_id = {$store_info['id']} or (store_id = 0 and display = 1)");
        $data = array(
            'site_name' => $systemInfo['site_name'],
            'index_name' => $systemInfo['index_name'],
            
            'client_index' => $systemInfo['client_index'],
            'title' => '材质管理_商家中心' . $systemInfo['site_name'],
            'keywords' => $systemInfo['site_keywords'],
            'description' => $systemInfo['site_description'],
            'site_copyright' => $systemInfo['site_copyright'],
            'icp_code' => $systemInfo['icp_code'],
            'html' => $systemInfo['html'],
            'template' => $this->_template,
            'item_list' => $item_list
        );
        $layout = array(
            'content' => $this->load->view("{$this->_template}/my_get_leather_list", $data, TRUE)
        );
        $this->load->view('layout/seller_layout', $layout);
        //缓存
        if ($systemInfo['cache'] == 1) {
            $this->output->cache($systemInfo['cache_time']);
        }
    }

    //编辑皮革
    public function my_save_leather($id = NULL) {
        //判断是否登录
        checkLogin(true);
        if ($id){
            checkPermission('leather_edit');
        }else{
            checkPermission('leather_add');
        }
        $systemInfo = $this->System_model->get('*', array('id' => 1));
        $seller_group_info = $this->Seller_group_model->get('user_id',array('id' => get_cookie('seller_group_id')));
        $store_info = $this->Stores_model->get('id', array('user_id' => $seller_group_info['user_id']));
        $item_info = $this->Leather_model->get('*', array('id' => $id, 'store_id' => $store_info['id']));
        if ($_POST) {
            $leather_name = $this->input->post('leather_name', TRUE);
//            $tag = $this->input->post('tag', TRUE);
            if (!$this->form_validation->required($leather_name)) {
                printAjaxError('brand_name', '皮革名称不能为空');
            }
            if (!$this->form_validation->max_length($leather_name, 100)) {
                printAjaxError('title', '皮革名称字数不能超过100字');
            }

            if ($id) {
                $fields = array(
                    'leather_name' => $leather_name,
//                    'tag' => $tag,
                    'store_id' => $store_info['id'],
                );
                if ($this->Leather_model->save($fields, array('id' => $id))) {
                    printAjaxSuccess(getBaseUrl(false, '', 'seller/my_get_leather_list.html', $systemInfo['client_index']), '修改成功');
                } else {
                    printAjaxError('fail', "操作失败！");
                }
            } else {
                $i = 0;
                $leather_name = preg_replace(array('/^\|+/', '/\|+$/', '/｜/'), array('', '', '|'), $leather_name);
                $titleArr = explode("|", $leather_name);
                foreach ($titleArr as $key => $title) {
                    $fields = array(
                        'leather_name' => $title,
//                        'tag' => $tag,
                        'store_id' => $store_info['id'],
                    );
                    if ($this->Leather_model->save($fields)) {
                        $i++;
                    }
                }
                if (count($titleArr) == $i) {
                    printAjaxSuccess(getBaseUrl(false, '', 'seller/my_get_leather_list.html', $systemInfo['client_index']), '添加成功');
                } else {
                    printAjaxError('fail', "操作失败！");
                }
            }
        }
        $data = array(
            'site_name' => $systemInfo['site_name'],
            'index_name' => $systemInfo['index_name'],
            
            'client_index' => $systemInfo['client_index'],
            'title' => '添加材质_商家中心' . $systemInfo['site_name'],
            'keywords' => $systemInfo['site_keywords'],
            'description' => $systemInfo['site_description'],
            'site_copyright' => $systemInfo['site_copyright'],
            'icp_code' => $systemInfo['icp_code'],
            'html' => $systemInfo['html'],
            'template' => $this->_template,
            'item_info' => $item_info,
        );
        $layout = array(
            'content' => $this->load->view("{$this->_template}/my_save_leather", $data, TRUE)
        );
        $this->load->view('layout/seller_layout', $layout);
        //缓存
        if ($systemInfo['cache'] == 1) {
            $this->output->cache($systemInfo['cache_time']);
        }
    }

    public function my_delete_leather() {
        checkLoginAjax(true);
        checkPermissionAjax('leather_delete');
        if ($_POST) {
            $seller_group_info = $this->Seller_group_model->get('user_id',array('id' => get_cookie('seller_group_id')));
            $store_info = $this->Stores_model->get('id', array('user_id' => $seller_group_info['user_id']));
            $id = $this->input->post('id', TRUE);
            if (!$this->form_validation->required($id)) {
                printAjaxError('fail', '请选择删除项');
            }
            $result = $this->Leather_model->delete(array('id' => intval($id), 'store_id' => $store_info['id']));
            if ($result) {
                printAjaxSuccess('success', '删除成功');
            } else {
                printAjaxError('fail', '删除失败');
            }
        }
    }
    //填充物列表
    public function my_get_filler_list() {
        //判断是否登录
        checkLogin(true);
        checkPermission('filler_index');
        $systemInfo = $this->System_model->get('*', array('id' => 1));
        $seller_group_info = $this->Seller_group_model->get('user_id',array('id' => get_cookie('seller_group_id')));
        $store_info = $this->Stores_model->get('id', array('user_id' => $seller_group_info['user_id']));
        $item_list = $this->Filler_model->gets("store_id = {$store_info['id']} or (store_id = 0 and display = 1)");
        $data = array(
            'site_name' => $systemInfo['site_name'],
            'index_name' => $systemInfo['index_name'],
            
            'client_index' => $systemInfo['client_index'],
            'title' => '材质管理_商家中心' . $systemInfo['site_name'],
            'keywords' => $systemInfo['site_keywords'],
            'description' => $systemInfo['site_description'],
            'site_copyright' => $systemInfo['site_copyright'],
            'icp_code' => $systemInfo['icp_code'],
            'html' => $systemInfo['html'],
            'template' => $this->_template,
            'item_list' => $item_list
        );
        $layout = array(
            'content' => $this->load->view("{$this->_template}/my_get_filler_list", $data, TRUE)
        );
        $this->load->view('layout/seller_layout', $layout);
        //缓存
        if ($systemInfo['cache'] == 1) {
            $this->output->cache($systemInfo['cache_time']);
        }
    }

    //编辑填充物
    public function my_save_filler($id = NULL) {
        //判断是否登录
        checkLogin(true);
        if ($id){
            checkPermission('filler_edit');
        }else{
            checkPermission('filler_add');
        }
        $systemInfo = $this->System_model->get('*', array('id' => 1));
        $seller_group_info = $this->Seller_group_model->get('user_id',array('id' => get_cookie('seller_group_id')));
        $store_info = $this->Stores_model->get('id', array('user_id' => $seller_group_info['user_id']));
        $item_info = $this->Filler_model->get('*', array('id' => $id, 'store_id' => $store_info['id']));
        if ($_POST) {
            $filler_name = $this->input->post('filler_name', TRUE);
//            $tag = $this->input->post('tag', TRUE);
            if (!$this->form_validation->required($filler_name)) {
                printAjaxError('brand_name', '填充物名称不能为空');
            }
            if (!$this->form_validation->max_length($filler_name, 100)) {
                printAjaxError('title', '填充物名称字数不能超过100字');
            }

            if ($id) {
                $fields = array(
                    'filler_name' => $filler_name,
//                    'tag' => $tag,
                    'store_id' => $store_info['id'],
                );
                if ($this->Filler_model->save($fields, array('id' => $id))) {
                    printAjaxSuccess(getBaseUrl(false, '', 'seller/my_get_filler_list.html', $systemInfo['client_index']), '修改成功');
                } else {
                    printAjaxError('fail', "操作失败！");
                }
            } else {
                $i = 0;
                $filler_name = preg_replace(array('/^\|+/', '/\|+$/', '/｜/'), array('', '', '|'), $filler_name);
                $titleArr = explode("|", $filler_name);
                foreach ($titleArr as $key => $title) {
                    $fields = array(
                        'filler_name' => $title,
//                        'tag' => $tag,
                        'store_id' => $store_info['id'],
                    );
                    if ($this->Filler_model->save($fields)) {
                        $i++;
                    }
                }
                if (count($titleArr) == $i) {
                    printAjaxSuccess(getBaseUrl(false, '', 'seller/my_get_filler_list.html', $systemInfo['client_index']), '添加成功');
                } else {
                    printAjaxError('fail', "操作失败！");
                }
            }
        }
        $data = array(
            'site_name' => $systemInfo['site_name'],
            'index_name' => $systemInfo['index_name'],
            
            'client_index' => $systemInfo['client_index'],
            'title' => '添加材质_商家中心' . $systemInfo['site_name'],
            'keywords' => $systemInfo['site_keywords'],
            'description' => $systemInfo['site_description'],
            'site_copyright' => $systemInfo['site_copyright'],
            'icp_code' => $systemInfo['icp_code'],
            'html' => $systemInfo['html'],
            'template' => $this->_template,
            'item_info' => $item_info,
        );
        $layout = array(
            'content' => $this->load->view("{$this->_template}/my_save_filler", $data, TRUE)
        );
        $this->load->view('layout/seller_layout', $layout);
        //缓存
        if ($systemInfo['cache'] == 1) {
            $this->output->cache($systemInfo['cache_time']);
        }
    }

    public function my_delete_filler() {
        checkLoginAjax(true);
        checkPermissionAjax('filler_delete');
        if ($_POST) {
            $seller_group_info = $this->Seller_group_model->get('user_id',array('id' => get_cookie('seller_group_id')));
            $store_info = $this->Stores_model->get('id', array('user_id' => $seller_group_info['user_id']));
            $id = $this->input->post('id', TRUE);
            if (!$this->form_validation->required($id)) {
                printAjaxError('fail', '请选择删除项');
            }
            $result = $this->Filler_model->delete(array('id' => intval($id), 'store_id' => $store_info['id']));
            if ($result) {
                printAjaxSuccess('success', '删除成功');
            } else {
                printAjaxError('fail', '删除失败');
            }
        }
    }

    //服务选项列表
    public function my_get_service_options_list() {
        //判断是否登录
        checkLogin(true);
        checkPermission('service_options_index');
        $systemInfo = $this->System_model->get('*', array('id' => 1));
        $seller_group_info = $this->Seller_group_model->get('user_id',array('id' => get_cookie('seller_group_id')));
        $store_info = $this->Stores_model->get('id', array('user_id' => $seller_group_info['user_id']));
        $item_list = $this->Service_options_model->gets("store_id = {$store_info['id']} or (store_id = 0 and display = 1)");
        $data = array(
            'site_name' => $systemInfo['site_name'],
            'index_name' => $systemInfo['index_name'],
            
            'client_index' => $systemInfo['client_index'],
            'title' => '服务选项管理_商家中心' . $systemInfo['site_name'],
            'keywords' => $systemInfo['site_keywords'],
            'description' => $systemInfo['site_description'],
            'site_copyright' => $systemInfo['site_copyright'],
            'icp_code' => $systemInfo['icp_code'],
            'html' => $systemInfo['html'],
            'template' => $this->_template,
            'item_list' => $item_list
        );
        $layout = array(
            'content' => $this->load->view("{$this->_template}/my_get_service_options_list", $data, TRUE)
        );
        $this->load->view('layout/seller_layout', $layout);
        //缓存
        if ($systemInfo['cache'] == 1) {
            $this->output->cache($systemInfo['cache_time']);
        }
    }

    //编辑服务选项
    public function my_save_service_options($id = NULL) {
        //判断是否登录
        checkLogin(true);
        if ($id){
            checkPermission('service_options_edit');
        }else{
            checkPermission('service_options_add');
        }
        $systemInfo = $this->System_model->get('*', array('id' => 1));
        $seller_group_info = $this->Seller_group_model->get('user_id',array('id' => get_cookie('seller_group_id')));
        $store_info = $this->Stores_model->get('id', array('user_id' => $seller_group_info['user_id']));
        $item_info = $this->Service_options_model->get('*', array('id' => $id, 'store_id' => $store_info['id']));
        if ($_POST) {
            $option = $this->input->post('option', TRUE);
            if (!$this->form_validation->required($option)) {
                printAjaxError('brand_name', '填充物名称不能为空');
            }
            if (!$this->form_validation->max_length($option, 100)) {
                printAjaxError('title', '填充物名称字数不能超过100字');
            }

            if ($id) {
                $fields = array(
                    'option' => $option,
                    'store_id' => $store_info['id'],
                );
                if ($this->Service_options_model->save($fields, array('id' => $id))) {
                    printAjaxSuccess(getBaseUrl(false, '', 'seller/my_get_service_options_list.html', $systemInfo['client_index']), '修改成功');
                } else {
                    printAjaxError('fail', "操作失败！");
                }
            } else {
                $i = 0;
                $option = preg_replace(array('/^\|+/', '/\|+$/', '/｜/'), array('', '', '|'), $option);
                $titleArr = explode("|", $option);
                foreach ($titleArr as $key => $title) {
                    $fields = array(
                        'option' => $title,
                        'store_id' => $store_info['id'],
                    );
                    if ($this->Service_options_model->save($fields)) {
                        $i++;
                    }
                }
                if (count($titleArr) == $i) {
                    printAjaxSuccess(getBaseUrl(false, '', 'seller/my_get_service_options_list.html', $systemInfo['client_index']), '添加成功');
                } else {
                    printAjaxError('fail', "操作失败！");
                }
            }
        }
        $data = array(
            'site_name' => $systemInfo['site_name'],
            'index_name' => $systemInfo['index_name'],
            
            'client_index' => $systemInfo['client_index'],
            'title' => '添加服务_商家中心' . $systemInfo['site_name'],
            'keywords' => $systemInfo['site_keywords'],
            'description' => $systemInfo['site_description'],
            'site_copyright' => $systemInfo['site_copyright'],
            'icp_code' => $systemInfo['icp_code'],
            'html' => $systemInfo['html'],
            'template' => $this->_template,
            'item_info' => $item_info,
        );
        $layout = array(
            'content' => $this->load->view("{$this->_template}/my_save_service_options", $data, TRUE)
        );
        $this->load->view('layout/seller_layout', $layout);
        //缓存
        if ($systemInfo['cache'] == 1) {
            $this->output->cache($systemInfo['cache_time']);
        }
    }

    public function my_delete_service_options() {
        checkLoginAjax(true);
        checkPermissionAjax('service_options_delete');
        if ($_POST) {
            $seller_group_info = $this->Seller_group_model->get('user_id',array('id' => get_cookie('seller_group_id')));
            $store_info = $this->Stores_model->get('id', array('user_id' => $seller_group_info['user_id']));
            $id = $this->input->post('id', TRUE);
            if (!$this->form_validation->required($id)) {
                printAjaxError('fail', '请选择删除项');
            }
            $result = $this->Service_options_model->delete(array('id' => intval($id), 'store_id' => $store_info['id']));
            if ($result) {
                printAjaxSuccess('success', '删除成功');
            } else {
                printAjaxError('fail', '删除失败');
            }
        }
    }

    //广告列表
    public function my_get_ad_list() {
        //判断是否登录
        checkLogin(true);
        checkPermission('ad_store_index');
        $systemInfo = $this->System_model->get('*', array('id' => 1));
        $seller_group_info = $this->Seller_group_model->get('user_id',array('id' => get_cookie('seller_group_id')));
        $store_info = $this->Stores_model->get('id', array('user_id' => $seller_group_info['user_id']));
        $ad_store_list_1 = $this->Ad_Stores_model->gets('*', array('position' => 1, 'store_id' => $store_info['id']));
        $ad_store_list_2 = $this->Ad_Stores_model->gets('*', array('position' => 2, 'store_id' => $store_info['id']));
        $data = array(
            'site_name' => $systemInfo['site_name'],
            'index_name' => $systemInfo['index_name'],
            
            'client_index' => $systemInfo['client_index'],
            'title' => '广告管理_商家中心' . $systemInfo['site_name'],
            'keywords' => $systemInfo['site_keywords'],
            'description' => $systemInfo['site_description'],
            'site_copyright' => $systemInfo['site_copyright'],
            'icp_code' => $systemInfo['icp_code'],
            'html' => $systemInfo['html'],
            'template' => $this->_template,
            'ad_store_list_1' => $ad_store_list_1,
            'ad_store_list_2' => $ad_store_list_2,
        );
        $layout = array(
            'content' => $this->load->view("{$this->_template}/my_get_ad_list", $data, TRUE)
        );
        $this->load->view('layout/seller_layout', $layout);
        //缓存
        if ($systemInfo['cache'] == 1) {
            $this->output->cache($systemInfo['cache_time']);
        }
    }

    public function my_ad_store_save() {
        checkLogin(true);
        checkPermission('ad_store_add');
        $seller_group_info = $this->Seller_group_model->get('user_id',array('id' => get_cookie('seller_group_id')));
        $store_info = $this->Stores_model->get('id', array('user_id' => $seller_group_info['user_id']));
        $json = file_get_contents('php://input');
//        var_dump($json);
        if (empty($json)) {
            printAjaxError('fail', '提交的数据为空');
        }
        $item_list = json_decode($json, true);
        if (empty($item_list)) {
            printAjaxError('fail', '提交的数据为空');
        }

        foreach ($item_list as $ls) {
            if ($ls['url'] && strpos($ls['url'], preg_replace('/\/$/', '', base_url()), 0) === false) {
                printAjaxError('fail', '其中有一项不是本站地址');
            }
        }
        foreach ($item_list as $item) {
            $this->Ad_Stores_model->save(array('sort' => intval($item['sort']), 'ad_text' => $item['ad_text'], 'url' => $item['url'], 'xcx_url' => $item['xcx_url'] ,'app_url' => $item['app_url']), array('id' => $item['id'], 'store_id' => $store_info['id']));
        }
        printAjaxSuccess('success', '保存成功');
    }

    public function my_delete_ad_store() {
        checkLogin(true);
        checkPermission('ad_store_delete');
        if ($_POST) {
            $seller_group_info = $this->Seller_group_model->get('user_id',array('id' => get_cookie('seller_group_id')));
            $store_info = $this->Stores_model->get('id', array('user_id' => $seller_group_info['user_id']));
            $id = $this->input->post('id');
            if (!$this->form_validation->required($id)) {
                printAjaxError('fail', '请选定删除项');
            }
            $item_info = $this->Ad_Stores_model->get('*', array('id' => intval($id), 'store_id' => $store_info['id']));
            if (empty($item_info)) {
                printAjaxError('fail', '不存在此项');
            }
            unlink($item_info['path']);
            unlink(str_replace('.', '_thumb.', $item_info['path']));
            $result = $this->Ad_Stores_model->delete(array('id' => intval($id), 'store_id' => $store_info['id']));
            if ($result) {
                printAjaxSuccess('success', '删除成功');
            } else {
                printAjaxError('fail', '删除失败');
            }
        }
    }

    //添加主题
    public function my_set_theme() {
        //判断是否登录
        checkLogin(true);
        checkPermission('theme_index');
        $systemInfo = $this->System_model->get('*', array('id' => 1));
        $seller_group_info = $this->Seller_group_model->get('user_id',array('id' => get_cookie('seller_group_id')));
        $user_id = $seller_group_info['user_id'];
        $store_info = $this->Stores_model->get2(array('user_id' => $user_id));
        $cur_theme_info = NULL;
        $item_list = NULL;
        if ($store_info && $store_info['theme_ids']) {
            $item_list = $this->Theme_model->gets("id in ({$store_info['theme_ids']}) and display = 1");
            if ($item_list) {
                foreach ($item_list as $key => $value) {
                    if ($value['alias'] == $store_info['theme']) {
                        $cur_theme_info = $value;
                        break;
                    }
                }
            }
        }


        $seller_group_info = $this->Seller_group_model->get('user_id',array('id' => get_cookie('seller_group_id')));
//        $store_info = $this->Stores_model->get('id', array('user_id' => $seller_group_info['user_id']));
        $ad_store_list_1 = $this->Ad_Stores_model->gets('*', array('position' => 1, 'store_id' => $store_info['id']));
        $ad_store_list_2 = $this->Ad_Stores_model->gets('*', array('position' => 2, 'store_id' => $store_info['id']));


        $data = array(
            'site_name' => $systemInfo['site_name'],
            'index_name' => $systemInfo['index_name'],
            
            'client_index' => $systemInfo['client_index'],
            'title' => '添加主题_商家中心' . $systemInfo['site_name'],
            'keywords' => $systemInfo['site_keywords'],
            'description' => $systemInfo['site_description'],
            'site_copyright' => $systemInfo['site_copyright'],
            'icp_code' => $systemInfo['icp_code'],
            'html' => $systemInfo['html'],
            'item_list' => $item_list,
            'cur_theme_info' => $cur_theme_info,
            'store_info' => $store_info,
            'template' => $this->_template,
            'ad_store_list_1' => $ad_store_list_1,
            'ad_store_list_2' => $ad_store_list_2,
        );
        $layout = array(
            'content' => $this->load->view("{$this->_template}/my_set_theme", $data, TRUE)
        );
        $this->load->view('layout/seller_layout', $layout);
        //缓存
        if ($systemInfo['cache'] == 1) {
            $this->output->cache($systemInfo['cache_time']);
        }
    }

    public function my_set_store_theme() {
        //判断是否登录
        checkLoginAjax(true);
        checkPermissionAjax('theme_add');
        if ($_POST) {
            $seller_group_info = $this->Seller_group_model->get('user_id',array('id' => get_cookie('seller_group_id')));
            $user_id = $seller_group_info['user_id'];
            $theme = $this->input->post('theme', TRUE);
            if (!$theme) {
                printAjaxError('fail', '请选择设置的主题');
            }
            $is_yes = false;
            $store_info = $this->Stores_model->get2(array('user_id' => $user_id));
            if ($store_info && $store_info['theme_ids']) {
                $item_list = $this->Theme_model->gets("id in ({$store_info['theme_ids']}) and display = 1");
                if ($item_list) {
                    foreach ($item_list as $key => $value) {
                        if ($value['alias'] == $theme) {
                            $is_yes = true;
                            break;
                        }
                    }
                }
            }
            if (!$is_yes) {
                printAjaxError('fali', '主题设置异常');
            }
            if (!$this->Stores_model->save(array('theme' => $theme), array('user_id' => $user_id, 'id' => $store_info['id']))) {
                printAjaxError('fali', '主题设置失败，刷新重试');
            }
            printAjaxSuccess('success', '主题设置成功');
        }
    }

    //导航管理
    public function my_get_nav_list() {
        //判断是否登录
        checkLogin(true);
        checkPermission('navigation_index');
        $systemInfo = $this->System_model->get('*', array('id' => 1));
        $seller_group_info = $this->Seller_group_model->get('user_id',array('id' => get_cookie('seller_group_id')));
        $store_info = $this->Stores_model->get('id', array('user_id' => $seller_group_info['user_id']));
        $item_list = $this->Navigation_model->gets(array('store_id' => $store_info['id']));
        $data = array(
            'site_name' => $systemInfo['site_name'],
            'index_name' => $systemInfo['index_name'],
            
            'client_index' => $systemInfo['client_index'],
            'title' => '导航管理_商家中心' . $systemInfo['site_name'],
            'keywords' => $systemInfo['site_keywords'],
            'description' => $systemInfo['site_description'],
            'site_copyright' => $systemInfo['site_copyright'],
            'icp_code' => $systemInfo['icp_code'],
            'html' => $systemInfo['html'],
            'template' => $this->_template,
            'item_list' => $item_list
        );
        $layout = array(
            'content' => $this->load->view("{$this->_template}/my_get_nav_list", $data, TRUE)
        );
        $this->load->view('layout/seller_layout', $layout);
        //缓存
        if ($systemInfo['cache'] == 1) {
            $this->output->cache($systemInfo['cache_time']);
        }
    }

    //添加导航
    public function my_save_nav($id = null) {
        //判断是否登录
        checkLogin(true);
        if ($id){
            checkPermission('navigation_edit');
        }else{
            checkPermission('navigation_add');
        }
        $systemInfo = $this->System_model->get('*', array('id' => 1));
        $seller_group_info = $this->Seller_group_model->get('user_id',array('id' => get_cookie('seller_group_id')));
        $store_info = $this->Stores_model->get('id', array('user_id' => $seller_group_info['user_id']));
        $item_info = $this->Navigation_model->get('*', array('id' => $id, 'store_id' => $store_info['id']));
        if ($_POST) {
            $title = $this->input->post('title', TRUE);
            $url = $this->input->post('url', TRUE);
            $sort = $this->input->post('sort', TRUE);
            $display = $this->input->post('display', TRUE);
            $content = $this->input->post('content');

            if (!$this->form_validation->required($title)) {
                printAjaxError('title', '导航名称不能为空');
            }
            if (!$this->form_validation->max_length($title, 20)) {
                printAjaxError('title', '导航名称字数不能超过20字');
            }
            if ($url && !preg_match('/^http[s]?:\/\/[A-Za-z0-9]+\.[A-Za-z0-9]+[\/=\?%\-&_~`@[\]\':+!]*([^<>\"\"])*$/', $url)) {
                printAjaxError('url', '导航链接格式错误');
            }
            if (!$this->form_validation->required($content)) {
                printAjaxError('content', '描述不能为空');
            }

            $fields = array(
                'title' => $title,
                'url' => $url,
                'sort' => intval($sort),
                'display' => $display ? 1 : 0,
                'content' => unhtml($content),
                'store_id' => $store_info['id']
            );
            $result = $this->Navigation_model->save($fields, $id ? array('id' => $id) : NULL);
            if ($result) {
                printAjaxSuccess(getBaseUrl(false, '', 'seller/my_get_nav_list.html', $systemInfo['client_index']), '提交成功');
            } else {
                printAjaxError('fail', '提交失败');
            }
        }
        $data = array(
            'site_name' => $systemInfo['site_name'],
            'index_name' => $systemInfo['index_name'],
            
            'client_index' => $systemInfo['client_index'],
            'title' => '添加导航_商家中心' . $systemInfo['site_name'],
            'keywords' => $systemInfo['site_keywords'],
            'description' => $systemInfo['site_description'],
            'site_copyright' => $systemInfo['site_copyright'],
            'icp_code' => $systemInfo['icp_code'],
            'html' => $systemInfo['html'],
            'template' => $this->_template,
            'item_info' => $item_info,
        );
        $layout = array(
            'content' => $this->load->view("{$this->_template}/my_save_nav", $data, TRUE)
        );
        $this->load->view('layout/seller_layout', $layout);
        //缓存
        if ($systemInfo['cache'] == 1) {
            $this->output->cache($systemInfo['cache_time']);
        }
    }

    public function my_change_nav_sort() {
        checkLoginAjax(true);
        checkPermissionAjax('navigation_edit');
        if ($_POST) {
            $seller_group_info = $this->Seller_group_model->get('user_id',array('id' => get_cookie('seller_group_id')));
            $store_info = $this->Stores_model->get('id', array('user_id' => $seller_group_info['user_id']));
            $id = $this->input->post('id', TRUE);
            $sort = $this->input->post('sort', TRUE);
            if (!$this->form_validation->required($id)) {
                printAjaxError('fail', '操作异常');
            }

            $result = $this->Navigation_model->save(array('sort' => intval($sort)), array('id' => intval($id), 'store_id' => $store_info['id']));
            if ($result) {
                $item_list = $this->Navigation_model->gets(array('store_id' => $store_info['id']));
                printAjaxData($item_list);
            } else {
                printAjaxError('fail', '保存失败');
            }
        }
    }

    public function my_change_nav_display() {
        checkLoginAjax(true);
        checkPermissionAjax('navigation_edit');
        if ($_POST) {
            $seller_group_info = $this->Seller_group_model->get('user_id',array('id' => get_cookie('seller_group_id')));
            $store_info = $this->Stores_model->get('id', array('user_id' => $seller_group_info['user_id']));
            $id = $this->input->post('id', TRUE);
            $display = $this->input->post('display', TRUE);
            if (!$this->form_validation->required($id)) {
                printAjaxError('fail', '操作异常');
            }

            $result = $this->Navigation_model->save(array('display' => $display ? 1 : 0), array('id' => intval($id), 'store_id' => $store_info['id']));
            if ($result) {
                printAjaxSuccess('success', '保存成功');
            } else {
                printAjaxError('fail', '保存失败');
            }
        }
    }

    public function my_delete_navigation() {
        checkLoginAjax(true);
        checkPermissionAjax('navigation_delete');
        if ($_POST) {
            $seller_group_info = $this->Seller_group_model->get('user_id',array('id' => get_cookie('seller_group_id')));
            $store_info = $this->Stores_model->get('id', array('user_id' => $seller_group_info['user_id']));
            $id = $this->input->post('id', TRUE);
            if (!$this->form_validation->required($id)) {
                printAjaxError('fail', '请选定删除项');
            }

            $result = $this->Navigation_model->delete(array('id' => intval($id), 'store_id' => $store_info['id']));
            if ($result) {
                printAjaxSuccess('success', '删除成功');
            } else {
                printAjaxError('fail', '删除失败');
            }
        }
    }

    //订单列表
    public function order_index($s = 'all', $clear = 1, $page = 0) {
        //判断是否登录
        checkLogin(true);
        checkPermission('order_index');
        //超过24小时关闭订单
        $time = time();
        $order_list = $this->Orders_model->gets('id',"create_time <= {$time} - (24*60*60) and status = 0");
        if ($order_list){
            foreach ($order_list as $value){
                $fields = array(
                    'status'=>10
                );
                if ($this->Orders_model->save($fields, array('id'=>$value['id']))) {
                    $fields = array(
                        'content' => '超时交易自动关闭',
                        'order_id' => $value['id'],
                        'change_status' => 10,
                        'order_status' => 0
                    );
                    $this->Orders_process_model->save($fields);
                }
            }
        }

        $systemInfo = $this->System_model->get('*', array('id' => 1));
        $seller_group_info = $this->Seller_group_model->get('user_id',array('id' => get_cookie('seller_group_id')));
        $user_id =$seller_group_info['user_id'];
        $store_info = $this->Stores_model->get('id',array('user_id'=>$user_id));
        $condition = "store_id = {$store_info['id']} ";
        if ($s != 'all') {
            $condition .= " and status = {$s} ";
        }
        if($clear) {
            $clear = 0;
            $this->session->unset_userdata('search');
        }
        $strWhere = $this->session->userdata('search') ? $this->session->userdata('search') : $condition;

        if ($_POST) {
            $strWhere = $condition;
            $order_number = $this->input->post('order_number');
            $status = $this->input->post('status');
            $startTime = $this->input->post('create_time_start');
            $endTime = $this->input->post('create_time_end');

            if (! empty($order_number) ) {
                $strWhere .= " and order_number = '{$order_number}' ";
            }
            if ($status != "") {
                $strWhere .= " and status = {$status} ";
            }
            if (! empty($startTime) && ! empty($endTime)) {
                $strWhere .= " and create_time > '{$startTime} 00:00:00' and create_time < '{$endTime} 23:59:59'";
            }
            $this->session->set_userdata('search', $strWhere);
        }
        //分页
        $paginationCount = $this->Orders_model->count($strWhere);
        $this->config->load('pagination_config', TRUE);
        $paginationConfig = $this->config->item('pagination_config');
        $paginationConfig['base_url'] = base_url() . "index.php/{$this->_template}/order_index/{$s}/{$clear}";
        $paginationConfig['total_rows'] = $paginationCount;
        $paginationConfig['uri_segment'] = 5;
        $paginationConfig['per_page'] = 20;
        $this->pagination->initialize($paginationConfig);
        $pagination = $this->pagination->create_links();

        $item_list = $this->Orders_model->gets('*', $strWhere, $paginationConfig['per_page'], $page);
        if ($item_list) {
            foreach ($item_list as $key => $order) {
                $order_detail_list = $this->Orders_detail_model->gets('*', array('order_id' => $order['id']));
                $item_list[$key]['order_detail_list'] = $order_detail_list;
            }
        }

        $data = array(
            'site_name' => $systemInfo['site_name'],
            'index_name' => $systemInfo['index_name'],
            
            'client_index' => $systemInfo['client_index'],
            'title' => '订单列表_商家中心' . $systemInfo['site_name'],
            'keywords' => $systemInfo['site_keywords'],
            'description' => $systemInfo['site_description'],
            'site_copyright' => $systemInfo['site_copyright'],
            'icp_code' => $systemInfo['icp_code'],
            'html' => $systemInfo['html'],
            'item_list' => $item_list,
            'pagination' => $pagination,
            'pageCount' => ceil($paginationCount / $paginationConfig['per_page']),
            'perPage' => $paginationConfig['per_page'],
            'status' => $this->_status,
            'exchange_status_arr' => $this->_exchange_status2_arr,
            'select_status' => $s,
            'template' => $this->_template
        );
        $layout = array(
            'content' => $this->load->view("{$this->_template}/order_index", $data, TRUE)
        );
        $this->load->view('layout/seller_layout', $layout);
        //缓存
        if ($systemInfo['cache'] == 1) {
            $this->output->cache($systemInfo['cache_time']);
        }
    }

    //查看详情
    public function my_get_order_view($id = NULL) {
        //判断是否登录
        checkLogin(true);
        checkPermission('order_index');

        $systemInfo = $this->System_model->get('*', array('id' => 1));
        $seller_group_info = $this->Seller_group_model->get('user_id',array('id' => get_cookie('seller_group_id')));
        $user_id =$seller_group_info['user_id'];
        $strWhere = "seller_id = {$user_id} ";
        $store_info = $this->Stores_model->get('id',array('user_id'=>$user_id));
        $strWhere .= " and store_id = {$store_info['id']} and id = {$id}";
        $item_info = $this->Orders_model->get('*', $strWhere);
        if (!$item_info) {
            $data = array(
                'user_msg' => '此订单信息不存在',
                'user_url' => base_url()
            );
            $this->session->set_userdata($data);
            redirect('/message/index');
        }
        $orders_detail_list = $this->Orders_detail_model->gets('*', array('order_id' => $item_info['id']));
        if($orders_detail_list){
            foreach ($orders_detail_list as $key=>$value){
                $exchange_info = $this->Exchange_model->get('id,status', array('orders_id' => $value['order_id'],'orders_detail_id'=>$value['id']));
                if ($exchange_info) {
                    $exchange_info['is_exchange'] = 1;
                    $orders_detail_list[$key]['exchange_info'] = $exchange_info;
                } else {
                    $orders_detail_list[$key]['exchange_info'] = array('is_exchange' => 0);
                }
            }
        }
        $item_info['orders_detail_list'] = $orders_detail_list;
        $item_info['orders_process_list'] = $this->Orders_process_model->gets('*', array('order_id' => $item_info['id']));
        $item_info['pay_time'] = null;
        $item_info['delivery_time'] = null;
        $item_info['receiving_time'] = null;
        if($item_info['orders_process_list']){
            foreach ($item_info['orders_process_list'] as $key=>$value){
                if($value['change_status'] == 1){
                    $item_info['pay_time'] = $value['add_time'];
                }
                if($value['change_status'] == 2){
                    $item_info['delivery_time'] = $value['add_time'];
                }
                if($value['change_status'] == 3){
                    $item_info['receiving_time'] = $value['add_time'];
                }
            }
        }

        $data = array(
            'site_name' => $systemInfo['site_name'],
            'index_name' => $systemInfo['index_name'],
            
            'client_index' => $systemInfo['client_index'],
            'title' => '订单详细_商家中心' . $systemInfo['site_name'],
            'keywords' => $systemInfo['site_keywords'],
            'description' => $systemInfo['site_description'],
            'site_copyright' => $systemInfo['site_copyright'],
            'icp_code' => $systemInfo['icp_code'],
            'html' => $systemInfo['html'],
            'item_info' => $item_info,
            'status' => $this->_status,
            'template' => $this->_template
        );
        $layout = array(
            'content' => $this->load->view("{$this->_template}/my_get_order_view", $data, TRUE)
        );
        $this->load->view('layout/seller_layout', $layout);
        //缓存
        if ($systemInfo['cache'] == 1) {
            $this->output->cache($systemInfo['cache_time']);
        }
    }

    //修改价格
    public function seller_change_price(){
        //判断是否登录
        checkLoginAjax(true);
        $seller_group_info = $this->Seller_group_model->get('user_id',array('id' => get_cookie('seller_group_id')));
        $store_info = $this->Stores_model->get('id', array('user_id' => $seller_group_info['user_id']));
        if ($_POST) {
            $id = $this->input->post('id', TRUE);
            $total = $this->input->post('total', TRUE);
            if (! $this->form_validation->required($total)) {
                printAjaxError('fail','修改价格不能为空！');
            }
            if (! $this->form_validation->numeric($total)) {
                printAjaxError('fail','修改价格必须为正确的金额！');
            }
            $count = $this->Orders_model->rowCount(array('id'=>$id,'store_id'=>$store_info['id']));
            if (!$count) {
                printAjaxError('fail','此订单不存在，修改价格失败！');
            }
            $fields = array(
                'total'=>$total
            );
            if ($this->Orders_model->save($fields, array('id'=>$id,'store_id'=>$store_info['id']))) {
                $fields = array(
                    'add_time'=>time(),
                    'content'=>'修改价格成功',
                    'order_id'=>$id
                );
                $this->Orders_process_model->save($fields);
                printAjaxSuccess('success', '修改价格成功！');
            } else {
                printAjaxError('fail',"修改价格失败！");
            }
        }
    }

    //交易关闭
    public function seller_close_order(){
        //判断是否登录
        checkLoginAjax(true);
        $seller_group_info = $this->Seller_group_model->get('user_id',array('id' => get_cookie('seller_group_id')));
        $store_info = $this->Stores_model->get('id', array('user_id' => $seller_group_info['user_id']));
        if ($_POST) {
            $id = $this->input->post('id', TRUE);
            $cancelCause = $this->input->post('cancel_cause', TRUE);
            if (! $this->form_validation->required($cancelCause)) {
                printAjaxError('fail','请填写交易关闭的原因！');
            }
            $count = $this->Orders_model->rowCount(array('id'=>$id,'store_id'=>$store_info['id']));
            if (!$count) {
                printAjaxError('fail','此订单不存在，交易关闭失败！');
            }
            $fields = array(
                'cancel_cause'=>$cancelCause,
                'status'=>4
            );
            if ($this->Orders_model->save($fields, array('id'=>$id,'store_id'=>$store_info['id']))) {
                $fields = array(
                    'add_time'=>time(),
                    'content'=>'交易关闭',
                    'order_id'=>$id
                );
                $this->Orders_process_model->save($fields);
                printAjaxSuccess('success', '交易关闭成功！');
            } else {
                printAjaxError('fail',"交易关闭失败！");
            }
        }
    }

    //修改已付款
    public function seller_change_status_2(){
        //判断是否登录
        checkLoginAjax(true);

        $seller_group_info = $this->Seller_group_model->get('user_id',array('id' => get_cookie('seller_group_id')));
        $store_info = $this->Stores_model->get('id', array('user_id' => $seller_group_info['user_id']));
        if ($_POST) {
            $id = $this->input->post('id', TRUE);
            $remark = $this->input->post('remark', TRUE);
            if (!$remark) {
                printAjaxError('remark', '备注不能为空');
            }
            $ordersInfo = $this->Orders_model->get('user_id, total, order_number', array('id' => $id, 'store_id' => $store_info['id']));
            if (!$ordersInfo) {
                printAjaxError('fail', "操作异常！");
            }
            $fields = array(
                'status' => 1
            );
            if ($this->Orders_model->save($fields, array('id' => $id))) {
                //财务记录

                $userInfo = $this->User_model->getInfo('username', array('id' => $ordersInfo['user_id']));
                if (!$userInfo) {
                    printAjaxError('fail', "操作异常！");
                }
                $this->load->model('Financial_model', '', TRUE);
                $fFields = array(
                    'cause' => "付款成功--{$ordersInfo['order_number']}",
                    'price' => -$ordersInfo['total'],
                    'add_time' => time(),
                    'username' => $userInfo['username']
                );
                $this->Financial_model->save($fFields);
                //订单跟踪记录
                $fields = array(
                    'add_time' => time(),
                    'content' => "已付款状态修改成功[{$remark}]",
                    'order_id' => $id
                );
                $this->Orders_process_model->save($fields);
                printAjaxSuccess('success', '操作成功！');
            } else {
                printAjaxError('fail', "操作失败！");
            }
        }
    }

    //发货
    public function seller_delivery() {
        //判断是否登录
        checkLoginAjax(true);
        $seller_group_info = $this->Seller_group_model->get('user_id',array('id' => get_cookie('seller_group_id')));
        $store_info = $this->Stores_model->get('id', array('user_id' => $seller_group_info['user_id']));
        if ($_POST) {
            $id = $this->input->post('id', TRUE);
            $deliveryName = $this->input->post('delivery_name', TRUE);
            $expressNumber = $this->input->post('express_number', TRUE);
            $remark = $this->input->post('remark', TRUE);
            if (! $this->form_validation->required($deliveryName)) {
                printAjaxError('fail','快递名称不能为空！');
            }
            if (! $this->form_validation->required($expressNumber)) {
                printAjaxError('fail','快递单号不能为空！');
            }
            $item_info = $this->Orders_model->get('user_id',array('id'=>$id, 'store_id' => $store_info['id']));
            if (!$item_info) {
                printAjaxError('fail','此订单不存在，发货失败！');
            }
            $exchange_info = $this->Exchange_model->get('*', array('orders_id'=>$id, 'user_id'=>$item_info['user_id']));
            if ($exchange_info) {
                if ($exchange_info['status'] >= 3) {
                    printAjaxError('fail', "此订单退款申请成功，不能完成下面的操作");
                } else {
                    if ($exchange_info['status'] != 1) {
                        printAjaxError('fail', "此订单退款申请审核中，不能完成下面的操作");
                    }
                }
            }
            $fields = array(
                'delivery_name'=>$deliveryName,
                'express_number'=>$expressNumber,
                'status'=>2
            );
            if ($this->Orders_model->save($fields, array('id'=>$id))) {
                $fields = array(
                    'add_time'=>time(),
                    'content'=>"发货成功[{$remark}]",
                    'order_id'=>$id,
                    'change_status'=> 2
                );
                $this->Orders_process_model->save($fields);
                printAjaxSuccess('success', '发货成功！');
            } else {
                printAjaxError('fail',"发货失败！");
            }
        }

    }

    //确认收货
    public function seller_receiving() {
        //判断是否登录
        checkLoginAjax(true);
        $seller_group_info = $this->Seller_group_model->get('user_id',array('id' => get_cookie('seller_group_id')));
        $store_info = $this->Stores_model->get('id', array('user_id' => $seller_group_info['user_id']));
        if ($_POST) {
            $id = $this->input->post('id', TRUE);

            if (!$id) {
                printAjaxError('fail', "操作异常，刷新重试");
            }
            $ordersInfo = $this->Orders_model->get('id, user_id,status,score, order_number, divide_total, divide_store_price', array('id' =>$id, 'store_id' => $store_info['id']));
            if (!$ordersInfo) {
                printAjaxError('fail', "不存在此订单");
            }
            if ($ordersInfo['status'] != 2) {
                printAjaxError('fail', "此订单状态异常，确认收货失败");
            }
            $exchange_info = $this->Exchange_model->get('*', array('orders_id'=>$id, 'user_id'=>$ordersInfo['user_id']));
            if ($exchange_info) {
                if ($exchange_info['status'] >= 3) {
                    printAjaxError('fail', "此订单退款申请成功，不能完成下面的操作");
                } else {
                    if ($exchange_info['status'] != 1) {
                        printAjaxError('fail', "此订单退款申请审核中，不能完成下面的操作");
                    }
                }
            }
            $fields = array(
                'status'=>3
            );
            if ($this->Orders_model->save($fields, array('id'=>$id))) {
                //订单记录跟踪(只修改状态，扣钱，是下线交易的)
                $fields = array(
                    'add_time'=>time(),
                    'content'=>'确认收货，交易成功',
                    'order_id'=>$id,
                    'change_status'=> 3
                );
                $this->Orders_process_model->save($fields);
                //积分记录操作
                if ($ordersInfo && $ordersInfo['score']) {
                    $userInfo = $this->User_model->getInfo('id, username, score', array('id'=>$ordersInfo['user_id']));
                    if ($userInfo) {
                        if ($this->User_model->save(array('score'=>$ordersInfo['score'] + $userInfo['score']), array('id'=>$ordersInfo['user_id']))) {
                            $sFields = array(
                                'cause' =>   "订单交易成功--{$ordersInfo['order_number']}",
                                'score' =>   $ordersInfo['score'],
                                'balance'=>  $ordersInfo['score'] + $userInfo['score'],
                                'type'=>     'product_in',
                                'add_time'=> time(),
                                'username' =>$userInfo['username'],
                                'user_id'=>  $userInfo['id'],
                                'ret_id'=>   $ordersInfo['id']
                            );
                            $this->Score_model->save($sFields);
                        }
                    }
                }
                //减库存与加销售量
                $orderdetailInfo = $this->Orders_detail_model->get('product_id, buy_number', array('order_id'=>$id));
                if ($orderdetailInfo) {
                    $productInfo = $this->Product_model->get('stock, sales', array('id'=>$orderdetailInfo['product_id']));
                    $stock = 0;
                    if ($productInfo['stock'] - $orderdetailInfo['buy_number'] > 0) {
                        $stock = $productInfo['stock'] - $orderdetailInfo['buy_number'];
                    }
                    $pFields = array(
                        'stock'=>$stock,
                        'sales'=>$productInfo['sales']+$orderdetailInfo['buy_number']
                    );
                    $this->Product_model->save($pFields, array('id'=>$orderdetailInfo['product_id']));
                }
                printAjaxSuccess('', '操作成功！');
            } else {
                printAjaxError("操作失败！");
            }
        }
    }

    //物流管理
    public function my_get_postage_way_list() {
        //判断是否登录
        checkLogin(true);
        checkPermission('postage_way_index');
        $systemInfo = $this->System_model->get('*', array('id' => 1));
        $seller_group_info = $this->Seller_group_model->get('user_id',array('id' => get_cookie('seller_group_id')));
        $store_info = $this->Stores_model->get('id', array('user_id' => $seller_group_info['user_id']));
        $item_list = $this->Postage_way_model->gets('*',array('store_id'=>$store_info['id']));
		if ($item_list) {
		   foreach ($item_list as $key=>$postageway) {
		   	   $store_info = $this->Stores_model->get2('store_name', array('id'=>$postageway['store_id']));
		   	   if ($store_info) {
		   	       $item_list[$key]['store_name'] = $store_info['store_name'];
		   	   } else {
		   	       $item_list[$key]['store_name'] = '';
		   	   }
		       $item_list[$key]['postagepriceList'] = $this->Postage_price_model->gets('*', array('postage_way_id'=>$postageway['id']));
		   }
		}

        $data = array(
            'site_name' => $systemInfo['site_name'],
            'index_name' => $systemInfo['index_name'],
            
            'client_index' => $systemInfo['client_index'],
            'title' => '物流管理_商家中心' . $systemInfo['site_name'],
            'keywords' => $systemInfo['site_keywords'],
            'description' => $systemInfo['site_description'],
            'site_copyright' => $systemInfo['site_copyright'],
            'icp_code' => $systemInfo['icp_code'],
            'html' => $systemInfo['html'],
            'template' => $this->_template,
            'item_list' => $item_list,
            'measurement' => $this->_measurement
        );
        $layout = array(
            'content' => $this->load->view("{$this->_template}/my_get_postage_way_list", $data, TRUE)
        );
        $this->load->view('layout/seller_layout', $layout);
        //缓存
        if ($systemInfo['cache'] == 1) {
            $this->output->cache($systemInfo['cache_time']);
        }
    }

    //编辑物流
    public function my_save_postage_way($id = NULL) {
        //判断是否登录
        checkLogin(true);
        if ($id){
            checkPermission('postage_way_edit');
        }else{
            checkPermission('postage_way_add');
        }
        $systemInfo = $this->System_model->get('*', array('id' => 1));
        $seller_group_info = $this->Seller_group_model->get('user_id',array('id' => get_cookie('seller_group_id')));
        $store_info = $this->Stores_model->get('id', array('user_id' => $seller_group_info['user_id']));
        if ($_POST) {
            $title = $this->input->post('title', TRUE);
            $areaNames = $this->input->post('area_name');
            $start_val = $this->input->post('start_val', TRUE);
            $startPrices = $this->input->post('start_price');
            $add_val = $this->input->post('add_val', TRUE);
            $addPrices = $this->input->post('add_price');
            $payer = $this->input->post('payer', TRUE);
            $charging_mode = $this->input->post('charging_mode', TRUE);
            $province_id = $this->input->post('province_id', TRUE);
            $city_id = $this->input->post('city_id', TRUE);
            $area_id = $this->input->post('area_id', TRUE);
            $content = $this->input->post('content', TRUE);
            if (!$title) {
                printAjaxError('fail', '配送方式名称不能为空!');
            }
            if (!$payer) {
                printAjaxError('fail', '请选择是否包邮!');
            }
            if (!$charging_mode) {
                printAjaxError('fail', '请选择计价方式!');
            }
            if (!$province_id) {
                printAjaxError('fail', '请选择省');
            }
            if($payer == 1){
                    if(empty($start_val[0])){
                         printAjaxError('start_val', '首件不能为空');
                    }
                    if(empty($startPrices[0])){
                         printAjaxError('start_pric', '首费不能为空');
                    }
                    if(empty($add_val[0])){
                         printAjaxError('add_val', '续件不能为空');
                    }
                    if(empty($addPrices[0])){
                         printAjaxError('add_price', '续费不能为空');
                    }
            }
            $txt_address = '';
            $area_info = $this->Area_model->get('name', array('id' => $province_id));
            if ($area_info) {
                $txt_address .= $area_info['name'];
            }
            $area_info = $this->Area_model->get('name', array('id' => $city_id));
            if ($area_info) {
                $txt_address .= ' ' . $area_info['name'];
            }
            $area_info = $this->Area_model->get('name', array('id' => $area_id));
            if ($area_info) {
                $txt_address .= ' ' . $area_info['name'];
            }

            $fields = array(
                'title' => $title,
                'content' => $content,
                'payer' => $payer,
                'charging_mode' => $charging_mode,
                'province_id' => $province_id ? $province_id : 0,
                'city_id' => $city_id ? $city_id : 0,
                'area_id' => $area_id ? $area_id : 0,
                'txt_address' => $txt_address,
                'store_id' => $store_info['id']
            );
            $retId = $this->Postage_way_model->save($fields, $id ? array('id' => $id) : $id);
            if ($retId) {
                //修改时删除原来所有的
                if ($id) {
                    $this->Postage_price_model->delete(array('postage_way_id' => $id));
                }
                //添加数据
                if ($areaNames) {
                    foreach ($areaNames as $key => $areaName) {
                        $data = array(
                            'postage_way_id' => $id ? $id : $retId,
                            'area' => $payer==2 ? '全国' : $areaNames[$key],
                            'start_val' => $payer==2 ? 1 : $start_val[$key],
                            'start_price' => $startPrices[$key],
                            'add_val' => $payer==2 ? 1 : $add_val[$key],
                            'add_price' => $addPrices[$key]
                        );
                        $this->Postage_price_model->save($data);
                        if($payer==2){
                            break;
                        }
                    }
                }
                printAjaxSuccess(base_url() . 'index.php/seller/my_get_postage_way_list', '操作成功');
            } else {
                printAjaxError('fail', "操作失败！");
            }
        }
        $item_info = $this->Postage_way_model->get('*', array('id' => $id));
        if ($item_info) {
            $item_info['postagepriceList'] = $this->Postage_price_model->gets('*', array('postage_way_id' => $id));
        }
        $areaList = $this->Area_model->gets('*', array('parent_id' => 0));
        $data = array(
            'site_name' => $systemInfo['site_name'],
            'index_name' => $systemInfo['index_name'],
            
            'client_index' => $systemInfo['client_index'],
            'title' => '添加物流_商家中心' . $systemInfo['site_name'],
            'keywords' => $systemInfo['site_keywords'],
            'description' => $systemInfo['site_description'],
            'site_copyright' => $systemInfo['site_copyright'],
            'icp_code' => $systemInfo['icp_code'],
            'html' => $systemInfo['html'],
            'template' => $this->_template,
            'areaList' => $areaList,
            'item_info' => $item_info
        );
        $layout = array(
            'content' => $this->load->view("{$this->_template}/my_save_postage_way", $data, TRUE)
        );
        $this->load->view('layout/seller_layout', $layout);
        //缓存
        if ($systemInfo['cache'] == 1) {
            $this->output->cache($systemInfo['cache_time']);
        }
    }
     public function my_delete_postage_way() {
        checkLoginAjax(true);
        checkPermissionAjax('postage_way_delete');
        if ($_POST) {
            $seller_group_info = $this->Seller_group_model->get('user_id',array('id' => get_cookie('seller_group_id')));
            $store_info = $this->Stores_model->get('id', array('user_id' => $seller_group_info['user_id']));
            $id = $this->input->post('id', TRUE);
            if (!$this->form_validation->required($id)) {
                printAjaxError('id', 'id不能为空');
            }
            $result = $this->Postage_way_model->delete(array('id' => intval($id), 'store_id' => $store_info['id']));
            if ($result) {
                //同时删除关联项
	        $this->Postage_price_model->delete(array('postage_way_id'=>$id));
                printAjaxSuccess('success', '删除成功');
            } else {
                printAjaxError('fail', '删除失败');
            }
        }
    }


    public function my_get_exchange_list($clear = 0, $page = 0) {
        //判断是否登录
        checkLogin(true);
        checkPermission('exchange_index');
        $systemInfo = $this->System_model->get('*', array('id' => 1));
        $seller_group_info = $this->Seller_group_model->get('user_id',array('id' => get_cookie('seller_group_id')));
        $store_info = $this->Stores_model->get('id', array('user_id' => $seller_group_info['user_id']));
        if ($clear) {
            $clear = 0;
            $this->session->unset_userdata('search');
        }
        $condition = "store_id = {$store_info['id']}";
        $strWhere = $this->session->userdata('search') ? $this->session->userdata('search') : $condition;

        if ($_POST) {
            $strWhere = $condition;
            $order_num = $this->input->post('order_num');
            $username = $this->input->post('username');
            $status = $this->input->post('status');
            $startTime = $this->input->post('add_time_start');
            $endTime = $this->input->post('add_time_end');

            if (! empty($order_num) ) {
                $strWhere .= " and order_num = '{$order_num}' ";
            }
            if (! empty($username) ) {
                $strWhere .= " and username like '%".$username."%'";
            }
            if ($status != "") {
                $strWhere .= " and status = {$status} ";
            }
            if (! empty($startTime) && ! empty($endTime)) {
                $strWhere .= ' and add_time > '.strtotime($startTime.' 00:00:00').' and add_time < '.strtotime($endTime.' 23:59:59').' ';
            }
            $this->session->set_userdata('search', $strWhere);
        }

        //分页
        $this->config->load('pagination_config', TRUE);
        $paginationCount = $this->Exchange_model->rowCount($strWhere);
        $paginationConfig = $this->config->item('pagination_config');
        $paginationConfig['base_url'] = base_url()."index.php/{$this->_template}/my_get_exchange_list/{$clear}/";
        $paginationConfig['total_rows'] = $paginationCount;
        $paginationConfig['uri_segment'] = 4;
        $this->pagination->initialize($paginationConfig);
        $pagination = $this->pagination->create_links();

        $item_list = $this->Exchange_model->gets('*', $strWhere, $paginationConfig['per_page'], $page);

        $data = array(
            'site_name' => $systemInfo['site_name'],
            'index_name' => $systemInfo['index_name'],
            
            'client_index' => $systemInfo['client_index'],
            'title' => '退换货管理_商家中心' . $systemInfo['site_name'],
            'keywords' => $systemInfo['site_keywords'],
            'description' => $systemInfo['site_description'],
            'site_copyright' => $systemInfo['site_copyright'],
            'icp_code' => $systemInfo['icp_code'],
            'html' => $systemInfo['html'],
            'template' => $this->_template,
            'item_list'  =>$item_list,
            'pagination'=>$pagination,
            'paginationCount'=>$paginationCount,
            'pageCount'=>ceil($paginationCount/$paginationConfig['per_page']),
            'exchange_reason_arr'=>$this->_exchange_reason_arr,
            'exchange_status_arr'=>$this->_exchange_status_arr,
            'clear'=>$clear
        );

        $layout = array(
            'content'=>$this->load->view("{$this->_template}/my_get_exchange_list", $data, TRUE)
        );
        $this->load->view('layout/seller_layout', $layout);
        //缓存
        if ($systemInfo['cache'] == 1) {
            $this->output->cache($systemInfo['cache_time']);
        }
    }

    public function my_save_exchange($id = NULL) {
        //判断是否登录
        checkLogin(true);
        if ($id){
            checkPermission('exchange_edit');
        }else{
            checkPermission('exchange_add');
        }
        $systemInfo = $this->System_model->get('*', array('id' => 1));
        $item_info = $this->Exchange_model->get('*', array('id'=>$id));
        //凭证图片
        $attachment_list = NULL;
        if ($item_info && $item_info['batch_path_ids']) {
            $tmp_atm_ids = preg_replace(array('/^_/', '/_$/', '/_/'), array('', '', ','), $item_info['batch_path_ids']);
            $attachment_list = $this->Attachment_model->gets2($tmp_atm_ids);
        }
        $payment_title = '';
        if ($item_info) {
            $orders_info = $this->Orders_model->get('payment_id, payment_title, total, status', array('id'=>$item_info['orders_id']));
            if ($orders_info) {
                $payment_title = $orders_info['payment_title'];
            }
            $item_info['payment_title'] = $payment_title;
        }
        $orders_detail_info = $this->Orders_detail_model->get('*', array('id' => $item_info['orders_detail_id']));
        $orders_detail_info['price_total'] = number_format($orders_detail_info['buy_number']*$orders_detail_info['buy_price'], 2, '.', '');
        $data = array(
            'site_name' => $systemInfo['site_name'],
            'index_name' => $systemInfo['index_name'],
            
            'client_index' => $systemInfo['client_index'],
            'title' => '处理退换货申请_商家中心' . $systemInfo['site_name'],
            'keywords' => $systemInfo['site_keywords'],
            'description' => $systemInfo['site_description'],
            'site_copyright' => $systemInfo['site_copyright'],
            'icp_code' => $systemInfo['icp_code'],
            'html' => $systemInfo['html'],
            'template' => $this->_template,
            'item_info'=>$item_info,
            'orders_info'=>$orders_info,
            'orders_detail_info'=>$orders_detail_info,
            'exchange_status_arr'=>$this->_exchange_status_arr,
            'exchange_reason_arr'=>$this->_exchange_reason_arr,
            'status_arr'=>$this->_status,
            'attachment_list'=>$attachment_list,
        );
        $layout = array(
            'content'=>$this->load->view("{$this->_template}/my_save_exchange", $data, TRUE)
        );
        $this->load->view('layout/seller_layout', $layout);
        //缓存
        if ($systemInfo['cache'] == 1) {
            $this->output->cache($systemInfo['cache_time']);
        }
    }

    public function change_check() {
        checkPermissionAjax('exchange_edit');
        if($_POST) {
            $id = $this->input->post('id', TRUE);
            $status = $this->input->post('status', TRUE);
            $client_remark = $this->input->post('client_remark', TRUE);
            $admin_remark = $this->input->post('admin_remark', TRUE);

            if (!$id) {
                printAjaxError('fail', '操作异常');
            }
            $item_info = $this->Exchange_model->get('*', array('id'=>$id));
            if (!$item_info) {
                printAjaxError('fail', '此退款信息不存在');
            }
            if ($item_info['status'] != 0) {
                printAjaxError('fail', '此退款状态异常');
            }
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
                'update_time'=>time()
            );
            if (!$this->Exchange_model->save($fields, array('id'=>$item_info['id']))) {
                printAjaxError('fail', '操作失败');
            }
            printAjaxSuccess('success', '操作成功');
        }
    }

    //原路返回退款
    public function refund()
    {
        checkPermissionAjax("exchange_edit");
        if ($_POST){
            $id = $this->input->post('id',TRUE);
            if(!$id) {
                printAjaxError('fail', '操作异常');
            }
            $item_info = $this->Exchange_model->get('*', array('id'=>$id));
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
            $order_detail_count = $this->Orders_detail_model->rowCount(array('order_id'=>$item_info['orders_id']));
            $user_info = $this->User_model->get('id,total,username',array('user.id'=>$orders_info['user_id']));
            if (!$user_info) {
                printAjaxError('fail', '买家信息不存在，退款失败');
            }
            //预存款支付
            if ($orders_info['payment_id'] == 1) {
                $financial_info = $this->Financial_model->get(array('type'=>'order_out', 'ret_id'=>$orders_info['id'],'pay_way'=>1));
                if (!$financial_info) {
                    printAjaxError('fail', '支付记录不存在，退款失败');
                }
                if (!$this->User_model->save(array('total'=>$user_info['total'] + $item_info['price']), array('id'=>$orders_info['user_id']))) {
                    printAjaxError('fail', '退款失败');
                }
            }else{
                $pay_log_info = $this->Pay_log_model->get('id,trade_status,trade_no',array('out_trade_no'=>$orders_info['order_number'], 'order_type'=>'orders'));
                if (!$pay_log_info) {
                    printAjaxError('fail', '支付记录不存在，退款失败');
                }
                $orders_detail_info = $this->Orders_detail_model->get('id,is_refund,out_refund_no',array('id'=>$item_info['orders_detail_id']));
                if (!$orders_detail_info){
                    printAjaxError('fail', '操作异常！');
                }
                if ($orders_detail_info['is_refund']){
                    printAjaxError('fail', '订单已退款成功！');
                }
                if ($orders_detail_info['out_refund_no']){
                    $out_refund_no = $orders_detail_info['out_refund_no'];
                }else{
                    $out_refund_no = $this->_get_unique_out_refund_no($orders_info['order_number']);
                    $this->Orders_detail_model->save(array('out_refund_no'=>$out_refund_no),array('id'=>$item_info['orders_detail_id']));
                }
                if ($orders_info['payment_id'] == 3 ||$orders_info['payment_id'] == 5){
                    $is_xcx = $orders_info['payment_id'] == 5 ? 1 : 0;
                    require_once "sdk/weixin_pay/lib/WxPay.Api.php";
                    $pay_api = new WxPayApi();
                    $result = $pay_api->do_refund($pay_log_info['trade_no'],$orders_info['order_number'],$out_refund_no,$orders_info['total']*100,$item_info['price']*100,$is_xcx);
                    if ($result['return_code'] != 'SUCCESS' || $result['result_code'] != 'SUCCESS'){
                        printAjaxError('fail','退款失败');
                    }
                }elseif ($orders_info['payment_id'] == 7){
                    require_once "sdk/wxpayv3/WxPay.Api.php";
                    $pay_api = new WxPayApi();
                    $result = $pay_api->do_refund($pay_log_info['trade_no'],$orders_info['order_number'],$out_refund_no,$orders_info['total']*100,$item_info['price']*100);
                    if ($result['return_code'] != 'SUCCESS' || $result['result_code'] != 'SUCCESS'){
                        printAjaxError('fail','退款失败');
                    }
                }elseif ($orders_info['payment_id'] == 2 || $orders_info['payment_id'] == 6){
                    require_once "sdk/alipay/aop/AlipayService.php";
                    $pay_api = new AlipayService();
                    $param = array(
                        'out_trade_no'=>$orders_info['order_number'],
                        'trade_no'=>$pay_log_info['trade_no'],
                        'refund_amount'=>$item_info['price'],
                        'refund_reason'=>'退款'
                    );
                    $result = $pay_api->refund($param);
                    if (!$result){
                        printAjaxError('fail','退款失败');
                    }
                }

            }

            if ($order_detail_count == 1){
                //操作订单
                $fields = array(
                    'cancel_cause'=> '交易关闭-[买家申请退款成功]',
                    'status'=> 4
                );
                if ($this->Orders_model->save($fields, array('id' => $orders_info['id']))) {
                    $fields = array(
                        'add_time' => time(),
                        'content' => "交易关闭成功-[买家申请退款成功]",
                        'order_id' => $orders_info['id'],
                        'order_status'=>$orders_info['status'],
                        'change_status'=>4
                    );
                    $this->Orders_process_model->save($fields);
                }
            }

            //操作退款申请状态
            if ($this->Exchange_model->save(array('status'=>4, 'update_time'=>time()), array('id'=>$item_info['id']))){
                $this->Orders_detail_model->save(array('is_refund'=>1, 'refund_time'=>time()), array('id'=>$item_info['orders_detail_id']));
                $fields = array(
                    'cause'=>"退款成功-[订单号：{$orders_info['order_number']}]",
                    'price'=>$item_info['price'],
                    'balance'=>$orders_info['payment_id'] == 1 ? $user_info['total']+$item_info['price'] : $user_info['total'],
                    'add_time'=>time(),
                    'user_id'=>$user_info['id'],
                    'username'=>$user_info['username'],
                    'type' =>  'order_in',
                    'pay_way'=>$orders_info['payment_id'],
                    'ret_id'=>$item_info['orders_detail_id'],
                    'from_user_id'=>$user_info['id']
                );
                $this->Financial_model->save($fields);
                printAjaxSuccess('success', '退款成功');
            }
        }
        printAjaxError('fail', '操作异常！');

    }

    /**
     * 退款至余额--弃用
     */
    public function refund_to_balance() {
        if ($_POST) {
            $id = $this->input->post('id', TRUE);
            if(!$id) {
                printAjaxError('fail', '操作异常');
            }
            $item_info = $this->Exchange_model->get('*', array('id'=>$id));
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
            //预存款支付
            if ($orders_info['payment_id'] == 1) {
                $financial_info = $this->Financial_model->get(array('type'=>'order_out', 'ret_id'=>$orders_info['id'],'pay_way'=>1));
                if (!$financial_info) {
                    printAjaxError('fail', '支付记录不存在，退款失败');
                }
                $user_info = $this->User_model->get('*',array('user.id'=>$orders_info['user_id']));
                if (!$user_info) {
                    printAjaxError('fail', '买家信息不存在，退款失败');
                }
                $this->_balance_trade_refund(NULL, $item_info, $orders_info, $user_info, 3);
            }
            //支付宝
            else if ($orders_info['payment_id'] == 2 || $orders_info['payment_id'] == 6) {
                $pay_log_info = $this->Pay_log_model->get('*',array('pay_log.out_trade_no'=>$orders_info['order_number'], 'pay_log.payment_type'=>'alipay', 'pay_log.order_type'=>'orders'));
                if (!$pay_log_info) {
                    printAjaxError('fail', '支付记录不存在，退款失败');
                }
                if ($pay_log_info['trade_status'] != 'TRADE_SUCCESS' && $pay_log_info['trade_status'] != 'TRADE_FINISHED') {
                    printAjaxError('fail', '订单未付款，退款失败');
                }
                $user_info = $this->User_model->get('*',array('user.id'=>$orders_info['user_id']));
                if (!$user_info) {
                    printAjaxError('fail', '买家信息不存在，退款失败');
                }
                $this->_balance_trade_refund(NULL, $item_info, $orders_info, $user_info, 3);
            }
            //微信
            else if ($orders_info['payment_id'] == 3 || $orders_info['payment_id'] == 5 || $orders_info['payment_id'] == 7) {
                $pay_log_info = $this->Pay_log_model->get('*',array('pay_log.out_trade_no'=>$orders_info['order_number'], 'pay_log.payment_type'=>'weixin', 'pay_log.order_type'=>'orders'));
                if (!$pay_log_info) {
                    printAjaxError('fail', '支付记录不存在，退款失败');
                }
                if ($pay_log_info['trade_status'] != 'TRADE_SUCCESS' && $pay_log_info['trade_status'] != 'TRADE_FINISHED') {
                    printAjaxError('fail', '订单未付款，退款失败');
                }
                $user_info = $this->User_model->get('*',array('user.id'=>$orders_info['user_id']));
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

    public function my_get_comment_list($clear = 1, $page = 0) {
        //判断是否登录
        checkLogin(true);
        checkPermission('comment_index');
        $systemInfo = $this->System_model->get('*', array('id' => 1));
        $seller_group_info = $this->Seller_group_model->get('user_id',array('id' => get_cookie('seller_group_id')));
        $store_info = $this->Stores_model->get('id', array('user_id' => $seller_group_info['user_id']));
        if ($clear) {
            $clear = 0;
            $this->session->unset_userdata('search');
        }
        $condition = "store_id = {$store_info['id']}";
        $strWhere = $this->session->userdata('search') ? $this->session->userdata('search') : $condition;

        if ($_POST) {
            $strWhere = $condition;
            $order_num = $this->input->post('order_num');
            $username = $this->input->post('username');
            $evaluate = $this->input->post('evaluate');
            $is_reply = $this->input->post('is_reply');
            $startTime = $this->input->post('add_time_start');
            $endTime = $this->input->post('add_time_end');

            if (! empty($order_num) ) {
                $strWhere .= " and order_number = '{$order_num}' ";
            }
            if (! empty($username) ) {
                $strWhere .= " and username REGEXP  '$username'";
            }
            if ($is_reply != "") {
                $strWhere .= " and is_reply = {$is_reply} ";
            }
            if ($evaluate != "") {
                $strWhere .= " and evaluate = {$evaluate} ";
            }
            if (! empty($startTime) && ! empty($endTime)) {
                $strWhere .= ' and add_time > '.strtotime($startTime.' 00:00:00').' and add_time < '.strtotime($endTime.' 23:59:59').' ';
            }
            $this->session->set_userdata('search', $strWhere);
        }

        //分页
        $this->config->load('pagination_config', TRUE);
        $paginationCount = $this->Comment_model->rowCount($strWhere);
        $paginationConfig = $this->config->item('pagination_config');
        $paginationConfig['base_url'] = base_url()."index.php/{$this->_template}/my_get_comment_list/{$clear}/";
        $paginationConfig['total_rows'] = $paginationCount;
        $paginationConfig['uri_segment'] = 4;
        $this->pagination->initialize($paginationConfig);
        $pagination = $this->pagination->create_links();

        $item_list = $this->Comment_model->gets('*', $strWhere, $paginationConfig['per_page'], $page);

        $data = array(
            'site_name' => $systemInfo['site_name'],
            'index_name' => $systemInfo['index_name'],
            
            'client_index' => $systemInfo['client_index'],
            'title' => '评价管理_商家中心' . $systemInfo['site_name'],
            'keywords' => $systemInfo['site_keywords'],
            'description' => $systemInfo['site_description'],
            'site_copyright' => $systemInfo['site_copyright'],
            'icp_code' => $systemInfo['icp_code'],
            'html' => $systemInfo['html'],
            'template' => $this->_template,
            'item_list'  =>$item_list,
            'pagination'=>$pagination,
            'paginationCount'=>$paginationCount,
            'pageCount'=>ceil($paginationCount/$paginationConfig['per_page']),
            'comment_status_arr'=>$this->_comment_status_arr,
            'evaluate_arr'=>$this->_evaluate_arr,
            'clear'=>$clear
        );

        $layout = array(
            'content'=>$this->load->view("{$this->_template}/my_get_comment_list", $data, TRUE)
        );
        $this->load->view('layout/seller_layout', $layout);
        //缓存
        if ($systemInfo['cache'] == 1) {
            $this->output->cache($systemInfo['cache_time']);
        }
    }

    public function my_save_comment($comment_id = NULL){
        //判断是否登录
        checkLogin();
        if ($comment_id){
            checkPermission('comment_edit');
        }else{
            checkPermission('comment_add');
        }
        $systemInfo = $this->System_model->get('*', array('id' => 1));
        $seller_group_info = $this->Seller_group_model->get('user_id',array('id' => get_cookie('seller_group_id')));
        $store_info = $this->Stores_model->get('id', array('user_id' => $seller_group_info['user_id']));
        $comment_id = intval($comment_id);
        $item_info = $this->Comment_model->get('*',array('id'=>$comment_id));
        $order_info = $this->Orders_model->get('id', array('order_number' => $item_info['order_number']));
        $comment_store_info = $this->Comment_Stores_model->get('*', array('order_id' => $order_info['id'], 'store_id' => $store_info['id']));
        $attachment_list = NULL;
        if($item_info['batch_path_ids']){
            $tmp_atm_ids = preg_replace(array('/^_/', '/_$/', '/_/'), array('', '', ','), $item_info['batch_path_ids']);
            $attachment_list = $this->Attachment_model->gets2($tmp_atm_ids);
        }
        $store_reply_info = $this->Store_reply_comment_model->get('*', array('comment_id'=>$item_info['id']));
        if($_POST){
            $evaluate = $this->input->post('evaluate');
            $content = $this->input->post('content');
            if (empty($evaluate)){
                printAjaxError('evaluate', '请对用户进行评价');
            }
            $datas = array(
                'comment_id'=>$item_info['id'],
                'order_id'=>$order_info['id'],
                'user_id'=>$item_info['user_id'],
                'product_id'=>$item_info['product_id'],
                'store_id'=>$store_info['id'],
                'add_time'=>time(),
                'evaluate'=>$evaluate,
                'content'=>$content,
            );
            if($this->Store_reply_comment_model->save($datas)){
                $res1 = $this->Comment_model->save(array('is_reply'=>1), array('id'=>$item_info['id']));
                if(!$res1){
                    printAjaxError('', '操作异常');
                }
                $user_info = $this->User_model->get('evaluate_a,evaluate_b,evaluate_c',array('id'=>$item_info['user_id']));
                if($evaluate == 1){
                    $this->User_model->save(array('evaluate_a'=>$user_info['evaluate_a']+1), array('id'=>$item_info['user_id']));
                }
                if($evaluate == 1){
                    $this->User_model->save(array('evaluate_b'=>$user_info['evaluate_b']+1), array('id'=>$item_info['user_id']));
                }
                if($evaluate == 1){
                    $this->User_model->save(array('evaluate_c'=>$user_info['evaluate_c']+1), array('id'=>$item_info['user_id']));
                }
                $prfUrl = base_url() . "index.php/seller/my_save_comment/{$comment_id}.html";
                printAjaxSuccess($prfUrl, '回复成功');
            }
            printAjaxError('fail', '回复失败');
        }
        $data = array(
            'site_name' => $systemInfo['site_name'],
            'index_name' => $systemInfo['index_name'],
            
            'client_index' => $systemInfo['client_index'],
            'title' => '评价处理_商家中心' . $systemInfo['site_name'],
            'keywords' => $systemInfo['site_keywords'],
            'description' => $systemInfo['site_description'],
            'site_copyright' => $systemInfo['site_copyright'],
            'icp_code' => $systemInfo['icp_code'],
            'html' => $systemInfo['html'],
            'template' => $this->_template,
            'item_info' => $item_info,
            'comment_store_info' => $comment_store_info,
            'attachment_list' => $attachment_list,
            'store_reply_info' => $store_reply_info,
        );
        $layout = array(
            'content' => $this->load->view("{$this->_template}/my_save_comment", $data, TRUE)
        );
        $this->load->view('layout/seller_layout', $layout);
        //缓存
        if ($systemInfo['cache'] == 1) {
            $this->output->cache($systemInfo['cache_time']);
        }
    }

    //部门设置
    public function my_seller_group_list() {

        checkLogin(true);
        //判断是否权限
        checkPermission("seller_group_index");

        $systemInfo = $this->System_model->get('*', array('id' => 1));
        $seller_group_info = $this->Seller_group_model->get('user_id',array('id' => get_cookie('seller_group_id')));
        $item_list = $this->Seller_group_model->gets('*',array('user_id' => $seller_group_info['user_id']));
        $data = array(
            'site_name' => $systemInfo['site_name'],
            'index_name' => $systemInfo['index_name'],
            
            'client_index' => $systemInfo['client_index'],
            'title' => '部门设置_商家中心' . $systemInfo['site_name'],
            'keywords' => $systemInfo['site_keywords'],
            'description' => $systemInfo['site_description'],
            'site_copyright' => $systemInfo['site_copyright'],
            'icp_code' => $systemInfo['icp_code'],
            'html' => $systemInfo['html'],
            'template' => $this->_template,
            'item_list' => $item_list
        );
        $layout = array(
            'content' => $this->load->view("{$this->_template}/my_seller_group_list", $data, TRUE)
        );
        $this->load->view('layout/seller_layout', $layout);
        //缓存
        if ($systemInfo['cache'] == 1) {
            $this->output->cache($systemInfo['cache_time']);
        }
    }

    //编辑部门
    public function my_save_seller_group($id = NULL) {
        checkLogin(true);
        if ($id) {
            checkPermission("seller_group_edit");
        } else {
            checkPermission("seller_group_add");
        }
        $systemInfo = $this->System_model->get('*', array('id' => 1));
        $item_info = $this->Seller_group_model->get('*', array('id' => $id));
        if ($_POST) {
            $seller_group_info = $this->Seller_group_model->get('user_id',array('id' => get_cookie('seller_group_id')));
            $fields = array(
                'group_name'=>$this->input->post('group_name', TRUE),
                'permissions'=>$this->input->post('permissions', TRUE),
                'user_id'=>$seller_group_info['user_id']
            );
            if ($this->Seller_group_model->save($fields, $id?array('id'=>$id):$id)) {
                $prfUrl = getBaseUrl(false, '', 'seller/my_seller_group_list.html', $systemInfo['client_index']);
                printAjaxSuccess($prfUrl, '操作成功');
            } else {
                printAjaxError('fail', "操作失败！");
            }


        }
        $data = array(
            'site_name' => $systemInfo['site_name'],
            'index_name' => $systemInfo['index_name'],
            
            'client_index' => $systemInfo['client_index'],
            'title' => '编辑部门_商家中心' . $systemInfo['site_name'],
            'keywords' => $systemInfo['site_keywords'],
            'description' => $systemInfo['site_description'],
            'site_copyright' => $systemInfo['site_copyright'],
            'icp_code' => $systemInfo['icp_code'],
            'html' => $systemInfo['html'],
            'template' => $this->_template,
            'item_info' => $item_info,
        );
        $layout = array(
            'content' => $this->load->view("{$this->_template}/my_save_seller_group", $data, TRUE)
        );
        $this->load->view('layout/seller_layout', $layout);
        //缓存
        if ($systemInfo['cache'] == 1) {
            $this->output->cache($systemInfo['cache_time']);
        }
    }

    //删除部门
    public function my_delete_seller_group() {
        checkLoginAjax(true);
        checkPermissionAjax('seller_group_delete');
        if ($_POST) {

            $id = intval($this->input->post('id', TRUE));
            if (!$this->form_validation->required($id)) {
                printAjaxError('title', 'id不能为空');
            }
            $seller_group_info = $this->Seller_group_model->get('user_id',array('id' => get_cookie('seller_group_id')));
            $user_info = $this->User_model->get('seller_group_id', array('id' => $seller_group_info['user_id']));
            if ($user_info['seller_group_id'] == $id){
                printAjaxError('fail','超级管理员不能删除！');
            }
            $result = $this->Seller_group_model->delete(array('id' => $id));
            if ($result) {
                $this->User_model->delete("seller_group_id = {$id} and id <> {$seller_group_info['user_id']}");
                printAjaxSuccess('success', '删除成功');
            } else {
                printAjaxError('fail', '删除失败');
            }
        }
    }

    //账号管理
    public function my_get_seller_list($clear = 0, $page = 0) {
        //判断是否登录
        checkLogin(true);
        checkPermission('user_index');
        $systemInfo = $this->System_model->get('*', array('id' => 1));
        $seller_group_info = $this->Seller_group_model->get('user_id',array('id' => get_cookie('seller_group_id')));
        $seller_group_list = $this->Seller_group_model->gets('id,group_name',array('user_id'=>$seller_group_info['user_id']));
        $ids = '';
        $group_name_arr = array();
        foreach ($seller_group_list as $seller){
            $ids .= $seller['id'].',';
            $group_name_arr[$seller['id']] = $seller['group_name'];
        }
        $ids = substr($ids, 0,-1);

        if (!$clear) {
            $clear = 1;
            $this->session->unset_userdata('search');
        }
        $condition = "seller_group_id in ({$ids})";
        $strWhere = $this->session->userdata('search') ? $this->session->userdata('search') : $condition;
        if($_POST){
            $strWhere = $condition;

            $username = $this->input->post('username');
            $seller_group = $this->input->post('seller_group');
            $startTime = $this->input->post('add_time_start');
            $endTime = $this->input->post('add_time_end');


            if (! empty($username) ) {
                $strWhere .= " and username like '%".$username."%'";
            }
            if (! empty($seller_group) ) {
                $strWhere .= " and seller_group_id = $seller_group";
            }

            if (! empty($startTime) && ! empty($endTime)) {
                $strWhere .= ' and add_time > '.strtotime($startTime.' 00:00:00').' and add_time < '.strtotime($endTime.' 23:59:59').' ';
            }
            $this->session->set_userdata('search', $strWhere);
        }
        //分页
        $paginationCount = $this->User_model->count($strWhere);
        $this->config->load('pagination_config', TRUE);
        $paginationConfig = $this->config->item('pagination_config');
        $paginationConfig['base_url'] = base_url() . "index.php/seller/my_get_seller_list/{$clear}/";
        $paginationConfig['total_rows'] = $paginationCount;
        $paginationConfig['uri_segment'] = 4;
        $paginationConfig['per_page'] = 10;
        $this->pagination->initialize($paginationConfig);
        $pagination = $this->pagination->create_links();
        $item_list = $this->User_model->gets('*', $strWhere, $paginationConfig['per_page'], $page);

        $data = array(
            'site_name' => $systemInfo['site_name'],
            'index_name' => $systemInfo['index_name'],
            
            'client_index' => $systemInfo['client_index'],
            'title' => '账号管理_商家中心' . $systemInfo['site_name'],
            'keywords' => $systemInfo['site_keywords'],
            'description' => $systemInfo['site_description'],
            'site_copyright' => $systemInfo['site_copyright'],
            'icp_code' => $systemInfo['icp_code'],
            'html' => $systemInfo['html'],
            'template' => $this->_template,
            'item_list' => $item_list,
            'pagination' => $pagination,
            'group_name_arr' => $group_name_arr,

        );
        $layout = array(
            'content' => $this->load->view("{$this->_template}/my_get_seller_list", $data, TRUE)
        );
        $this->load->view('layout/seller_layout', $layout);
        //缓存
        if ($systemInfo['cache'] == 1) {
            $this->output->cache($systemInfo['cache_time']);
        }
    }

    //编辑账号
    public function my_save_seller($id = NULL) {
        checkLogin(true);
        if ($id) {
            checkPermission("user_edit");
        } else {
            checkPermission("user_add");
        }
        $seller_group_info = $this->Seller_group_model->get('user_id',array('id' => get_cookie('seller_group_id')));
        $seller_group_list = $this->Seller_group_model->gets('id,group_name',array('user_id'=>$seller_group_info['user_id']));
        $group_name_arr = array();
        foreach ($seller_group_list as $seller){
            $group_name_arr[$seller['id']] = $seller['group_name'];
        }
        $systemInfo = $this->System_model->get('*', array('id' => 1));
        $item_info = $this->User_model->get('*', array('id' => $id));
        if ($_POST) {
            $username = $this->input->post('username', TRUE);
            $password = $this->input->post('password', TRUE);
            $ref_password = $this->input->post('ref_password', TRUE);
            $seller_group_id = $this->input->post('seller_group_id', TRUE);

            if (!$username){
                printAjaxError('fail','请填写用户名');
            }
            if (!$id){
                if (!$password){
                    printAjaxError('fail','请填写密码');
                }
                if ($password != $ref_password){
                    printAjaxError('fail','密码不一致');
                }
            }

            if (!$seller_group_id){
                printAjaxError('fail','请选择部门');
            }
            $addTime = time();
            $password = $this->createPasswordSALT($username, $addTime, $password);
            $fields = array(
                'username'=>      $username,
                'password'=>      $password,
                'user_group_id'=>      1,
                'seller_group_id'=>      $seller_group_id,
                'nickname'=>      $this->input->post('nickname', TRUE),
                'real_name'=>     $this->input->post('real_name', TRUE),
                'mobile'=>         $this->input->post('mobile', TRUE),
                'add_time' => $addTime
            );

            if (empty($id)) {
                if ($this->User_model->validateUnique($username)) {
                    printAjaxError('fail', "用户名已经存在，请换个用户名！");
                }
            }
            if ($this->User_model->save($fields, $id?array('id'=>$id):$id)) {
                $prfUrl = getBaseUrl(false, '', 'seller/my_get_seller_list.html', $systemInfo['client_index']);
                printAjaxSuccess($prfUrl, '操作成功');
            } else {
                printAjaxError('fail', "操作失败！");
            }


        }
        $data = array(
            'site_name' => $systemInfo['site_name'],
            'index_name' => $systemInfo['index_name'],
            
            'client_index' => $systemInfo['client_index'],
            'title' => '编辑账号_商家中心' . $systemInfo['site_name'],
            'keywords' => $systemInfo['site_keywords'],
            'description' => $systemInfo['site_description'],
            'site_copyright' => $systemInfo['site_copyright'],
            'icp_code' => $systemInfo['icp_code'],
            'html' => $systemInfo['html'],
            'template' => $this->_template,
            'item_info' => $item_info,
            'group_name_arr' => $group_name_arr,
        );
        $layout = array(
            'content' => $this->load->view("{$this->_template}/my_save_seller", $data, TRUE)
        );
        $this->load->view('layout/seller_layout', $layout);
        //缓存
        if ($systemInfo['cache'] == 1) {
            $this->output->cache($systemInfo['cache_time']);
        }
    }

    //删除账号
    public function my_delete_seller() {
        checkLoginAjax(true);
        checkPermissionAjax('user_delete');
        if ($_POST) {

            $id = intval($this->input->post('id', TRUE));
            if (!$this->form_validation->required($id)) {
                printAjaxError('title', 'id不能为空');
            }
            $seller_group_info = $this->Seller_group_model->get('user_id',array('id' => get_cookie('seller_group_id')));
            if ($id == $seller_group_info['user_id']){
                printAjaxError('fail','店铺主账号不能删除');
            }

            $result = $this->User_model->delete("id = {$id} and id <> {$seller_group_info['user_id']}");
            if ($result) {
                printAjaxSuccess('success', '删除成功');
            } else {
                printAjaxError('fail', '删除失败');
            }
        }
    }

    //团预购
    public function promotion_ptkj_list($clear = '1', $page = 0)
    {
        //判断是否登录
        checkLogin(true);
        checkPermission("groupon_index");
        $systemInfo = $this->System_model->get('*', array('id' => 1));
        $seller_group_info = $this->Seller_group_model->get('user_id',array('id' => get_cookie('seller_group_id')));
        $store_info = $this->Stores_model->get('id', array('user_id' => $seller_group_info['user_id']));
        if ($clear) {
            $clear = 0;
            $this->session->unset_userdata('search');
        }
        $condition = "promotion_ptkj.store_id = {$store_info['id']}";
        $strWhere = $this->session->userdata('search') ? $this->session->userdata('search') : $condition;
        //分页
        $this->config->load('pagination_config', TRUE);
        $paginationConfig = $this->config->item('pagination_config');
        $paginationCount = $this->Promotion_ptkj_model->rowCount($strWhere);
        $paginationConfig['base_url'] = base_url()."admincp.php/{$this->_template}/promotion_ptkj_list/{$clear}/";
        $paginationConfig['total_rows'] = $paginationCount;
        $paginationConfig['uri_segment'] = 4;
        $this->pagination->initialize($paginationConfig);
        $pagination = $this->pagination->create_links();

        $item_list = $this->Promotion_ptkj_model->gets($strWhere, $paginationConfig['per_page'], $page);
        if ($item_list){
            foreach ($item_list as $key=>$value){
                $is_can_refund = 0;
                if (!$value['is_refund']){
                    if ($value['end_time'] < time() && $value['is_success']==0 && $value['pintuan_people'] < $value['min_number']){
                        $is_can_refund = 1;
                    }else{
                        //是否逾期
                        $success_time = $value['is_success'] ? $value['success_time'] : $value['end_time'];
                        if (strtotime('+24hours',$success_time) < time()){
                            $is_can_refund = 2;
                        }
                    }
                }
                $item_list[$key]['is_can_refund'] = $is_can_refund;
            }
        }

        $data = array(
            'site_name' => $systemInfo['site_name'],
            'index_name' => $systemInfo['index_name'],
            
            'client_index' => $systemInfo['client_index'],
            'title' => '团预购活动管理_商家中心' . $systemInfo['site_name'],
            'keywords' => $systemInfo['site_keywords'],
            'description' => $systemInfo['site_description'],
            'site_copyright' => $systemInfo['site_copyright'],
            'icp_code' => $systemInfo['icp_code'],
            'html' => $systemInfo['html'],
            'template' => $this->_template,
            'check_status_arr' => $this->_check_status_arr,
            'item_list' => $item_list,
            'pagination' => $pagination,
        );
        $layout = array(
            'content' => $this->load->view("{$this->_template}/promotion_ptkj_list", $data, TRUE)
        );
        $this->load->view('layout/seller_layout', $layout);
        //缓存
        if ($systemInfo['cache'] == 1) {
            $this->output->cache($systemInfo['cache_time']);
        }
    }

    public function promotion_ptkj_save($id = NULL)
    {
        //判断是否登录
        checkLogin(true);
        if ($id) {
            checkPermission("groupon_edit");
        } else {
            checkPermission("groupon_add");
        }
        $prfUrl = base_url()."index.php/{$this->_template}/promotion_ptkj_list/1";
        $systemInfo = $this->System_model->get('*', array('id' => 1));
        $seller_group_info = $this->Seller_group_model->get('user_id',array('id' => get_cookie('seller_group_id')));
        $store_info = $this->Stores_model->get('id', array('user_id' => $seller_group_info['user_id']));
        $productInfo = array();
        $pintuan_arr = array();
        if ($id) {
            $itemInfo = $this->Promotion_ptkj_model->get('*', array('id' => $id));
            $productInfo = $this->Product_model->get('*',array('product.id' => $itemInfo['product_id']));
            $pintuan_arr = $this->Pintuan_model->gets(array('ptkj_id' => $id));
        } else {
            $itemInfo = array();
        }
        if ($_POST) {
            $product_id = $this->input->post('product_id', true);
            $type = $this->input->post('type', true);
            $sale_price = $this->input->post('sale_price', true);
            $deposit = $this->input->post('deposit', true);
            $min_number = $this->input->post('min_number', true);
            $max_number = $this->input->post('max_number', true);
            $start_time = strtotime($this->input->post('start_time', true));
            $end_time = strtotime($this->input->post('end_time', true).' 23:59:59');
//            $is_open = $this->input->post('is_open', true);
            if ($end_time <= $start_time) {
                printAjaxError('error', "团预购活动结束时间必须大于团预购活动开始时间");
            }
            if ($start_time < time()) {
                printAjaxError('fail', "团预购活动开始时间必须大于当前时间");
            }
            if ($end_time < time()) {
                printAjaxError('error', "团预购活动结束时间必须大于当前时间");
            }
            if (!empty($id) && time() > $itemInfo['start_time'] && time() < $itemInfo['end_time']) {
                $count = $this->Ptkj_record_model->rowCount(array('ptkj_id' => $id, 'item_type' => 0));
                if ($count > 0) {
                    printAjaxError('error', "活动正在进行,不可修改！");
                }
            }

            $tmp_data = $this->Promotion_ptkj_model->get('*', array('product_id' => $product_id,'id <>'=>$id));
            if (!empty($tmp_data) && time() < $tmp_data['end_time'] && !$tmp_data['is_success']) {
                printAjaxError('error', '该商品团预购活动未结束');
            }

            $productInfo = $this->Product_model->get('*',array('product.id' => $product_id));
            $fields = array(
                'product_id' => $product_id,
                'type' => $type,
                'sale_price' => $sale_price,
                'deposit' => $deposit,
                'min_number' => $min_number ? $min_number : 0,
                'max_number' => $max_number ? $max_number : 0,
                'start_time' => $start_time,
                'end_time' => $end_time,
//                'is_open' => $is_open,
                'add_time' => time(),
//                'path' => $productInfo['path'],
                'store_id' => $store_info['id']
            );
            $retId = $this->Promotion_ptkj_model->save($fields, $id ? array('id' => $id) : $id);

            if ($retId) {
                if ($type == 0){
                    $retId = $id ? $id : $retId;
                    $this->Pintuan_model->delete(array('ptkj_id' => $retId));
                    $low_arr = $this->input->post('low', TRUE);
                    $high_arr = $this->input->post('high', TRUE);
                    $money_arr = $this->input->post('money', TRUE);
                    if (empty($low_arr)) {
                        printAjaxError('error', "团预购规则不能为空!");
                    }
                    $low_str = implode(',', $low_arr);
                    $high_str = implode(',', $high_arr);
                    $money_str = implode(',', $money_arr);
                    $high_price = 0;
                    $low_price = 0;
                    if (!empty($low_str) && !empty($high_str) && !empty($money_str)) {
                        foreach ($low_arr as $key => $ls) {
                            if (!is_numeric($ls) || !is_numeric($high_arr[$key]) || empty($money_arr[$key])) {
                                printAjaxError('error', "团预购规则有一项为空!");
                            }
                            $high_price = max($high_price,$money_arr[$key]);
                            $low_price = min($low_price,$money_arr[$key]);
                            $min_number = min($min_number,$ls);
                            $max_number = max($max_number,$high_arr[$key]);
                            $fields_data = array(
                                'low' => $ls,
                                'high' => $high_arr[$key],
                                'money' => $money_arr[$key],
                                'ptkj_id' => $retId,
                                'add_time' => time(),
                            );
                            $this->Pintuan_model->save($fields_data);
                        }
                    } else {
                        printAjaxError('error', "团预购规则有一项为空!");
                    }
                    if (!$this->Promotion_ptkj_model->save(array('high_price'=>$high_price,'low_price'=>$low_price,'min_number'=>$min_number,'max_number'=>$max_number), array('id' => $retId))){
                        $this->Promotion_ptkj_model->delete(array('id' => $retId));
                        $this->Pintuan_model->delete(array('ptkj_id' => $retId));
                        printAjaxError('fail', "申请失败");
                    }
                }

                printAjaxSuccess($prfUrl, "保存成功");
            } else {
                printAjaxError('fail', "保存失败");
            }
        }

        $data = array(
            'site_name' => $systemInfo['site_name'],
            'index_name' => $systemInfo['index_name'],
            
            'client_index' => $systemInfo['client_index'],
            'title' => '添加团预购活动_商家中心' . $systemInfo['site_name'],
            'keywords' => $systemInfo['site_keywords'],
            'description' => $systemInfo['site_description'],
            'site_copyright' => $systemInfo['site_copyright'],
            'icp_code' => $systemInfo['icp_code'],
            'html' => $systemInfo['html'],
            'template' => $this->_template,
            'itemInfo' => $itemInfo,
            'productInfo' => $productInfo,
            'pintuan_arr' => $pintuan_arr,
        );
        $layout = array(
            'content' => $this->load->view("{$this->_template}/promotion_ptkj_save", $data, TRUE)
        );
        $this->load->view('layout/seller_layout', $layout);
        //缓存
        if ($systemInfo['cache'] == 1) {
            $this->output->cache($systemInfo['cache_time']);
        }
    }


    public function promotion_ptkj_view($id = NULL)
    {
        //判断是否登录
        checkLogin(true);
        if ($id) {
            checkPermission("groupon_edit");
        } else {
            checkPermission("groupon_add");
        }
        $prfUrl = base_url()."index.php/{$this->_template}/promotion_ptkj_list/1";
        $systemInfo = $this->System_model->get('*', array('id' => 1));
        $seller_group_info = $this->Seller_group_model->get('user_id',array('id' => get_cookie('seller_group_id')));
        $store_info = $this->Stores_model->get('id', array('user_id' => $seller_group_info['user_id']));
        $productInfo = array();
        $pintuan_arr = array();
        if ($id) {
            $itemInfo = $this->Promotion_ptkj_model->get('*', array('id' => $id));
            $productInfo = $this->Product_model->get('*',array('product.id' => $itemInfo['product_id']));
            $pintuan_arr = $this->Pintuan_model->gets(array('ptkj_id' => $id));
        } else {
            $itemInfo = array();
        }


        $data = array(
            'site_name' => $systemInfo['site_name'],
            'index_name' => $systemInfo['index_name'],
            
            'client_index' => $systemInfo['client_index'],
            'title' => '查看团预购活动_商家中心' . $systemInfo['site_name'],
            'keywords' => $systemInfo['site_keywords'],
            'description' => $systemInfo['site_description'],
            'site_copyright' => $systemInfo['site_copyright'],
            'icp_code' => $systemInfo['icp_code'],
            'html' => $systemInfo['html'],
            'template' => $this->_template,
            'itemInfo' => $itemInfo,
            'productInfo' => $productInfo,
            'pintuan_arr' => $pintuan_arr,
        );
        $layout = array(
            'content' => $this->load->view("{$this->_template}/promotion_ptkj_view", $data, TRUE)
        );
        $this->load->view('layout/seller_layout', $layout);
        //缓存
        if ($systemInfo['cache'] == 1) {
            $this->output->cache($systemInfo['cache_time']);
        }
    }

    public function my_delete_promotion_ptkj() {
        checkLoginAjax(true);
        checkPermissionAjax('groupon_delete');
        if ($_POST) {
            $seller_group_info = $this->Seller_group_model->get('user_id',array('id' => get_cookie('seller_group_id')));
            $store_info = $this->Stores_model->get('id', array('user_id' => $seller_group_info['user_id']));
            $id = $this->input->post('id', TRUE);

            if (!$id) {
                printAjaxError('fail', '请选择删除项');
            }
            $itemInfo = $this->Promotion_ptkj_model->get('*', array('id' => $id,'store_id'=>$store_info['id']));
            if (!$itemInfo){
                printAjaxError('fail', '参数异常，活动信息不存在');
            }
            if ($itemInfo['is_open'] == 1 && $itemInfo['end_time'] > time()){
                printAjaxError('fail', '活动进行中，不可删除');
            }
            $count = $this->Ptkj_record_model->rowCount(array('ptkj_id'=>$id, 'item_type' => 0));
            if ($count > 0) {
                printAjaxError('fail', '有相关记录,不可删除！');
            }

            if ($this->Promotion_ptkj_model->delete(array('id'=>$id))) {
                //删除拼团规则
                $this->Pintuan_model->delete(array('ptkj_id'=>$id));
                //删除抢团的关联
                $this->Group_purchase_model->save(array('ptkj_id'=>0),array('ptkj_id'=>$id));

                $this->Exchange_condition_model->delete(array('groupon_id'=>$id));
                printAjaxSuccess('success','删除成功！');
            }
            printAjaxError('fail', '删除失败！');
        }
    }

    public function refund_deposit()
    {
        if ($_POST){
            $id = $this->input->post('id',TRUE);
            $type = $this->input->post('type',TRUE);
            $item_info = $this->Promotion_ptkj_model->get('end_time,is_success,pintuan_people,min_number', array('id' => $id));
            if (!$item_info){
                printAjaxError('fail', '参数异常！');
            }
            if($item_info['end_time'] > time() || $item_info['is_success'] != 0 || $item_info['pintuan_people'] >= $item_info['min_number']){
                printAjaxError('fail', '未成团才能退定金！');
            }
            $record_list = $this->Ptkj_record_model->gets(array('ptkj_record.ptkj_id'=>$id,'ptkj_record.item_type'=>0,'ptkj_record.is_bond'=>1,'ptkj_record.is_refund'=>0,'ptkj_record.order_id'=>0));
            if ($record_list){
                $is_error = 0;
                $return_msg = '存在未成功退款项';
                foreach ($record_list as $value){
                    $is_fail = 0;
                    if ($value['payment_id'] == 1){
                        if (!$this->User_model->save_column('total','total+'.$value['total_fee'],array('id'=>$value['user_id']))){
                            $is_fail = 1;
                            $is_error = 1;
                            $return_msg = '操作异常，请重试';
                        }
                    }else{
                        $pay_log_info = $this->Pay_log_model->get('id,out_refund_no',array('out_trade_no' => $value['bond_number'], 'order_type' => 'groupon'));
                        if ($pay_log_info && $pay_log_info['out_refund_no']){
                            $out_refund_no = $pay_log_info['out_refund_no'];
                        }else{
                            $out_refund_no = $this->_get_unique_deposit_out_refund_no($value['bond_number']);
                            $this->Pay_log_model->save(array('out_refund_no'=>$out_refund_no), array('id' => $pay_log_info['id']));
                        }
                        if ($value['payment_id'] == 3 ||$value['payment_id'] == 5){
                            $is_xcx = $value['payment_id'] == 5 ? 1 : 0;
                            require_once "sdk/weixin_pay/lib/WxPay.Api.php";
                            $pay_api = new WxPayApi();
                            $result = $pay_api->do_refund($value['trade_no'],$value['bond_number'],$out_refund_no,$value['total_fee']*100,$value['total_fee']*100,$is_xcx);
                            if ($result['return_code'] == 'SUCCESS' && $result['result_code'] == 'SUCCESS'){
                                if ($this->Pay_log_model->save(array('is_refund'=>1,'refund_time'=>time()), array('id' => $pay_log_info['id']))){
                                    $this->Ptkj_record_model->save(array('is_refund'=>1),array('id'=>$value['id']));
                                }
                            }else{
                                $is_fail = 1;
                                $is_error = 1;
                                $return_msg = $result['return_msg'];
                            }
                        }elseif ($value['payment_id'] == 7){
                            require_once "sdk/wxpayv3/WxPay.Api.php";
                            $pay_api = new WxPayApi();
                            $result = $pay_api->do_refund($value['trade_no'],$value['bond_number'],$out_refund_no,$value['total_fee']*100,$value['total_fee']*100);
                            if ($result['return_code'] == 'SUCCESS' && $result['result_code'] == 'SUCCESS'){
                                if ($this->Pay_log_model->save(array('is_refund'=>1,'refund_time'=>time()), array('id' => $pay_log_info['id']))){
                                    $this->Ptkj_record_model->save(array('is_refund'=>1),array('id'=>$value['id']));
                                }
                            }else{
                                $is_fail = 1;
                                $is_error = 1;
                                $return_msg = $result['return_msg'];
                            }
                        }elseif ($value['payment_id'] == 2 || $value['payment_id'] == 6){
                            require_once "sdk/alipay/aop/AlipayService.php";
                            $pay_api = new AlipayService();
                            $param = array(
                                'out_trade_no'=>$value['bond_number'],
                                'trade_no'=>$value['trade_no'],
                                'refund_amount'=>$value['total_fee'],
                                'refund_reason'=>$type == 1 ? '拼团未成功退定金' : '拼团尾款逾期退定金'
                            );
                            $result = $pay_api->refund($param);
                            if ($result){
                                if ($this->Pay_log_model->save(array('is_refund'=>1,'refund_time'=>time()), array('id' => $pay_log_info['id']))){
                                    $this->Ptkj_record_model->save(array('is_refund'=>1),array('id'=>$value['id']));
                                }
                            }else{
                                $is_fail = 1;
                                $is_error = 1;
                                $return_msg = '退款未成功';
                            }
                        }
                    }
                    if ($is_fail == 0){
                        $user_info = $this->User_model->getInfo('id,username,total',array('id'=>$value['user_id']));
                        $financial_data = array(
                            'cause'=>$value['payment_id'] == 1 ? '拼团未成功,退定金至余额' : ($type == 1 ? '拼团未成功,原路退回定金' : '拼团尾款逾期,原路退回定金'),
                            'price'=>$value['total_fee'],
                            'balance' => $user_info['total'] + $value['total_fee'],
                            'add_time' => time(),
                            'user_id' => $user_info['id'],
                            'username' => $user_info['username'],
                            'type' => 'order_in',
                            'pay_way' => $value['payment_id'],
                            'ret_id' => $value['id'],
                            'seller_id'=>    $value['seller_id'],
                            'store_id'=>    $value['store_id'],
                        );
                        $this->Financial_model->save($financial_data);
                    }

                }
                if ($is_error){
                    printAjaxError('fail', $return_msg);
                }else{
                    if ($this->Promotion_ptkj_model->save(array('is_refund'=>1),array('id'=>$id))){
                        printAjaxSuccess('success','退定金成功！');
                    }
                }
            }else{
                printAjaxError('fail', '无相关参团记录');
            }
        }
        printAjaxError('fail', '操作异常！');

    }


    //拼团列表
    public function group_purchase_list($status = 0,$clear = '1', $page = 0)
    {
        //判断是否登录
        checkLogin(true);
        checkPermission("group_purchase_index");
        $systemInfo = $this->System_model->get('*', array('id' => 1));
        if ($clear) {
            $clear = 0;
            $this->session->unset_userdata('search');
        }
        $condition = "group_purchase.display = 1 and group_purchase.is_draft = 0";

        $seller_group_info = $this->Seller_group_model->get('user_id',array('id' => get_cookie('seller_group_id')));
        $store_info = $this->Stores_model->get('id', array('user_id' => $seller_group_info['user_id']));

        if ($status){
            $condition .= " and group_purchase.id in (select relate_id from store_relation where relate_type = 0 and store_id = {$store_info['id']})";
            $strWhere = $this->session->userdata('search') ? $this->session->userdata('search') : $condition;
            //分页
            $this->config->load('pagination_config', TRUE);
            $paginationConfig = $this->config->item('pagination_config');
            $paginationCount = $this->Group_purchase_model->count_join_user($strWhere);
            $paginationConfig['base_url'] = base_url()."admincp.php/{$this->_template}/group_purchase_list/{$status}/{$clear}/";
            $paginationConfig['total_rows'] = $paginationCount;
            $paginationConfig['uri_segment'] = 5;
            $this->pagination->initialize($paginationConfig);
            $pagination = $this->pagination->create_links();
            $select = "group_purchase.*, user.nickname, user.path, user.is_eli_guest";
            $item_list = $this->Group_purchase_model->gets_join_user($select, $strWhere, $paginationConfig['per_page'], $page);

        }else{
            $condition .= " and group_purchase.ptkj_id = 0 and group_purchase.is_check_groupon = 0 and group_purchase.id not in (select relate_id from store_relation where relate_type = 0 and store_id = {$store_info['id']}) ";
            $strWhere = $this->session->userdata('search') ? $this->session->userdata('search') : $condition;
            //分页
            $this->config->load('pagination_config', TRUE);
            $paginationConfig = $this->config->item('pagination_config');
            $paginationCount = $this->Group_purchase_model->count_join_user($strWhere);
            $paginationConfig['base_url'] = base_url()."admincp.php/{$this->_template}/group_purchase_list/{$status}/{$clear}/";
            $paginationConfig['total_rows'] = $paginationCount;
            $paginationConfig['uri_segment'] = 5;
            $this->pagination->initialize($paginationConfig);
            $pagination = $this->pagination->create_links();
            $select = "group_purchase.*, user.nickname, user.path, user.is_eli_guest";
            $item_list = $this->Group_purchase_model->gets_join_user($select, $strWhere, $paginationConfig['per_page'], $page);
        }

        if ($item_list){
            foreach ($item_list as $key => $value){
                $path_info = '';
                if ($value['batch_path_ids']){
                    $batch_path_arr = explode('_',$value['batch_path_ids']);
                    $path_info = $this->Attachment_model->get('path',array('id'=>$batch_path_arr[0]));
                }
                $item_list[$key]['path'] = $path_info ? $path_info['path'] : '';

            }
        }

        $data = array(
            'site_name' => $systemInfo['site_name'],
            'index_name' => $systemInfo['index_name'],
            
            'client_index' => $systemInfo['client_index'],
            'title' => '抢团发布列表_商家中心' . $systemInfo['site_name'],
            'keywords' => $systemInfo['site_keywords'],
            'description' => $systemInfo['site_description'],
            'site_copyright' => $systemInfo['site_copyright'],
            'icp_code' => $systemInfo['icp_code'],
            'html' => $systemInfo['html'],
            'template' => $this->_template,
            'item_list' => $item_list,
            'pagination' => $pagination,
            'status' => $status,
        );
        $layout = array(
            'content' => $this->load->view("{$this->_template}/group_purchase_list", $data, TRUE)
        );
        $this->load->view('layout/seller_layout', $layout);
        //缓存
        if ($systemInfo['cache'] == 1) {
            $this->output->cache($systemInfo['cache_time']);
        }
    }

    //拼团详情
    public function group_purchase_view($id = NULL)
    {
        //判断是否登录
        checkLogin(true);
        checkPermission("group_purchase_edit");
        $systemInfo = $this->System_model->get('*', array('id' => 1));
        $seller_group_info = $this->Seller_group_model->get('user_id',array('id' => get_cookie('seller_group_id')));
        $store_info = $this->Stores_model->get('id', array('user_id' => $seller_group_info['user_id']));
        $item_info = $this->Group_purchase_model->get('*', array('id' => $id));
        if ($_POST) {
            $ptkj_id = $this->input->post('ptkj_id', TRUE);
            $groupon_info = $this->Promotion_ptkj_model->get3('product.title,product.path,product.category_id_1,promotion_ptkj.is_success,promotion_ptkj.end_time,promotion_ptkj.start_time,promotion_ptkj.low_price', array('promotion_ptkj.id' => $ptkj_id,'promotion_ptkj.is_open'=>1,'promotion_ptkj.store_id'=>$store_info['id']));
            if (!$groupon_info) {
                printAjaxError('fail', '本商户不存在此团购活动或者未通过审核！');
            }
            if ($item_info['category_id_1'] != $groupon_info['category_id_1']){
                printAjaxError('fail',"团预购活动商品分类与拼团商品分类不匹配！");
            }
            if ($groupon_info['end_time'] < time() || $groupon_info['is_success']) {
                printAjaxError('fail', '该团预购活动已结束');
            }

            if ($this->Store_relation_model->rowCount(array('relate_id'=>$id,'relate_type'=>0,'item_id'=>$ptkj_id,'item_type'=>0))){
                printAjaxError('fail', '该团预购活动已关联推荐');
            }

            $data = array(
                'store_id'=>$store_info['id'],
                'relate_id'=>$id,
                'relate_type'=>0,
                'item_id'=>$ptkj_id,
                'item_type'=>0,
                'item_title'=>$groupon_info['title'],
                'item_path'=>$groupon_info['path'],
                'item_price'=>$groupon_info['low_price'],
                'add_time'=>time(),
            );

            if ($this->Store_relation_model->save($data)){
                $prfUrl = base_url() . "index.php/{$this->_template}/group_purchase_list/1";
                printAjaxSuccess($prfUrl, '提交成功!');
            }else{
                printAjaxError('fail', '提交失败！');
            }

        }
        $attachment_list = NULL;
        if($item_info['batch_path_ids']){
            $tmp_atm_ids = preg_replace(array('/^_/', '/_$/', '/_/'), array('', '', ','), $item_info['batch_path_ids']);
            $attachment_list = $this->Attachment_model->gets2($tmp_atm_ids);
        }
        $category_info_1 = $this->Product_category_model->get('product_category_name',array('id'=>$item_info['category_id_1']));
        $category_info_2 = $this->Product_category_model->get('product_category_name',array('id'=>$item_info['category_id_2']));
        $category_info_3 = $this->Product_category_model->get('product_category_name',array('id'=>$item_info['category_id_3']));
        $item_info['category_name_1'] = $category_info_1 ? $category_info_1['product_category_name'] : '';
        $item_info['category_name_2'] = $category_info_2 ? $category_info_2['product_category_name'] : '';
        $item_info['category_name_3'] = $category_info_3 ? $category_info_3['product_category_name'] : '';

        $data = array(
            'site_name' => $systemInfo['site_name'],
            'index_name' => $systemInfo['index_name'],
            
            'client_index' => $systemInfo['client_index'],
            'title' => '查看抢团详情_商家中心' . $systemInfo['site_name'],
            'keywords' => $systemInfo['site_keywords'],
            'description' => $systemInfo['site_description'],
            'site_copyright' => $systemInfo['site_copyright'],
            'icp_code' => $systemInfo['icp_code'],
            'html' => $systemInfo['html'],
            'template' => $this->_template,
            'item_info' => $item_info,
            'attachment_list' => $attachment_list,
        );
        $layout = array(
            'content' => $this->load->view("{$this->_template}/group_purchase_view", $data, TRUE)
        );
        $this->load->view('layout/seller_layout', $layout);
        //缓存
        if ($systemInfo['cache'] == 1) {
            $this->output->cache($systemInfo['cache_time']);
        }
    }


    //预存款订单结算
    public function bonus_yue_pay(){
        checkLoginAjax(true);
        if ($_POST) {
//            $user_id = $this->session->userdata('user_id');
            $seller_group_info = $this->Seller_group_model->get('user_id',array('id' => get_cookie('seller_group_id')));
            $user_id = $seller_group_info['user_id'];
            $store_info = $this->Stores_model->get('id', array('user_id' => $user_id));
            $product_id = $this->input->post('product_id');
            $bonus = $this->input->post('bonus');
            $pay_password = $this->input->post('pay_password');
            if (!$pay_password) {
                printAjaxError('fail', '支付密码不能为空');
            }
            //判断下单用户是否存在
            $user_info = $this->User_model->get('*', array('user.id' => $user_id));
            if (!$user_info) {
                printAjaxError('fail', '此用户不存在，结算失败');
            }

            //预存款支付
            if ($bonus > $user_info['total']) {
                printAjaxError('fail', '余额不足，请先充值');
            }
            if (create_password_salt($user_info['username'], $user_info['add_time'], $pay_password) != $user_info['pay_password']) {
                printAjaxError('fail', '支付密码错误，请重新输入');
            }


            //进行扣款
            if (!$this->User_model->save(array('total' => $user_info['total'] - $bonus), array('id' => $user_id))) {
                printAjaxError('fail', '支付失败');
            }
            $this->Product_model->save(array('open_bonus'=>1,'bonus'=>$bonus),array('id'=>$product_id));
            //财务记录还没有添加
            $fields = array(
                'cause' => "支付预注奖励金",
                'price' => -$bonus,
                'balance' => $user_info['total'] - $bonus,
                'add_time' => time(),
                'user_id' => $user_info['id'],
                'username' => $user_info['username'],
                'type' => 'bonus_out',
                'pay_way' => 1,
                'ret_id' => $product_id,
                'from_user_id' => $user_info['id'],
                'seller_id'=>    $user_id,
                'store_id'=>    $store_info['id'],
            );
            $this->Financial_model->save($fields);
            printAjaxSuccess('success', '恭喜您支付成功!');
        }
    }

    //加盐算法
    public function createPasswordSALT($user, $salt, $password) {

        return md5($user.$salt.$password);
    }

    private function _balance_trade_refund($order_detail_count, $item_info, $orders_info, $user_info, $status = '4') {
        $fields = array(
            'total'=>$user_info['total'] + $item_info['price']
        );
        if (!$this->User_model->save($fields, array('id'=>$orders_info['user_id']))) {
            printAjaxError('fail', '退款操作失败');
        }
        $fields = array(
            'cause'=>"退款成功-[订单号：{$orders_info['order_number']}]",
            'price'=>$item_info['price'],
            'balance'=>$user_info['total'] + $item_info['price'],
            'add_time'=>time(),
            'user_id'=>$user_info['id'],
            'username'=>$user_info['username'],
            'type' =>  'order_in',
            'pay_way'=>'1',
            'ret_id'=>$item_info['orders_detail_id'],
            'from_user_id'=>$user_info['id']
        );
        $this->Financial_model->save($fields);
        if ($order_detail_count == 1){
            //操作订单
            $fields = array(
                'cancel_cause'=> '交易关闭-[买家申请退款成功]',
                'status'=> 4
            );
            if ($this->Orders_model->save($fields, array('id' => $orders_info['id']))) {
                $fields = array(
                    'add_time' => time(),
                    'content' => "交易关闭成功-[买家申请退款成功]",
                    'order_id' => $orders_info['id'],
                    'order_status'=>$orders_info['status'],
                    'change_status'=>4
                );
                $this->Orders_process_model->save($fields);
            }
        }

        //操作退款申请状态
        if ($this->Exchange_model->save(array('status'=>$status, 'update_time'=>time()), array('id'=>$item_info['id']))){
            $this->Orders_detail_model->save(array('is_refund'=>1, 'refund_time'=>time()), array('id'=>$item_info['orders_detail_id']));
            printAjaxSuccess('success', '退款成功');
        }
        printAjaxSuccess('success', '退款成功');

    }

    /**获取唯一退款单号
     * @param $order_num
     * @return string
     */
    private function _get_unique_out_refund_no($order_num) {
        $randCode = '';
        while (true) {
            $randCode = $order_num.getRandCode(3);
            $count = $this->Orders_detail_model->rowCount(array('out_refund_no' => $randCode));
            if ($count > 0) {
                $randCode = '';
                continue;
            } else {
                break;
            }
        }
        return $randCode;
    }

    //获取唯一的订单号
    private function _get_unique_deposit_out_refund_no($order_num) {
        $randCode = '';
        while (true) {
            $randCode = $order_num.getRandCode(3);
            $count = $this->Pay_log_model->rowCount(array('out_refund_no' => $randCode));
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
     * 发布套餐
     */
    public function my_save_combos($id = NULL)
    {
        //判断是否登录
        checkLogin(true);
        if ($id) {
            checkPermission("combos_edit");
        } else {
            checkPermission("combos_add");
        }
        $systemInfo = $this->System_model->get('*', array('id' => 1));
        $seller_group_info = $this->Seller_group_model->get('user_id',array('id' => get_cookie('seller_group_id')));
        $store_info = $this->Stores_model->get('id,type_id,user_id', array('user_id' => $seller_group_info['user_id']));

        //产品详细
        $item_info = $this->Combos_model->get('*', array("id" => $id, 'store_id' => $store_info['id']));

        if ($_POST) {
            $name = $this->input->post('title', TRUE);
            $batch_path_ids = $this->input->post('batch_path_ids', TRUE);
            $path = $this->input->post('path', TRUE);
            $content = $this->input->post('content');
            $usage_rules = $this->input->post('usage_rules');
            $stock = $this->input->post('stock', TRUE);
            $dishes_ids = $this->input->post('dishes_ids', TRUE);
            $price = $this->input->post('price', TRUE);
            $original_price = $this->input->post('original_price', TRUE);
            $display = $this->input->post('display', TRUE);
            $tag_id = $this->input->post('tag_id', TRUE);
            $attribute = $this->input->post('attribute', TRUE);
            $address = $this->input->post('address', TRUE);
            $is_shared = $this->input->post('is_shared', TRUE);
            $reward = $this->input->post('reward', TRUE);

            if (!$this->form_validation->required($name)) {
                printAjaxError('title', '商品标题不能为空');
            }

            if (!$this->form_validation->required($path)) {
                if (!$this->form_validation->required($batch_path_ids)){
                    printAjaxError('path', '请上传封面图');
                }
                $path_info = $this->Attachment_model->get('path',array('id'=>$batch_path_ids[0]));
                if (!$path_info){
                    printAjaxError('path', '请上传封面图');
                }
                $path = $path_info['path'];
            }

            if ($is_shared) {
                if ($stock <= 0 || $reward <= 0) {
                    printAjaxError('path', '请填写库存、佣金');
                }
            }

            if($batch_path_ids){
                $batch_path_ids = implode('_', $batch_path_ids);
                $batch_path_ids .= '_';
            } else {
                $batch_path_ids = '';
            }

            if($dishes_ids){
                $dishes_ids = implode(',', $dishes_ids);
            }

            $attribute = $attribute ? implode(',', $attribute) : '';

            $fields = array(
                'store_id' => $store_info['id'],
                'name' => $name,
                'dishes_ids' => $dishes_ids,
                'content' => unhtml($content),
                'usage_rules' => unhtml($usage_rules),
                'cover_image' => $path,
                'image_ids' => $batch_path_ids,
                'price' => $price,
                'original_price' => $original_price,
                'stock' => $stock,
                'tag_id' => $tag_id,
                'attribute' => $attribute,
                'address' => $address,
                'display' => $display,
                'is_shared' => $is_shared,
                'reward' => $reward,
            );
            if ($address) {
                $location = get_lat_lng($address);
                if ($location) {
                    $fields['lat'] = $location['lat'];
                    $fields['lng'] = $location['lng'];
                }
            }
            $retId = $this->Combos_model->save($fields, $id ? array('id' => $id) : $id);
            if ($retId) {
                $goods_id = $id ? $id : $retId;
                //新增吆喝
                $share_goods_info = $this->Share_goods_model->get('*', ['goods_id'=>$goods_id]);
                if ($is_shared) {
                    if (!$share_goods_info) {
                        $fields = array(
                            'user_id' => $store_info['user_id'],
                            'store_id' => $store_info['id'],
                            'category_id' => $store_info['type_id'],
                            'name' => $name,
                            'content' => unhtml($content),
                            'cover_image' => $path,
                            'image_ids' => $batch_path_ids,
                            'price' => $price,
                            'original_price' => $original_price,
                            'stock' => $stock,
                            'stock_total' => $stock,
                            'reward' => $reward,
                            'type' => ($item_info && $item_info['type']) ? 1 : 2,
                            'goods_id' => $goods_id,
                        );
                        $this->Share_goods_model->save($fields);
                    }
                } else {
                    if ($share_goods_info) {
                        if ($this->Orders_detail_model->count('item_id in (' . $share_goods_info['id'] . ')')) {
                            $this->Share_goods_model->save(['display'=>0], ['id'=>$share_goods_info['id']]);
                        } else {
                            $this->Share_goods_model->delete(['id'=>$share_goods_info['id']]);
                        }
                    }
                }
                
                printAjaxSuccess(getBaseUrl(false, '', 'seller/my_get_combos_list.html', $systemInfo['client_index']), '保存成功');
            } else {
                printAjaxError('fail', '保存失败');
            }
        }

        $attachment_list = array();
        if($item_info && $item_info['image_ids']){
            $tmp_atm_ids = preg_replace(array('/^_/', '/_$/', '/_/'), array('', '', ','), $item_info['image_ids']);
            $attachment_list = $this->Attachment_model->gets('id,path',"id in ($tmp_atm_ids)");
        }
        
        $dishes_list = $this->Dishes_model->gets('*', ['store_id' => $store_info['id']]);

        $tags_list = $this->Tags_model->gets('id,name');

        $data = array(
            'site_name' => $systemInfo['site_name'],
            'index_name' => $systemInfo['index_name'],
            'client_index' => $systemInfo['client_index'],
            'title' => '新增套餐_商家中心' . $systemInfo['site_name'],
            'keywords' => $systemInfo['site_keywords'],
            'description' => $systemInfo['site_description'],
            'site_copyright' => $systemInfo['site_copyright'],
            'icp_code' => $systemInfo['icp_code'],
            'html' => $systemInfo['html'],
            'template' => $this->_template,
            'item_info' => $item_info,
            'attachment_list' => $attachment_list,
            'dishes_list' => $dishes_list,
            'tags_list' => $tags_list,
        );
        $layout = array(
            'content' => $this->load->view("{$this->_template}/my_save_combos", $data, TRUE)
        );
        $this->load->view('layout/seller_layout', $layout);
        //缓存
        if ($systemInfo['cache'] == 1) {
            $this->output->cache($systemInfo['cache_time']);
        }
    }

    /**
     * 套餐列表
     */
    public function my_get_combos_list($clear = 1, $page = 0)
    {
        //判断是否登录
        checkLogin(true);
        checkPermission("combos_index");
        if ($clear) {
            $clear = 0;
            $this->session->unset_userdata('search');
        }
        $systemInfo = $this->System_model->get('*', array('id' => 1));
        $seller_group_info = $this->Seller_group_model->get('user_id',array('id' => get_cookie('seller_group_id')));
        $store_info = $this->Stores_model->get('id', array('user_id' => $seller_group_info['user_id']));
        $condition = "store_id = {$store_info['id']}";
        $strWhere = $this->session->userdata('search') ? $this->session->userdata('search') : $condition;
        if($_POST){
        	$strWhere = $condition;
            $title = $this->input->post('title',TRUE);
            $sell_price_start = $this->input->post('sell_price_start',TRUE);
            $sell_price_end = $this->input->post('sell_price_end',TRUE);

            if($title){
                $strWhere .= " and name regexp '{$title}' ";
            }

            if(!empty($sell_price_start) && !empty($sell_price_end)){
                $strWhere .= " and (price >= '{$sell_price_start}' and price <= '{$sell_price_end}') ";
            }

            $this->session->set_userdata('search', $strWhere);
            $page = 0;
        }
        //分页
        $paginationCount = $this->Combos_model->count($strWhere);
        $this->config->load('pagination_config', TRUE);
        $paginationConfig = $this->config->item('pagination_config');
        $paginationConfig['base_url'] = base_url() . "index.php/seller/my_get_combos_list/{$clear}/";
        $paginationConfig['total_rows'] = $paginationCount;
        $paginationConfig['uri_segment'] = 4;
        $paginationConfig['per_page'] = 20;
        $this->pagination->initialize($paginationConfig);
        $pagination = $this->pagination->create_links();
        $item_list = $this->Combos_model->gets('id,cover_image,sort,name,price,stock,display,type', $strWhere, $paginationConfig['per_page'], $page);

        $tags_list = $this->Tags_model->gets('id,name');

        $data = array(
            'site_name' => $systemInfo['site_name'],
            'index_name' => $systemInfo['index_name'],
            'client_index' => $systemInfo['client_index'],
            'title' => '套餐管理_商家中心' . $systemInfo['site_name'],
            'keywords' => $systemInfo['site_keywords'],
            'description' => $systemInfo['site_description'],
            'site_copyright' => $systemInfo['site_copyright'],
            'icp_code' => $systemInfo['icp_code'],
            'html' => $systemInfo['html'],
            'template' => $this->_template,
            'item_list' => $item_list,
            'tags_list' => $tags_list,
            'pagination' => $pagination,
        );
        $layout = array(
            'content' => $this->load->view("{$this->_template}/my_get_combos_list", $data, TRUE)
        );
        $this->load->view('layout/seller_layout', $layout);
        //缓存
        if ($systemInfo['cache'] == 1) {
            $this->output->cache($systemInfo['cache_time']);
        }
    }

    /**
     * 删除套餐
     */
    public function my_delete_combos() {
        checkPermission('combos_delete');
        $ids = $this->input->post('ids', TRUE);
        if (!$this->form_validation->required($ids)) {
            printAjaxError('title', '请选择您要删除的项');
        }

        if (!empty($ids)) {
            if ($this->Orders_detail_model->count('item_id in (' . $ids . ')')) {
                printAjaxError('title', '已存在订单，不能删除，可下架处理');
            }
            if ($this->Combos_model->delete('id in (' . $ids . ')')) {
            
                printAjaxData(array('ids' => explode(',', $ids)));
            }
        }
        printAjaxError('','删除失败！');
    }


    /**
     * 套餐排序
     */
    public function my_sort_combos() {
        checkPermissionAjax("combos_edit");
        $ids = $this->input->post('ids', TRUE);
        $sorts = $this->input->post('sorts', TRUE);

        if (!empty($ids) && !empty($sorts)) {
            $ids = explode(',', $ids);
            $sorts = explode(',', $sorts);

            foreach ($ids as $key => $value) {
                $this->Combos_model->save(array('sort' => $sorts[$key]), array('id' => $value));
            }
            printAjaxSuccess('排序成功！');
        }

        printAjaxError('排序失败！');
    }

    /**
     * 套餐状态修改
     */
    public function my_display_combos() {
        checkPermissionAjax("combos_edit");
        $ids = $this->input->post('ids', TRUE);
        $display = $this->input->post('display', TRUE);

        if (!empty($ids)) {
            $this->Combos_model->save(array('display' => $display), "id in ({$ids})");
            printAjaxSuccess('修改成功！');
        }

        printAjaxError('修改失败！');
    }
    /**
     * 套餐标签修改
     */
    public function combos_change_tag() {
        checkPermissionAjax("combos_edit");
        $ids = $this->input->post('ids', TRUE);
        $tag_id = $this->input->post('tag_id', TRUE);

        if (!empty($ids)) {
            $this->Combos_model->save(array('tag_id' => $tag_id), "id in ({$ids})");
            printAjaxSuccess('修改成功！');
        }

        printAjaxError('修改失败！');
    }

     /**
     * 新增吆喝产品
     */
    public function my_save_share_goods($id = NULL)
    {
        //判断是否登录
        checkLogin(true);
        if ($id) {
            checkPermission("share_goods_edit");
        } else {
            checkPermission("share_goods_add");
        }
        $systemInfo = $this->System_model->get('*', array('id' => 1));
        $seller_group_info = $this->Seller_group_model->get('user_id',array('id' => get_cookie('seller_group_id')));
        $store_info = $this->Stores_model->get('id,user_id,type_id', array('user_id' => $seller_group_info['user_id']));

        //产品详细
        $item_info = $this->Share_goods_model->get('*', array("id" => $id, 'store_id' => $store_info['id']));

        if ($_POST) {
            $name = $this->input->post('title', TRUE);
            $batch_path_ids = $this->input->post('batch_path_ids', TRUE);
            $path = $this->input->post('path', TRUE);
            $content = $this->input->post('content');
            $stock = $this->input->post('stock', TRUE);
            $price = $this->input->post('price', TRUE);
            $original_price = $this->input->post('original_price', TRUE);
            $display = $this->input->post('display', TRUE);
            $reward = $this->input->post('reward', TRUE);

            if (!$this->form_validation->required($name)) {
                printAjaxError('title', '请填写商品标题');
            }

            if ($reward < 0) {
                printAjaxError('title', '请填写佣金');
            }

            if (!$this->form_validation->required($path)) {
                if (!$this->form_validation->required($batch_path_ids)){
                    printAjaxError('path', '请上传封面图');
                }
                $path_info = $this->Attachment_model->get('path',array('id'=>$batch_path_ids[0]));
                if (!$path_info){
                    printAjaxError('path', '请上传封面图');
                }
                $path = $path_info['path'];
            }

            if($batch_path_ids){
                $batch_path_ids = implode('_', $batch_path_ids);
                $batch_path_ids .= '_';
            }

            $fields = array(
                'user_id' => $store_info['user_id'],
                'store_id' => $store_info['id'],
                'category_id' => $store_info['type_id'],
                'name' => $name,
                'content' => unhtml($content),
                'cover_image' => $path,
                'image_ids' => $batch_path_ids,
                'price' => $price,
                'original_price' => $original_price,
                'stock' => $stock,
                'reward' => $reward,
                'display' => $display,
            );
            if (!$item_info) {
                $fields['status'] = 0;
            }
            $fields['stock_total'] = $item_info ? ($stock - $item_info['stock']) + $item_info['stock_total'] : $stock;
            $retId = $this->Share_goods_model->save($fields, $id ? array('id' => $id) : $id);
            if ($retId) {
                printAjaxSuccess(getBaseUrl(false, '', 'seller/my_get_share_goods_list.html', $systemInfo['client_index']), '保存成功');
            } else {
                printAjaxError('fail', '保存失败');
            }
        }

        
        $store_type_list = $this->Store_type_model->gets('*', array('display' => 1));

        $attachment_list = array();
        if($item_info && $item_info['image_ids']){
            $tmp_atm_ids = preg_replace(array('/^_/', '/_$/', '/_/'), array('', '', ','), $item_info['image_ids']);
            $attachment_list = $this->Attachment_model->gets('id,path',"id in ($tmp_atm_ids)");
        }


        $data = array(
            'site_name' => $systemInfo['site_name'],
            'index_name' => $systemInfo['index_name'],
            'client_index' => $systemInfo['client_index'],
            'title' => '新增吆喝商品_商家中心' . $systemInfo['site_name'],
            'keywords' => $systemInfo['site_keywords'],
            'description' => $systemInfo['site_description'],
            'site_copyright' => $systemInfo['site_copyright'],
            'icp_code' => $systemInfo['icp_code'],
            'html' => $systemInfo['html'],
            'template' => $this->_template,
            'item_info' => $item_info,
            'store_type_list' => $store_type_list,
            'attachment_list' => $attachment_list,
        );
        $layout = array(
            'content' => $this->load->view("{$this->_template}/my_save_share_goods", $data, TRUE)
        );
        $this->load->view('layout/seller_layout', $layout);
        //缓存
        if ($systemInfo['cache'] == 1) {
            $this->output->cache($systemInfo['cache_time']);
        }
    }


    /**
     * 吆喝列表
     */
    public function my_get_share_goods_list($clear = 1, $page = 0)
    {
        //判断是否登录
        checkLogin(true);
        checkPermission("share_goods_index");
        if ($clear) {
            $clear = 0;
            $this->session->unset_userdata('search');
        }
        $systemInfo = $this->System_model->get('*', array('id' => 1));
        $seller_group_info = $this->Seller_group_model->get('user_id',array('id' => get_cookie('seller_group_id')));
        $store_info = $this->Stores_model->get('id', array('user_id' => $seller_group_info['user_id']));
        $condition = "store_id = {$store_info['id']}";
        $strWhere = $this->session->userdata('search') ? $this->session->userdata('search') : $condition;
        if($_POST){
        	$strWhere = $condition;
            $title = $this->input->post('title',TRUE);
            $sell_price_start = $this->input->post('sell_price_start',TRUE);
            $sell_price_end = $this->input->post('sell_price_end',TRUE);

            if($title){
                $strWhere .= " and name regexp '{$title}' ";
            }

            if(!empty($sell_price_start) && !empty($sell_price_end)){
                $strWhere .= " and (price >= '{$sell_price_start}' and price <= '{$sell_price_end}') ";
            }

            $this->session->set_userdata('search', $strWhere);
            $page = 0;
        }
        //分页
        $paginationCount = $this->Share_goods_model->count($strWhere);
        $this->config->load('pagination_config', TRUE);
        $paginationConfig = $this->config->item('pagination_config');
        $paginationConfig['base_url'] = base_url() . "index.php/seller/my_get_share_goods_list/{$clear}/";
        $paginationConfig['total_rows'] = $paginationCount;
        $paginationConfig['uri_segment'] = 4;
        $paginationConfig['per_page'] = 20;
        $this->pagination->initialize($paginationConfig);
        $pagination = $this->pagination->create_links();
        $item_list = $this->Share_goods_model->gets('id,cover_image,name,price,stock,reward,display,status', $strWhere, $paginationConfig['per_page'], $page);

        $data = array(
            'site_name' => $systemInfo['site_name'],
            'index_name' => $systemInfo['index_name'],
            'client_index' => $systemInfo['client_index'],
            'title' => '吆喝商品管理_商家中心' . $systemInfo['site_name'],
            'keywords' => $systemInfo['site_keywords'],
            'description' => $systemInfo['site_description'],
            'site_copyright' => $systemInfo['site_copyright'],
            'icp_code' => $systemInfo['icp_code'],
            'html' => $systemInfo['html'],
            'template' => $this->_template,
            'item_list' => $item_list,
            'pagination' => $pagination,
            'check_status_arr' => $this->_check_status_arr,
        );
        $layout = array(
            'content' => $this->load->view("{$this->_template}/my_get_share_goods_list", $data, TRUE)
        );
        $this->load->view('layout/seller_layout', $layout);
        //缓存
        if ($systemInfo['cache'] == 1) {
            $this->output->cache($systemInfo['cache_time']);
        }
    }

    /**
     * 删除吆喝
     */
    public function my_delete_share_goods() {
        checkPermission('share_goods_delete');
        $ids = $this->input->post('ids', TRUE);
        if (!$this->form_validation->required($ids)) {
            printAjaxError('title', '请选择您要删除的项');
        }

        if (!empty($ids)) {
            // if ($this->Orders_detail_model->count('item_id in (' . $ids . ')')) {
            //     printAjaxError('title', '已存在订单，不能删除，可下架处理');
            // }
            
            foreach (explode(',', $ids) as $id) {
                $goods_info = $this->Share_goods_model->get('type, goods_id', ['id'=>$id]);
                if ($goods_info && $goods_info['type'] > 0) {
                    $this->Combos_model->save(['is_shared'=>0, 'reward'=>0], ['id'=>$goods_info['goods_id']]);
                }
            }

            if ($this->Share_goods_model->delete('id in (' . $ids . ')')) {
            
                printAjaxData(array('ids' => explode(',', $ids)));
            }
        }
        printAjaxError('','删除失败！');
    }

    /**
     * 新增种草内容
     */
    public function my_save_grasses($id = NULL)
    {
        //判断是否登录
        checkLogin(true);
        if ($id) {
            checkPermission("grasses_edit");
        } else {
            checkPermission("grasses_add");
        }
        $systemInfo = $this->System_model->get('*', array('id' => 1));
        $seller_group_info = $this->Seller_group_model->get('user_id',array('id' => get_cookie('seller_group_id')));
        $store_info = $this->Stores_model->get('id,user_id,type_id', array('user_id' => $seller_group_info['user_id']));

        //产品详细
        $item_info = $this->Grasses_model->get('*', array("id" => $id, 'store_id' => $store_info['id']));

        if ($_POST) {
            $title = $this->input->post('title', TRUE);
            $batch_path_ids = $this->input->post('batch_path_ids', TRUE);
            $path = $this->input->post('path', TRUE);
            $content = $this->input->post('content');
            $display = $this->input->post('display', TRUE);

            if (!$this->form_validation->required($title)) {
                printAjaxError('title', '请填写标题');
            }

            if (!$this->form_validation->required($path)) {
                if (!$this->form_validation->required($batch_path_ids)){
                    printAjaxError('path', '请上传封面图');
                }
                $path_info = $this->Attachment_model->get('path',array('id'=>$batch_path_ids[0]));
                if (!$path_info){
                    printAjaxError('path', '请上传封面图');
                }
                $path = $path_info['path'];
            }

            if($batch_path_ids){
                $batch_path_ids = implode('_', $batch_path_ids);
                $batch_path_ids .= '_';
            }

            $fields = array(
                'store_id' => $store_info['id'],
                'title' => $title,
                'content' => unhtml($content),
                'cover_image' => $path,
                'image_ids' => $batch_path_ids,
                'display' => $display,
            );

            $retId = $this->Grasses_model->save($fields, $id ? array('id' => $id) : $id);
            if ($retId) {
                printAjaxSuccess(getBaseUrl(false, '', 'seller/my_get_grasses_list.html', $systemInfo['client_index']), '保存成功');
            } else {
                printAjaxError('fail', '保存失败');
            }
        }

        

        $attachment_list = array();
        if($item_info && $item_info['image_ids']){
            $tmp_atm_ids = preg_replace(array('/^_/', '/_$/', '/_/'), array('', '', ','), $item_info['image_ids']);
            $attachment_list = $this->Attachment_model->gets('id,path',"id in ($tmp_atm_ids)");
        }


        $data = array(
            'site_name' => $systemInfo['site_name'],
            'index_name' => $systemInfo['index_name'],
            'client_index' => $systemInfo['client_index'],
            'title' => '新增种草内容_商家中心' . $systemInfo['site_name'],
            'keywords' => $systemInfo['site_keywords'],
            'description' => $systemInfo['site_description'],
            'site_copyright' => $systemInfo['site_copyright'],
            'icp_code' => $systemInfo['icp_code'],
            'html' => $systemInfo['html'],
            'template' => $this->_template,
            'item_info' => $item_info,
            'attachment_list' => $attachment_list,
        );
        $layout = array(
            'content' => $this->load->view("{$this->_template}/my_save_grasses", $data, TRUE)
        );
        $this->load->view('layout/seller_layout', $layout);
        //缓存
        if ($systemInfo['cache'] == 1) {
            $this->output->cache($systemInfo['cache_time']);
        }
    }


    /**
     * 种草列表
     */
    public function my_get_grasses_list($clear = 1, $page = 0)
    {
        //判断是否登录
        checkLogin(true);
        checkPermission("grasses_index");
        if ($clear) {
            $clear = 0;
            $this->session->unset_userdata('search');
        }
        $systemInfo = $this->System_model->get('*', array('id' => 1));
        $seller_group_info = $this->Seller_group_model->get('user_id',array('id' => get_cookie('seller_group_id')));
        $store_info = $this->Stores_model->get('id', array('user_id' => $seller_group_info['user_id']));
        $condition = "store_id = {$store_info['id']}";
        $strWhere = $this->session->userdata('search') ? $this->session->userdata('search') : $condition;
        if($_POST){
        	$strWhere = $condition;
            $title = $this->input->post('title',TRUE);

            if($title){
                $strWhere .= " and title regexp '{$title}' ";
            }

            $this->session->set_userdata('search', $strWhere);
            $page = 0;
        }
        //分页
        $paginationCount = $this->Grasses_model->count($strWhere);
        $this->config->load('pagination_config', TRUE);
        $paginationConfig = $this->config->item('pagination_config');
        $paginationConfig['base_url'] = base_url() . "index.php/seller/my_get_grasses_list/{$clear}/";
        $paginationConfig['total_rows'] = $paginationCount;
        $paginationConfig['uri_segment'] = 4;
        $paginationConfig['per_page'] = 20;
        $this->pagination->initialize($paginationConfig);
        $pagination = $this->pagination->create_links();
        $item_list = $this->Grasses_model->gets('*', $strWhere, $paginationConfig['per_page'], $page);

        $data = array(
            'site_name' => $systemInfo['site_name'],
            'index_name' => $systemInfo['index_name'],
            'client_index' => $systemInfo['client_index'],
            'title' => '种草管理_商家中心' . $systemInfo['site_name'],
            'keywords' => $systemInfo['site_keywords'],
            'description' => $systemInfo['site_description'],
            'site_copyright' => $systemInfo['site_copyright'],
            'icp_code' => $systemInfo['icp_code'],
            'html' => $systemInfo['html'],
            'template' => $this->_template,
            'item_list' => $item_list,
            'pagination' => $pagination,
            'check_status_arr' => $this->_check_status_arr,
        );
        $layout = array(
            'content' => $this->load->view("{$this->_template}/my_get_grasses_list", $data, TRUE)
        );
        $this->load->view('layout/seller_layout', $layout);
        //缓存
        if ($systemInfo['cache'] == 1) {
            $this->output->cache($systemInfo['cache_time']);
        }
    }

    /**
     * 删除套餐
     */
    public function my_delete_grasses() {
        checkPermission('grasses_delete');
        $ids = $this->input->post('ids', TRUE);
        if (!$this->form_validation->required($ids)) {
            printAjaxError('title', '请选择您要删除的项');
        }

        if (!empty($ids)) {

            if ($this->Grasses_model->delete('id in (' . $ids . ')')) {
            
                printAjaxData(array('ids' => explode(',', $ids)));
            }
        }
        printAjaxError('','删除失败！');
    }

    /**
     * 财务概况
     */
    public function store_account()
    {
        //判断是否登录
        checkLogin(true);
        // checkPermission("account_index");
        $systemInfo = $this->System_model->get('*', array('id' => 1));
        $seller_group_info = $this->Seller_group_model->get('user_id',array('id' => get_cookie('seller_group_id')));
        if ($this->session->userdata('user_id') != $seller_group_info['user_id']) {
            alert_message('仅店主拥有权限');
        }
        $store_info = $this->Stores_model->get('id,account_amount', array('user_id' => $seller_group_info['user_id']));
        $orders_ids_info = $this->Orders_model->get('group_concat(id) as ids', ['store_id'=>$store_info['id'], 'status <'=>9, 'status >'=>1, 'is_settlement'=>0, 'DATEDIFF(now(), update_time) >'=>3]);
        if ($orders_ids_info && $orders_ids_info['ids']) {
            $orders_ids = $orders_ids_info['ids'];
            $orders_total = $this->Orders_model->sum('total', "id in ({$orders_ids})");
            $orders_reward_total = $this->Orders_detail_model->sum('reward', "order_id in ({$orders_ids})");
            $account_amount = $orders_total * (1 - $systemInfo['platform_commission']) - $orders_reward_total;
            if ($account_amount) {
                if ($this->Stores_model->save(['account_amount'=>$store_info['account_amount']+$account_amount], ['id'=>$store_info['id']])) {
                    $this->Orders_model->save(['is_settlement'=>1], "id in ({$orders_ids})");
                }
            }
        }
        $item_info = $this->Stores_model->get('id,account_amount', ['id'=>$store_info['id']]);
        $unrecorded_orders_total = $this->Orders_model->sum('total', ['store_id'=>$store_info['id'], 'status <'=>9, 'status >'=>1, 'is_settlement'=>0, 'DATEDIFF(now(), update_time) <='=>3]);
        $item_info['unrecorded_orders_total'] = $unrecorded_orders_total;
        $this->load->model('Withdrawal_record_model', "", TRUE);
        $item_list = $this->Withdrawal_record_model->gets('*', ['store_id'=>$store_info['id'], 'withdrawal_type'=>1]);
        $data = array(
            'site_name' => $systemInfo['site_name'],
            'index_name' => $systemInfo['index_name'],
            'client_index' => $systemInfo['client_index'],
            'title' => '财务概况_商家中心' . $systemInfo['site_name'],
            'keywords' => $systemInfo['site_keywords'],
            'description' => $systemInfo['site_description'],
            'site_copyright' => $systemInfo['site_copyright'],
            'icp_code' => $systemInfo['icp_code'],
            'html' => $systemInfo['html'],
            'template' => $this->_template,
            'item_info' => $item_info,
            'item_list' => $item_list
        );
        $layout = array(
            'content' => $this->load->view("{$this->_template}/store_account", $data, TRUE)
        );
        $this->load->view('layout/seller_layout', $layout);
        //缓存
        if ($systemInfo['cache'] == 1) {
            $this->output->cache($systemInfo['cache_time']);
        }
    }

    /**
     * 提现
     */
    public function store_draw()
    {
        //判断是否登录
        checkLogin(true);
        $systemInfo = $this->System_model->get('*', array('id' => 1));
        $seller_group_info = $this->Seller_group_model->get('user_id',array('id' => get_cookie('seller_group_id')));
        if ($this->session->userdata('user_id') != $seller_group_info['user_id']) {
            alert_message('仅店主拥有权限');
        }
        $item_info = $this->Stores_model->get('id,account_amount,bank_card_number', array('user_id' => $seller_group_info['user_id']));
        $data = array(
            'site_name' => $systemInfo['site_name'],
            'index_name' => $systemInfo['index_name'],
            'client_index' => $systemInfo['client_index'],
            'title' => '提现_商家中心' . $systemInfo['site_name'],
            'keywords' => $systemInfo['site_keywords'],
            'description' => $systemInfo['site_description'],
            'site_copyright' => $systemInfo['site_copyright'],
            'icp_code' => $systemInfo['icp_code'],
            'html' => $systemInfo['html'],
            'systemInfo' => $systemInfo,
            'template' => $this->_template,
            'item_info' => $item_info,
        );
        $layout = array(
            'content' => $this->load->view("{$this->_template}/store_draw", $data, TRUE)
        );
        $this->load->view('layout/seller_layout', $layout);
        //缓存
        if ($systemInfo['cache'] == 1) {
            $this->output->cache($systemInfo['cache_time']);
        }
    }

    /**
     * 提现
     */
    public function withdraw(){
        //判断是否登录
        checkLoginAjax(true);
        $seller_group_info = $this->Seller_group_model->get('user_id',array('id' => get_cookie('seller_group_id')));
        $store_info = $this->Stores_model->get('id,account_amount,bank_card_number,user_id', array('user_id' => $seller_group_info['user_id']));
        if ($_POST) {
            $amount = $this->input->post('amount', TRUE);
            if (! $this->form_validation->required($amount)) {
                printAjaxError('fail','修改价格不能为空！');
            }
            if (! $this->form_validation->numeric($amount)) {
                printAjaxError('fail','请填写正确的提现金额！');
            }
            if ($store_info['account_amount'] < $amount) {
                printAjaxError('fail','账户余额不足！');
            }
            $systemInfo = $this->System_model->get('withdrawal_commission', array('id' => 1));
            $fields = array(
                'user_id' => $store_info['user_id'],
                'amount' => $amount,
                'arrival_amount' => $amount * (1 - $systemInfo['withdrawal_commission']),
                'balance' => $store_info['account_amount'] - $amount,
                'account' => $store_info['bank_card_number'],
                'type' => '银行卡',
                'withdrawal_type'=>1,
                'store_id'=>$store_info['id']
            );
            $this->load->model('Withdrawal_record_model', "", TRUE);
            if ($this->Withdrawal_record_model->save($fields)) {
                $this->Stores_model->save(['account_amount'=>$store_info['account_amount'] - $amount], ['id'=>$store_info['id']]);
                printAjaxSuccess('success', '提交成功，等待审核！');
            } else {
                printAjaxError('fail',"提现失败！");
            }
        }
    }
}

/* End of file page.php */
/* Location: ./application/client/controllers/page.php */