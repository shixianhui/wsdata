<?php
class User extends CI_Controller {

    private $_table = 'user';
    private $_template = 'user';
    private $_status = array(
        '0' => '<font color="#ff4200">未付款</font>',
        '1' => '<font color="#cc3333">已付款</font>',
        '2' => '<font color="#ff811f">已发货</font>',
        '3' => '<font color="#066601">交易成功</font>',
        '4' => '<font color="#a0a0a0">交易关闭</font>',
    );
    private $_order_type = array(
        '0' => '普通订单',
        '1' => '拼团砍价订单',
        '2' => '限时秒杀订单',
        '3' => '我的竞猜订单',
    );
    private $_sex_arr = array('0' => '保密', '1' => '男', '2' => '女');
    private $_exchange_type = array('1' => '退款', '2' => '退货');
    private $_distributor_arr = array('1' => '城市合伙人', '2' => '店面分销商');
    private $_payment_type_arr = array(
        'order_out' => '订单支付',
        'order_in' => '订单退款',
        'recharge_in' => '充值',
        'recharge_out' => '扣款',
        'presenter_in' => '推广分成',
        'presenter_out' => '推广退款'
    );
    private $_message_type = array('order' => '订单通知', 'system' => '系统通知');
    private $_exchange_reason_arr = array(
        '0' => '无理由退货',
        '1' => '不需要/不想的商品',
        '2' => '其它'
    );
    private $_exchange_status_arr = array(
        '0' => '<font color="red">审核中</font>',
        '1' => '审核未通过',
        '2' => '审核通过',
        '3' => '退款到余额成功',
//        '4' => '原路返回退款成功'
    );

    private $_exchange_status_hide = array(
      'a' => 0,
      'b' => 1,
      'c' => 2,
      'd' => 3,
    );
    private $_arrival_time_arr= array('15天内','16 - 30天','31 - 60天','61 - 90天','91 - 120天');

    public function __construct() {
        parent::__construct();
        $this->load->model('Menu_model', '', TRUE);
        $this->load->model('System_model', '', TRUE);
        $this->load->model('User_model', '', TRUE);
        $this->load->model('Sms_model', '', TRUE);
        $this->load->library('Securitysecoderclass');
        $this->load->library('Form_validation');
    }

    public function index() {
        //判断是否登录
        checkLogin();
        $systemInfo = $this->System_model->get('*', array('id' => 1));
        $userInfo = $this->User_model->get('*', array('user.id' => $this->session->userdata('user_id')));
        //订单
        $item_list = $this->Orders_model->gets('*', "user_id = " . $this->session->userdata('user_id'), 5, 0);
        foreach ($item_list as $key => $order) {
            $item_list[$key]['order_detail_list'] = $this->Orders_detail_model->gets('*', "order_id = {$order['id']}");
        }
        $data = array(
            'site_name' => $systemInfo['site_name'],
            'index_name' => $systemInfo['index_name'],
            'index_url' => $systemInfo['index_url'],
            'client_index' => $systemInfo['client_index'],
            'title' => '个人信息_会员中心' . $systemInfo['site_name'],
            'keywords' => $systemInfo['site_keywords'],
            'description' => $systemInfo['site_description'],
            'site_copyright' => $systemInfo['site_copyright'],
            'icp_code' => $systemInfo['icp_code'],
            'html' => $systemInfo['html'],
            'userInfo' => $userInfo,
            'item_list' => $item_list,
            'status' => $this->_status,
        );
        $layout = array(
            'content' => $this->load->view("{$this->_template}/index", $data, TRUE)
        );
        $this->load->view('layout/user_layout', $layout);
        //缓存
        if ($systemInfo['cache'] == 1) {
            $this->output->cache($systemInfo['cache_time']);
        }
    }

    public function login() {
        $prfUrl = $this->session->userdata('gloabPreUrl') ? $this->session->userdata('gloabPreUrl') : base_url() . "index.php/seller";
        $systemInfo = $this->System_model->get('*', array('id' => 1));
        if ($_POST) {
            $username = $this->input->post('username', TRUE);
            $password = $this->input->post('password', TRUE);
            //$code = $this->input->post('code', TRUE);
            $remember = $this->input->post('remember', TRUE);

            if (!$this->form_validation->required($username)) {
                printAjaxError('username', '输入用户名');
            }
            if (!$this->form_validation->required($password)) {
                printAjaxError('username', '输入密码');
            }
//     	    if (! $this->form_validation->required($code)) {
//     	    	printAjaxError('code', '输入验证码');
//     	    }
// 		    $securitysecoder = new Securitysecoderclass();
// 	        if (! $securitysecoder->check(strtolower($code))) {
// 	            printAjaxError('code_fail', '验证码错误');
// 	        }
            $count = $this->User_model->count(array('lower(username)' => strtolower($username)));
            if (!$count) {
                printAjaxError('fail', "用户不存在，请先注册");
            }
            $userInfo = $this->User_model->login($username, $password);
            if (!$userInfo) {
                printAjaxError('fail', "登录用户名或密码错误,登录失败");
            }
            if ($userInfo['display'] == 0) {
                printAjaxError('fail', "你的账户未激活，若有疑问，请联系网站客服！");
            }
            if ($userInfo['display'] == 2) {
                printAjaxError('fail', "你的账户被冻结，若有疑问，请联系网站客服！");
            }
            // $this->load->model('Stores_model', '', TRUE);
            // if (!$this->Stores_model->count(['status'=>1, 'user_id'=>$userInfo['id']])) {
            //     printAjaxError('fail', "非商户用户禁止登陆");
            // }
//            $ip_arr = getUserIPAddress();
            $fields = array(
                'login_time' => time(),
//                'ip' => $ip_arr[0],
//                'ip_address' => $ip_arr[1]
            );

            $ret_id = $this->User_model->save($fields, array('id' => $userInfo['id']));
            if ($ret_id) {
                $this->_setCookie($userInfo, $remember ? 604800 : 0);
                //登录成功
                printAjaxSuccess('success_login_go', $prfUrl);
            } else {
                printAjaxError('fail', '登录失败！');
            }
        }

        $data = array(
            'site_name' => $systemInfo['site_name'],
            'index_name' => $systemInfo['index_name'],
            'client_index' => $systemInfo['client_index'],
            'title' => '登录' . $systemInfo['site_name'],
            'action_title' => '欢迎登录',
            'keywords' => $systemInfo['site_keywords'],
            'description' => $systemInfo['site_description'],
            'site_copyright' => $systemInfo['site_copyright'],
            'icp_code' => $systemInfo['icp_code'],
            'parent_id' => 0,
            'html' => $systemInfo['html']
        );
        $layout = array(
            'content' => $this->load->view("{$this->_template}/login", $data, TRUE)
        );
        $this->load->view('layout/login_reg_layout', $layout);
        //缓存
        if ($systemInfo['cache'] == 1) {
            $this->output->cache($systemInfo['cache_time']);
        }
    }

    public function weixin_login(){
        $code = $this->input->get("code", true);
        if (empty($code)) {
            exit("DO NOT ACCESS!");
        }
        $appid = 'wx3ee6137b544586f2';
        $appSecret = '09eea83f2d9b69e56959d41fbb216a66';
        $json = file_get_contents("https://api.weixin.qq.com/sns/oauth2/access_token?appid=$appid&secret=$appSecret&code=$code&grant_type=authorization_code");
        $obj = json_decode($json);
        if (isset($obj->errmsg)) {
            exit("invalid code");
        }
        $access_token = $obj->access_token;
        $openid = $obj->openid;
        $result = file_get_contents("https://api.weixin.qq.com/sns/auth?access_token=$access_token&openid=$openid");
        $access_token_obj = json_decode($result);
        if ($access_token_obj->errcode != 0) {
            exit($access_token_obj->errmsg);
        }
        $user_info = file_get_contents("https://api.weixin.qq.com/sns/userinfo?access_token=$access_token&openid=$openid");
        $userinfo = json_decode($user_info, true);
        $exist = $this->User_model->get('*', "wx_unionid = '{$userinfo['unionid']}'");
        $this->first_auth_login('wechat', $exist, $userinfo['nickname'], str_replace('/0', '/132',$userinfo['headimgurl']), $userinfo['sex'], $userinfo['unionid']);
    }

    public function qq_login(){
        require("sdk/authlogin/qqlogin/API/qqConnectAPI.php");
        $qc = new QC();
        $access_token = $qc->qq_callback();
        $openid = $qc->get_openid();
        $qc2 = new QC($access_token, $openid);
        $qq_user_info = $qc2->get_user_info();
        if ($qq_user_info['gender'] == '男') {
            $sex = 1;
        } else if ($qq_user_info['gender'] == '女') {
            $sex = 2;
        } else {
            $sex = 0;
        }
        $exist = $this->User_model->get('*', "qq_unionid = '{$openid}'");
        $is_mobile = is_mobile_request();
        $this->first_auth_login('qq', $exist, $qq_user_info['nickname'], $qq_user_info['figureurl_qq_2'], $sex, $openid, $is_mobile);
    }

    public function reg() {
        $systemInfo = $this->System_model->get('*', array('id' => 1));
        $prfUrl = $this->session->userdata('gloabPreUrl') ? $this->session->userdata('gloabPreUrl') : base_url() . "index.php/user";
        if ($_POST) {
            $username = $this->input->post('mobile', TRUE);
            $password = $this->input->post('password', TRUE);
            $refPassword = $this->input->post('ref_password', TRUE);
            $code = $this->input->post('code', TRUE);
            $smscode = $this->input->post('smscode', TRUE);
            $remember = $this->input->post('remember', TRUE);
            if (!$remember) {
                printAjaxError('fail', '必须同意“服务协议”才能完成注册');
            }
            if (!$this->form_validation->required($username)) {
                printAjaxError('username', '请输入用户名');
            }
            if (!$this->form_validation->valid_mobile($username)) {
                printAjaxError('mobile', '请输入正确的手机号');
            }
//            if (!$this->form_validation->max_length($username, 32)) {
//                printAjaxError('username', '用户名长度不能大于32字符');
//            }
            if ($this->User_model->validateUnique($username)) {
                printAjaxError('username', '用户名已经存在，请换一个');
            }
            if (!$this->form_validation->required($password)) {
                printAjaxError('password', '请输入登录密码');
            }
            if (!$this->form_validation->required($refPassword)) {
                printAjaxError('ref_password', '请输入确认密码');
            }
            if ($password != $refPassword) {
                printAjaxError('ref_password', '前后密码不一致');
            }
            if (!$this->form_validation->valid_password($password)){
                printAjaxError('password', '登录密码至少包含数字跟字母，可以有字符,长度6-20');
            }
            if (!$this->form_validation->required($code)) {
                printAjaxError('code', '请输入验证码');
            }
            $securitysecoder = new Securitysecoderclass();
            if (!$securitysecoder->check(strtolower($code))) {
                printAjaxError('code_fail', '验证码错误');
            }

            $timestamp = time() - 5*60;
            if (!$this->Sms_model->get('id', "smscode = '$smscode' and mobile = $username and add_time > $timestamp")) {
                printAjaxError('smscode', '短信验证码错误或者已过期');
            }
            $addTime = time();
//            $ip_arr = getUserIPAddress();
            $fields = array(
                // 'user_group_id' => 1,
                'username' => $username,
                'nickname'=>substr(md5($username),0,9),
                'login_time' => time(),
//                'ip' => $ip_arr[0],
//                'ip_address' => $ip_arr[1],
                'password' => $this->_createPasswordSALT($username, $addTime, $password),
                'mobile' => $username,
                'add_time' => $addTime,
            );
            $ret = $this->User_model->save($fields);
            if ($ret) {
                $ret = $this->User_model->get('*', array('user.id' => $ret));
                $this->_setCookie($ret, 0);
                printAjaxSuccess('success_store', '注册成功!');
            } else {
                printAjaxError('fail', '注册失败！');
            }
        }

        $data = array(
            'site_name' => $systemInfo['site_name'],
            'index_name' => $systemInfo['index_name'],
            'client_index' => $systemInfo['client_index'],
            'title' => '注册' . $systemInfo['site_name'],
            'action_title' => '欢迎注册',
            'keywords' => $systemInfo['site_keywords'],
            'description' => $systemInfo['site_description'],
            'site_copyright' => $systemInfo['site_copyright'],
            'icp_code' => $systemInfo['icp_code'],
            'parent_id' => 0,
            'html' => $systemInfo['html']
        );
        $layout = array(
            'content' => $this->load->view("{$this->_template}/reg", $data, TRUE)
        );
        $this->load->view('layout/login_reg_layout', $layout);
        //缓存
        if ($systemInfo['cache'] == 1) {
            $this->output->cache($systemInfo['cache_time']);
        }
    }

    /**
     * 注册获取短信验证码
     * @param mobile 手机号
     * @return json
     */
    public function get_reg_sms_code() {
        if ($_POST) {
            $type = $this->input->post('type', TRUE);
            $mobile = $this->input->post('mobile', TRUE);
            $code = $this->input->post('code', TRUE);
            if (!preg_match('/^((\(\d{2,3}\))|(\d{3}\-))?(13|14|15|16|17|18|19)\d{9}$/', $mobile)) {
                printAjaxError('mobile', '请输入正确的手机号');
            }
            if (!$this->form_validation->required($code)) {
                printAjaxError('code', '请输入验证码');
            }
        //    $securitysecoder = new Securitysecoderclass();
        //    if (!$securitysecoder->check(strtolower($code))) {
        //        printAjaxError('code_fail', '验证码错误');
        //    }
            if (!$this->securitysecoderclass->check(strtolower($code))) {
                printAjaxError('code_fail', '验证码错误');
            }
            if ($type == 'reg') {
                $count = $this->User_model->count(array("username" => $mobile));
                if ($count) {
                    printAjaxError('mobile', '此手机号已被使用，请换一个');
                }
            } else if ($type == 'get_pass') {
                $count = $this->User_model->count(array("username" => $mobile));
                if ($count == 0) {
                    printAjaxError('mobile', '您注册的手机号不存在!');
                }
            } else if ($type == 'change_mobile') {
                $count = $this->User_model->count(array("mobile" => $mobile));
                if ($count) {
                    printAjaxError('mobile', '此手机已被使用');
                }
            } else {
                printAjaxError('type', 'type值异常!');
            }

            $add_time = time();
            $diff_time = $add_time - 60;
            $sms_info = $this->Sms_model->get('*', "mobile = '{$mobile}' and add_time > {$diff_time}");
            if ($sms_info) {
                printAjaxError('fail', '操作太频繁，请至少间隔一分钟再发');
            }
            $verify_code = getRandCode(4);
            $sms_content = "【千户万物】验证码：{$verify_code} , 如非本人操作请忽略此信息 ,10分钟内有效 !";
            /*             * *************************半小时限制**************************** */
            $cur_time = $add_time - 1800;
            //30分钟内最多5次
            $count = $this->Sms_model->rowCount("mobile = '{$mobile}' and add_time > {$cur_time} ");
            if ($count >= 4) {
                printAjaxError('fail', '半小时内只能发4次，等一下再来');
            }
            /*             * ************************一天限制*************************** */
            $start_time = strtotime(date('Y-m-d 00:00:00', $add_time));
            $end_time = strtotime(date('Y-m-d 23:59:59', $add_time));
            $count = $this->Sms_model->rowCount("mobile = '{$mobile}' and add_time > {$start_time} and add_time <= {$end_time} ");
            //同一手机一天最多20次
            if ($count >= 15) {
                printAjaxError('fail', '你的手机号发送验证码次数超限，请更换手机号或明天再来');
            }
            $fields = array(
                'mobile' => $mobile,
                'smscode' => $verify_code,
                'sms_content' => $sms_content,
                'add_time' => $add_time
            );
            if (!$this->Sms_model->save($fields)) {
                printAjaxError('fail', '发送验证码失败');
            }
            $reponse = send_sms($mobile, $sms_content);
            if ($reponse > 0) {
                printAjaxSuccess('success', '验证码已经发送，注意查看手机短信');
            } else {
                printAjaxError('fail', '验证码发送失败，请重试');
            }
        }
    }

    public function logout() {
        $this->_deleteCookie();
        redirect(base_url() . 'index.php/user/login.html');
    }

    //找回密码
    public function get_pass() {
        $systemInfo = $this->System_model->get('*', array('id' => 1));
        if ($_POST) {
            $username = $this->input->post('username', TRUE);
            $code = $this->input->post('code', TRUE);
            $password = $this->input->post('password', TRUE);
            $refPassword = $this->input->post('ref_password', TRUE);
            $smscode = $this->input->post('smscode', TRUE);
            if (!$username) {
                printAjaxError('username', "手机号不能为空");
            }
            if (!$code) {
                printAjaxError('code', "验证码不能为空");
            }
            if (!$this->form_validation->required($password)) {
                printAjaxError('password', '请输入新密码');
            }
            if (!$this->form_validation->required($refPassword)) {
                printAjaxError('ref_password', '请输入确认密码');
            }
            if ($password != $refPassword) {
                printAjaxError('ref_password', '前后密码不一致');
            }
            $securitysecoder = new Securitysecoderclass();
            if (!$securitysecoder->check(strtolower($code))) {
                printAjaxError('code_fail', '验证码错误');
            }
            $userInfo = $this->User_model->get('id,username', array('lower(username)' => strtolower($username)));
            if (!$userInfo) {
                printAjaxError('fail', "手机号不存在");
            }
            $timestamp = time();
            if (!$this->Sms_model->get('id', "smscode = '$smscode' and mobile = $username and add_time > $timestamp - 15*60")) {
                printAjaxError('smscode', '短信验证码错误或者已过期');
            }
            $fields = array(
                'password' => $this->User_model->getPasswordSalt($userInfo['username'], $refPassword)
            );
            if ($this->User_model->save($fields, array('id' => $userInfo['id']))) {
                printAjaxSuccess(getBaseUrl(false, 'user/login.html', 'user/login.html', $systemInfo['client_index']), '密码修改成功');
            } else {
                printAjaxError('fail', '密码修改失败');
            }
        }

        $data = array(
            'site_name' => $systemInfo['site_name'],
            'index_name' => $systemInfo['index_name'],
            'client_index' => $systemInfo['client_index'],
            'title' => '找回密码' . $systemInfo['site_name'],
            'action_title' => '找回密码',
            'keywords' => $systemInfo['site_keywords'],
            'description' => $systemInfo['site_description'],
            'site_copyright' => $systemInfo['site_copyright'],
            'icp_code' => $systemInfo['icp_code'],
            'html' => $systemInfo['html']
        );
        $layout = array(
            'content' => $this->load->view("{$this->_template}/get_pass", $data, TRUE)
        );
        $this->load->view('layout/login_reg_layout', $layout);
        //缓存
        if ($systemInfo['cache'] == 1) {
            $this->output->cache($systemInfo['cache_time']);
        }
    }

    //修改个人资料
    public function my_change_user_info() {
        $this->session->set_userdata(array("gloabPreUrl" => base_url() . 'index.php/' . uri_string()));
        //判断是否登录
        checkLogin();
        $systemInfo = $this->System_model->get('*', array('id' => 1));
        //当前位置
        $location = "<a href='index.php/user.html'>会员中心</a> > <a href='javascript:void(0);'>我的账户</a> > 完善资料";
        $item_info = $this->User_model->get('*', array('user.id' => $this->session->userdata('user_id')));
        if ($_POST) {
            $nickname = $this->input->post("nickname", TRUE);
            $real_name = $this->input->post("real_name", TRUE);
            $sex = $this->input->post("sex", TRUE);
            $email = $this->input->post("email", TRUE);
            $province_id = $this->input->post("province_id", TRUE);
            $city_id = $this->input->post("city_id", TRUE);
            $area_id = $this->input->post("area_id", TRUE);
            $path = $this->input->post("path", TRUE);
            if (!$nickname) {
                printAjaxError('nickname', '昵称不能为空');
            }
            if (!$real_name) {
                printAjaxError('real_name', '姓名不能为空');
            }
            if (!$sex) {
                printAjaxError('sex', '请选择性别');
            }
            if (!(intval($province_id) && intval($city_id) && intval($area_id))) {
                printAjaxError('area', '请选择省市区');
            }
            $txt_address_str = '';
            $area_info = $this->Area_model->get('name', array('id' => $province_id));
            if ($area_info) {
                $txt_address_str .= mb_substr($area_info['name'], 0, -1, 'utf-8');
            }
            if($province_id != 110000 && $province_id != 310000 && $province_id != 120000 && $province_id != 500000) {
                $area_info = $this->Area_model->get('name', array('id' => $city_id));
                if ($area_info) {
                    $txt_address_str .= ' '.mb_substr($area_info['name'], 0, -1, 'utf-8');
                }
            }else{
                $area_info = $this->Area_model->get('name', array('id' => $area_id));
                if ($area_info) {
                    $txt_address_str .= ' '.mb_substr($area_info['name'], 0, -1, 'utf-8');
                }
            }
//            if (!$email) {
//            	if (!preg_match('/([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)/', $email)) {
//            		printAjaxError('email', '请输入正确的邮箱');
//            	}
//            }

            $fields = array(
                'nickname' => $nickname,
                'real_name' => $real_name,
                'sex' => $sex,
                //'email' => $email
                'province_id' => $province_id,
                'city_id' => $city_id,
                'area_id' => $area_id,
                'path' => $path,
                'txt_address' => $txt_address_str
            );
            $ret = $this->User_model->save($fields, array('id' => $this->session->userdata('user_id')));
            if ($ret) {
                printAjaxSuccess('success', '修改成功');
            } else {
                printAjaxError('fail', '修改失败');
            }
        }
        $areaList = $this->Area_model->gets('*', array('parent_id' => 0));
        $data = array(
            'site_name' => $systemInfo['site_name'],
            'index_name' => $systemInfo['index_name'],
            'index_url' => $systemInfo['index_url'],
            'client_index' => $systemInfo['client_index'],
            'title' => '完善资料_会员中心' . $systemInfo['site_name'],
            'keywords' => $systemInfo['site_keywords'],
            'description' => $systemInfo['site_description'],
            'site_copyright' => $systemInfo['site_copyright'],
            'icp_code' => $systemInfo['icp_code'],
            'item_info' => $item_info,
            'location' => $location,
            'sex_arr' => $this->_sex_arr,
            'html' => $systemInfo['html'],
            'areaList' => $areaList,
        );
        $layout = array(
            'content' => $this->load->view('user/my_change_user_info', $data, TRUE)
        );
        $this->load->view('layout/user_layout', $layout);
        //缓存
        if ($systemInfo['cache'] == 1) {
            $this->output->cache($systemInfo['cache_time']);
        }
    }

    public function my_change_mobile() {
        //判断是否登录
        checkLogin();
        $systemInfo = $this->System_model->get('*', array('id' => 1));
        $user_info = $this->User_model->get('*', "id = " . $this->session->userdata('user_id'));
        if ($_POST) {
            $mobile = $this->input->post('mobile', TRUE);
            $code = $this->input->post('code', TRUE);
            $smscode = $this->input->post('smscode', TRUE);
            if (!$this->form_validation->required($mobile)) {
                printAjaxError('mobile', '请输入手机号码');
            }
            if (!preg_match("/1[356789]\d{9}/", $mobile)) {
                printAjaxError('username', '手机号码格式不正确');
            }
            if (!$this->form_validation->required($code)) {
                printAjaxError('code', '请输入验证码');
            }
            $securitysecoder = new Securitysecoderclass();
            if (!$securitysecoder->check(strtolower($code))) {
                printAjaxError('code_fail', '图片验证码错误');
            }
            $timestamp = time();
            if (!$this->Sms_model->get('id', "smscode = '$smscode' and mobile = $mobile and add_time > $timestamp - 15*60")) {
                printAjaxError('smscode', '短信验证码错误或者已过期');
            }
            $result = $this->User_model->save(array('mobile' => $mobile), array('id' => $this->session->userdata('user_id')));
            if ($result) {
                printAjaxSuccess(getBaseUrl(false, '', 'user/my_change_user_info.html', $systemInfo['client_index']), '修改成功');
            } else {
                printAjaxError('fail', '修改失败');
            }
        }
        $data = array(
            'site_name' => $systemInfo['site_name'],
            'index_name' => $systemInfo['index_name'],
            'index_url' => $systemInfo['index_url'],
            'client_index' => $systemInfo['client_index'],
            'title' => '更换手机号码',
            'keywords' => $systemInfo['site_keywords'],
            'description' => $systemInfo['site_description'],
            'site_copyright' => $systemInfo['site_copyright'],
            'icp_code' => $systemInfo['icp_code'],
            'html' => $systemInfo['html'],
            'user_info' => $user_info
        );
        $layout = array(
            'content' => $this->load->view('user/my_change_mobile', $data, TRUE)
        );
        $this->load->view('layout/user_layout', $layout);
        //缓存
        if ($systemInfo['cache'] == 1) {
            $this->output->cache($systemInfo['cache_time']);
        }
    }

    public function my_change_email() {
        //判断是否登录
        checkLogin();
        $systemInfo = $this->System_model->get('*', array('id' => 1));
        $user_info = $this->User_model->get('*', "id = " . $this->session->userdata('user_id'));
        if ($_POST) {
            $email = $this->input->post('email', TRUE);
            $code = $this->input->post('code', TRUE);
            $email_code = $this->input->post('email_code', TRUE);

            if (!$this->form_validation->required($email)) {
                printAjaxError('email', '请输入邮箱');
            }
            if (!preg_match("/([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)/", $email)) {
                printAjaxError('email', '邮箱格式不正确');
            }
            if (!$this->form_validation->required($code)) {
                printAjaxError('code', '请输入图片验证码');
            }
            if (!$this->form_validation->required($email_code)) {
                printAjaxError('email_code', '请输入邮件验证码');
            }
            $securitysecoder = new Securitysecoderclass();
            if (!$securitysecoder->check(strtolower($code))) {
                printAjaxError('code_fail', '图片验证码错误');
            }
            $timestamp = time();
            if (!$this->Sms_email_model->get('id', "code = {$email_code} and email = '{$email}' and add_time > {$timestamp} - 15*60")) {
                printAjaxError('smscode', '邮箱验证码错误或者已过期');
            }
            $result = $this->User_model->save(array('email' => $email, 'is_check_email' => 1), array('id' => $this->session->userdata('user_id')));
            if ($result) {
                printAjaxSuccess(getBaseUrl(false, '', 'user/my_change_user_info.html', $systemInfo['client_index']), '修改成功');
            } else {
                printAjaxError('fail', '修改失败');
            }
        }
        $data = array(
            'site_name' => $systemInfo['site_name'],
            'index_name' => $systemInfo['index_name'],
            'index_url' => $systemInfo['index_url'],
            'client_index' => $systemInfo['client_index'],
            'title' => '邮箱认证',
            'keywords' => $systemInfo['site_keywords'],
            'description' => $systemInfo['site_description'],
            'site_copyright' => $systemInfo['site_copyright'],
            'icp_code' => $systemInfo['icp_code'],
            'html' => $systemInfo['html'],
            'user_info' => $user_info
        );
        $layout = array(
            'content' => $this->load->view('user/my_change_email', $data, TRUE)
        );
        $this->load->view('layout/user_layout', $layout);
        //缓存
        if ($systemInfo['cache'] == 1) {
            $this->output->cache($systemInfo['cache_time']);
        }
    }

    public function send_email() {
        if ($_POST) {
            $email = $this->input->post('email', true);
            $code = $this->input->post('code', true);

            if (!$this->form_validation->required($email)) {
                printAjaxError('mobile', '请输入邮箱');
            }
            if (!$this->form_validation->required($code)) {
                printAjaxError('mobile', '图片验证码不能为空');
            }
            $securitysecoder = new Securitysecoderclass();
            if (!$securitysecoder->check(strtolower($code))) {
                printAjaxError('code_fail', '图片验证码错误');
            }
            $count = $this->User_model->count(array('email' => $email));
            if ($count > 0) {
                printAjaxError('fail', '此邮箱已被绑定，请换其他邮箱');
            }
            $add_time = time();
            $verify_code = getRandCode(4);
            $sms_content = "您的邮箱验证码是：{$verify_code}。请不要把验证码泄露给其他人。如非本人操作，可不用理会！";
            /*             * ************************一天限制*************************** */
            $start_time = strtotime(date('Y-m-d 00:00:00', $add_time));
            $end_time = strtotime(date('Y-m-d 23:59:59', $add_time));
            $count = $this->Sms_email_model->rowCount("email = '{$email}' and add_time > {$start_time} and add_time <= {$end_time} ");
            //同一邮箱一天最多20次
            if ($count >= 20) {
                printAjaxError('fail', '你的邮箱发送邮件次数超限，请更换邮箱或明天再来');
            }
            $fields = array(
                'email' => $email,
                'code' => $verify_code,
                'content' => $sms_content,
                'add_time' => $add_time
            );
            if (!$this->Sms_email_model->save($fields)) {
                printAjaxError('fail', '发送验证码失败');
            }
            $message = <<<EOT
                   <div>
                           <p style="text-indent: 2em;">您的邮箱验证码是：{$verify_code}。请不要把验证码泄露给其他人。如非本人操作，可不用理会！</p>
                           <p style="text-indent: 2em;">以上邮件验证码有效时间为15分钟</p>
                           <p style="text-indent: 2em;">------------------------------------------------------------------------------------------</p>
                           <p style="text-indent: 2em;">此邮件为系统所发，请勿直接回复。</p>
                           <p style="text-indent: 2em;">蚁立网</p>
                    </div>
EOT;

            //发送邮件
            $this->load->library('email');
            $this->config->load('mail_config', TRUE);
            $mailConfig = $this->config->item('mail_config');
            $this->email->initialize($mailConfig);
            $this->email->from($mailConfig['smtp_user'], '蚁立网');
            $this->email->to($email);
            $this->email->subject('蚁立网 用户绑定邮箱');
            $this->email->message($message);
            if (@$this->email->send()) {
                //记录发送时间
                printAjaxSuccess('success', '邮件发送成功，请注意查收');
            } else {
                printAjaxError('success', '邮件发送失败，请重试');
            }
        }
    }

    /**
     * 修改密码
     */
    public function my_change_pass() {
        $this->session->set_userdata(array("gloabPreUrl" => base_url() . 'index.php/' . uri_string()));
        //判断是否登录
        checkLogin();
        $systemInfo = $this->System_model->get('*', array('id' => 1));
        //当前位置
        $location = "<a href='index.php/user.html'>会员中心</a> > <a href='javascript:void(0);'>我的账户</a> > 修改登录密码";
        if ($_POST) {
            $old_password = $this->input->post('old_password', TRUE);
            $new_password = $this->input->post('new_password', TRUE);
            $con_password = $this->input->post('con_password', TRUE);

            //检测
            if (!$this->form_validation->required($old_password)) {
                printAjaxError('old_password', '旧密码不能为空');
            }
            if (!$this->form_validation->required($new_password)) {
                printAjaxError('new_password', '新密码不能为空');
            }
            if (!$this->form_validation->required($con_password)) {
                printAjaxError('con_password', '确认新密码不能为空');
            }

            if ($new_password != $con_password) {
                printAjaxError('con_password', '新密码前后不一致');
            }
            if (strlen($con_password) < 6) {
                printAjaxError('con_password', '密码长度不能小于6位');
            }

            //验证密码是否正确
            $user_info = $this->User_model->get('password, username', array('user.id' => $this->session->userdata('user_id')));
            if (!$user_info) {
                printAjaxError('fail', '此用户不存在');
            }
            if ($user_info['password'] != $this->User_model->getPasswordSalt($user_info['username'], $old_password)) {
                printAjaxError('old_password', '旧密码错误');
            }
            if ($this->User_model->getPasswordSalt($user_info['username'], $con_password) == $user_info['password']) {
                printAjaxError('old_password', '新密码不能与旧密码一样');
            }
            $fields = array(
                'password' => $this->User_model->getPasswordSalt($user_info['username'], $new_password)
            );
            if ($this->User_model->save($fields, array('id' => $this->session->userdata('user_id')))) {
                $this->_deleteCookie();
                printAjaxSuccess('success', '登录密码修改成功,请重新登录');
            } else {
                printAjaxError('fail', '登录密码修改失败');
            }
        }

        $data = array(
            'site_name' => $systemInfo['site_name'],
            'index_name' => $systemInfo['index_name'],
            'index_url' => $systemInfo['index_url'],
            'client_index' => $systemInfo['client_index'],
            'title' => '修改登录密码_会员中心' . $systemInfo['site_name'],
            'keywords' => $systemInfo['site_keywords'],
            'description' => $systemInfo['site_description'],
            'site_copyright' => $systemInfo['site_copyright'],
            'icp_code' => $systemInfo['icp_code'],
            'location' => $location,
            'html' => $systemInfo['html']
        );
        $layout = array(
            'content' => $this->load->view('user/my_change_pass', $data, TRUE)
        );
        $this->load->view('layout/user_layout', $layout);
        //缓存
        if ($systemInfo['cache'] == 1) {
            $this->output->cache($systemInfo['cache_time']);
        }
    }

    /**
     * 修改支付密码
     */
    public function my_change_pay_pass() {
        $this->session->set_userdata(array("gloabPreUrl" => base_url() . 'index.php/' . uri_string()));
        //判断是否登录
        checkLogin();
        $systemInfo = $this->System_model->get('*', array('id' => 1));
        //当前位置
        $location = "<a href='index.php/user.html'>会员中心</a> > <a href='javascript:void(0);'>我的账户</a> > 修改支付密码";
        $item_info = $this->User_model->get('pay_password,username', array('user.id' => $this->session->userdata('user_id')));
        if ($_POST) {
            $oldPassword = $this->input->post('old_password', TRUE);
            $newPassword = $this->input->post('new_password', TRUE);
            $conPassword = $this->input->post('con_password', TRUE);

            //检测
            if ($item_info && $item_info['pay_password']) {
                if (!$this->form_validation->required($oldPassword)) {
                    printAjaxError('old_password', '原始密码不能为空');
                }
            }
            if (!$this->form_validation->required($newPassword)) {
                printAjaxError('new_password', '新密码不能为空');
            }
            if (!$this->form_validation->required($conPassword)) {
                printAjaxError('con_password', '确认新密码不能为空');
            }

            if ($newPassword != $conPassword) {
                printAjaxError('con_password', '新密码前后不一致');
            }
            if (strlen($conPassword) < 6) {
                printAjaxError('con_password', '密码长度不能小于6位');
            }

            if ($item_info && $item_info['pay_password']) {
                if ($item_info['pay_password'] != $this->User_model->getPasswordSalt($item_info['username'], $oldPassword)) {
                    printAjaxError('old_password', '原始密码错误');
                }
            }
            if ($this->User_model->getPasswordSalt($item_info['username'], $conPassword) == $item_info['pay_password']) {
                printAjaxError('old_password', '旧密码不能与新密码一样');
            }
            $fields = array(
                'pay_password' => $this->User_model->getPasswordSalt($item_info['username'], $newPassword)
            );
            if ($this->User_model->save($fields, array('id' => $this->session->userdata('user_id')))) {
                printAjaxSuccess('success', '支付密码修改成功');
            } else {
                printAjaxError('fail', '支付密码修改失败');
            }
        }

        $data = array(
            'site_name' => $systemInfo['site_name'],
            'index_name' => $systemInfo['index_name'],
            'index_url' => $systemInfo['index_url'],
            'client_index' => $systemInfo['client_index'],
            'title' => '修改支付密码_会员中心' . $systemInfo['site_name'],
            'keywords' => $systemInfo['site_keywords'],
            'description' => $systemInfo['site_description'],
            'site_copyright' => $systemInfo['site_copyright'],
            'icp_code' => $systemInfo['icp_code'],
            'location' => $location,
            'item_info' => $item_info,
            'html' => $systemInfo['html']
        );
        $layout = array(
            'content' => $this->load->view('user/my_change_pay_pass', $data, TRUE)
        );
        $this->load->view('layout/user_layout', $layout);
        //缓存
        if ($systemInfo['cache'] == 1) {
            $this->output->cache($systemInfo['cache_time']);
        }
    }

    //产品收藏列表
    public function my_get_favorite_list($page = 0) {
        $this->session->set_userdata(array("gloabPreUrl" => base_url() . 'index.php/' . uri_string()));
        //判断是否登录
        checkLogin();
        $systemInfo = $this->System_model->get('*', array('id' => 1));
        //当前位置
        $location = "<a href='index.php/user.html'>会员中心</a> > <a href='javascript:void(0);'>我是消费者</a> > 我的收藏";

        $strWhere = array('favorite.user_id' => $this->session->userdata('user_id'), 'type' => 'product');
        //分页
        $paginationCount = $this->Favorite_model->rowCount('product', $strWhere);
        $this->config->load('pagination_config', TRUE);
        $paginationConfig = $this->config->item('pagination_config');
        $paginationConfig['base_url'] = base_url() . "index.php/user/my_get_favorite_list/";
        $paginationConfig['total_rows'] = $paginationCount;
        $paginationConfig['uri_segment'] = 3;
        $paginationConfig['per_page'] = 20;
        $this->pagination->initialize($paginationConfig);
        $pagination = $this->pagination->create_links();

        $item_list = $this->Favorite_model->gets('product', $strWhere, $paginationConfig['per_page'], $page);

        $data = array(
            'site_name' => $systemInfo['site_name'],
            'index_name' => $systemInfo['index_name'],
            'index_url' => $systemInfo['index_url'],
            'client_index' => $systemInfo['client_index'],
            'title' => '收藏产品_会员中心' . $systemInfo['site_name'],
            'keywords' => $systemInfo['site_keywords'],
            'description' => $systemInfo['site_description'],
            'site_copyright' => $systemInfo['site_copyright'],
            'icp_code' => $systemInfo['icp_code'],
            'item_list' => $item_list,
            'pagination' => $pagination,
            'pageCount' => ceil($paginationCount / $paginationConfig['per_page']),
            'perPage' => $paginationConfig['per_page'],
            'location' => $location,
            'html' => $systemInfo['html']
        );
        $layout = array(
            'content' => $this->load->view('user/my_get_favorite_list', $data, TRUE)
        );
        $this->load->view('layout/user_layout', $layout);
        //缓存
        if ($systemInfo['cache'] == 1) {
            $this->output->cache($systemInfo['cache_time']);
        }
    }

    public function my_favorite_store_list($page = 0) {
        $this->session->set_userdata(array("gloabPreUrl" => base_url() . 'index.php/' . uri_string()));
        //判断是否登录
        checkLogin();
        $systemInfo = $this->System_model->get('*', array('id' => 1));
        $strWhere = array('favorite.user_id' => $this->session->userdata('user_id'), 'type' => 'store');
        //分页
        $paginationCount = $this->Favorite_model->rowCount('store', $strWhere);
        $this->config->load('pagination_config', TRUE);
        $paginationConfig = $this->config->item('pagination_config');
        $paginationConfig['base_url'] = base_url() . "index.php/user/my_favorite_store_list/";
        $paginationConfig['total_rows'] = $paginationCount;
        $paginationConfig['uri_segment'] = 3;
        $paginationConfig['per_page'] = 10;
        $this->pagination->initialize($paginationConfig);
        $pagination = $this->pagination->create_links();

        $item_list = $this->Favorite_model->gets('store', $strWhere, $paginationConfig['per_page'], $page);
        if ($item_list) {
            foreach ($item_list as $key => $item) {
                $product_list = $this->Product_model->gets('title,id,path,sell_price', array('store_id' => $item['item_id']), 4, 0);
                $item_list[$key]['product_list'] = $product_list;
            }
        }

        $data = array(
            'site_name' => $systemInfo['site_name'],
            'index_name' => $systemInfo['index_name'],
            'index_url' => $systemInfo['index_url'],
            'client_index' => $systemInfo['client_index'],
            'title' => '收藏店铺_会员中心' . $systemInfo['site_name'],
            'keywords' => $systemInfo['site_keywords'],
            'description' => $systemInfo['site_description'],
            'site_copyright' => $systemInfo['site_copyright'],
            'icp_code' => $systemInfo['icp_code'],
            'item_list' => $item_list,
            'pagination' => $pagination,
            'pageCount' => ceil($paginationCount / $paginationConfig['per_page']),
            'perPage' => $paginationConfig['per_page'],
            'html' => $systemInfo['html']
        );
        $layout = array(
            'content' => $this->load->view('user/my_favorite_store_list', $data, TRUE)
        );
        $this->load->view('layout/user_layout', $layout);
        //缓存
        if ($systemInfo['cache'] == 1) {
            $this->output->cache($systemInfo['cache_time']);
        }
    }

    public function my_delete_favorite() {
        //判断是否登录
        checkLoginAjax();
        if ($_POST) {
            $id = $this->input->post('id', TRUE);
            $type = $this->input->post('type', TRUE);
            if (!$id) {
                printAjaxError('fail', '操作异常');
            }
            if ($this->Favorite_model->delete(array('id' => $id, 'user_id' => $this->session->userdata('user_id'), 'type' => $type))) {
                printAjaxData(array('id' => $id));
            } else {
                printAjaxError('fail', '删除失败！');
            }
        }
    }

    public function my_delete_address() {
        checkLoginAjax();
        if ($_POST) {
            $uid = $this->session->userdata('user_id');
            $address_ids = trim($this->input->post('address_ids', true), ',');
            $result = $this->User_address_model->delete("id in ($address_ids) and user_id = $uid");
            if ($result) {
                printAjaxSuccess('success', '删除成功!');
            } else {
                printAjaxError('fail', '删除失败！');
            }
        }
    }

    //投诉列表
    public function my_get_user_probleme($page = 0) {
        $this->session->set_userdata(array("gloabPreUrl" => base_url() . 'index.php/' . uri_string()));
        //判断是否登录
        checkLogin();
        $systemInfo = $this->System_model->get('*', array('id' => 1));
        //当前位置
        $location = "<a href='" . base_url() . "index.php/user'>会员中心</a> > <a>我的交易</a> > 我的退换货";
        $status = array(0 => '待审核', 1 => '审核未通过', 2 => '审核通过');

        //分页
        $paginationCount = $this->Guestbook_model->rowCount(array('user_id' => $this->session->userdata('user_id')));
        $this->config->load('pagination_config', TRUE);
        $paginationConfig = $this->config->item('pagination_config');
        $paginationConfig['base_url'] = base_url() . "index.php/user/my_get_user_probleme/";
        $paginationConfig['total_rows'] = $paginationCount;
        $paginationConfig['uri_segment'] = 3;
        $paginationConfig['per_page'] = 10;
        $this->pagination->initialize($paginationConfig);
        $pagination = $this->pagination->create_links();

        $item_list = $this->Guestbook_model->gets('*', array('user_id' => $this->session->userdata('user_id')), $paginationConfig['per_page'], $page);

        $data = array(
            'site_name' => $systemInfo['site_name'],
            'index_name' => $systemInfo['index_name'],
            'index_url' => $systemInfo['index_url'],
            'client_index' => $systemInfo['client_index'],
            'title' => '投诉列表' . $systemInfo['site_name'],
            'keywords' => $systemInfo['site_keywords'],
            'description' => $systemInfo['site_description'],
            'site_copyright' => $systemInfo['site_copyright'],
            'icp_code' => $systemInfo['icp_code'],
            'status' => $status,
            'item_list' => $item_list,
            'pagination' => $pagination,
            'pageCount' => ceil($paginationCount / $paginationConfig['per_page']),
            'perPage' => $paginationConfig['per_page'],
            'location' => $location,
            'html' => $systemInfo['html']
        );
        $layout = array(
            'content' => $this->load->view('user/my_get_user_probleme', $data, TRUE)
        );
        $this->load->view('layout/user_layout', $layout);
        //缓存
        if ($systemInfo['cache'] == 1) {
            $this->output->cache($systemInfo['cache_time']);
        }
    }

    //我要投诉
    public function my_save_probleme() {
        $prfUrl = $this->session->userdata('gloabPreUrl');
        //判断是否登录
        checkLogin();
        $systemInfo = $this->System_model->get('*', array('id' => 1));
        //当前位置
        $location = "<a href='index.php/user'>会员中心</a> > <a>我的交易</a> > <a href='index.php/user/my_get_user_probleme.html'>我的投诉</a> > 我要投诉";

        if ($_POST) {
            $content = $this->input->post("content", TRUE);
            if (!$content) {
                printAjaxError('fail', '投诉内容不能为空');
            }

            $fields = array(
                'content' => $content,
                'add_time' => time(),
                'user_id' => $this->session->userdata('user_id')
            );
            if ($this->Guestbook_model->save($fields)) {
                printAjaxSuccess($prfUrl, '投诉成功');
            } else {
                printAjaxError('fail', '投诉失败');
            }
        }

        $data = array(
            'site_name' => $systemInfo['site_name'],
            'index_name' => $systemInfo['index_name'],
            'index_url' => $systemInfo['index_url'],
            'client_index' => $systemInfo['client_index'],
            'title' => '我要投诉' . $systemInfo['site_name'],
            'keywords' => $systemInfo['site_keywords'],
            'description' => $systemInfo['site_description'],
            'site_copyright' => $systemInfo['site_copyright'],
            'icp_code' => $systemInfo['icp_code'],
            'location' => $location,
            'prfUrl' => $prfUrl,
            'html' => $systemInfo['html']
        );
        $layout = array(
            'content' => $this->load->view('user/my_save_probleme', $data, TRUE)
        );
        $this->load->view('layout/user_layout', $layout);
        //缓存
        if ($systemInfo['cache'] == 1) {
            $this->output->cache($systemInfo['cache_time']);
        }
    }

    //退换货列表
    public function my_get_exchange_list($status = 'all', $page = 0) {
        $this->session->set_userdata(array("gloabPreUrl" => base_url() . 'index.php/' . uri_string()));
        //判断是否登录
        checkLogin();
        $systemInfo = $this->System_model->get('*', array('id' => 1));
        //当前位置
        $location = "<a href='index.php/user.html'>会员中心</a> > <a href='javascript:void(0);'>我是消费者</a> > 退换货管理";
        $user_id = $this->session->userdata('user_id');
        $strWhere = "user_id = {$user_id}";
        if($status != 'all'){
            $strWhere .= " and status = {$this->_exchange_status_hide[$status]}";
        }
        //分页
        $paginationCount = $this->Exchange_model->rowCount($strWhere);
        $this->config->load('pagination_config', TRUE);
        $paginationConfig = $this->config->item('pagination_config');
        $paginationConfig['base_url'] = base_url() . "index.php/user/my_get_exchange_list/$status";
        $paginationConfig['total_rows'] = $paginationCount;
        $paginationConfig['uri_segment'] = 4;
        $paginationConfig['per_page'] = 10;
        $this->pagination->initialize($paginationConfig);
        $pagination = $this->pagination->create_links();

        $item_list = $this->Exchange_model->gets('*', $strWhere, $paginationConfig['per_page'], $page);
        $orders_detail_info = '';
        if ($item_list){
            foreach ($item_list as $key => $item) {
                $orders_detail_info = $this->Orders_detail_model->get('*', array('id' => $item['orders_detail_id']));
//                if ($orders_detail_info){
//                    $orders_detail_info['price_total'] = number_format($orders_detail_info['buy_number']*$orders_detail_info['buy_price'], 2, '.', '');
//                }
                $item_list[$key]['orders_detail_info'] = $orders_detail_info;
            }
        }
        $data = array(
            'site_name' => $systemInfo['site_name'],
            'index_name' => $systemInfo['index_name'],
            'index_url' => $systemInfo['index_url'],
            'client_index' => $systemInfo['client_index'],
            'title' => '退换货管理_会员中心' . $systemInfo['site_name'],
            'keywords' => $systemInfo['site_keywords'],
            'description' => $systemInfo['site_description'],
            'site_copyright' => $systemInfo['site_copyright'],
            'icp_code' => $systemInfo['icp_code'],
            'exchange_status_arr' => $this->_exchange_status_arr,
            'exchange_reason_arr' => $this->_exchange_reason_arr,
            'select_status' => $status,
            'item_list' => $item_list,
            'pagination' => $pagination,
            'pageCount' => ceil($paginationCount / $paginationConfig['per_page']),
            'perPage' => $paginationConfig['per_page'],
            'location' => $location,
            'html' => $systemInfo['html']
        );
        $layout = array(
            'content' => $this->load->view('user/my_get_exchange_list', $data, TRUE)
        );
        $this->load->view('layout/user_layout', $layout);
        //缓存
        if ($systemInfo['cache'] == 1) {
            $this->output->cache($systemInfo['cache_time']);
        }
    }

    public function my_delete_problem() {
        //判断是否登录
        checkLoginAjax();
        if ($_POST) {
            $id = $this->input->post("id", TRUE);
            if (!$id) {
                printAjaxError('fail', '操作异常');
            }

            if ($this->Guestbook_model->delete(array('id' => $id, 'user_id' => $this->session->userdata('user_id')))) {
                printAjaxData(array('id' => $id));
            } else {
                printAjaxError('fail', '删除失败！');
            }
        }
    }

    //申请退换货
    public function my_save_exchange($orders_detail_id = NULL) {
        $prfUrl = $this->session->userdata('gloabPreUrl');
        //判断是否登录
        checkLogin();
        $user_id = $this->session->userdata('user_id');
        $orders_detail_info = $this->Orders_detail_model->get('*', array('id' => $orders_detail_id));
        $exchange_info = $this->Exchange_model->get('*', array('orders_id'=>$orders_detail_info['order_id'], 'orders_detail_id' => $orders_detail_id, 'user_id' => $user_id));

        if ($_POST) {
            checkLoginAjax();
            $exchange_reason_id = $this->input->post('exchange_reason_id', TRUE);
            $price = $this->input->post('price', TRUE);
            $content = $this->input->post('content', TRUE);
            $batch_path_ids = $this->input->post('batch_path_ids', TRUE);
            if ($batch_path_ids) {
                $batch_path_ids = implode($batch_path_ids, '_');
            }

            if ($exchange_reason_id == '') {
                printAjaxError('exchange_reason_id', '请选择退款原因');
            }
            if (!$price) {
                printAjaxError('price', '请输入退款金额');
            }
            if (!$content) {
                printAjaxError('content', '请输入退款说明');
            }
            $user_info = $this->User_model->get('id, username', array('id' => $user_id));
            if (!$user_info) {
                printAjaxError('fail', '退款异常');
            }
            $item_info = $this->Orders_model->get('id,order_number,seller_id,store_id,store_name', "id = {$orders_detail_info['order_id']} and user_id = {$user_id} and status in (1,2) ");
            if (!$item_info) {
                printAjaxError('fail', '此订单信息不存在');
            }
            if ($exchange_info) {
                if ($exchange_info['status'] >= 3) {
                    printAjaxError('fail', '已成功退款，不能重复操作');
                } else {
                    if ($exchange_info['status'] != 1) {
                        printAjaxError('fail', '退款申请审核中，请耐心等待');
                    }
                }
            }

            $fields = array(
                'user_id' => $user_info['id'],
                'username' => $user_info['username'],
                'orders_id' => $item_info['id'],
                'orders_detail_id' => $orders_detail_info['id'],
                'order_num' => $item_info['order_number'],
                'seller_id' => $item_info['seller_id'],
                'store_id' => $item_info['store_id'],
                'store_name' => $item_info['store_name'],
                'add_time' => time(),
                'status' => 0,
                'content' => $content,
                'price' => $price,
                'exchange_type' => 1,
                'exchange_reason_id' => $exchange_reason_id,
                'batch_path_ids' => $batch_path_ids ? $batch_path_ids . '_' : ''
            );
            if (!$this->Exchange_model->save($fields, $exchange_info ? array('id' => $exchange_info['id']) : NULL)) {
                printAjaxError('fail', '提交申请失败');
            }
            printAjaxSuccess($prfUrl, '提交申请成功');
        }
        //凭证图片
        $attachment_list = NULL;
        if ($exchange_info && $exchange_info['batch_path_ids']) {
            $tmp_atm_ids = preg_replace(array('/^_/', '/_$/', '/_/'), array('', '', ','), $exchange_info['batch_path_ids']);
            $attachment_list = $this->Attachment_model->gets2($tmp_atm_ids);
        }
        $systemInfo = $this->System_model->get('*', array('id' => 1));

        $orders_info = $this->Orders_model->get('*', "id = {$orders_detail_info['order_id']} and user_id = {$user_id} and status in (1,2,3)");
        if (!$orders_info) {
            $data = array(
                'user_msg' => '此订单信息不存在',
                'user_url' => $prfUrl
            );
            $this->session->set_userdata($data);
            redirect(base_url() . 'index.php/message/index');
        }
        $orders_detail_count = $this->Orders_detail_model->rowCount(array('order_id'=>$orders_info['id']));
        if ($orders_info['status'] == 1 && $orders_detail_count == 1){
            $orders_detail_info['price_total'] = number_format($orders_detail_info['buy_number']*$orders_detail_info['buy_price'] + $orders_info['postage_price'], 2, '.', '');
        }else{
            $orders_detail_info['price_total'] = number_format($orders_detail_info['buy_number']*$orders_detail_info['buy_price'], 2, '.', '');
        }


        $data = array(
            'site_name' => $systemInfo['site_name'],
            'index_name' => $systemInfo['index_name'],
            'index_url' => $systemInfo['index_url'],
            'client_index' => $systemInfo['client_index'],
            'title' => '退款' . $systemInfo['site_name'],
            'keywords' => $systemInfo['site_keywords'],
            'description' => $systemInfo['site_description'],
            'site_copyright' => $systemInfo['site_copyright'],
            'icp_code' => $systemInfo['icp_code'],
            'html' => $systemInfo['html'],
            'orders_info' => $orders_info,
            'status_arr' => $this->_status,
            'attachment_list' => $attachment_list,
            'orders_detail_info' => $orders_detail_info,
            'exchange_info' => $exchange_info,
            'template' => $this->_template,
            'prfUrl' => $prfUrl,
            'exchange_reason_arr' => $this->_exchange_reason_arr,
        );
        $layout = array(
            'content' => $this->load->view('user/my_save_exchange', $data, TRUE)
        );
        $this->load->view('layout/user_layout', $layout);
        //缓存
        if ($systemInfo['cache'] == 1) {
            $this->output->cache($systemInfo['cache_time']);
        }
    }

    //查看退换货详情
    public function my_view_exchange($orders_detail_id = NULL) {
        $prfUrl = $this->session->userdata('gloabPreUrl');
        $user_id = $this->session->userdata('user_id');
        checkLogin();
        $systemInfo = $this->System_model->get('*', array('id' => 1));
        //当前位置
        $location = "<a href='index.php/user.html'>会员中心</a> > <a href='javascript:void(0);'>我是消费者</a> > <a href='javascript:void(0);'>我的订单</a> > 查看退款进度";
        $exchange_info = $this->Exchange_model->get('*', array('orders_detail_id' => $orders_detail_id, 'user_id' => $user_id));
        if (!$exchange_info) {
            $data = array(
                'user_msg' => '此订单不存在退款记录',
                'user_url' => $prfUrl
            );
            $this->session->set_userdata($data);
            redirect(base_url() . 'index.php/message/index');
        }
        //凭证图片
        $attachment_list = NULL;
        if ($exchange_info['batch_path_ids']) {
            $tmp_atm_ids = preg_replace(array('/^_/', '/_$/', '/_/'), array('', '', ','), $exchange_info['batch_path_ids']);
            $attachment_list = $this->Attachment_model->gets2($tmp_atm_ids);
        }

        $item_info = $this->Orders_model->get('*', "id = {$exchange_info['orders_id']} and user_id = {$user_id} and status <> 0 ");
        if (!$item_info) {
            $data = array(
                'user_msg' => '此订单信息不存在',
                'user_url' => $prfUrl
            );
            $this->session->set_userdata($data);
            redirect(base_url() . 'index.php/message/index');
        }
        $orders_detail_info = $this->Orders_detail_model->get('*', array('id' => $exchange_info['orders_detail_id']));
        $orders_detail_info['price_total'] = number_format($orders_detail_info['buy_number']*$orders_detail_info['buy_price'], 2, '.', '');
        $data = array(
            'site_name' => $systemInfo['site_name'],
            'index_name' => $systemInfo['index_name'],
            'index_url' => $systemInfo['index_url'],
            'client_index' => $systemInfo['client_index'],
            'title' => '查看退款进度' . $systemInfo['site_name'],
            'keywords' => $systemInfo['site_keywords'],
            'description' => $systemInfo['site_description'],
            'site_copyright' => $systemInfo['site_copyright'],
            'icp_code' => $systemInfo['icp_code'],
            'html' => $systemInfo['html'],
            'item_info' => $item_info,
            'orders_detail_info' => $orders_detail_info,
            'exchange_info' => $exchange_info,
            'attachment_list' => $attachment_list,
            'status_arr' => $this->_status,
            'exchange_reason_arr' => $this->_exchange_reason_arr,
            'exchange_status_arr' => $this->_exchange_status_arr,
            'template' => $this->_template,
            'prfUrl' => $prfUrl,
            'location' => $location
        );
        $layout = array(
            'content' => $this->load->view('user/my_view_exchange', $data, TRUE)
        );
        $this->load->view('layout/user_layout', $layout);
        //缓存
        if ($systemInfo['cache'] == 1) {
            $this->output->cache($systemInfo['cache_time']);
        }
    }

    //删除退换货申请
    public function my_delete_exchange() {
        //判断是否登录
        checkLoginAjax();
        if ($_POST) {
            $id = $this->input->post("id", TRUE);
            if (!$id) {
                printAjaxError('fail', '操作异常');
            }
            if ($this->Exchange_model->delete(array('id' => $id, 'status' => 0, 'user_id' => $this->session->userdata('user_id')))) {
                printAjaxSuccess('success', '取消成功！');
            } else {
                printAjaxError('fail', '取消失败！');
            }
        }
    }

    //收货地址列表
    public function my_get_user_address() {
        $this->session->set_userdata(array("gloabPreUrl" => base_url() . 'index.php/' . uri_string()));
        //判断是否登录
        checkLogin();
        $systemInfo = $this->System_model->get('*', array('id' => 1));
        //当前位置
        $location = "<a href='index.php/user.html'>会员中心</a> > <a href='javascript:void(0);'>我是消费者</a> > 收货地址";
        $user_address_list = $this->User_address_model->gets('*', array('user_id' => $this->session->userdata('user_id')));

        $data = array(
            'site_name' => $systemInfo['site_name'],
            'index_name' => $systemInfo['index_name'],
            'index_url' => $systemInfo['index_url'],
            'client_index' => $systemInfo['client_index'],
            'title' => '收货地址_会员中心' . $systemInfo['site_name'],
            'keywords' => $systemInfo['site_keywords'],
            'description' => $systemInfo['site_description'],
            'site_copyright' => $systemInfo['site_copyright'],
            'icp_code' => $systemInfo['icp_code'],
            'user_address_list' => $user_address_list,
            'location' => $location,
            'html' => $systemInfo['html']
        );
        $layout = array(
            'content' => $this->load->view('user/my_get_user_address', $data, TRUE)
        );
        $this->load->view('layout/user_layout', $layout);
        //缓存
        if ($systemInfo['cache'] == 1) {
            $this->output->cache($systemInfo['cache_time']);
        }
    }

    //实名认证
    public function my_user_auth() {
        $this->session->set_userdata(array("gloabPreUrl" => base_url() . 'index.php/' . uri_string()));
        //判断是否登录
        checkLogin();
        $systemInfo = $this->System_model->get('*', array('id' => 1));
        //当前位置
        $location = "<a href='index.php/user.html'>会员中心</a> > <a href='javascript:void(0);'>我是消费者</a> > 实名认证";
        $user_address_list = $this->User_address_model->gets('*', array('user_id' => $this->session->userdata('user_id')));

        $data = array(
            'site_name' => $systemInfo['site_name'],
            'index_name' => $systemInfo['index_name'],
            'index_url' => $systemInfo['index_url'],
            'client_index' => $systemInfo['client_index'],
            'title' => '实名认证_会员中心' . $systemInfo['site_name'],
            'keywords' => $systemInfo['site_keywords'],
            'description' => $systemInfo['site_description'],
            'site_copyright' => $systemInfo['site_copyright'],
            'icp_code' => $systemInfo['icp_code'],
            'user_address_list' => $user_address_list,
            'location' => $location,
            'html' => $systemInfo['html']
        );
        $layout = array(
            'content' => $this->load->view('user/my_user_auth', $data, TRUE)
        );
        $this->load->view('layout/user_layout', $layout);
        //缓存
        if ($systemInfo['cache'] == 1) {
            $this->output->cache($systemInfo['cache_time']);
        }
    }

    //编辑收货地址
    public function my_save_address($id = NULL) {
        $prfUrl = $this->session->userdata('gloabPreUrl');
        //判断是否登录
        checkLogin();
        $systemInfo = $this->System_model->get('*', array('id' => 1));
        $tabTitle = '添加';
        if ($id) {
            $tabTitle = '修改';
        }
        //当前位置
        $location = "<a href='index.php/user'>会员中心</a> > <a>个人信息管理</a> > <a href='index.php/user/my_get_user_address.html'>收货地址 </a> > {$tabTitle}收货地址";
        if ($_POST) {
            $buyer_name = $this->input->post('buyer_name', TRUE);
            $mobile = $this->input->post('mobile', TRUE);
            $phone = $this->input->post('phone', TRUE);
            $zip = $this->input->post('zip', TRUE);
            $province_id = $this->input->post('province_id', TRUE);
            $city_id = $this->input->post('city_id', TRUE);
            $area_id = $this->input->post('area_id', TRUE);
            $address = $this->input->post('address', TRUE);
            $is_default = $this->input->post('is_default', TRUE);

            if (!$buyer_name) {
                printAjaxError('buyer_name', '姓名不能为空');
            }
            if (!$mobile) {
                printAjaxError('mobile', '手机号不能为空');
            }
            if (!preg_match("/^((\(\d{2,3}\))|(\d{3}\-))?(13|14|15|16|17|18|19)\d{9}$/", $mobile)) {
                printAjaxError('mobile', '请输入正确的手机号');
            }
            if ($phone) {
                if (!preg_match("/^((\(\d{2,3}\))|(\d{3}\-))?(\(0\d{2,3}\)|0\d{2,3}-)?[1-9]\d{6,7}(\-\d{1,4})?$/", $phone)) {
                    printAjaxError('phone', '请输入正确的固定电话');
                }
            }
            if (!$province_id) {
                printAjaxError('province_id', '选择省');
            }
            if (!$city_id) {
                printAjaxError('city_id', '选择市');
            }
            if (!$area_id) {
                printAjaxError('area_id', '选择区/县');
            }
            if (!$address) {
                printAjaxError('address', '请填写详细地址');
            }
            $txt_address_str = '';
            $area_info = $this->Area_model->get('name', array('id' => $province_id));
            if ($area_info) {
                $txt_address_str .= $area_info['name'];
            }
            $area_info = $this->Area_model->get('name', array('id' => $city_id));
            if ($area_info) {
                $txt_address_str .= ' ' . $area_info['name'];
            }
            $area_info = $this->Area_model->get('name', array('id' => $area_id));
            if ($area_info) {
                $txt_address_str .= ' ' . $area_info['name'];
            }
            if ($zip && !preg_match("/^[1-9]\d{5}$/", $zip)) {
                printAjaxError('zip', '请输入正确的邮编');
            }

            $fields = array(
                'buyer_name' => $buyer_name,
                'mobile' => $mobile,
                'phone' => $phone,
                'zip' => $zip,
                'province_id' => $province_id,
                'city_id' => $city_id,
                'area_id' => $area_id,
                'txt_address' => $txt_address_str,
                'address' => $address,
                'is_default' => $is_default,
                'user_id' => $this->session->userdata('user_id'),
            );
            //当收货地址为一个时，设为默认
            if ($this->User_address_model->rowCount(array('user_id' => $this->session->userdata('user_id'))) == 0) {
                $fields['is_default'] = 1;
            }
            if ($this->User_address_model->rowCount(array('user_id' => $this->session->userdata('user_id'))) > 10) {
                printAjaxError('fail', '最多只能设置十个收货地址');
            }
            if ($is_default == 1) {
                $this->User_address_model->save(array('is_default' => 0), array('user_id' => $this->session->userdata('user_id'), 'is_default' => 1));
            }
            if ($this->User_address_model->save($fields, $id ? array('id' => $id) : NULL)) {
                printAjaxSuccess($prfUrl, '收货地址操作成功');
            } else {
                printAjaxError('fail', '收货地址操作失败');
            }
        }
        $item_info = $this->User_address_model->get('*', array('user_id' => $this->session->userdata('user_id'), 'id' => $id));
        $areaList = $this->Area_model->gets('*', array('parent_id' => 0));
        $data = array(
            'site_name' => $systemInfo['site_name'],
            'index_name' => $systemInfo['index_name'],
            'index_url' => $systemInfo['index_url'],
            'client_index' => $systemInfo['client_index'],
            'title' => '编辑收货地址' . $systemInfo['site_name'],
            'keywords' => $systemInfo['site_keywords'],
            'description' => $systemInfo['site_description'],
            'site_copyright' => $systemInfo['site_copyright'],
            'icp_code' => $systemInfo['icp_code'],
            'item_info' => $item_info,
            'areaList' => $areaList,
            'tabTitle' => $tabTitle,
            'location' => $location,
            'prfUrl' => $prfUrl,
            'html' => $systemInfo['html']
        );
        $layout = array(
            'content' => $this->load->view('user/my_save_address', $data, TRUE)
        );
        $this->load->view('layout/user_layout', $layout);
        //缓存
        if ($systemInfo['cache'] == 1) {
            $this->output->cache($systemInfo['cache_time']);
        }
    }

    public function my_change_default_for_user_address() {
        //判断是否登录
        checkLoginAjax();
        if ($_POST) {
            $is_default = $this->input->post('is_default', TRUE);
            $id = $this->input->post('id', TRUE);

            if ($is_default != NULL && $is_default != "" && $id) {
                //设置成默认
                if ($is_default) {
                    $fields = array(
                        'is_default' => $is_default
                    );
                    $this->User_address_model->save($fields, array('id' => $id, 'user_id' => $this->session->userdata('user_id')));

                    $fields = array(
                        'is_default' => 0
                    );
                    if ($this->User_address_model->save($fields, "user_id = " . $this->session->userdata('user_id') . " and id <> {$id} ")) {
                        printAjaxSuccess('success', '操作成功');
                    } else {
                        printAjaxError('fail', '操作失败');
                    }
                } else {
                    $fields = array(
                        'is_default' => $is_default
                    );
                    if ($this->User_address_model->save($fields, array('id' => $id, 'user_id' => $this->session->userdata('user_id')))) {
                        printAjaxSuccess('success', '操作成功');
                    } else {
                        printAjaxError('fail', '操作失败');
                    }
                }
            } else {
                printAjaxError('fail', '操作异常');
            }
        }
    }

    public function my_score_list($page = 0) {
        //判断是否登录
        checkLogin();
        $systemInfo = $this->System_model->get('*', array('id' => 1));
        $score_setting = $this->Score_setting_model->get('*', array('id' => 1));
        $strWhere = "user_id = '" . $this->session->userdata('user_id') . "'";
        //当前位置
        $location = "<a href='index.php/user'>会员中心</a> > <a>我的积分</a> > 消费记录";
        //分页
        $paginationCount = $this->Score_model->rowCount($strWhere);
        $this->config->load('pagination_config', TRUE);
        $paginationConfig = $this->config->item('pagination_config');
        $paginationConfig['base_url'] = base_url() . "index.php/user/my_score_list/";
        $paginationConfig['total_rows'] = $paginationCount;
        $paginationConfig['uri_segment'] = 3;
        $paginationConfig['per_page'] = 10;
        $this->pagination->initialize($paginationConfig);
        $pagination = $this->pagination->create_links();

        $item_list = $this->Score_model->gets($strWhere, $paginationConfig['per_page'], $page);
        $user_info = $this->User_model->get('score', array('user.id' => $this->session->userdata('user_id')));

        $data = array(
            'site_name' => $systemInfo['site_name'],
            'index_name' => $systemInfo['index_name'],
            'index_url' => $systemInfo['index_url'],
            'client_index' => $systemInfo['client_index'],
            'title' => '积分消费记录' . $systemInfo['site_name'],
            'keywords' => $systemInfo['site_keywords'],
            'description' => $systemInfo['site_description'],
            'site_copyright' => $systemInfo['site_copyright'],
            'icp_code' => $systemInfo['icp_code'],
            'item_list' => $item_list,
            'html' => $systemInfo['html'],
            'location' => $location,
            'user_info' => $user_info,
            'pagination' => $pagination,
            'pageCount' => ceil($paginationCount / $paginationConfig['per_page']),
            'perPage' => $paginationConfig['per_page'],
            'score_setting' => $score_setting,
        );
        $layout = array(
            'content' => $this->load->view('user/my_score_list', $data, TRUE)
        );
        $this->load->view('layout/user_layout', $layout);
        //缓存
        if ($systemInfo['cache'] == 1) {
            $this->output->cache($systemInfo['cache_time']);
        }
    }

    public function my_financial_list($type = 'all', $page = 0) {
        //判断是否登录
        checkLogin();
        $systemInfo = $this->System_model->get('*', array('id' => 1));
        $strWhere = "user_id = '" . $this->session->userdata('user_id') . "'";
        //当前位置
        $location = "<a href='index.php/user'>会员中心</a> > <a>预存款</a> > 财务记录";
        if ($type != 'all'){
            $strWhere .= " and type = '{$type}'";
        }
        //分页
        $paginationCount = $this->Financial_model->rowCount($strWhere);
        $this->config->load('pagination_config', TRUE);
        $paginationConfig = $this->config->item('pagination_config');
        $paginationConfig['base_url'] = base_url() . "index.php/user/my_financial_list/$type";
        $paginationConfig['total_rows'] = $paginationCount;
        $paginationConfig['uri_segment'] = 4;
        $paginationConfig['per_page'] = 10;
        $this->pagination->initialize($paginationConfig);
        $pagination = $this->pagination->create_links();
        $item_list = $this->Financial_model->gets($strWhere, $paginationConfig['per_page'], $page);
        $user_info = $this->User_model->get('total', array('user.id' => $this->session->userdata('user_id')));

        $data = array(
            'site_name' => $systemInfo['site_name'],
            'index_name' => $systemInfo['index_name'],
            'index_url' => $systemInfo['index_url'],
            'client_index' => $systemInfo['client_index'],
            'title' => '财务记录' . $systemInfo['site_name'],
            'keywords' => $systemInfo['site_keywords'],
            'description' => $systemInfo['site_description'],
            'site_copyright' => $systemInfo['site_copyright'],
            'icp_code' => $systemInfo['icp_code'],
            'item_list' => $item_list,
            'html' => $systemInfo['html'],
            'location' => $location,
            'user_info' => $user_info,
            'pagination' => $pagination,
            'pageCount' => ceil($paginationCount / $paginationConfig['per_page']),
            'perPage' => $paginationConfig['per_page'],
            'select_status' => $type
        );
        $layout = array(
            'content' => $this->load->view('user/my_financial_list', $data, TRUE)
        );
        $this->load->view('layout/user_layout', $layout);
        //缓存
        if ($systemInfo['cache'] == 1) {
            $this->output->cache($systemInfo['cache_time']);
        }
    }

    //评论
    public function my_comment_save($order_id = 0) {
        //判断是否登录
        checkLogin();
        $prfUrl = $this->session->userdata('gloabPreUrl') ? $this->session->userdata('gloabPreUrl') : base_url() . "index.php/order/my_order_index.html";
        $systemInfo = $this->System_model->get('*', array('id' => 1));
        $order_id = intval($order_id);
        $order_info = $this->Orders_model->get('*', array('id' => $order_id, 'user_id' => $this->session->userdata('user_id'), 'status' => 3, 'is_comment_to_seller' => 0));
        if (empty($order_info)) {
            $data = array(
                'user_msg' => '不存在要评价的订单',
                'user_url' => base_url() . "index.php/order/my_order_index.html"
            );
            $this->session->set_userdata($data);
            redirect(base_url() . 'index.php/message/index');
        }
        $user_info = $this->User_model->get('username,nickname', array('id' => $this->session->userdata('user_id')));
        if ($_POST) {
            $orders_detail_id = $this->input->post('orders_detail_id', TRUE);
            $des_grade = intval($this->input->post('des_grade', TRUE));
            $serve_grade = intval($this->input->post('serve_grade', TRUE));
            $express_grade = intval($this->input->post('express_grade', TRUE));
//            foreach ($orders_detail_id as $ls) {
//                $evaluate = $this->input->post('evaluate_' . $ls, TRUE);
//                $content = $this->input->post('content_' . $ls, TRUE);
//                $is_anonymous = $this->input->post('is_anonymous_' . $ls, TRUE);
//                if (!$this->form_validation->required($evaluate)) {
//                    printAjaxError('evaluate', '请选择好评、中评、差评的一项');
//                }
//                if ($evaluate == 2 || $evaluate == 3) {
//                    if (!$this->form_validation->required($content)) {
//                        printAjaxError('content', '选择中评、差评的一项评价不能为空');
//                    }
//                }
//            }
            if (empty($des_grade)) {
                printAjaxError('des_grade', '请给宝贝与描述相符打分');
            }
            if (empty($serve_grade)) {
                printAjaxError('serve_grade', '请给卖家的服务态度打分');
            }
            if (empty($express_grade)) {
                printAjaxError('express_grade', '请给卖家发货的速度打分');
            }
            foreach ($orders_detail_id as $ls) {
                $evaluate = $this->input->post('evaluate_' . $ls, TRUE);
                $content = $this->input->post('content_' . $ls, TRUE);
                $is_anonymous = $this->input->post('is_anonymous_' . $ls, TRUE);
                $batch_path_ids = $this->input->post('batch_path_ids_' . $ls, TRUE);
                if (!$this->form_validation->required($evaluate)) {
                    printAjaxError('evaluate', '请选择好评、中评、差评的一项');
                }
                if ($evaluate == 2 || $evaluate == 3) {
                    if (!$this->form_validation->required($content)) {
                        printAjaxError('content', '选择中评、差评的一项评价不能为空');
                    }
                }
                if($batch_path_ids){
                    $batch_path_ids = implode('_', $batch_path_ids);
                    $batch_path_ids .= '_';
                }
                $order_detail = $this->Orders_detail_model->get('*', array('id' => $ls));
                $fields = array(
                    'order_number' => $order_info['order_number'],
                    'product_id' => $order_detail['product_id'],
                    'user_id' => $this->session->userdata('user_id'),
                    'username' => $user_info['username'] ? $user_info['username'] : $user_info['nickname'],
                    'store_id' => $order_info['store_id'],
                    'content' => $content,
                    'product_title' => $order_detail['product_title'],
                    'path' => $order_detail['path'],
                    'batch_path_ids' => $batch_path_ids,
                    'evaluate' => $evaluate,
                    'is_anonymous' => $is_anonymous ? 1 : 0,
                    'add_time' => time(),
                );
                $result = $this->Comment_model->save($fields);
                if ($result) {
                    $store_info = $this->Store_model->get('evaluate_a,evaluate_b,evaluate_c,des_grade,serve_grade,express_grade', array('id' => $order_info['store_id']));
                    if ($evaluate == 1) {
                        $this->Store_model->save(array('evaluate_a' => $store_info['evaluate_a'] + 1), array('id' => $order_info['store_id']));
                    }
                    if ($evaluate == 2) {
                        $this->Store_model->save(array('evaluate_b' => $store_info['evaluate_b'] + 1), array('id' => $order_info['store_id']));
                    }
                    if ($evaluate == 3) {
                        $this->Store_model->save(array('evaluate_c' => $store_info['evaluate_c'] + 1), array('id' => $order_info['store_id']));
                    }
                }
            }
            $store_info = $this->Store_model->get('evaluate_a,evaluate_b,evaluate_c,des_grade,serve_grade,express_grade', array('id' => $order_info['store_id']));
            $data = array(
                'order_id'=>$order_id,
                'user_id'=>$this->session->userdata('user_id'),
                'store_id'=>$order_info['store_id'],
                'add_time'=>time(),
                'des_grade'=>$des_grade,
                'serve_grade'=>$serve_grade,
                'express_grade'=>$express_grade,
            );
            $this->Comment_store_model->save($data);
            $this->Store_model->save(array('des_grade' => $store_info['des_grade'] + $des_grade, 'serve_grade' => $store_info['serve_grade'] + $serve_grade, 'express_grade' => $store_info['express_grade'] + $express_grade), array('id' => $order_info['store_id']));
            $this->Orders_model->save(array('is_comment_to_seller' => 1), array('id' => $order_id));
            printAjaxSuccess($prfUrl, '评价成功');
        }

        $orders_detail = $this->Orders_detail_model->gets('*', array('order_id' => $order_id));
        $data = array(
            'site_name' => $systemInfo['site_name'],
            'index_name' => $systemInfo['index_name'],
            'index_url' => $systemInfo['index_url'],
            'client_index' => $systemInfo['client_index'],
            'title' => '评论' . $systemInfo['site_name'],
            'keywords' => $systemInfo['site_keywords'],
            'description' => $systemInfo['site_description'],
            'site_copyright' => $systemInfo['site_copyright'],
            'icp_code' => $systemInfo['icp_code'],
            'html' => $systemInfo['html'],
            'order_info' => $order_info,
            'orders_detail' => $orders_detail,
        );
        $layout = array(
            'content' => $this->load->view('user/my_comment_save', $data, TRUE)
        );
        $this->load->view('layout/user_layout', $layout);
        //缓存
        if ($systemInfo['cache'] == 1) {
            $this->output->cache($systemInfo['cache_time']);
        }
    }

    //评论列表
    public function my_comment_view($order_id = 0) {
        //判断是否登录
        checkLogin();
        $systemInfo = $this->System_model->get('*', array('id' => 1));
        //当前位置
        $location = "<a href='index.php/user.html'>会员中心</a> > <a href='javascript:void(0);'>我是消费者</a> > 商品评价";
        $order_id = intval($order_id);
        $order_info = $this->Orders_model->get('*', array('id' => $order_id, 'user_id' => $this->session->userdata('user_id'), 'status' => 3, 'is_comment_to_seller' => 1));
        if (empty($order_info)) {
            $data = array(
                'user_msg' => '不存在已评价的订单',
                'user_url' => base_url() . "index.php/order/my_order_index.html"
            );
            $this->session->set_userdata($data);
            redirect(base_url() . 'index.php/message/index');
        }
        $orders_detail_list = $this->Orders_detail_model->gets('*', array('order_id' => $order_id));
        if ($orders_detail_list){
            foreach ($orders_detail_list as $key => $value){
                $comment_info = $this->Comment_model->get('*',array('order_number'=>$order_info['order_number'], 'product_id'=>$value['product_id']));
                if($comment_info['is_reply']){
                    $store_reply_info = $this->Store_reply_comment_model->get('evaluate,content,add_time',array('comment_id'=>$comment_info['id']));
                    $comment_info['reply_evaluate'] = $store_reply_info['evaluate'];
                    $comment_info['reply_content'] = $store_reply_info['content'];
                    $comment_info['reply_time'] = $store_reply_info['add_time'];
                }
                $attachment_list = NULL;
                if($comment_info['batch_path_ids']){
                    $tmp_atm_ids = preg_replace(array('/^_/', '/_$/', '/_/'), array('', '', ','), $comment_info['batch_path_ids']);
                    $attachment_list = $this->Attachment_model->gets2($tmp_atm_ids);
                }
                $orders_detail_list[$key]['attachment_list'] = $attachment_list;
                $orders_detail_list[$key]['comment_info'] = $comment_info;
            }
        }
        $comment_store_info = $this->Comment_store_model->get('*', array('order_id' => $order_id, 'user_id' => $this->session->userdata('user_id')));
        $data = array(
            'site_name' => $systemInfo['site_name'],
            'index_name' => $systemInfo['index_name'],
            'index_url' => $systemInfo['index_url'],
            'client_index' => $systemInfo['client_index'],
            'title' => '商品评价_会员中心' . $systemInfo['site_name'],
            'keywords' => $systemInfo['site_keywords'],
            'description' => $systemInfo['site_description'],
            'site_copyright' => $systemInfo['site_copyright'],
            'icp_code' => $systemInfo['icp_code'],
            'html' => $systemInfo['html'],
            'location' => $location,
            'orders_detail_list' => $orders_detail_list,
            'comment_store_info' => $comment_store_info,
        );
        $layout = array(
            'content' => $this->load->view('user/my_comment_view', $data, TRUE)
        );
        $this->load->view('layout/user_layout', $layout);
        //缓存
        if ($systemInfo['cache'] == 1) {
            $this->output->cache($systemInfo['cache_time']);
        }
    }

    //评论列表
    public function my_get_comment_list($page = 0) {
        //判断是否登录
        checkLogin();
        $systemInfo = $this->System_model->get('*', array('id' => 1));
        //当前位置
        $location = "<a href='index.php/user.html'>会员中心</a> > <a href='javascript:void(0);'>我是消费者</a> > 商品评价";
       $strWhere = array('user_id' => $this->session->userdata('user_id'));
        //分页
        $paginationCount = $this->Comment_model->rowCount($strWhere);
        $this->config->load('pagination_config', TRUE);
        $paginationConfig = $this->config->item('pagination_config');
        $paginationConfig['base_url'] = base_url() . "index.php/user/my_get_comment_list/";
        $paginationConfig['total_rows'] = $paginationCount;
        $paginationConfig['uri_segment'] = 3;
        $paginationConfig['per_page'] = 10;
        $this->pagination->initialize($paginationConfig);
        $pagination = $this->pagination->create_links();
        $comment_list = $this->Comment_model->gets("*", $strWhere, $paginationConfig['per_page'], $page);
        if ($comment_list){
            foreach ($comment_list as $key=>$comment){
                $attachment_list = NULL;
                if($comment['batch_path_ids']){
                    $tmp_atm_ids = preg_replace(array('/^_/', '/_$/', '/_/'), array('', '', ','), $comment['batch_path_ids']);
                    $attachment_list = $this->Attachment_model->gets2($tmp_atm_ids);
                }
                $comment_list[$key]['attachment_list'] = $attachment_list;
            }
        }
        $data = array(
            'site_name' => $systemInfo['site_name'],
            'index_name' => $systemInfo['index_name'],
            'index_url' => $systemInfo['index_url'],
            'client_index' => $systemInfo['client_index'],
            'title' => '商品评价_会员中心' . $systemInfo['site_name'],
            'keywords' => $systemInfo['site_keywords'],
            'description' => $systemInfo['site_description'],
            'site_copyright' => $systemInfo['site_copyright'],
            'icp_code' => $systemInfo['icp_code'],
            'html' => $systemInfo['html'],
            'location' => $location,
            'pagination' => $pagination,
            'pageCount' => ceil($paginationCount / $paginationConfig['per_page']),
            'comment_list' => $comment_list
        );
        $layout = array(
            'content' => $this->load->view('user/my_get_comment_list', $data, TRUE)
        );
        $this->load->view('layout/user_layout', $layout);
        //缓存
        if ($systemInfo['cache'] == 1) {
            $this->output->cache($systemInfo['cache_time']);
        }
    }

    //我的拼团活动
    public function my_group_purchase_list($is_draft = 0,$page = 0) {
        $this->session->set_userdata(array("gloabPreUrl" => base_url() . 'index.php/' . uri_string()));
        //判断是否登录
        checkLogin();
        $systemInfo = $this->System_model->get('*', array('id' => 1));
        $user_id = $this->session->userdata('user_id');
        $strWhere = "group_purchase.is_draft = {$is_draft} and group_purchase.user_id = {$user_id}";
        //分页
        $paginationCount = $this->Group_purchase_model->count_join_user($strWhere);
        $this->config->load('pagination_config', TRUE);
        $paginationConfig = $this->config->item('pagination_config');
        $paginationConfig['base_url'] = base_url() . "index.php/user/my_group_purchase_list/{$is_draft}/";
        $paginationConfig['total_rows'] = $paginationCount;
        $paginationConfig['uri_segment'] = 4;
        $paginationConfig['per_page'] = 10;
        $this->pagination->initialize($paginationConfig);
        $pagination = $this->pagination->create_links();
        $select = "group_purchase.*";
        $item_list = $this->Group_purchase_model->gets_join_user($select, $strWhere, $paginationConfig['per_page'], $page);
        if ($item_list) {
            foreach ($item_list as $key => $value) {
                $attachment_list = array();
                if ($value['batch_path_ids']){
                    $tmp_atm_ids = preg_replace(array('/^_/', '/_$/', '/_/'), array('', '', ','), $value['batch_path_ids']);
                    $attachment_list = $this->Attachment_model->gets3($tmp_atm_ids);
                }

                $item_list[$key]['path'] = $attachment_list ? $attachment_list[0]['path'] : '';

                $item_list[$key]['title'] = my_substr($value['title'],28);
            }
        }

        $data = array(
            'site_name' => $systemInfo['site_name'],
            'index_name' => $systemInfo['index_name'],
            'index_url' => $systemInfo['index_url'],
            'client_index' => $systemInfo['client_index'],
            'title' => '我的抢团活动_会员中心' . $systemInfo['site_name'],
            'keywords' => $systemInfo['site_keywords'],
            'description' => $systemInfo['site_description'],
            'site_copyright' => $systemInfo['site_copyright'],
            'icp_code' => $systemInfo['icp_code'],
            'item_list' => $item_list,
            'pagination' => $pagination,
            'pageCount' => ceil($paginationCount / $paginationConfig['per_page']),
            'perPage' => $paginationConfig['per_page'],
            'html' => $systemInfo['html'],
            'is_draft'=>$is_draft
        );
        $layout = array(
            'content' => $this->load->view('user/my_group_purchase_list', $data, TRUE)
        );
        $this->load->view('layout/user_layout', $layout);
        //缓存
        if ($systemInfo['cache'] == 1) {
            $this->output->cache($systemInfo['cache_time']);
        }
    }

    //删除拼团
    public function delete_my_group_purchase()
    {
        checkLoginAjax();
        $user_id = $this->session->userdata('user_id');
        if ($_POST){
            $id = $this->input->post('id',TRUE);
            if (!$id){
                printAjaxError('fail','参数异常！');
            }
            $item_info = $this->Group_purchase_model->get('id,enroll_num,ptkj_id',array('id'=>$id,'user_id'=>$user_id));
            if (!$item_info){
                printAjaxError('fail','拼团信息不存在！');
            }

            if($item_info['enroll_num'] || $item_info['ptkj_id']){
                printAjaxError('fail','该拼团已有报名，不能删除！');
            }
            if ($this->Group_purchase_model->delete(array('id'=>$id))){
                printAjaxSuccess('success','删除成功！');
            }
        }

        printAjaxError('fail','删除失败！');
    }

    //添加拼团活动
    public function my_group_purchase_save($id = 0) {
        //判断是否登录
        checkLogin();
        $systemInfo = $this->System_model->get('*', array('id' => 1));
        $user_id = $this->session->userdata('user_id');
        $item_info = $this->Group_purchase_model->get('*',array('id'=>$id,'user_id'=>$user_id));
        if ($_POST){
            $category_ids = $this->input->post('category_ids', TRUE);
            $title = $this->input->post('title', TRUE);
            $abstract = unhtml($this->input->post('abstract'));
            $batch_path_ids = $this->input->post('batch_path_ids', TRUE);
            $brand_name = $this->input->post('brand_name', TRUE);
            $price = $this->input->post('price', TRUE);
            $url = $this->input->post('url', TRUE);
            $arrival_time = $this->input->post('arrival_time', TRUE);
            $shop_address = $this->input->post('shop_address', TRUE);
            $username = $this->input->post('username', TRUE);
            $mobile   = $this->input->post('mobile', TRUE);
            $province_id = $this->input->post('province_id',TRUE);
            $city_id = $this->input->post('city_id',TRUE);
            $area_id = $this->input->post('area_id',TRUE);
            $is_draft = $this->input->post('is_draft',TRUE);

            //产品分类－$category_ids
            $category_id_1 = 0;
            $category_id_2 = 0;
            $category_id_3 = 0;
            $category_ids_arr = explode(',', $category_ids);
            if ($category_ids_arr) {
                if (count($category_ids_arr) >= 1) {
                    $category_id_1 = $category_ids_arr[0];
                }
                if (count($category_ids_arr) >= 2) {
                    $category_id_2 = $category_ids_arr[1];
                }
                if (count($category_ids_arr) >= 3) {
                    $category_id_3 = $category_ids_arr[2];
                }
            }
            if (!$is_draft){
                if (!$this->form_validation->required($category_id_1)){
                    printAjaxError('fail','请选择商品类别！');
                }
                if (!$this->form_validation->required($title)){
                    printAjaxError('fail','请填写标题！');
                }
                if (!$this->form_validation->required($price)){
                    printAjaxError('fail','请填写报价！');
                }
                if (!$this->form_validation->required($batch_path_ids)){
                    printAjaxError('fail','请至少上传一张照片！');
                }
                if ($url){
                    if (!preg_match("/^http[s]?:\/\/[A-Za-z0-9]+\.[A-Za-z0-9]+[\/=\?%\-&_~`@[\]\\':+!]*([^<>\"\"])*$/",$url)){
                        printAjaxError('fail','请填写以http、https开头的正确网址！');
                    }
                }
                if (!$shop_address && !$url){
                    printAjaxError('fail','请填写商户地址或者商品链接其中之一！');
                }

                if (!$this->form_validation->required($username)){
                    printAjaxError('fail','请填写姓名！');
                }
                if (!preg_match('/^(([\xe4-\xe9][\x80-\xbf]{2}){2,10}|[a-zA-Z\.\s]{1,20})$/', $username)) {
                    printAjaxError('fail', '请填写正确格式的姓名！');
                }
                if (!$this->form_validation->required($mobile)){
                    printAjaxError('fail','请填写手机号！');
                }
                if (!preg_match('/^((\(\d{2,3}\))|(\d{3}\-))?(13|14|15|16|17|18|19)\d{9}$/', $mobile)) {
                    printAjaxError('fail', '请输入正确的手机号!');
                }
                if (!$this->form_validation->required($province_id)){
                    printAjaxError('fail','请选择省！');
                }
                if (!$this->form_validation->required($city_id)){
                    printAjaxError('fail','请选择市！');
                }
                if (!$this->form_validation->required($area_id)){
                    printAjaxError('fail','请选择区！');
                }
                if (!$this->form_validation->required($arrival_time)){
                    printAjaxError('fail','请选择期望到货时间！');
                }
            }

            $res = anti_spam($abstract);
            $abstract = $res['content'];
            $address = '';
            $area_info = $this->Area_model->get('name', array('id' => $province_id));
            if ($area_info) {
                $address .= $area_info['name'];
            }
            if($province_id != 110000 && $province_id != 310000 && $province_id != 120000 && $province_id != 500000) {
                $area_info = $this->Area_model->get('name', array('id' => $city_id));
                if ($area_info) {
                    $address .= $area_info['name'];
                }
            }
            $area_info = $this->Area_model->get('name', array('id' => $area_id));
            if ($area_info) {
                $address .= $area_info['name'];
            }
            if($batch_path_ids){
                $batch_path_ids = implode('_', $batch_path_ids);
                $batch_path_ids .= '_';
            }
            $fields = array(
                'user_id'        => $user_id,
                'title'          => $title,
                'abstract'       => $abstract,
                'batch_path_ids' => $batch_path_ids,
                'category_id_1'  => $category_id_1,
                'category_id_2'  => $category_id_2,
                'category_id_3'  => $category_id_3,
                'brand_name'     => $brand_name,
                'price'          => $price,
                'url'            => $url,
                'shop_address'   => $shop_address,
                'username'   => $username,
                'mobile'   => $mobile,
                'province_id'=>$province_id,
                'city_id'=>$city_id,
                'area_id'=>$area_id,
                'address'   => $address,
                'arrival_time'   => $arrival_time,
                'is_draft'   => $is_draft,
                'add_time'       => time(),
            );

            $ret_id = $this->Group_purchase_model->save($fields,$id ? array('id'=>$id) : NULL);
            if ($ret_id){
                $ret_id = $id ? $id : $ret_id;
                if (!$is_draft){
                    //自动发送邀请蚁客信息
                    $eli_guest_list = $this->Eli_guest_model->gets(array('eli_guest.trade_type_id'=>$category_id_1,'eli_guest.status'=>1));
                    if ($eli_guest_list){
                        $message = [];
                        foreach ($eli_guest_list as $value){
                            if ($value['user_id'] != $user_id){
                                $message[] = array(
                                    'message_type'=>'system',
                                    'content'=>"有新的拼团活动《{$title}》发布,赶快去评估吧！",
                                    'add_time'=>time(),
                                    'to_user_id'=>$value['user_id'],
                                    'item_id'=>$ret_id,
                                    'item_title'=>$title,
                                );
                            }

                        }
                        $this->Message_model->save_batch($message);
                    }

                    //给粉丝发送关注消息
                    $fans_list = $this->Follow_model->gets(array('follow.to_user_id'=>$user_id));
                    if ($fans_list){
                        $user_info = $this->User_model->get('nickname',array('id'=>$user_id));
                        $message = [];
                        foreach ($fans_list as $key=>$value){
                            $message[] = array(
                                'message_type'=>'follow',
                                'content'=>"您关注的{$user_info['nickname']}发布了新的拼团活动,赶快去看看吧！",
                                'add_time'=>time(),
                                'to_user_id'=>$value['user_id'],
                                'item_id'=>$ret_id,
                                'item_title'=>$title,
                            );
                        }
                        $this->Message_model->save_batch($message);
                    }
                }
                $field_url = base_url().'index.php/user/my_group_purchase_list/'.$is_draft.'.html';
                printAjaxSuccess($field_url,$is_draft ? '保存成功！' : '发布成功！');
            }
            printAjaxError('fail',$is_draft ? '保存失败！' : '发布失败！');

        }
        $attachment_list = NULL;
        if ($item_info && $item_info['batch_path_ids']) {
            $tmp_atm_ids = preg_replace(array('/^_/', '/_$/', '/_/'), array('', '', ','), $item_info['batch_path_ids']);
            $attachment_list = $this->Attachment_model->gets2($tmp_atm_ids);
        }
        //产品分类
        $product_category_list = $this->Product_category_model->get_sub_tree();
        if ($product_category_list){
            foreach ($product_category_list as $key=>$value){
                $product_category_list[$key]['product_category_name'] = str_repeat( '&nbsp;&nbsp;&nbsp;&nbsp;', $value['level'] - 1) . $value['product_category_name'];
            }
        }
        $province_list = $this->Area_model->gets('*', array('parent_id' => 0));
        $data = array(
            'site_name' => $systemInfo['site_name'],
            'index_name' => $systemInfo['index_name'],
            'index_url' => $systemInfo['index_url'],
            'client_index' => $systemInfo['client_index'],
            'title' => '发布抢团活动_会员中心' . $systemInfo['site_name'],
            'keywords' => $systemInfo['site_keywords'],
            'description' => $systemInfo['site_description'],
            'site_copyright' => $systemInfo['site_copyright'],
            'icp_code' => $systemInfo['icp_code'],
            'html' => $systemInfo['html'],
            'product_category_list'=>$product_category_list,
            'attachment_list'=>$attachment_list,
            'province_list'=>$province_list,
            'item_info'=>$item_info,
            'arrival_time_arr'=>$this->_arrival_time_arr,
        );
        $layout = array(
            'content' => $this->load->view('user/my_group_purchase_save', $data, TRUE)
        );
        $this->load->view('layout/user_layout', $layout);
        //缓存
        if ($systemInfo['cache'] == 1) {
            $this->output->cache($systemInfo['cache_time']);
        }
    }

    //拼团详情
    public function my_group_purchase_view($id = 0)
    {
        //判断是否登录
        checkLogin();
        $systemInfo = $this->System_model->get('*', array('id' => 1));
        $user_id = $this->session->userdata('user_id');
        $item_info = $this->Group_purchase_model->get('*', array('id' => $id, 'user_id' => $user_id));
        if (!$item_info){
            $data = array(
                'user_msg' => '参数异常！',
                'user_url' => $this->session->userdata('gloabPreUrl')
            );
            $this->session->set_userdata($data);
            redirect(base_url() . 'index.php/message/index');
        }
        $attachment_list = NULL;
        if ($item_info['batch_path_ids']) {
            $tmp_atm_ids = preg_replace(array('/^_/', '/_$/', '/_/'), array('', '', ','), $item_info['batch_path_ids']);
            $attachment_list = $this->Attachment_model->gets2($tmp_atm_ids);
        }
        $category_info_1 = $this->Product_category_model->get('product_category_name',array('id'=>$item_info['category_id_1']));
        $category_info_2 = $this->Product_category_model->get('product_category_name',array('id'=>$item_info['category_id_2']));
        $category_info_3 = $this->Product_category_model->get('product_category_name',array('id'=>$item_info['category_id_3']));
        $category_name = $category_info_1 ? $category_info_1['product_category_name'] : '';
        $category_name .= $category_info_2 ? '-'.$category_info_2['product_category_name'] : '';
        $category_name .= $category_info_3 ? '-'.$category_info_3['product_category_name'] : '';
        $item_info['category_name'] = $category_name;

        $pintuan_info = array();
        if ($item_info['ptkj_id']){
            $pintuan_info = $this->Promotion_ptkj_model->get2(array('promotion_ptkj.id' => $item_info['ptkj_id'],'promotion_ptkj.is_open'=>1));
        }
        $data = array(
            'site_name' => $systemInfo['site_name'],
            'index_name' => $systemInfo['index_name'],
            'index_url' => $systemInfo['index_url'],
            'client_index' => $systemInfo['client_index'],
            'title' => '我的抢团活动详情_会员中心' . $systemInfo['site_name'],
            'keywords' => $systemInfo['site_keywords'],
            'description' => $systemInfo['site_description'],
            'site_copyright' => $systemInfo['site_copyright'],
            'icp_code' => $systemInfo['icp_code'],
            'html' => $systemInfo['html'],
            'attachment_list'=>$attachment_list,
            'item_info'=>$item_info,
            'pintuan_info'=>$pintuan_info,
        );
        $layout = array(
            'content' => $this->load->view('user/my_group_purchase_view', $data, TRUE)
        );
        $this->load->view('layout/user_layout', $layout);
        //缓存
        if ($systemInfo['cache'] == 1) {
            $this->output->cache($systemInfo['cache_time']);
        }
    }

    //我的心得活动
    public function my_notes_list($is_draft = 0,$page = 0) {
        $this->session->set_userdata(array("gloabPreUrl" => base_url() . 'index.php/' . uri_string()));
        //判断是否登录
        checkLogin();
        $systemInfo = $this->System_model->get('*', array('id' => 1));
        $user_id = $this->session->userdata('user_id');
        $strWhere = "notes.is_draft = {$is_draft} and notes.user_id = {$user_id}";
        //分页
        $paginationCount = $this->Notes_model->count_join_user_topic($strWhere);
        $this->config->load('pagination_config', TRUE);
        $paginationConfig = $this->config->item('pagination_config');
        $paginationConfig['base_url'] = base_url() . "index.php/user/my_notes_list/{$is_draft}/";
        $paginationConfig['total_rows'] = $paginationCount;
        $paginationConfig['uri_segment'] = 4;
        $paginationConfig['per_page'] = 10;
        $this->pagination->initialize($paginationConfig);
        $pagination = $this->pagination->create_links();

        $item_list = $this->Notes_model->gets_join_user_topic("notes.*, user.nickname, user.path, user.is_eli_guest", $strWhere, $paginationConfig['per_page'], $page);
        if ($item_list) {
            foreach ($item_list as $key => $value) {
                $attachment_list = array();
                if ($value['batch_path_ids']){
                    $tmp_atm_ids = preg_replace(array('/^_/', '/_$/', '/_/'), array('', '', ','), $value['batch_path_ids']);
                    $attachment_list = $this->Attachment_model->gets3($tmp_atm_ids);
                }

                $item_list[$key]['path'] = $attachment_list ? $attachment_list[0]['path'] : '';

                $item_list[$key]['content'] = common_substr(text($value['content']),28);
                $topic_info = '';
                if ($value['topic_id']) {
                    $topic_info = $this->Topic_model->get('topic_name', array('id' => $value['topic_id']));
                }
                $item_list[$key]['topic_name'] = $topic_info ? $topic_info['topic_name'] : '';
            }
        }

        $data = array(
            'site_name' => $systemInfo['site_name'],
            'index_name' => $systemInfo['index_name'],
            'index_url' => $systemInfo['index_url'],
            'client_index' => $systemInfo['client_index'],
            'title' => '我的心得_会员中心' . $systemInfo['site_name'],
            'keywords' => $systemInfo['site_keywords'],
            'description' => $systemInfo['site_description'],
            'site_copyright' => $systemInfo['site_copyright'],
            'icp_code' => $systemInfo['icp_code'],
            'item_list' => $item_list,
            'pagination' => $pagination,
            'pageCount' => ceil($paginationCount / $paginationConfig['per_page']),
            'perPage' => $paginationConfig['per_page'],
            'html' => $systemInfo['html'],
            'is_draft'=>$is_draft
        );
        $layout = array(
            'content' => $this->load->view('user/my_notes_list', $data, TRUE)
        );
        $this->load->view('layout/user_layout', $layout);
        //缓存
        if ($systemInfo['cache'] == 1) {
            $this->output->cache($systemInfo['cache_time']);
        }
    }

    //添加心得
    public function my_notes_save($id = 0)
    {
        //判断是否登录
        checkLogin();
        $systemInfo = $this->System_model->get('*', array('id' => 1));
        $user_id = $this->session->userdata('user_id');
        $item_info = $this->Notes_model->get('*', array('id' => $id, 'user_id' => $user_id));
        $attachment_list = NULL;
        if ($item_info && $item_info['batch_path_ids']) {
            $tmp_atm_ids = preg_replace(array('/^_/', '/_$/', '/_/'), array('', '', ','), $item_info['batch_path_ids']);
            $attachment_list = $this->Attachment_model->gets2($tmp_atm_ids);
        }
        if ($_POST){
            $title = $this->input->post('title', TRUE);
            $topic_id = $this->input->post('topic_id', TRUE);
            $content = unhtml($this->input->post('content'));
            $batch_path_ids = $this->input->post('batch_path_ids',TRUE);
            $is_draft = $this->input->post('is_draft',TRUE);
            $video_file_id = $this->input->post('video_file_id',TRUE);
            $video_name = $this->input->post('video_name',TRUE);
            $video_url = $this->input->post('video_url',TRUE);
            if (!$is_draft){
                if (!$this->form_validation->required($topic_id)){
                    printAjaxError('fail','请选择话题！');
                }
                if (!$this->form_validation->required($batch_path_ids)){
                    printAjaxError('fail','请至少上传一张照片！');
                }
                if (!$this->form_validation->required($title)){
                    printAjaxError('fail','请填写标题！');
                }
                if (!$this->form_validation->required($content)){
                    printAjaxError('fail','请说点什么！');
                }
            }
            if($batch_path_ids){
                $batch_path_ids = implode('_', $batch_path_ids);
                $batch_path_ids .= '_';
            }
            $res = anti_spam($content);
            $content = $res['content'];
            $files = array(
                'user_id' => $user_id,
                'batch_path_ids' => $batch_path_ids,
                'video_file_id' => $video_file_id,
                'video_name' => $video_name,
                'video_url' => $video_url,
                'topic_id' => $topic_id,
                'title' => $title,
                'content' => $content,
                'is_draft' => $is_draft,
                'add_time' => time()
            );
            $ret_id = $this->Notes_model->save($files,$id ? array('id'=>$id) : NULL);
            if ($ret_id){
                $ret_id = $id ? $id : $ret_id;
                if (!$is_draft){
                    $this->Topic_model->save_column('note_num','note_num+1',array('id'=>$topic_id));
                    //给粉丝发送关注消息
                    $fans_list = $this->Follow_model->gets(array('follow.to_user_id'=>$user_id));
                    if ($fans_list){
                        $user_info = $this->User_model->get('nickname',array('id'=>$user_id));
                        $message = [];
                        foreach ($fans_list as $key=>$value){
                            $message[] = array(
                                'message_type'=>'follow',
                                'content'=>"您关注的{$user_info['nickname']}发布了新的心得,赶快去看看吧！",
                                'add_time'=>time(),
                                'to_user_id'=>$value['user_id'],
                                'item_id'=>$ret_id,
                                'item_type'=>1,
                                'item_title'=>$title,
                            );
                        }
                        $this->Message_model->save_batch($message);
                    }
                }
                $field_url = base_url().'index.php/user/my_notes_list/'.$is_draft.'.html';
                printAjaxSuccess($field_url,$is_draft ? '保存成功！' : '发布成功！');
            }
            printAjaxError('fail',$is_draft ? '保存失败！' : '发布失败！');
        }
        $category_list = $this->Product_category_model->gets2('id,product_category_name',array('display'=>1,'parent_id'=>0,'store_id'=>0));
        if ($item_info) {
            $topic_info = '';
            if ($item_info['topic_id']){
                $topic_info = $this->Topic_model->get('topic_name', array('id' => $item_info['topic_id']));
            }
            $item_info['topic_name'] = $topic_info ? $topic_info['topic_name'] : '';
        }

        $data = array(
            'site_name' => $systemInfo['site_name'],
            'index_name' => $systemInfo['index_name'],
            'index_url' => $systemInfo['index_url'],
            'client_index' => $systemInfo['client_index'],
            'title' => '我的拼团活动_会员中心' . $systemInfo['site_name'],
            'keywords' => $systemInfo['site_keywords'],
            'description' => $systemInfo['site_description'],
            'site_copyright' => $systemInfo['site_copyright'],
            'icp_code' => $systemInfo['icp_code'],
            'html' => $systemInfo['html'],
            'attachment_list'=>$attachment_list,
            'category_list'=>$category_list,
            'item_info'=>$item_info,
        );
        $layout = array(
            'content' => $this->load->view('user/my_notes_save', $data, TRUE)
        );
        $this->load->view('layout/user_layout', $layout);
        //缓存
        if ($systemInfo['cache'] == 1) {
            $this->output->cache($systemInfo['cache_time']);
        }
    }


    public function get_search_topic_list()
    {
        if ($_POST){
            $value = $this->input->post('value', TRUE);
            $str_where = $value ? array('topic_name regexp'=>"{$value}",'display'=>1) : NULL;
            $limit = $value ? NULL : 10;
            $item_list = $this->Topic_model->gets("topic.*",$str_where,$limit);
            printAjaxData($item_list);

        }
    }

    //获取格式
    public function get_notes_format($topic_name = NULL)
    {
        $item_info = $this->Topic_model->get_join_product_category('product_category.format_1,product_category.format_2',array('topic.topic_name'=>urldecode($topic_name)));
        if ($item_info){
            $item_info['format_1'] = html($item_info['format_1']);
            $item_info['format_2'] = html($item_info['format_2']);
        }
        printAjaxData($item_info);
    }

    //创建话题
    public function add_new_topic()
    {
        checkLoginAjax();
        $user_id = $this->session->userdata('user_id');
        if ($_POST) {
            $category_id = $this->input->post('category_id', TRUE);
            $new_topic_name = $this->input->post('new_topic_name', TRUE);
            //过滤字符
            $regex = "/\ |\/|\~|\!|\@|\#|\\$|\%|\^|\&|\*|\(|\)|\_|\+|\{|\}|\:|\<|\>|\?|\[|\]|\,|\.|\/|\;|\'|\`|\-|\=|\\\|\|/";
            $name = preg_replace($regex, "", $new_topic_name);

            $res = anti_spam($name);
            if (!empty($res) && $res['spam'] != 0){
                printAjaxError('fail', '含有违禁字符');
            }

            $topic_info = $this->Topic_model->count(array('topic_name' => $name));
            if ($topic_info) {
                printAjaxError('fail', "话题已存在！");
            }
            $files = array(
                'user_id'    => $user_id,
                'topic_name' => $name,
                'category_id' => $category_id,
                'add_time'   => time(),
            );
            $ret_id = $this->Topic_model->save($files);
            if ($ret_id) {
                printAjaxData(array('id' => $ret_id, 'topic_name' => $name));
            }
            printAjaxError('fail', '创建失败！');
        }
    }

    //删除拼团
    public function delete_my_notes()
    {
        checkLoginAjax();
        $user_id = $this->session->userdata('user_id');
        if ($_POST){
            $id = $this->input->post('id',TRUE);
            if (!$id){
                printAjaxError('fail','参数异常！');
            }
            $item_info = $this->Notes_model->get('id',array('id'=>$id,'user_id'=>$user_id));
            if (!$item_info){
                printAjaxError('fail','心得不存在！');
            }

            if ($this->Notes_model->delete(array('id'=>$id))){
                printAjaxSuccess('success','删除成功！');
            }
        }

        printAjaxError('fail','删除失败！');
    }

    //心得详情
    public function my_notes_view($id = 0)
    {
        //判断是否登录
        checkLogin();
        $systemInfo = $this->System_model->get('*', array('id' => 1));
        $user_id = $this->session->userdata('user_id');
        $item_info = $this->Notes_model->get('*', array('id' => $id, 'user_id' => $user_id));
        if (!$item_info){
            $data = array(
                'user_msg' => '参数异常！',
                'user_url' => $this->session->userdata('gloabPreUrl')
            );
            $this->session->set_userdata($data);
            redirect(base_url() . 'index.php/message/index');
        }
        $attachment_list = NULL;
        if ($item_info['batch_path_ids']) {
            $tmp_atm_ids = preg_replace(array('/^_/', '/_$/', '/_/'), array('', '', ','), $item_info['batch_path_ids']);
            $attachment_list = $this->Attachment_model->gets2($tmp_atm_ids);
        }

        $category_list = $this->Product_category_model->gets2('id,product_category_name',array('display'=>1,'parent_id'=>0,'store_id'=>0));
        $topic_info = '';
        if ($item_info['topic_id']) {
            $topic_info = $this->Topic_model->get('topic_name', array('id' => $item_info['topic_id']));
        }
        $item_info['topic_name'] = $topic_info ? $topic_info['topic_name'] : '';
        $data = array(
            'site_name' => $systemInfo['site_name'],
            'index_name' => $systemInfo['index_name'],
            'index_url' => $systemInfo['index_url'],
            'client_index' => $systemInfo['client_index'],
            'title' => '我的心得详情_会员中心' . $systemInfo['site_name'],
            'keywords' => $systemInfo['site_keywords'],
            'description' => $systemInfo['site_description'],
            'site_copyright' => $systemInfo['site_copyright'],
            'icp_code' => $systemInfo['icp_code'],
            'html' => $systemInfo['html'],
            'attachment_list'=>$attachment_list,
            'category_list'=>$category_list,
            'item_info'=>$item_info,
        );
        $layout = array(
            'content' => $this->load->view('user/my_notes_view', $data, TRUE)
        );
        $this->load->view('layout/user_layout', $layout);
        //缓存
        if ($systemInfo['cache'] == 1) {
            $this->output->cache($systemInfo['cache_time']);
        }
    }

    //我的限时抢购
    public function my_flash_sale() {
        //判断是否登录
        checkLogin();
        $user_id = $this->session->userdata('user_id');
        $systemInfo = $this->System_model->get('*', array('id' => 1));
        $flash_sale_info = $this->Flash_sale_record_model->gets(array('user_id' => $this->session->userdata('user_id')));
        foreach ($flash_sale_info as $key => $ls) {
            $flash = $this->Flash_sale_model->get('*', array('id' => $ls['flash_sale_id']));
            $order_info = $this->Orders_model->get('status', array('id' => $ls['order_id']));
            $order_detail = $this->Orders_detail_model->get('size_name,color_name', array('order_id' => $ls['order_id']));
            $flash_sale_info[$key]['name'] = $flash['name'];
            $flash_sale_info[$key]['flash_sale_price'] = $flash['flash_sale_price'];
            $flash_sale_info[$key]['path'] = $flash['path'];
            $flash_sale_info[$key]['status'] = $this->_status[$order_info['status']];
            $flash_sale_info[$key]['size_name'] = $order_detail['size_name'];
            $flash_sale_info[$key]['color_name'] = $order_detail['color_name'];
        }
        $data = array(
            'site_name' => $systemInfo['site_name'],
            'index_name' => $systemInfo['index_name'],
            'index_url' => $systemInfo['index_url'],
            'client_index' => $systemInfo['client_index'],
            'title' => '我的拼团活动',
            'keywords' => $systemInfo['site_keywords'],
            'description' => $systemInfo['site_description'],
            'site_copyright' => $systemInfo['site_copyright'],
            'icp_code' => $systemInfo['icp_code'],
            'html' => $systemInfo['html'],
            'flash_sale_info' => $flash_sale_info,
        );
        $layout = array(
            'content' => $this->load->view('user/my_flash_sale', $data, TRUE)
        );
        $this->load->view('layout/user_layout', $layout);
        //缓存
        if ($systemInfo['cache'] == 1) {
            $this->output->cache($systemInfo['cache_time']);
        }
    }

    //我的消息
    public function my_message_list($page = 0) {
        //判断是否登录
        checkLogin();
        //当前位置
        $location = "<a href='index.php/user.html'>会员中心</a> > <a href='javascript:void(0);'>我的账户</a> > 我的消息";
        $go_url = $this->session->userdata('gloabPreUrl');
        $user_id = $this->session->userdata('user_id');
        $systemInfo = $this->System_model->get('*', array('id' => 1));
        $strWhere = 'to_user_id = ' . $user_id;
        //分页
        $paginationCount = $this->Message_model->rowCount($strWhere);
        $this->config->load('pagination_config', TRUE);
        $paginationConfig = $this->config->item('pagination_config');
        $paginationConfig['base_url'] = base_url() . "index.php/user/my_message_list/";
        $paginationConfig['total_rows'] = $paginationCount;
        $paginationConfig['uri_segment'] = 3;
        $paginationConfig['per_page'] = 10;
        $this->pagination->initialize($paginationConfig);
        $pagination = $this->pagination->create_links();
        $item_list = $this->Message_model->gets('*', $strWhere, $paginationConfig['per_page'], $page);
        foreach ($item_list as $key => $item) {
            $user_info = $this->User_model->get('username,nickname', array('id' => $item['from_user_id']));
            if ($user_info) {
                $item_list[$key]['from_name'] = $user_info['nickname'] ? $user_info['nickname'] : hideStar($user_info['username']);
            } else {
                $item_list[$key]['from_name'] = '系统';
            }
        }
        $data = array(
            'site_name' => $systemInfo['site_name'],
            'index_name' => $systemInfo['index_name'],
            'index_url' => $systemInfo['index_url'],
            'client_index' => $systemInfo['client_index'],
            'title' => '我的消息_会员中心' . $systemInfo['site_name'],
            'keywords' => $systemInfo['site_keywords'],
            'description' => $systemInfo['site_description'],
            'site_copyright' => $systemInfo['site_copyright'],
            'icp_code' => $systemInfo['icp_code'],
            'html' => $systemInfo['html'],
            'item_list' => $item_list,
            'location' => $location,
            'pagination' => $pagination,
            'message_type' => $this->_message_type
        );
        $layout = array(
            'content' => $this->load->view('user/my_message_list', $data, TRUE)
        );
        $this->load->view('layout/user_layout', $layout);
        //缓存
        if ($systemInfo['cache'] == 1) {
            $this->output->cache($systemInfo['cache_time']);
        }
    }

    public function my_read_message() {
        checkLoginAjax();
        if ($_POST) {
            $id = $this->input->post('id', true);
            if (!$id) {
                printAjaxError('fail', '操作异常，刷新重试');
            }
            $ret = $this->Message_model->save(array('is_read' => 1), array('id' => $id, 'to_user_id' => $this->session->userdata('user_id')));
            if (!$ret) {
                printAjaxError('fail', '操作失败');
            }
            printAjaxSuccess('success', '操作成功');
        }
    }

    public function my_delete_message() {
        checkLoginAjax();
        if ($_POST) {
            $ids = trim($this->input->post('ids', true), ',');
            if (!$ids) {
                printAjaxError('fail', '请选择删除项');
            }
            $ret = $this->Message_model->delete("id in ($ids) and to_user_id = " . $this->session->userdata('user_id'));
            if (!$ret) {
                printAjaxError('fail', '删除失败');
            }
            printAjaxSuccess('success', '删除成功');
        }
    }


    //我的优惠券
    public function my_coupon_list() {
        //判断是否登录
        checkLogin();
        $go_url = $this->session->userdata('gloabPreUrl');
        //当前位置
        $location = "<a href='index.php/user.html'>会员中心</a> > <a href='javascript:void(0);'>我的账户</a> > 申请提现";
        $user_id = $this->session->userdata('user_id');
        $systemInfo = $this->System_model->get('*', array('id' => 1));

        $data = array(
            'site_name' => $systemInfo['site_name'],
            'index_name' => $systemInfo['index_name'],
            'index_url' => $systemInfo['index_url'],
            'client_index' => $systemInfo['client_index'],
            'title' => '我的优惠券_会员中心' . $systemInfo['site_name'],
            'keywords' => $systemInfo['site_keywords'],
            'description' => $systemInfo['site_description'],
            'site_copyright' => $systemInfo['site_copyright'],
            'icp_code' => $systemInfo['icp_code'],
            'html' => $systemInfo['html'],
            'location' => $location
        );
        $layout = array(
            'content' => $this->load->view('user/my_coupon_list', $data, TRUE)
        );
        $this->load->view('layout/user_layout', $layout);
        //缓存
        if ($systemInfo['cache'] == 1) {
            $this->output->cache($systemInfo['cache_time']);
        }
    }

    //提现申请
    public function my_draw() {
        //判断是否登录
        checkLogin();
        $go_url = $this->session->userdata('gloabPreUrl');
        //当前位置
        $location = "<a href='index.php/user.html'>会员中心</a> > <a href='javascript:void(0);'>我的账户</a> > 申请提现";
        $user_id = $this->session->userdata('user_id');
        $systemInfo = $this->System_model->get('*', array('id' => 1));

        $user_info = $this->User_model->get('*', array('user.id' => $user_id));

        $data = array(
            'site_name' => $systemInfo['site_name'],
            'index_name' => $systemInfo['index_name'],
            'index_url' => $systemInfo['index_url'],
            'client_index' => $systemInfo['client_index'],
            'title' => '申请提现_会员中心' . $systemInfo['site_name'],
            'keywords' => $systemInfo['site_keywords'],
            'description' => $systemInfo['site_description'],
            'site_copyright' => $systemInfo['site_copyright'],
            'icp_code' => $systemInfo['icp_code'],
            'html' => $systemInfo['html'],
            'user_info' => $user_info,
            'location' => $location
        );
        $layout = array(
            'content' => $this->load->view('user/my_draw', $data, TRUE)
        );
        $this->load->view('layout/user_layout', $layout);
        //缓存
        if ($systemInfo['cache'] == 1) {
            $this->output->cache($systemInfo['cache_time']);
        }
    }

    public function get_city() {
        if ($_POST) {
            $this->load->model('Area_model', '', TRUE);
            $parent_id = $this->input->post('parent_id', TRUE);
            $item_list = $this->Area_model->gets('id, name', array('parent_id' => $parent_id, 'display' => 1));
            printAjaxData($item_list);
        }
    }

    public function set_city() {
        if ($_POST) {
            $city = $this->input->post('city', TRUE);

            if (!$city) {
                printAjaxError('fail', '城市设置失败');
            }
            $this->session->set_userdata(array("gloab_city_name" => $city));
            printAjaxSuccess('success', '城市设置成功');
        }
    }

    //充值
    public function my_recharge() {
        //判断是否登录
        checkLogin();
        $this->session->set_userdata(array("gloabPreUrl" => base_url() . 'index.php/' . uri_string()));
        //当前位置
        $location = "<a href='index.php/user.html'>会员中心</a> > <a href='javascript:void(0);'>我的账户</a> > 申请提现";
        $user_id = $this->session->userdata('user_id');
        $systemInfo = $this->System_model->get('*', array('id' => 1));

        $user_id = $this->session->userdata('user_id');
        $user_info = $this->User_model->get('*', array('user.id' => $user_id));
        $data = array(
            'site_name' => $systemInfo['site_name'],
            'index_name' => $systemInfo['index_name'],
            'index_url' => $systemInfo['index_url'],
            'client_index' => $systemInfo['client_index'],
            'title' => '申请提现_会员中心' . $systemInfo['site_name'],
            'keywords' => $systemInfo['site_keywords'],
            'description' => $systemInfo['site_description'],
            'site_copyright' => $systemInfo['site_copyright'],
            'icp_code' => $systemInfo['icp_code'],
            'html' => $systemInfo['html'],
            'user_info' => $user_info,
            'location' => $location
        );
        $layout = array(
            'content' => $this->load->view('user/my_recharge', $data, TRUE)
        );
        $this->load->view('layout/user_layout', $layout);
        //缓存
        if ($systemInfo['cache'] == 1) {
            $this->output->cache($systemInfo['cache_time']);
        }
    }

    //支付宝支付
    public function alipay_pay_recharge() {
        header('Content-type:text/html;charset=utf-8');
        $gloabPreUrl = $this->session->userdata('gloabPreUrl');
        checkLogin();
        $money = $this->input->post('money',TRUE);
        if (!$money) {
            $data = array(
                'user_msg' => '操作异常',
                'user_url' => $gloabPreUrl
            );

            $this->session->set_userdata($data);
            redirect(base_url() . 'index.php/message/index');
        }

        if (!preg_match('/^\d+(\.\d+)?$/', $money)) {
            $data = array(
                'user_msg' => '请输入正确的充值金额',
                'user_url' => $gloabPreUrl
            );

            $this->session->set_userdata($data);
            redirect(base_url() . 'index.php/message/index');
        }
        if ($money <= 0) {
            $data = array(
                'user_msg' => '充值金额必须大于零',
                'user_url' => $gloabPreUrl
            );

            $this->session->set_userdata($data);
            redirect(base_url() . 'index.php/message/index');
        }
        $user_id = $this->session->userdata('user_id');
        //判断下单用户是否存在
        $user_info = $this->User_model->get('*', array('user.id' => $user_id));
        if (!$user_info) {
            $data = array(
                'user_msg' => '用户信息不存在，结算失败',
                'user_url' => $gloabPreUrl
            );
            $this->session->set_userdata($data);
            redirect(base_url() . 'index.php/message/index');
        }
        $order_number = $this->_get_unique_recharge_number();
        $total_fee =  $money;
        //生成充值记录
        $column_arr = array(
            'user_id'=>$user_id,
            'order_number'=>$order_number,
            'pay_type'=>'支付宝',
            'status'=>0,
            'money'=>$total_fee,
            'balance'=>$user_info['total'],
            'add_time'=>time(),
        );
        if (!$this->Recharge_record_model->save($column_arr)) {
            $data = array(
                'user_msg' => '创建充值订单失败，稍后重试',
                'user_url' => $gloabPreUrl
            );
            $this->session->set_userdata($data);
            redirect(base_url() . 'index.php/message/index');
        }
        if(!$this->Pay_log_model->rowCount(array('out_trade_no'=>$order_number, 'payment_type'=>'alipay', 'order_type'=>'recharge'))) {
            $fields = array(
                'user_id'=>       $user_id,
                'total_fee'=>     $money,
                'total_fee_give'=>0,
                'out_trade_no'=>  $order_number,
                'order_num'=>     $order_number,
                'trade_status'=>  'WAIT_BUYER_PAY',
                'add_time'=>      time(),
                'payment_type'=>  'alipay',
                'order_type'=>    'recharge',
            );
            if (!$this->Pay_log_model->save($fields)) {
                $data = array(
                    'user_msg' => '支付失败，请重试',
                    'user_url' => $gloabPreUrl
                );
                $this->session->set_userdata($data);
                redirect(base_url() . 'index.php/message/index');
            }
        }


        /********************支付***********************/
        require_once("sdk/alipay/alipay.config.php");
        require_once("sdk/alipay/lib/alipay_submit.class.php");

        //构造要请求的参数数组，无需改动
        $parameter = array(
            "service"       => $alipay_config['service'],
            "partner"       => $alipay_config['partner'],
            "seller_id"  =>    $alipay_config['seller_id'],
            "payment_type"	=> $alipay_config['payment_type'],
            "notify_url"	=> base_url().'index.php/user/alipay_notify_recharge',
            "return_url"	=> base_url().'index.php/user/alipay_return_recharge',
            "anti_phishing_key"=>$alipay_config['anti_phishing_key'],
            "exter_invoke_ip"=>$alipay_config['exter_invoke_ip'],
            "out_trade_no"	=> $order_number,
            "subject"	=>"蚁立网账户余额充值",
            "total_fee"	=> $total_fee,
            "body"	=> '蚁立网账户余额充值即时到账支付',
            "_input_charset"	=> trim(strtolower($alipay_config['input_charset']))
        );
        $alipay_config['notify_url'] = $parameter['notify_url'];
        $alipay_config['return_url'] = $parameter['return_url'];
        //建立请求
        $alipaySubmit = new AlipaySubmit($alipay_config);
        $html_text = $alipaySubmit->buildRequestForm($parameter,"get", "确认");
        echo $html_text;
    }

    //支付 宝异步通知
    public function alipay_notify_recharge() {
        if ($_POST) {
            require_once("sdk/alipay/alipay.config.php");
            require_once("sdk/alipay/lib/alipay_notify.class.php");
            //计算得出通知验证结果
            $alipay_config['notify_url'] = base_url() . 'index.php/user/alipay_notify_recharge';
            $alipay_config['return_url'] = base_url() . 'index.php/user/alipay_return_recharge';

            $alipayNotify = new AlipayNotify($alipay_config);
            $verify_result = $alipayNotify->verifyNotify();
            if ($verify_result) {
                //商户订单号
                $out_trade_no = $this->input->post('out_trade_no', TRUE);
                //支付宝交易号
                $trade_no = $this->input->post('trade_no', TRUE);
                //交易状态
                $trade_status = $this->input->post('trade_status', TRUE);
                //买家支付宝账号
                $buyer_email = $this->input->post('buyer_email', TRUE);
                //通知时间
                $notify_time = strtotime($this->input->post('notify_time', TRUE));
                $seller_id = $this->input->post('seller_id', TRUE);
                $total_fee = $this->input->post('total_fee', TRUE);

                if ($trade_status == 'TRADE_FINISHED' || $trade_status == 'TRADE_SUCCESS') {
                    $pay_log_info = $this->Pay_log_model->get('*', array('out_trade_no' => $out_trade_no, 'order_type' => 'recharge', 'payment_type' => 'alipay'));
                    if ($pay_log_info && $alipay_config['seller_id'] == $seller_id && $total_fee == $pay_log_info['total_fee']) {
                        if ($pay_log_info['trade_status'] != $trade_status && $pay_log_info['trade_status'] != 'TRADE_FINISHED' && $pay_log_info['trade_status'] != 'TRADE_SUCCESS' && $pay_log_info['trade_status'] != 'TRADE_CLOSED') {
                            //支付记录
                            $fields = array(
                                'payment_type' => 'alipay',
                                'order_type'   => 'recharge',
                                'trade_no'     => $trade_no,
                                'trade_status' => $trade_status,
                                'buyer_email'  => $buyer_email,
                                'notify_time'  => $notify_time
                            );
                            if ($this->Pay_log_model->save($fields, array('id' => $pay_log_info['id']))) {
                                $item_info = $this->Recharge_record_model->get('*', array('order_number' => $out_trade_no, 'status' => 0));
                                $user_info = $this->User_model->get('id, total, username', array('id' => $item_info['user_id']));
                                if ($item_info && $user_info) {
                                    $balance = $user_info['total'] + $item_info['money'];
                                    if ($this->User_model->save(array('total' => $balance), array('id' => $item_info['user_id']))) {
                                        $this->Recharge_record_model->save(array('status' => 1), array('id' => $item_info['id']));
                                        //财务记录
                                        if (!$this->Financial_model->rowCount(array('type' => 'recharge_in', 'ret_id' => $item_info['id'], 'user_id' => $item_info['user_id']))) {
                                            $fields = array(
                                                'cause'        => "充值成功-[充值交易号：{$pay_log_info['out_trade_no']}]",
                                                'price'        => $item_info['money'],
                                                'balance'      => $balance,
                                                'add_time'     => time(),
                                                'user_id'      => $user_info['id'],
                                                'username'     => $user_info['username'],
                                                'type'         => 'recharge_in',
                                                'pay_way'      => '2',
                                                'ret_id'       => $item_info['id'],
                                                'from_user_id' => $user_info['id']
                                            );
                                            $this->Financial_model->save($fields);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                echo "success";
            } else {
                echo "fail";
            }
        }
    }

    //支付 宝同步通知
    public function alipay_return_recharge() {
        require_once("sdk/alipay/alipay.config.php");
        require_once("sdk/alipay/lib/alipay_notify.class.php");
        $alipay_config['notify_url'] = base_url() . 'index.php/user/alipay_notify_recharge';
        $alipay_config['return_url'] = base_url() . 'index.php/user/alipay_return_recharge';

        $gloabPreUrl = $this->session->userdata('gloabPreUrl');
        $alipayNotify = new AlipayNotify($alipay_config);
        $verify_result = $alipayNotify->verifyReturn();
        if (!$verify_result) {
            $data = array(
                'user_msg' => '充值支付失败',
                'user_url' => $gloabPreUrl
            );

            $this->session->set_userdata($data);
            redirect(base_url() . 'index.php/message/index');
        }
        $out_trade_no = $_GET['out_trade_no'];
        //支付宝交易号
        $trade_no = $_GET['trade_no'];
        //交易状态
        $trade_status = $_GET['trade_status'];
        //买家支付宝账号
        $buyer_email = $this->input->get('buyer_email', TRUE);
        //通知时间
        $notify_time = strtotime($this->input->get('notify_time', TRUE));

        if ($_GET['trade_status'] == 'TRADE_FINISHED' || $_GET['trade_status'] == 'TRADE_SUCCESS') {
            //判断该笔订单是否在商户网站中已经做过处理
            //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
            //如果有做过处理，不执行商户的业务程序
            $pay_log_info = $this->Pay_log_model->get('*', array('out_trade_no' => $out_trade_no, 'order_type' => 'recharge', 'payment_type' => 'alipay'));
            if (!$pay_log_info) {
                $data = array(
                    'user_msg' => '此充值记录不存在,支付失败',
                    'user_url' => $gloabPreUrl
                );
                $this->session->set_userdata($data);
                redirect(base_url() . 'index.php/message/index');
            }
            if ($pay_log_info['trade_status'] != $trade_status && $pay_log_info['trade_status'] != 'TRADE_FINISHED' && $pay_log_info['trade_status'] != 'TRADE_SUCCESS' && $pay_log_info['trade_status'] != 'TRADE_CLOSED') {
//                //支付记录
//                $fields = array(
//                    'payment_type' => 'alipay',
//                    'order_type' => 'recharge',
//                    'trade_no' => $trade_no,
//                    'trade_status' => $trade_status,
//                    'buyer_email' => $buyer_email,
//                    'notify_time' => $notify_time
//                );
//                if (!$this->Pay_log_model->save($fields, array('id' => $pay_log_info['id']))) {
//                    $data = array(
//                        'user_msg' => '充值失败，请重试',
//                        'user_url' => $gloabPreUrl
//                    );
//                    $this->session->set_userdata($data);
//                    redirect(base_url() . 'index.php/message/index');
//                }
//                $item_info = $this->Recharge_record_model->get('*',array('order_number' => $out_trade_no,'status'=>0));
//                $user_info = $this->User_model->get('id, total, username', array('id' => $item_info['user_id']));
//                if ($item_info && $user_info) {
//                    //财务记录
//                    if (!$this->Financial_model->rowCount(array('type' => 'recharge_in', 'ret_id' => $item_info['id'], 'user_id'=>$item_info['user_id']))) {
//                        $balance = $user_info['total'] + $item_info['money'];
//                        if ($this->User_model->save(array('total'=>$balance), array('id'=>$item_info['user_id']))) {
//                            $this->Recharge_record_model->save(array('status'=>1),array('id'=>$item_info['id']));
//                            $fields = array(
//                                'cause' => "充值成功-[充值交易号：{$pay_log_info['out_trade_no']}]",
//                                'price' =>   $item_info['money'],
//                                'balance' => $balance,
//                                'add_time' => time(),
//                                'user_id' => $user_info['id'],
//                                'username' => $user_info['username'],
//                                'type' => 'recharge_in',
//                                'pay_way' => '2',
//                                'ret_id' => $item_info['id'],
//                                'from_user_id'=>$user_info['id']
//                            );
//                            $this->Financial_model->save($fields);
//                        }
//                    }
//                }
                redirect(base_url() . "index.php/user/my_pay_result_recharge/{$pay_log_info['id']}.html");
            } else {
                $fields = array('payment_type' => 'alipay', 'order_type' => 'recharge');
                if (!$pay_log_info['buyer_email']) {
                    $fields['buyer_email'] = $buyer_email;
                }
                if (!$pay_log_info['notify_time']) {
                    $fields['notify_time'] = $notify_time;
                }
                if ($fields) {
                    $this->Pay_log_model->save($fields, array('id' => $pay_log_info['id']));
                }
                redirect(base_url() . "index.php/user/my_pay_result_recharge/{$pay_log_info['id']}.html");
            }
        }else{
            $data = array(
                'user_msg' => $this->_trade_status_msg[$trade_status] . '，支付失败，请重试',
                'user_url' => $gloabPreUrl
            );
            $this->session->set_userdata($data);
            redirect(base_url() . 'index.php/message/index');
        }
    }

    //付款-微信支付界面
    public function my_pay_weixin_recharge() {
        $gloabPreUrl = $this->session->userdata('gloabPreUrl');
        checkLogin();
        $systemInfo = $this->System_model->get('*', array('id' => 1));
        $money = $this->input->post('money',TRUE);
        if (!$money) {
            $data = array(
                'user_msg' => '操作异常',
                'user_url' => $gloabPreUrl
            );

            $this->session->set_userdata($data);
            redirect(base_url() . 'index.php/message/index');
        }

        if (!preg_match('/^\d+(\.\d+)?$/', $money)) {
            $data = array(
                'user_msg' => '请输入正确的充值金额',
                'user_url' => $gloabPreUrl
            );

            $this->session->set_userdata($data);
            redirect(base_url() . 'index.php/message/index');
        }
        if ($money <= 0) {
            $data = array(
                'user_msg' => '充值金额必须大于零',
                'user_url' => $gloabPreUrl
            );

            $this->session->set_userdata($data);
            redirect(base_url() . 'index.php/message/index');
        }
        $user_id = $this->session->userdata('user_id');
        //判断下单用户是否存在
        $user_info = $this->User_model->get('*', array('user.id' => $user_id));
        if (!$user_info) {
            $data = array(
                'user_msg' => '用户信息不存在，结算失败',
                'user_url' => $gloabPreUrl
            );
            $this->session->set_userdata($data);
            redirect(base_url() . 'index.php/message/index');
        }
        $out_trade_no = $this->_get_unique_recharge_number();

        $total_fee =  $money;
        //生成充值记录
        $column_arr = array(
            'user_id'=>$user_id,
            'order_number'=>$out_trade_no,
            'pay_type'=>'微信',
            'status'=>0,
            'money'=>$total_fee,
            'balance'=>$user_info['total'],
            'add_time'=>time(),
        );
        if (!$this->Recharge_record_model->save($column_arr)) {
            $data = array(
                'user_msg' => '创建充值订单失败，稍后重试',
                'user_url' => $gloabPreUrl
            );
            $this->session->set_userdata($data);
            redirect(base_url() . 'index.php/message/index');
        }
        if(!$this->Pay_log_model->rowCount(array('out_trade_no'=>$out_trade_no, 'payment_type'=>'weixin', 'order_type'=>'recharge'))) {
            $fields = array(
                'user_id'=>       $user_id,
                'total_fee'=>     $money,
                'total_fee_give'=>0,
                'out_trade_no'=>  $out_trade_no,
                'order_num'=>     $out_trade_no,
                'trade_status'=>  'WAIT_BUYER_PAY',
                'add_time'=>      time(),
                'payment_type'=>  'weixin',
                'order_type'=>    'recharge',
            );
            $pay_log_id = $this->Pay_log_model->save($fields);
            if (!$pay_log_id) {
                $data = array(
                    'user_msg' => '支付失败，请重试',
                    'user_url' => $gloabPreUrl
                );
                $this->session->set_userdata($data);
                redirect(base_url() . 'index.php/message/index');
            }
        }


        $pay_log_info = $this->Pay_log_model->get('*', array('out_trade_no'=>$out_trade_no, 'user_id'=>$user_id, 'payment_type' =>'weixin', 'trade_status'=>'WAIT_BUYER_PAY','order_type'=>'recharge'));
        if (!$pay_log_info) {
            $data = array(
                'user_msg' => '此充值记录不存在',
                'user_url' => $gloabPreUrl
            );
            $this->session->set_userdata($data);
            redirect(base_url() . 'index.php/message/index');
        }

        /********************微信支付**********************/
        require_once "sdk/weixin_pay/lib/WxPay.Api.php";
        require_once "sdk/weixin_pay/WxPay.NativePay.php";

        $product_id = $out_trade_no;

        $notify = new NativePay();
        $input = new WxPayUnifiedOrder();
        $input->SetBody("蚁立网账户余额充值");
        $input->SetAttach($out_trade_no);
        $input->SetTotal_fee($pay_log_info['total_fee'] * 100);
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 600));
        $input->SetNotify_url(base_url() . "index.php/user/weixin_notify_recharge");
        $input->SetTrade_type("NATIVE");
        $input->SetProduct_id($product_id);
        $input->SetOut_trade_no($out_trade_no);
        $result = $notify->GetPayUrl($input);
        $qr_url = '';
        if ($result['return_code'] == 'SUCCESS' && $result['result_code'] == 'SUCCESS') {
            $qr_url = $result["code_url"];
        }


        $data = array(
            'site_name' => $systemInfo['site_name'],
            'index_name' => $systemInfo['index_name'],
            'index_url' => $systemInfo['index_url'],
            'client_index' => $systemInfo['client_index'],
            'title' => '充值付款',
            'keywords' => $systemInfo['site_keywords'],
            'description' => $systemInfo['site_description'],
            'site_copyright' => $systemInfo['site_copyright'],
            'icp_code' => $systemInfo['icp_code'],
            'html' => $systemInfo['html'],
            'pay_log_info' => $pay_log_info,
            'total' => $pay_log_info ? $pay_log_info['total_fee'] : '0.00',
            'qr_url' => $qr_url,
            'result' => $result,
            'out_trade_no' => $out_trade_no,
            'template' => $this->_template
        );
        $layout = array(
            'content' => $this->load->view("{$this->_template}/my_pay_weixin_recharge", $data, TRUE)
        );
        $this->load->view('layout/default', $layout);
        //缓存
        if ($systemInfo['cache'] == 1) {
            $this->output->cache($systemInfo['cache_time']);
        }
    }

    //微信支付异步通知
    public function weixin_notify_recharge() {
        /********************微信支付**********************/
        require_once "sdk/weixin_pay/lib/WxPay.Api.php";

        $xml = $GLOBALS['HTTP_RAW_POST_DATA'];
        try {
            $data = WxPayResults::Init($xml);
            if (array_key_exists("transaction_id", $data)) {
                $input = new WxPayOrderQuery();
                $input->SetTransaction_id($data["transaction_id"]);
                $result = WxPayApi::orderQuery($input);
                if (array_key_exists("return_code", $result)
                    && array_key_exists("result_code", $result)
                    && $result["return_code"] == "SUCCESS"
                    && $result["result_code"] == "SUCCESS"
                ) {
                    //订单号
                    $order_num = $result['attach'];
                    //商户订单号
                    $out_trade_no = $result['out_trade_no'];
                    //微信交易号
                    $trade_no = $result['transaction_id'];
                    //通知时间
                    $notify_time = $result['time_end'];
                    $total_fee = $result['total_fee'];

                    $pay_log_info = $this->Pay_log_model->get('*', array('out_trade_no' => $out_trade_no, 'order_type' => 'recharge', 'payment_type' => 'weixin'));
                    if ($pay_log_info && $total_fee == $pay_log_info['total_fee'] * 100) {
                        if ($pay_log_info['trade_status'] != 'TRADE_FINISHED' && $pay_log_info['trade_status'] != 'TRADE_SUCCESS' && $pay_log_info['trade_status'] != 'TRADE_CLOSED') {
                            //支付记录
                            $fields = array(
                                'trade_status' => 'TRADE_SUCCESS',
                                'buyer_email' => '',
                                'trade_no' => $trade_no,
                                'notify_time' => strtotime($notify_time)
                            );
                            if ($this->Pay_log_model->save($fields, array('id' => $pay_log_info['id']))) {
                                $item_info = $this->Recharge_record_model->get('*', array('order_number' => $out_trade_no, 'status' => 0));
                                $user_info = $this->User_model->get('id, total, username', array('id' => $item_info['user_id']));
                                if ($item_info && $user_info) {
                                    $balance = $user_info['total'] + $item_info['money'];
                                    if ($this->User_model->save(array('total' => $balance), array('id' => $item_info['user_id']))) {
                                        $this->Recharge_record_model->save(array('status' => 1), array('id' => $item_info['id']));
                                        //财务记录
                                        if (!$this->Financial_model->rowCount(array('type' => 'recharge_in', 'ret_id' => $item_info['id'], 'user_id' => $item_info['user_id']))) {
                                            $fields = array(
                                                'cause'        => "充值成功-[充值交易号：{$pay_log_info['out_trade_no']}]",
                                                'price'        => $item_info['money'],
                                                'balance'      => $balance,
                                                'add_time'     => time(),
                                                'user_id'      => $user_info['id'],
                                                'username'     => $user_info['username'],
                                                'type'         => 'recharge_in',
                                                'pay_way'      => '3',
                                                'ret_id'       => $item_info['id'],
                                                'from_user_id' => $user_info['id']
                                            );
                                            $this->Financial_model->save($fields);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        } catch (WxPayException $e) {
            $msg = $e->errorMessage();
        }
    }

    /***
     * 微信支付心跳程序
     */
    public function get_weixin_heart_recharge() {
        if ($_POST) {
            $out_trade_no = $this->input->post('out_trade_no');
            if (!$out_trade_no) {
                printAjaxError('fail', '参数错误');
            }
            $pay_log_info = $this->Pay_log_model->get('id, trade_status, out_trade_no', array('out_trade_no' => $out_trade_no, 'payment_type' => 'weixin', 'order_type' => 'recharge'));
            if (!$pay_log_info) {
                printAjaxError('fail', '支付记录不存在');
            }
            printAjaxData($pay_log_info);
        }
    }

    //付款完成
    public function my_pay_result_recharge($pay_log_id = NULL) {
        $this->session->set_userdata(array("gloabPreUrl" => base_url() . 'index.php/' . uri_string()));
        //判断是否登录
        checkLogin();
        if (!$pay_log_id) {
            $data = array(
                'user_msg' => '此充值记录信息不存在',
                'user_url' => base_url()
            );
            $this->session->set_userdata($data);
            redirect('/message/index');
        }
        $user_id = $this->session->userdata('user_id');
        $systemInfo = $this->System_model->get('*', array('id' => 1));
        $item_info = $this->Pay_log_model->get('*', "trade_status in ('TRADE_SUCCESS','TRADE_FINISHED') and user_id = {$user_id} and id = {$pay_log_id} ");
        if (!$item_info) {
            $data = array(
                'user_msg' => '此充值记录信息不存在',
                'user_url' => base_url()
            );
            $this->session->set_userdata($data);
            redirect('/message/index');
        }

        $data = array(
            'site_name' => $systemInfo['site_name'],
            'index_name' => $systemInfo['index_name'],
            'index_url' => $systemInfo['index_url'],
            'client_index' => $systemInfo['client_index'],
            'title' => '充值完成' . $systemInfo['site_name'],
            'keywords' => $systemInfo['site_keywords'],
            'description' => $systemInfo['site_description'],
            'site_copyright' => $systemInfo['site_copyright'],
            'icp_code' => $systemInfo['icp_code'],
            'html' => $systemInfo['html'],
            'item_info' => $item_info
        );
        $layout = array(
            'content' => $this->load->view('user/my_pay_result_recharge', $data, TRUE)
        );
        $this->load->view('layout/default', $layout);
        //缓存
        if ($systemInfo['cache'] == 1) {
            $this->output->cache($systemInfo['cache_time']);
        }
    }

    /**
     * 第三方登录代码复用
     * @param type $auth_type 微信wechat，微博weibo,腾讯QQ qq
     * @param type $exist
     * @param type $nickname
     * @param type $headimgurl
     * @param type $unique_str
     */
    private function first_auth_login($auth_type = '', $exist = array(), $nickname = '', $headimgurl = '', $sex = 0, $unique_str = '', $is_mobile = false) {
        if (!$exist) {
            //积分
            $score_setting_info = $this->Score_setting_model->get('reg_score', array('id' => 1));
            $addTime = time();
//            $ip_arr = getUserIPAddress();
            $fields = array(
                'user_group_id' => 1,
                'username' => '',
                'login_time' => $addTime,
//                'ip' => $ip_arr[0],
//                'ip_address' => $ip_arr[1],
                'password' => '',
                'mobile' => '',
                'score'=>$score_setting_info['reg_score'],
                'add_time' => $addTime,
                'nickname' => $nickname,
                'path' => empty($headimgurl) ? 'images/xcx/user-default.png' : $headimgurl,
                'sex' => $sex,
            );
            switch ($auth_type) {
                case 'wechat' :
                    $fields['wx_unionid'] = $unique_str;
                    break;
                case 'weibo' :
                    $fields['wb_uid'] = $unique_str;
                    break;
                default :
                    $fields['qq_unionid'] = $unique_str;
            }
//            do{
//                $user_num = getRandPass(6);
//            }while($this->User_model->count2(array('user_num'=>$user_num)) ? true : false);
//            $fields['user_num'] = $user_num;
            $ret_id = $this->User_model->save($fields);
            if ($ret_id) {
                if ($score_setting_info['reg_score']) {
                    $sFields = array(
                        'cause' => '注册送积分-注册成功',
                        'score' => $score_setting_info['reg_score'],
                        'balance' => $score_setting_info['reg_score'],
                        'type' => 'reg_score_in',
                        'add_time' => time(),
                        'username' => '',
                        'user_id' => $ret_id,
                        'ret_id' => $ret_id
                    );
                    $this->Score_model->save($sFields);
                }
                //QQ登录并且用手机登录
                if ($auth_type == 'qq' && $is_mobile) {
                    $session_id = $this->session->userdata['session_id'];
                    $this->session->set_userdata(array('user_id' => $ret_id));
                    redirect(base_url().'wx/member-weixin.html?login_type=sf&sid='.$session_id);
                    exit;
                } else {
                    $this->_setCookie(array('id'=>$ret_id, 'nickname'=>'', 'username'=>'', 'login_time'=>time(), 'ip'=>$ip_arr[0], 'ip_address'=>$ip_arr[1]), 0);
                }
            } else {
                printAjaxError('fail', '登录失败！');
            }
        } else {
//            $ip_arr = getUserIPAddress();
            $fields = array(
                'login_time' => time(),
//                'ip' => $ip_arr[0],
//                'ip_address' => $ip_arr[1]
            );
            //判断最积分
            $score = 0;
            $balance = 0;
            $score_setting_info = $this->Score_setting_model->get('login_score,login_score_max', array('id' => 1));
            $todayScoreTotal = $this->Score_model->getTodayTotal($exist['id']);
            if ($todayScoreTotal + $score_setting_info['login_score'] <= $score_setting_info['login_score_max']) {
                $balance = $exist['score'] + $score_setting_info['login_score'];
                $score = $score_setting_info['login_score'];
                $fields['score'] = $balance;
            }
            $ret_id = $this->User_model->save($fields, array('id' => $exist['id']));
            if ($ret_id) {
                if ($score) {
                    $sFields = array(
                        'cause' => '每日签到送积分-登录成功',
                        'score' => $score,
                        'balance' => $balance,
                        'type' => 'login_score_in',
                        'add_time' => time(),
                        'username' => $exist['username'],
                        'user_id' => $exist['id'],
                        'ret_id' => $exist['id']
                    );
                    $this->Score_model->save($sFields);
                }
                //QQ登录并且用手机登录
                if ($auth_type == 'qq' && $is_mobile) {
                    $session_id = $this->session->userdata['session_id'];
                    $this->session->set_userdata(array('user_id' => $exist['id']));
                    redirect(base_url().'wx/member-weixin.html?login_type=sf&sid='.$session_id);
                    exit;
                } else {
                    $this->_setCookie($exist, 0);
                }
            }
        }
        if (!$is_mobile) {
            redirect(base_url() . "index.php/user/index");
        }
    }

    //获取唯一充值订单号
    public function _get_unique_pay_log_number(){

        //一秒钟一万件的量
        $randCode = '';
        while (true) {
            $randCode = getOrderNumber(6);
            $count = $this->Ptkj_record_model->rowCount(array("bond_number" => $randCode));
            $count1 = $this->Orders_model->rowCount(array("order_number" => $randCode));
            if ($count > 0 || $count1 > 0) {
                $randCode = '';
                continue;
            } else {
                break;
            }
        }
        return $randCode;
    }

    //获取唯一的充值订单号
    private function _get_unique_recharge_number() {
        //一秒钟一万件的量
        $randCode = '';
        while (true) {
            $randCode = getOrderNumber(5);
            $count = $this->Recharge_record_model->rowCount(array('order_number' => $randCode));
            if ($count > 0) {
                $randCode = '';
                continue;
            } else {
                break;
            }
        }
        return $randCode;
    }

    //获取唯一的蚁立号
    private function _get_unique_eli_code($id) {
        //一秒钟一万件的量
        $randCode = '';
        $len = 9 - strlen($id);
        while (true) {
            $randCode = $id.getRandCode($len);
            $count = $this->User_model->count2(array('eli_code' => $randCode));
            if ($count > 0) {
                $randCode = '';
                continue;
            } else {
                break;
            }
        }
        return $randCode;
    }

    private function _deleteCookie() {
        $this->session->unset_userdata("user_id");
        delete_cookie('user_id');
        delete_cookie('user_username');
        delete_cookie('user_login_time');
        delete_cookie('user_ip');
        delete_cookie('user_ip_address');
        delete_cookie('seller_group_id');
    }

    private function _setCookie($data, $expire = 0) {
        $cookie1 = array(
            'name' => 'user_id',
            'value' => $data['id'],
            'expire' => $expire
        );
        $this->session->set_userdata(array('user_id'=>$data['id']));
        set_cookie($cookie1);
        $cookie2 = array(
            'name' => 'user_username',
            'value' => $data['nickname'] ? $data['nickname'] : $data['username'],
            'expire' => $expire
        );
        set_cookie($cookie2);
        $cookie4 = array(
            'name' => 'user_login_time',
            'value' => $data['login_time'],
            'expire' => $expire
        );
        set_cookie($cookie4);
        // $cookie5 = array(
        //     'name' => 'user_ip',
        //     'value' => $data['ip'],
        //     'expire' => $expire
        // );
        // set_cookie($cookie5);
        // $cookie6 = array(
        //     'name' => 'user_ip_address',
        //     'value' => $data['ip_address'],
        //     'expire' => $expire
        // );
        // set_cookie($cookie6);

        $cookie7 = array(
            'name'  =>'seller_group_id',
            'value' =>$data['seller_group_id'],
            'expire'=>$expire
        );
        set_cookie($cookie7);
    }

    //加盐算法
    private function _createPasswordSALT($user, $salt, $password) {
        return md5(strtolower($user) . $salt . $password);
    }

    //生成推广码
    private function _createPopCode() {
        $pop_code = getRandCode(8);
        $count = $this->User_model->count(array("pop_code" => $pop_code));
        if ($count) {
            $i = 0;
            while ($i < 100) {
                $i++;
                $pop_code = getRandCode(8);
                $count = $this->User_model->count(array("pop_code" => $pop_code));
                if ($count) {
                    continue;
                } else {
                    break;
                }
            }
        }

        return $pop_code;
    }

}

/* End of file page.php */
/* Location: ./application/client/controllers/page.php */