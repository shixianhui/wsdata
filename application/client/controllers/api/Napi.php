<?php

class Napi extends CI_Controller
{
    // private $appid = 'wx975deacfd9306283';
    // private $appSecret = '2441e631e721375a89127ac0b3888c60';
    private $appid = 'wxf3079356aedb39a8';
    private $appSecret = '093decdce3971440e0bd71ba04330306';
    private $byteAppId = 'tt18da6e6787e2d65c01';
    private $byteAppSecret = '9e20a6cf3dddeecc0d4721ab6f7f5f9b87405985';
    private $_business_status_arr = ['暂未营业','正在营业','休息中'];
    private $_orders_status_arr = [
        '0' => '待付款',
        '1' => '待使用',
        '2' => '待评价',
        '3' => '交易成功',
        '9' => '已退款',
        '10' => '交易关闭',
    ];
    public function __construct()
    {
        parent::__construct();
        $this->load->library('Form_validation');
        $this->load->library('Hbdapiclass');
        $this->_beforeFilter();
    }

    private function _tmp_user_info($user_id = NULL, $session_id = 0, $is_new = 0) {
        $user_info = $this->Users_model->get('id,username,mobile,path,nickname,total,sex,real_name,type', array('id' => $user_id));
        $user_info['session_id'] = $session_id;
        $tmp_path = filter_image_path($user_info['path']);
        $user_info['path'] = $tmp_path['path'];
        $user_info['path_thumb'] = $tmp_path['path_thumb'];

        $user_info['is_new'] = $is_new;
        if ($is_new) {
            $this->load->model('Coupons_model', '', TRUE);
            $coupon_list = $this->Coupons_model->gets('*', ['way'=>1, 'status'=>1]);
            $user_info['coupon_list'] = $coupon_list;
        }

        return $user_info;
    }

    private function _delete_session() {
        $this->session->unset_userdata("user_id");
        session_write_close();
    }

    private function _set_session($user_id = "") {
        $this->session->set_userdata(array('user_id' => $user_id));
        session_write_close();
    }

    //加盐算法
    private function _createPasswordSALT($user, $salt, $password) {

        return md5($user . $salt . $password);
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
        $this->load->model('Users_model', '', TRUE);
        $item_info = $this->Users_model->get('*', array('id' => $user_id, 'display <>'=>3));
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

    /**
     * 登录接口
     * @param username 用户名
     * @param password 密码
     *
     * @return json
     */
    public function login()
    {
        if ($_POST) {
            $this->load->model('Users_model', '', TRUE);
            $username = trim($this->input->post('username', TRUE));
            $password = $this->input->post('password', TRUE);
            if (!$username) {
                printAjaxError('username', '用户名不能为空');
            }
            if (!$password) {
                printAjaxError('password', '登录密码不能为空');
            }
            $count = $this->Users_model->count(array('lower(username)' => strtolower($username)));
            if (!$count) {
                printAjaxError('username', '用户名不存在，登录失败');
            }
            $user_info = $this->Users_model->login($username, $password);
            if (!$user_info) {
                printAjaxError('fail', '用户名或密码错误，登录失败');
            }
            if ($user_info['display'] == 0) {
                printAjaxError('fail', '你的账户还未激活，请联系客服激活');
            } else if ($user_info['display'] == 2) {
                printAjaxError('fail', '你的账户已冻结，请联系客服');
            }
            $session_id = session_id();
//            session_write_close();
            $this->_set_session($user_info['id']);
            printAjaxData($this->_tmp_user_info($user_info['id'], $session_id));
        }
    }


    /**
     * 手机验证码登录
     */
    public function mobile_login() {
        $this->load->model('Users_model', '', TRUE);
        $this->load->model('Sms_model', '', TRUE);

        if ($_POST) {
            $mobile = $this->input->post('mobile', TRUE);
            $smscode = $this->input->post('smscode', TRUE);
            $push_cid = $this->input->post('push_cid', TRUE);
            if (!$this->form_validation->required($mobile)) {
                printAjaxError('username', '请输入手机号');
            }
            if (!$this->form_validation->valid_mobile($mobile)) {
                printAjaxError('username', '请输入正确的手机号');
            }
            if (!$this->form_validation->required($smscode)) {
                printAjaxError('smscode', '请输入短信验证码');
            }
            $timestamp = time() - 5*60;
            if (!$this->Sms_model->get('id', "smscode = '{$smscode}' and mobile = '{$mobile}' and add_time > {$timestamp}")) {
                printAjaxError('smscode', '短信验证码错误或者已过期');
            }
            $user_info = $this->Users_model->get('id,username,nickname,push_cid', array('username'=>$mobile, 'display <>'=>3));

            if ($user_info){
                $fields = array(
                    'login_time' => time()
                );
                if ($push_cid && $user_info['push_cid'] != $push_cid) {
                    $fields['push_cid'] = $push_cid;
                }

                $ret_id = $this->Users_model->save($fields, array('id' => $user_info['id']));

                $session_id = session_id();
                $this->_set_session($user_info['id']);
                printAjaxData($this->_tmp_user_info($user_info['id'], $session_id));
            }else{
                $parent_id = $this->input->post('parent_id', TRUE);
                $addTime = time();
                $data = array(
                    'username'=>$mobile,
                    'mobile'=>$mobile,
                    'nickname'=>substr(md5($mobile),0,9),
                    'add_time' => $addTime,
                    'login_time' => $addTime,
                    'parent_id' => $parent_id,
                    'push_cid' => $push_cid ? $push_cid : ''
                );
                $user_id = $this->Users_model->save($data);
                if (!$user_id){
                    printAjaxError('fail','注册失败');
                }
                //新用户奖励等
                $this->reg_reward($user_id, $parent_id);

                $session_id = session_id();
                $this->_set_session($user_id);
                printAjaxData($this->_tmp_user_info($user_id, $session_id, 1));

            }

        }

        printAjaxError('fail','登录失败');
    }

    public function reg_reward($user_id, $parent_id)
    {
        //新人券
        $this->load->model('Coupons_model', '', TRUE);
        $this->load->model('User_coupons_model', '', TRUE);
        $coupon_list = $this->Coupons_model->gets('id', ['way'=>1, 'status'=>1]);
        if ($coupon_list) {
            $c_data = [];
            foreach ($coupon_list as $value) {
                $c_data[] = [
                    'user_id' => $user_id,
                    'coupon_id' => $value['id']
                ];
            }
            
            $this->User_coupons_model->save_batch($c_data);
        }
        //邀新券
        $reward_coupon_info = $this->Coupons_model->get('id', ['way'=>2, 'status'=>1]);
        if ($reward_coupon_info) {
            $u_data = [];
            for ($i=0; $i < 10; $i++) { 
                $u_data[] = [
                    'user_id' => $user_id,
                    'coupon_id' => $reward_coupon_info['id'],
                    'status' => 2
                ];
            }
            $this->User_coupons_model->save_batch($u_data);

            //邀新激活
            $parent_coupon_info = $this->User_coupons_model->get('id', ['user_id'=>$parent_id, 'status'=>2, 'coupon_id'=>$reward_coupon_info['id']]);
            if ($parent_coupon_info) {
                $this->User_coupons_model->save(['status'=>0], ['id'=>$parent_coupon_info['id']]);
            }
        }

        //平台奖金
        $this->load->model('System_model', '', TRUE);
        $system_info = $this->System_model->get('bonus_amount,invite_reward', ['id'=>1]);
        if ($this->System_model->save_column('bonus_amount', 'bonus_amount+1', ['id'=>1])) {
            $this->load->model('Bonus_record_model', '', TRUE);
            $reg_data = [
                'amount' => 1,
                'cause' => '新用户注册',
                'balance' => $system_info['bonus_amount'] + 1,
                'user_id' => $user_id,
                'type' => 1,
                'ret_id' => $user_id
            ];
            $this->Bonus_record_model->save($reg_data);
            //邀新奖励
            if ($parent_id) {
                $parent_info = $this->Users_model->get('reward', ['id'=>$parent_id, 'display' => 1]);
                if ($parent_info) {
                    $reward_balance = $parent_info['reward'] + $system_info['invite_reward'];
                    if ($this->Users_model->save(['reward'=>$reward_balance], ['id'=>$parent_id])) {
                        $financial_data = [
                            'cause' => '邀请新用户',
                            'price' => $system_info['invite_reward'],
                            'balance' => $reward_balance,
                            'user_id' => $parent_id,
                            'type' => 'invite_reward',
                            'record_type' => 1,
                            'from_user_id' => $user_id,
                            'ret_id' => $user_id,
                        ];
                        $this->load->model('Financial_model', '', TRUE);
                        $this->Financial_model->save($financial_data);
                        //平台支出
                        $balance = $system_info['bonus_amount'] + 1 - $system_info['invite_reward'];
                        if ($this->System_model->save(['bonus_amount'=>$balance], ['id'=>1])) {
                            $invite_data = [
                                'amount' => -$system_info['invite_reward'],
                                'cause' => '邀请新用户',
                                'balance' => $balance,
                                'user_id' => $parent_id,
                                'type' => 3,
                                'ret_id' => $user_id
                            ];
                            $this->Bonus_record_model->save($invite_data);
                        }
                    }
                }
                
            }
            
        }
    }

    //微信-QQ登录-注册新用户
    public function third_login_to_user()
    {
        if ($_POST) {
            $this->load->model('Users_model', '', TRUE);
            $sex = $this->input->post('sex', TRUE);
            $unionid = $this->input->post('unionid', TRUE);
            $path_url = $this->input->post('path_url', TRUE);
            $type = $this->input->post('type', TRUE);
            $push_cid = $this->input->post('push_cid', TRUE);
            $nickname = $this->input->post('nickname', TRUE);
            $parent_id = $this->input->post('parent_id', TRUE);

            if (!$unionid || !$type) {
                printAjaxError('fail', '操作异常');
            }
            $user_info = NULL;
            if ($type == 'weixin') {
                $user_info = $this->Users_model->get('*', array('wx_unionid' => $unionid, 'display <>'=>3));
            } else if ($type == 'qq') {
                $user_info = $this->Users_model->get('*', array('qq_unionid' => $unionid, 'display <>'=>3));
            } else {
                printAjaxError('fail', '无效的登录认证通道');
            }
            //已绑定用户直接登录
            if ($user_info) {
                if ($user_info['display'] == 2) {
                    printAjaxError('fail', '你的账户被冻结，请联系客服');
                }
                //登录成功
                $fields['login_time'] = time();
                if ($push_cid && $user_info['push_cid'] != $push_cid) {
                    $fields['push_cid'] = $push_cid;
                }
                $this->Users_model->save($fields, array('id'=>$user_info['id']));

                $session_id = 0;
                if ($user_info['display'] == 1) {
                    $session_id = session_id();
                    $this->_set_session($user_info['id']);
                }
                printAjaxData($this->_tmp_user_info($user_info['id'], $session_id));
            } else {
                $addTime = time();
                $fields = array(
                    'display' => 0,
                    'username' => '',
                    'password' => '',
                    'mobile' => '',
                    'login_time' => $addTime,
                    'add_time' => $addTime,
                    'nickname' => $nickname,
                    'sex' => $sex,
                    'push_cid' => $push_cid ? $push_cid : '',
                    'parent_id' => $parent_id
                );
                if ($path_url){
                    $fields['path'] = $path_url;
                }
                if ($type == 'weixin') {
                    $fields['wx_unionid'] = $unionid;
                } else if ($type == 'qq') {
                    $fields['qq_unionid'] = $unionid;
                }
                $ret_id = $this->Users_model->save($fields);
                if (!$ret_id) {
                    printAjaxError('fail', '登录失败');
                }
                $session_id = session_id();
                $this->_set_session($ret_id);
                printAjaxData($this->_tmp_user_info($ret_id, $session_id));
            }
        }
    }


    /**
     * 小程序登录
     */
    public function wx_login()
    {
        $this->load->model('Users_model', '', TRUE);
        if ($_POST) {
            $code = $this->input->post("code", true);
            $iv = $this->input->post('iv', TRUE);
            $encryptedData = $this->input->post('encryptedData', TRUE);
            $parent_id = $this->input->post('parent_id', TRUE);

            if (!$code) {
                printAjaxError('fail', 'DO NOT ACCESS!');
            }

            $appid = $this->appid;
            $appSecret = $this->appSecret;
            $json = http_curl("https://api.weixin.qq.com/sns/jscode2session?appid={$appid}&secret={$appSecret}&js_code={$code}&grant_type=authorization_code");
            $obj = json_decode($json);
            if (isset($obj->errmsg)) {
                printAjaxError('fail', 'invalid code!');
            }
            if (empty($obj->unionid)) {
                printAjaxError('fail', '小程序登录异常！');
            }
            $session_key = $obj->session_key;
            $openid = $obj->openid;
            
            $param = array('appid' => $appid, 'sessionKey' => $session_key);
            $this->load->library('WXBizDataCrypt/WXBizDataCrypt', $param);
            $pc = new WXBizDataCrypt($param);
            $errCode = $pc->decryptData($encryptedData, $iv, $data);

            if ($errCode != 0) {
                printAjaxError('fail', $errCode);
            }

            $get_user_info = json_decode($data);
            

            $user_info = $this->Users_model->get('*', array('wx_unionid' => $obj->unionid, 'display <>'=>3));

            //已绑定用户直接登录
            if ($user_info) {
                if ($user_info['display'] == 2) {
                    printAjaxError('fail', '你的账户被冻结，请联系客服');
                }

                $fields = array(
                    'login_time' => time(),
                );
                if (!$user_info['wx_openid']) {
                    $fields['wx_openid'] = $obj->openid;
                }
                $this->Users_model->save($fields, array('id' => $user_info['id']));

                $session_id = 0;
                if ($user_info['display'] == 1) {
                    $session_id = session_id();
                    $this->_set_session($user_info['id']);
                }
                
                printAjaxData($this->_tmp_user_info($user_info['id'], $session_id));
            } else {
                $addTime = time();
                $fields = array(
                    'username' => '',
                    'password' => '',
                    'mobile' => '',
                    'login_time' => $addTime,
                    'add_time' => $addTime,
                    'nickname' => $get_user_info->nickName,
                    'path' => $get_user_info->avatarUrl,
                    'sex' => $get_user_info->gender,
                    'wx_unionid' => $obj->unionid,
                    'wx_openid' => $obj->openid,
                    'display' => 0
                );
                if ($parent_id) {
                    $fields['parent_id'] = $parent_id;
                }

                $ret_id = $this->Users_model->save($fields);
                if (!$ret_id) {
                    printAjaxError('fail', '登录失败');
                }
                
                $session_id = 0;
                // $session_id = session_id();
                // $this->_set_session($ret_id);
                printAjaxData($this->_tmp_user_info($ret_id, $session_id));
            }

        }

    }

    //小程序绑定手机号
    public function bind_mobile()
    {
        if ($_POST){
            $code = $this->input->post("code", true);
            $iv = $this->input->post('iv', TRUE);
            $encryptedData = $this->input->post('encryptedData', TRUE);
            $user_id = $this->input->post('user_id', TRUE);

            $this->load->model('Users_model', '', TRUE);
            $user_info = $this->Users_model->get('*', ['id'=>$user_id]);
            if (!$user_info) {
                printAjaxError('fail', '参数异常');
            }

            $appid = $this->appid;
            $appSecret = $this->appSecret;
            $json = file_get_contents("https://api.weixin.qq.com/sns/jscode2session?appid={$appid}&secret={$appSecret}&js_code={$code}&grant_type=authorization_code");
            $obj = json_decode($json);
            if (isset($obj->errmsg)) {
                printAjaxError('fail', 'invalid code!');
            }
            $session_key = $obj->session_key;
            $param = array('appid'=>$this->appid,'sessionKey'=>$session_key);
            $this->load->library('WXBizDataCrypt/WXBizDataCrypt',$param);
            $pc = new WXBizDataCrypt($param);
            $data = '';
            $errCode = $pc->decryptData($encryptedData, $iv, $data);

            if ($errCode != 0) {
                printAjaxError('fail',$errCode);
            }

            $get_phone_info = json_decode($data);
            $mobile = $get_phone_info->purePhoneNumber;

            $mobile_user_info = $this->Users_model->get('*', array("username" => $mobile,'id <>'=>$user_id, 'display <>'=>3));
            if ($mobile_user_info){
                $user_id = $mobile_user_info['id'];
                $result = $this->Users_model->save(array('wx_unionid' => $user_info['wx_unionid'],'wx_openid'=>$user_info['wx_openid'], 'sex'=> $user_info['sex'], 'login_time'=>time()), array('id' => $user_id));
                if ($result) {
                    $this->Users_model->delete(['id'=>$user_info['id']]);
                }
            } else {
                $result = $this->Users_model->save(array('mobile' => $mobile,'username'=>$mobile,'display'=>1, 'login_time'=>time()), array('id' => $user_id));
                //新用户奖励等
                $this->reg_reward($user_id, $user_info['parent_id']);
            }
            if ($result) {

                $session_id = session_id();
                $this->_set_session($user_id);
                printAjaxData($this->_tmp_user_info($user_id, $session_id, 1));
            } else {
                printAjaxError('fail', '绑定手机号失败');
            }
        }
    }

    /**
     * 注册-绑定手机号
     */
    public function app_bind_mobile() {
        $this->load->model('Users_model', '', TRUE);
        $this->load->model('Sms_model', '', TRUE);

        if ($_POST) {
            $mobile = $this->input->post('mobile', TRUE);
            $smscode = $this->input->post('smscode', TRUE);
            $user_id = $this->input->post('user_id', TRUE);
            $user_info = $this->Users_model->get('*', ['id'=>$user_id]);
            if (!$user_info) {
                printAjaxError('fail', '参数异常');
            }
            if (!$this->form_validation->required($mobile)) {
                printAjaxError('username', '请输入手机号');
            }
            if (!$this->form_validation->valid_mobile($mobile)) {
                printAjaxError('username', '请输入正确的手机号');
            }
            if (!$this->form_validation->required($smscode)) {
                printAjaxError('smscode', '请输入短信验证码');
            }
            $timestamp = time() - 5*60;
            if (!$this->Sms_model->get('id', "smscode = '{$smscode}' and mobile = '{$mobile}' and add_time > {$timestamp}")) {
                printAjaxError('smscode', '短信验证码错误或者已过期');
            }

            $mobile_user_info = $this->Users_model->get('*', array("username" => $mobile,'id <>'=>$user_id, 'display <>'=>3));
            if ($mobile_user_info){
                $user_id = $mobile_user_info['id'];
                $result = $this->Users_model->save(array('wx_unionid' => $user_info['wx_unionid'],'wx_openid'=>$user_info['wx_openid'], 'sex'=> $user_info['sex'], 'login_time'=>time()), array('id' => $user_id));
                if ($result) {
                    $this->Users_model->delete(['id'=>$user_info['id']]);
                }
            } else {
                $result = $this->Users_model->save(array('mobile' => $mobile,'username'=>$mobile,'display'=>1, 'login_time'=>time()), array('id' => $user_id));
                //新用户奖励等
                $this->reg_reward($user_id, $user_info['parent_id']);
            }
            if (!$result){
                printAjaxError('fail','注册失败');
            }

            

            $session_id = session_id();
            $this->_set_session($user_id);
            printAjaxData($this->_tmp_user_info($user_id, $session_id, 1));

        }

        printAjaxError('fail','登录失败');
    }

    /**
     * 字节跳动小程序登录
     */
    public function byte_login()
    {
        $this->load->model('Users_model', '', TRUE);
        if ($_POST) {
            $code = $this->input->post("code", true);
            $iv = $this->input->post('iv', TRUE);
            $encryptedData = $this->input->post('encryptedData', TRUE);
            $parent_id = $this->input->post('parent_id', TRUE);

            if (!$code) {
                printAjaxError('fail', 'DO NOT ACCESS!');
            }

            $appid = $this->byteAppId;
            $appSecret = $this->byteAppSecret;
            $json = http_curl("https://developer.toutiao.com/api/apps/jscode2session?appid={$appid}&secret={$appSecret}&code={$code}");
            $obj = json_decode($json);
            if ($obj->error != 0) {
                printAjaxError('fail', '登录错误！');
            }
            if (empty($obj->unionid)) {
                printAjaxError('fail', '登录异常！');
            }

            $session_key = $obj->session_key;
            $openid = $obj->openid;
            
            $param = array('appid' => $appid, 'sessionKey' => $session_key);
            $this->load->library('WXBizDataCrypt/WXBizDataCrypt', $param);
            $pc = new WXBizDataCrypt($param);
            $errCode = $pc->decryptData($encryptedData, $iv, $data);

            if ($errCode != 0) {
                printAjaxError('fail', $errCode);
            }

            $get_user_info = json_decode($data);

            $user_info = $this->Users_model->get('*', array('byte_unionid' => $obj->unionid, 'display <>'=>3));

            //已绑定用户直接登录
            if ($user_info) {
                if ($user_info['display'] == 2) {
                    printAjaxError('fail', '你的账户被冻结，请联系客服');
                }

                $fields = array(
                    'login_time' => time(),
                );
                if (!$user_info['byte_openid']) {
                    $fields['byte_openid'] = $obj->openid;
                }
                $this->Users_model->save($fields, array('id' => $user_info['id']));

                $session_id = 0;
                if ($user_info['display'] == 1) {
                    $session_id = session_id();
                    $this->_set_session($user_info['id']);
                }
                
                printAjaxData($this->_tmp_user_info($user_info['id'], $session_id));
            } else {
                $addTime = time();
                $fields = array(
                    'username' => '',
                    'password' => '',
                    'mobile' => '',
                    'nickname' => $get_user_info->nickName,
                    'path' => $get_user_info->avatarUrl,
                    'sex' => $get_user_info->gender,
                    'login_time' => $addTime,
                    'add_time' => $addTime,
                    'byte_unionid' => $obj->unionid,
                    'byte_openid' => $obj->openid,
                    'display' => 0
                );
                if ($parent_id) {
                    $fields['parent_id'] = $parent_id;
                }

                $ret_id = $this->Users_model->save($fields);
                if (!$ret_id) {
                    printAjaxError('fail', '登录失败');
                }
                
                $session_id = 0;
                // $session_id = session_id();
                // $this->_set_session($ret_id);
                printAjaxData($this->_tmp_user_info($ret_id, $session_id));
            }

        }

    }

    //字节小程序绑定手机号
    public function byte_bind_mobile()
    {
        if ($_POST){
            $code = $this->input->post("code", true);
            $iv = $this->input->post('iv', TRUE);
            $encryptedData = $this->input->post('encryptedData', TRUE);
            $user_id = $this->input->post('user_id', TRUE);

            $this->load->model('Users_model', '', TRUE);
            $user_info = $this->Users_model->get('*', ['id'=>$user_id]);
            if (!$user_info) {
                printAjaxError('fail', '参数异常');
            }

            $appid = $this->byteAppId;
            $appSecret = $this->byteAppSecret;
            $json = http_curl("https://developer.toutiao.com/api/apps/jscode2session?appid={$appid}&secret={$appSecret}&code={$code}");
            $obj = json_decode($json);
            if ($obj->error != 0) {
                printAjaxError('fail', 'invalid code!');
            }
            $session_key = $obj->session_key;
            $param = array('appid'=>$appid,'sessionKey'=>$session_key);
            $this->load->library('WXBizDataCrypt/WXBizDataCrypt',$param);
            $pc = new WXBizDataCrypt($param);
            $data = '';
            $errCode = $pc->decryptData($encryptedData, $iv, $data);

            if ($errCode != 0) {
                printAjaxError('fail',$errCode);
            }

            $get_phone_info = json_decode($data);
            $mobile = $get_phone_info->purePhoneNumber;

            $mobile_user_info = $this->Users_model->get('*', array("username" => $mobile,'id <>'=>$user_id, 'display <>'=>3));
            if ($mobile_user_info){
                $user_id = $mobile_user_info['id'];
                $result = $this->Users_model->save(array('byte_unionid' => $user_info['byte_unionid'],'byte_openid'=>$user_info['byte_openid'], 'sex'=> $user_info['sex'], 'login_time'=>time()), array('id' => $user_id));
                if ($result) {
                    $this->Users_model->delete(['id'=>$user_info['id']]);
                }
            } else {
                $result = $this->Users_model->save(array('mobile' => $mobile,'username'=>$mobile,'display'=>1, 'login_time'=>time()), array('id' => $user_id));
                //新用户奖励等
                $this->reg_reward($user_id, $user_info['parent_id']);
            }
            if ($result) {

                $session_id = session_id();
                $this->_set_session($user_id);
                printAjaxData($this->_tmp_user_info($user_id, $session_id, 1));
            } else {
                printAjaxError('fail', '绑定手机号失败');
            }
        }
    }

    /**
     * 短信验证码校验
     * @param username 手机号
     * @param smscode 短信验证码
     * @return json
     */
    public function check_sms_code() {
        $this->load->model('Sms_model', '', TRUE);
        if ($_POST) {
            $username = trim($this->input->post('username', TRUE));
            $smscode = $this->input->post('smscode', TRUE);
            if (!$this->form_validation->required($username)) {
                printAjaxError('username', '请输入手机号');
            }
            if (!preg_match('/^((\(\d{2,3}\))|(\d{3}\-))?(13|14|15|16|17|18|19)\d{9}$/', $username)) {
                printAjaxError('username', '请输入正确的手机号');
            }
            if (!$this->form_validation->required($smscode)) {
                printAjaxError('smscode', '请输入短信验证码');
            }
            $timestamp = time() - 5*60;
            if (!$this->Sms_model->get('id', "smscode = '{$smscode}' and mobile = '{$username}' and add_time > {$timestamp}")) {
                printAjaxError('smscode', '短信验证码错误或者已过期');
            }
            printAjaxSuccess('success', '短信验证码校验成功');
        }
    }

    /**
     * 找回密码
     *
     * @param username 手机号
     * @param passwd 密码
     * @param ref_password 确认密码
     * @param smscode 短信验证码
     *
     * @return json
     */
    public function get_pass() {
        $this->load->model('Users_model', '', TRUE);
        $this->load->model('Sms_model', '', TRUE);
        if ($_POST) {
            $username = $this->input->post('username', TRUE);
            $password = $this->input->post('password', TRUE);
            $refPassword = $this->input->post('ref_password', TRUE);
            $smscode = $this->input->post('smscode', TRUE);
            if (!$username) {
                printAjaxError('username', "手机号不能为空");
            }
            if (!preg_match('/^((\(\d{2,3}\))|(\d{3}\-))?(13|14|15|16|17|18|19)\d{9}$/', $username)) {
                printAjaxError('username', '请输入正确的手机号');
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
            if (!$smscode) {
                printAjaxError('smscode', "短信验证码不能为空");
            }
            $userInfo = $this->Users_model->get('id,username', array('lower(username)' => strtolower($username)));
            if (!$userInfo) {
                printAjaxError('fail', "手机号不存在");
            }
            $timestamp = time() - 5*60;
            if (!$this->Sms_model->get('id', "smscode = '{$smscode}' and mobile = '{$username}' and add_time > {$timestamp}")) {
                printAjaxError('smscode', '短信验证码错误或者已过期');
            }
            $fields = array(
                'password' => $this->Users_model->getPasswordSalt($userInfo['username'], $refPassword)
            );
            if ($this->Users_model->save($fields, array('id' => $userInfo['id']))) {
                printAjaxSuccess('success', '密码修改成功');
            } else {
                printAjaxError('fail', '密码修改失败');
            }
        }
    }

    /**
     * 获取短信验证码
     * @param username 手机号
     * @param code 验证码
     * @return json
     */
    public function get_sms_code() {
        $this->load->model('Users_model', '', TRUE);
        $this->load->model('Sms_model', '', TRUE);
        if ($_POST) {
            $type = $this->input->post('type', TRUE);
            $mobile = $this->input->post('mobile', TRUE);
            if (!$this->form_validation->valid_mobile($mobile)) {
                printAjaxError('username', '请输入正确的手机号');
            }

            if ($type == 'reg') {
                $count = $this->Users_model->count(array("username" => $mobile));
                if ($count) {
                    printAjaxError('username', '此手机号已被使用');
                }
            } else if ($type == 'get_pass') {
                $count = $this->Users_model->count(array("username" => $mobile));
                if ($count == 0) {
                    printAjaxError('username', '您注册的手机号不存在!');
                }
            } else if ($type == 'change_mobile') {
                $count = $this->Users_model->count(array("mobile" => $mobile));
                if ($count) {
                    printAjaxError('username', '此手机已被使用');
                }
            } else if ($type == 'change_pay_pass') {
                $user_info = $this->_check_login();
                if (!$user_info['mobile']) {
                    printAjaxError('mobile', '您暂未绑定手机号,请先绑定手机号码!');
                }
                if ($user_info['mobile'] != $mobile) {
                    printAjaxError('username', '手机号错误');
                }
            } else if ($type == 'reg_login') {

            } else {
                printAjaxError('type', 'type值异常!');
            }

            $add_time = time();
            $time_dif = $add_time - 60;
            $sms_info = $this->Sms_model->get('*', "mobile = '{$mobile}' and add_time > $time_dif");
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
                printAjaxError('fail', '发送验证码次数超限');
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



    /**
     * 退出登录
     */
    public function logout() {
        $this->_delete_session();
        printAjaxSuccess('success', '退出成功');
    }

    //获取用户信息
    public function get_user_info()
    {
        $user_info = $this->_check_login();
        if ($user_info) {
            //头像
            $tmp_image_arr = filter_image_path($user_info['path']);
            $user_info['path'] = $tmp_image_arr['path'];
            $user_info['path_thumb'] = $tmp_image_arr['path_thumb'];

            $attachment_list = array();
            if ($user_info['is_id_card_auth'] && $user_info['id_card_path']) {
                $this->load->model('Attachment_model', '', TRUE);
                $tmp_atm_ids = preg_replace(array('/^_/', '/_$/', '/_/'), array('', '', ','), $user_info['id_card_path']);
                $attachment_list = $this->Attachment_model->gets('id,path',"id in ($tmp_atm_ids)");
                if ($attachment_list) {
                    foreach ($attachment_list as $key => $value) {
                        $tmp_image = filter_image_path($value['path']);
                        $attachment_list[$key]['path'] = $tmp_image['path'];
                        $attachment_list[$key]['path_thumb'] = $tmp_image['path_thumb'];
                    }
                }
            }
            $user_info['attachment_list'] = $attachment_list;

            $this->load->model('Stores_model', '', TRUE);
            $store_info = $this->Stores_model->get('id,status', ['user_id'=>$user_info['id'], 'status'=>1]);
            $user_info['is_business'] = $store_info ? 1 : 0;
        }

        printAjaxData($user_info);
    }

    /**
     * 修改用户昵称
     */
    public function change_user_nickname()
    {
        $user_info = $this->_check_login();
        if ($_POST) {
            $nickname = $this->input->post('nickname', TRUE);
            if (!$nickname) {
                printAjaxError('fail', '昵称不能为空');
            }
            if ($this->Users_model->count(['nickname'=>$nickname, 'id <>'=>$user_info['id']])) {
                printAjaxError('fail', '昵称已被占用');
            }
            $result = $this->Users_model->save(array('nickname' => $nickname), array('id' => $user_info['id']));
            if ($result) {
                printAjaxSuccess('success', '修改成功');
            } else {
                printAjaxError('fail', '修改失败');
            }
        }
    }

    /**
     * 获取首页广告
     */
    public function get_ad_list($id = 1, $num = 10){
        $this->load->model('Ad_model', '', TRUE);
        $item_list = $this->Ad_model->gets('path, url, ad_text', array('category_id'=>$id, 'ad_type'=>'image', 'enable'=>1), $num, 0);
        if($item_list){
            foreach ($item_list as $key => $value){
                $tmp_image = filter_image_path($value['path']);
                $item_list[$key]['path'] = $tmp_image['path'];
                $item_list[$key]['path_thumb'] = $tmp_image['path_thumb'];
            }
        }
        printAjaxData(array('item_list' => $item_list));
    }

    /*
     * 文章内容
     */
    public function get_page_detail($id = NULL){
        $this->load->model('Page_model', '', TRUE);
        $item_info = $this->Page_model->get('*', array('id'=>$id));
        if ($item_info){
            $item_info['content'] = filter_content(html($item_info['content']),base_url());
        }
        printAjaxData(array('item_info' => $item_info));
    }

    /**
     * 获取商户分类
     */
    public function get_store_type_list()
    {
        $this->load->model('Store_type_model', '', TRUE);
        // $item_list = $this->Store_type_model->menu_tree();
        $item_list = $this->Store_type_model->gets('*', array('display' => 1));
        if ($item_list) {
            foreach($item_list as $key=>$item){
                $tmp_image_arr = filter_image_path($item['path']);
                $item_list[$key]['path'] = $tmp_image_arr['path'];
                $item_list[$key]['path_thumb'] = $tmp_image_arr['path_thumb'];
                // if($item['subMenuList']){
                //      foreach($item['subMenuList'] as $subkey=>$subitem){
                //          $tmp_image_arr = filter_image_path($subitem['path']);
                //          $item_list[$key]['subMenuList'][$subkey]['path'] = $tmp_image_arr['path'];
                //          $item_list[$key]['subMenuList'][$subkey]['path_thumb'] = $tmp_image_arr['path_thumb'];
                //     }
                // }
            }
        }
        printAjaxData(array('item_list' => $item_list));
    }

    /**
     * 店铺信息
     */
    public function get_store_check_info()
    {
        $user_info = $this->_check_login();
        $this->load->model('Stores_model', '', TRUE);

        $item_info = $this->Stores_model->get('id,status', ['user_id'=>$user_info['id']]);

        printAjaxData(['item_info'=>$item_info]);
    }

    /**
     * 申请商户
     */
    public function apply_store()
    {
        $this->load->model('Stores_model', '', TRUE);
        $user_info = $this->_check_login();

        $store_info = $this->Stores_model->get('id,status', ['user_id'=>$user_info['id']]);
        if ($store_info && $store_info['status'] == 1) {
            printAjaxError('fail', '请勿重复提交');
        }
        if ($_POST) {
            $store_name = $this->input->post('store_name', TRUE);
            $is_branch = $this->input->post('is_branch', TRUE);
            $type_id = $this->input->post('type_id', TRUE);
            $image_ids = $this->input->post('image_ids', TRUE);
            $license_image = $this->input->post('license_image', TRUE);
            $province_id = $this->input->post('province_id', TRUE);
            $city_id = $this->input->post('city_id', TRUE);
            $city = $this->input->post('city', TRUE);
            $address = $this->input->post('address', TRUE);
            $lat = $this->input->post('lat', TRUE);
            $lng = $this->input->post('lng', TRUE);
            // $business_status = $this->input->post('business_status', TRUE);
            $phone = $this->input->post('phone', TRUE);
            $business_hours = $this->input->post('business_hours', TRUE);
            $remark = $this->input->post('remark', TRUE);
            $id_card_number = $this->input->post('id_card_number', TRUE);
            $id_card_image = $this->input->post('id_card_image', TRUE);
            $biz_lic_image = $this->input->post('biz_lic_image', TRUE);
            $bank_card_number = $this->input->post('bank_card_number', TRUE);

            $datas = [
                'user_id' => $user_info['id'],
                'store_name' => $store_name,
                'is_branch' => $is_branch,
                'type_id' => $type_id,
                'image_ids' => $image_ids,
                'license_image' => $license_image,
                'province_id' => $province_id,
                'city_id' => $city_id,
                'city' => $city,
                'address' => $address,
                'lat' => $lat,
                'lng' => $lng,
                // 'business_status' => $business_status,
                'phone' => $phone,
                'business_hours' => $business_hours,
                'remark' => $remark,
                'id_card_number' => $id_card_number,
                'id_card_image' => $id_card_image,
                'biz_lic_image' => $biz_lic_image,
                'bank_card_number' => $bank_card_number,
                'status' => 0,
            ];

            if ($this->Stores_model->save($datas, $store_info ? ['id'=>$store_info['id']] : NULL)) {
                printAjaxSuccess('success', '提交成功');
            }
        }
        printAjaxError('fail', '提交失败');
    }
    

    /**
     * 外卖商家列表
     * @param int $per_page
     * @param int $page
     */
    public function get_out_store_list($per_page = 20, $page = 1)
    {
        $this->load->model('Stores_model', '', TRUE);

        $str_where = "status = 1 and store_type in (1,2)";
        $type_id = $this->input->get('type_id', TRUE);
        if ($type_id || $type_id === '0') {
            $str_where .= " and type_id = {$type_id}";
        }
        $item_list = $this->Stores_model->gets('id,store_name,logo,per_amount', $str_where, $per_page, $per_page * ($page - 1), 'browse_num');
        if ($item_list) {
            foreach ($item_list as $key=>$value) {
                $image_arr = filter_image_path($value['logo']);
                $item_list[$key]['logo'] = $image_arr['path'];
                $item_list[$key]['logo_thumb'] = $image_arr['path_thumb'];
            }
        }

        //是否有下一页
        $cur_count = $per_page * ($page - 1) + count($item_list);
        $total_count = $this->Stores_model->count($str_where);
        $is_next_page = 0;
        if ($total_count > $cur_count) {
            $is_next_page = 1;
        }
        printAjaxData(['item_list'=>$item_list, 'is_next_page'=>$is_next_page]);
    }

    /**
     * 美食商家列表
     * @param int $per_page
     * @param int $page
     */
    public function get_restaurant_store_list($per_page = 20, $page = 1)
    {
        $this->load->model('Stores_model', '', TRUE);
        $this->load->model('Store_type_model', '', TRUE);

        $str_where = "status = 1 and store_type in (0,2)";
        $type_id = $this->input->get('type_id', TRUE);
        if ($type_id || $type_id === '0') {
            $str_where .= " and type_id = {$type_id}";
        }
        $item_list = $this->Stores_model->gets('id,store_name,logo,per_amount,type_id', $str_where, $per_page, $per_page * ($page - 1), 'browse_num');
        if ($item_list) {
            foreach ($item_list as $key=>$value) {
                $image_arr = filter_image_path($value['logo']);
                $item_list[$key]['logo'] = $image_arr['path'];
                $item_list[$key]['logo_thumb'] = $image_arr['path_thumb'];
                $type_info = $this->Store_type_model->get('name',array('id'=>$value['type_id']));
                $item_list[$key]['type_str'] = $type_info ? $type_info['name'] : '';
            }
        }

        //是否有下一页
        $cur_count = $per_page * ($page - 1) + count($item_list);
        $total_count = $this->Stores_model->count($str_where);
        $is_next_page = 0;
        if ($total_count > $cur_count) {
            $is_next_page = 1;
        }
        printAjaxData(['item_list'=>$item_list, 'is_next_page'=>$is_next_page]);
    }

    /**
     * 店铺信息
     * @param int $id
     */
    public function get_store_info($id = null)
    {
        if (!$id) {
            printAjaxError('fail', '参数异常');
        }
        $this->load->model('Stores_model', '', TRUE);

        $item_info = $this->Stores_model->get('*', ['id'=>$id, 'status'=>1]);
        if (!$item_info) {
            printAjaxError('fail', '参数异常！');
        }
        $image_arr = filter_image_path($item_info['logo']);
        $item_info['logo'] = $image_arr['path'];
        $item_info['logo_thumb'] = $image_arr['path_thumb'];

        $item_info['business_status_str'] = $this->_business_status_arr[$item_info['business_status']];
        $item_info['is_collected'] = 0;
        $user_id = $this->session->userdata("user_id");
        if ($user_id) {
            $this->load->model('Browsing_histories_model','',TRUE);
            $this->Browsing_histories_model->delete(['user_id'=>$user_id, 'item_id'=>$item_info['id'], 'item_type'=>2]);
            $this->Browsing_histories_model->save(['user_id'=>$user_id, 'item_id'=>$item_info['id'], 'item_type'=>2]);
            //是否收藏
            $this->load->model('Collections_model', '', TRUE);
            if ($this->Collections_model->count(['item_id'=>$item_info['id'], 'item_type'=>2, 'user_id'=>$user_id])) {
                $item_info['is_collected'] = 1;
            }
        }
        printAjaxData(['item_info'=>$item_info]);
    }

    /**
     * 获取商家美食分类
     * @param int $store_id
     */
    public function get_dishes_category_list($store_id = null)
    {
        if (!$store_id) {
            printAjaxError('fail', '参数异常');
        }
        $this->load->model('Dishes_category_model', '', TRUE);

        $item_list = $this->Dishes_category_model->gets('id,name', ['store_id'=>$store_id, 'display'=>1]);

        printAjaxData(['item_list'=>$item_list]);
    }


    /**
     * 获取商品标签
     */
    public function get_tags_list()
    {
        $this->load->model('Tags_model', '', TRUE);

        $item_list = $this->Tags_model->gets('id,name', ['display'=>1]);

        printAjaxData(['item_list'=>$item_list]);
    }

    /**
     * 美食列表
     * @param int $per_page
     * @param int $page
     */
    public function get_combos_list($per_page = 20, $page = 1)
    {
        $this->load->model('Combos_model', '', TRUE);

        $str_where = "combos.display = 1";
        $tag_id = $this->input->post('tag_id',TRUE);
        if ($tag_id) {
            $str_where .= " and combos.tag_id = {$tag_id}";
        }
        $attribute = $this->input->post('attribute',TRUE);
        if ($attribute) {
            $str_where .= " and find_in_set('{$attribute}', combos.attribute)";
        }
        $item_list = $this->Combos_model->gets_join_stores('combos.id,combos.type,combos.name,combos.cover_image,combos.price,combos.original_price,stores.store_name', $str_where, $per_page, $per_page * ($page - 1), 'combos.browse_num');
        if ($item_list) {
            foreach ($item_list as $key=>$value) {
                $image_arr = filter_image_path($value['cover_image']);
                $item_list[$key]['cover_image'] = $image_arr['path'];
                $item_list[$key]['cover_image_thumb'] = $image_arr['path_thumb'];
            }
        }

        //是否有下一页
        $cur_count = $per_page * ($page - 1) + count($item_list);
        $total_count = $this->Combos_model->count_join_stores($str_where);
        $is_next_page = 0;
        if ($total_count > $cur_count) {
            $is_next_page = 1;
        }

        printAjaxData(['item_list'=>$item_list, 'is_next_page'=>$is_next_page]);
    }

    /**
     * 附近美食列表
     * @param int $per_page
     * @param int $page
     */
    public function get_nearby_combos_list($per_page = 20, $page = 1)
    {
        $lat = $this->input->post('lat', TRUE);
        $lng = $this->input->post('lng', TRUE);
        $this->load->model('Combos_model', '', TRUE);

        $lat = $lat ? $lat : 31.236289;
        $lng = $lng ? $lng : 121.473083;
        $str_where = "combos.display = 1";
        $item_list = $this->Combos_model->gets_join_stores("combos.id,combos.type,combos.name,combos.cover_image,combos.price,combos.original_price,ROUND(6378.137*2*ASIN(SQRT(POW(SIN(({$lat}*PI()/180-combos.lat*PI()/180)/2),2)+COS({$lat}*PI()/180)*COS(combos.lat*PI()/180)*POW(SIN(({$lng}*PI()/180-combos.lng*PI()/180)/2),2)))) AS distance,stores.store_name", $str_where, $per_page, $per_page * ($page - 1), 'distance', 'ASC');
        if ($item_list) {
            foreach ($item_list as $key=>$value) {
                $image_arr = filter_image_path($value['cover_image']);
                $item_list[$key]['cover_image'] = $image_arr['path'];
                $item_list[$key]['cover_image_thumb'] = $image_arr['path_thumb'];
            }
        }

        //是否有下一页
        $cur_count = $per_page * ($page - 1) + count($item_list);
        $total_count = $this->Combos_model->count_join_stores($str_where);
        $is_next_page = 0;
        if ($total_count > $cur_count) {
            $is_next_page = 1;
        }

        printAjaxData(['item_list'=>$item_list, 'is_next_page'=>$is_next_page]);
    }

    /**
     * 商家美食套餐列表
     * @param int $per_page
     * @param int $page
     */
    public function get_store_combos_list($store_id = NULL)
    {
        if (!$store_id || $store_id == 'undefined') {
            printAjaxError('fail','参数异常');
        }
        $this->load->model('Combos_model', '', TRUE);

        $str_where = "display = 1 and store_id = {$store_id}";
        $item_list = $this->Combos_model->gets('id,name,cover_image,price,original_price,type', $str_where);
        if ($item_list) {
            foreach ($item_list as $key=>$value) {
                $image_arr = filter_image_path($value['cover_image']);
                $item_list[$key]['cover_image'] = $image_arr['path'];
                $item_list[$key]['cover_image_thumb'] = $image_arr['path_thumb'];
            }
        }

        printAjaxData(['item_list'=>$item_list]);
    }

    /**
     * 套餐详情
     */
    public function get_combos_detail($id = null)
    {
        if (!$id) {
            printAjaxError('fail','参数异常');
        }
        $this->load->model('Combos_model', '', TRUE);
        $item_info = $this->Combos_model->get('*', ['id'=>$id]);

        if ($item_info) {
            $item_info['is_collected'] = 0;
            $this->Combos_model->save_column('browse_num', 'browse_num+1', ['id'=>$id]);
            $user_id = $this->session->userdata("user_id");
            if ($user_id) {
                $this->load->model('Browsing_histories_model','',TRUE);
                $this->Browsing_histories_model->delete(['user_id'=>$user_id, 'item_id'=>$item_info['id'], 'item_type'=>0]);
                $this->Browsing_histories_model->save(['user_id'=>$user_id, 'item_id'=>$item_info['id'], 'item_type'=>0, 'goods_type'=>$item_info['type'] ? 1 : 0]);
                //是否收藏
                $this->load->model('Collections_model', '', TRUE);
                if ($this->Collections_model->count(['item_id'=>$item_info['id'], 'item_type'=>0, 'user_id'=>$user_id])) {
                    $item_info['is_collected'] = 1;
                }
            }

            if ($item_info['type'] == 1) {
                $product_info = $this->hbdapiclass->hbd_query_product_info($item_info['product_id']);
                $sold_out = (strtotime($product_info['END_DATE']) > time() && strtotime($product_info['XJ_DATE']) > time()) ? 0 : 1;
                $product_info['sold_out'] = $sold_out;
                if ($sold_out && $item_info['display'] == 1) {
                    $this->Combos_model->save(['display'=>0], ['id'=>$item_info['id']]);
                    $item_info['display'] = 0;
                }
                $item_info['product_info'] = $product_info;
            } else {
                $item_info['cover_image'] = filter_image_path($item_info['cover_image'])['path_thumb'];
                $this->load->model('Attachment_model', '', TRUE);
                $attachment_list = [];
                if ($item_info['image_ids']) {
                    $tmp_atm_ids = preg_replace(array('/^_/', '/_$/', '/_/'), array('', '', ','), $item_info['image_ids']);
                    $attachment_list = $this->Attachment_model->gets('id,path',"id in ($tmp_atm_ids)");
                    if ($attachment_list) {
                        foreach ($attachment_list as $key => $value) {
                            $tmp_image = filter_image_path($value['path']);
                            $attachment_list[$key]['path'] = $tmp_image['path'];
                            $attachment_list[$key]['path_thumb'] = $tmp_image['path_max'];
                        }
                    }
                }
                $item_info['attachment_list'] = $attachment_list;
    
                $item_info['content'] = filter_content(html($item_info['content']), base_url());
                $item_info['usage_rules'] = filter_content(html($item_info['usage_rules']), base_url());
    
                $dishes_list = [];
                if ($item_info['dishes_ids']) {
                    $this->load->model('Dishes_model', '', TRUE);
                    $dishes_list = $this->Dishes_model->gets('name, price', "id in ({$item_info['dishes_ids']})");
                }
                $item_info['dishes_list'] = $dishes_list;
                if ($item_info['stock'] <= 0) {
                    $this->Combos_model->save(['display'=>0], ['id'=>$item_info['id']]);
                    $item_info['display'] = 0;
                }
            }

            $item_info['location'] = ['lat'=>$item_info['lat'], 'lng'=>$item_info['lng']];
            if ($item_info['address'] && $item_info['lat'] == 0 && $item_info['lng'] == 0) {
                $location = get_lat_lng($item_info['address']);
                if ($location) {
                    $item_info['location'] = $location;
                }
            }

            $item_info['sales'] = $item_info['custom_sales'] ?: $item_info['sales'];
            $this->load->model('Witticisms_model', '', TRUE);
            $witticism_info = $this->Witticisms_model->gets('title', ['display'=>1], 1, 0, 'id', 'RANDOM');
            $item_info['share_title'] = $witticism_info ? $witticism_info[0]['title'].' '.$item_info['name'] : $item_info['name'];
            
        }
        printAjaxData(['item_info'=>$item_info]);
    }

    /**
     * 商家推荐菜品列表
     * @param int $store_id
     * @param int $per_page
     * @param int $page
     */
    public function get_store_dishes_list($store_id = NULL, $per_page = 20, $page = 1)
    {
        if (!$store_id || $store_id == 'undefined') {
            printAjaxError('fail','参数异常');
        }
        $this->load->model('Dishes_model', '', TRUE);

        $str_where = "display = 1 and store_id = {$store_id} and type in (0,2) and find_in_set('tj',attribute)";
        $item_list = $this->Dishes_model->gets('id,name,cover_image,price', $str_where, $per_page, $per_page * ($page - 1), 'sort');
        if ($item_list) {
            foreach ($item_list as $key=>$value) {
                $image_arr = filter_image_path($value['cover_image']);
                $item_list[$key]['cover_image'] = $image_arr['path'];
                $item_list[$key]['cover_image_thumb'] = $image_arr['path_thumb'];
            }
        }

        //是否有下一页
        $cur_count = $per_page * ($page - 1) + count($item_list);
        $total_count = $this->Dishes_model->count($str_where);
        $is_next_page = 0;
        if ($total_count > $cur_count) {
            $is_next_page = 1;
        }

        printAjaxData(['item_list'=>$item_list, 'is_next_page'=>$is_next_page]);
    }

    /**
     * 菜品详情
     */
    public function get_dishes_detail($id = null)
    {
        if (!$id) {
            printAjaxError('fail','参数异常');
        }
        $this->load->model('Dishes_model', '', TRUE);
        $item_info = $this->Dishes_model->get('*', ['id'=>$id]);

        if ($item_info) {
            $this->load->model('Attachment_model', '', TRUE);

            $attachment_list = [];
            if ($item_info['image_ids']) {
                $tmp_atm_ids = preg_replace(array('/^_/', '/_$/', '/_/'), array('', '', ','), $item_info['image_ids']);
                $attachment_list = $this->Attachment_model->gets('id,path',"id in ($tmp_atm_ids)");
                if ($attachment_list) {
                    foreach ($attachment_list as $key => $value) {
                        $tmp_image = filter_image_path($value['path']);
                        $attachment_list[$key]['path'] = $tmp_image['path'];
                        $attachment_list[$key]['path_thumb'] = $tmp_image['path_max'];
                    }
                }
            }
            $item_info['attachment_list'] = $attachment_list;

            $item_info['content'] = filter_content(html($item_info['content']), base_url());
        }
        printAjaxData(['item_info'=>$item_info]);
    }

    /**
     * 评价列表
     */
    public function get_comments_list($item_id = NULL, $item_type = 0, $per_page = 10, $page = 1)
    {
        $this->load->model('Comments_model', "", TRUE);
        $this->load->model('Attachment_model', "", TRUE);

        if (!$item_id){
            printAjaxError('fail','参数异常！');
        }

        $strWhere = "comments.item_id = {$item_id} and comments.item_type = {$item_type} and comments.display = 1";

        $comment_list = $this->Comments_model->gets_join_user('comments.*, user.nickname, user.path as user_path', $strWhere, $per_page, $per_page * ($page - 1));
        if ($comment_list){
            foreach ($comment_list as $key=>$value){
                $tmp_image_arr = filter_image_path($value['user_path']);
                $comment_list[$key]['user_path'] = $tmp_image_arr['path_thumb'];
                $comment_list[$key]['create_time'] = date('Y-m-d', strtotime($value['create_time']));
                $attachment_list = [];
                if ($value['image_ids']) {
                    $tmp_atm_ids = preg_replace(array('/^_/', '/_$/', '/_/'), array('', '', ','), $value['image_ids']);
                    $attachment_list = $this->Attachment_model->gets('id,path',"id in ($tmp_atm_ids)");
                    foreach ($attachment_list as $k => $ls) {
                        $tmp_image_arr = filter_image_path($ls['path']);
                        $attachment_list[$k]['path'] = $tmp_image_arr['path_max'];
                        $attachment_list[$k]['path_thumb'] = $tmp_image_arr['path_thumb'];
                    }
                }
                $comment_list[$key]['attachment_list'] = $attachment_list;
            }
        }

        //是否有下一页
        $cur_count = $per_page * ($page - 1) + count($comment_list);
        $total_count = $this->Comments_model->count_join_user($strWhere);
        $is_next_page = 0;
        if ($total_count > $cur_count) {
            $is_next_page = 1;
        }
        $item_info['is_next_page'] = $is_next_page;
        printAjaxData(array('item_list' => $comment_list, 'is_next_page' => $is_next_page));
    }


    /**
     * 发布评价
     */
    public function add_comments()
    {
        $user_info = $this->_check_login();
        $order_id = $this->input->post('order_id',TRUE);
        $content = strip_tags($this->input->post('content',TRUE));
        $image_ids = $this->input->post('image_ids',TRUE);
        $grade = $this->input->post('grade',TRUE);
        $per_amount = $this->input->post('per_amount',TRUE);
        if (!$content){
            printAjaxError('fail','请填写评论内容！');
        }
        $this->load->library('baiduapiclass');
        $result = $this->baiduapiclass->text_review($content);
        if (!$result) {
            printAjaxError('fail', '文本存在不合规字符');
        }
        $this->load->model('Comments_model', "", TRUE);
        $this->load->model('Orders_detail_model', "", TRUE);
        $this->load->model('Orders_model', "", TRUE);
        $order_detail_info = $this->Orders_detail_model->get('item_id, item_type', ['order_id'=>$order_id]);
        if (!$order_detail_info) {
            printAjaxError('fail', '参数异常');
        }
        $data = array(
            'user_id' => $user_info['id'],
            'order_id' => $order_id,
            'item_id' => $order_detail_info['item_id'],
            'item_type' => $order_detail_info['item_type'],
            'content' => $content,
            'image_ids' => $image_ids,
            'grade' => $grade ? $grade : 5,
            'per_amount' => $per_amount,
        );
        if ($this->Comments_model->save($data)) {
            $this->Orders_model->save(['status'=>3], ['id'=>$order_id, 'user_id'=>$user_info['id']]);

            $this->load->model('Life_news_model', "", TRUE);
            $data = [
                'user_id' => $user_info['id'],
                'content' => $content,
                'image_ids' => $image_ids,
            ];
            $this->Life_news_model->save($data);
            printAjaxSuccess('success', '评价成功');
        }
        printAjaxError('fail', '评价失败');
    }

    /**
     * 添加购物车
     */
    public function add_cart()
    {
        $user_info = $this->_check_login();
        if ($_POST) {
            $this->load->model('Cart_model', '', TRUE);
            $this->load->model('Combos_model', '', TRUE);
            $item_id = $this->input->post("item_id", true);
            $buy_type = $this->input->post("buy_type", true);

            $item_info = $this->Combos_model->get('id,store_id,type', ['id'=>$item_id, 'display'=>1]);
            if (!$item_info) {
                printAjaxError('fail', '商品已下架');
            }
            $display = $buy_type == 1 ? 0 : 1;
            $cart_info = [];
            if ($display) {
                $cart_info = $this->Cart_model->get('id, buy_number', ['user_id'=>$user_info['id'], 'item_id'=>$item_id, 'display'=>1]);
            }
            
            if ($cart_info) {
                if ($this->Cart_model->save_column('buy_number', 'buy_number+1', ['id'=>$cart_info['id']])) {
                    printAjaxData(array('cart_ids'=>$cart_info['id']));
                } else {
                    printAjaxError('fail', '加入购物车失败');
                }
            } else {
                $data = [
                    'user_id' => $user_info['id'],
                    'item_id' => $item_id,
                    'item_type' => $item_info['type'],
                    'store_id' => $item_info['store_id'],
                    'display' => $display
                ];
                $ret_id = $this->Cart_model->save($data);
                if ($ret_id) {
                    printAjaxData(array('cart_ids'=>$ret_id));
                } else {
                    printAjaxError('fail', '加入购物车失败');
                }
            }
        }
    }

        /*
     * 购物车列表
     */

    public function get_cart_list() {
        $user_info = $this->_check_login();
        $this->load->model('Cart_model', '', TRUE);

        //按店铺分组显示
        $item_list = $this->Cart_model->gets_join_stores('stores.id as store_id,stores.store_name', array("cart.user_id" => $user_info['id'], "cart.display" => 1));
        if ($item_list) {
            foreach ($item_list as $key => $value) {
                $cart_list = $this->Cart_model->gets_join_combos('cart.*,combos.name,combos.price,combos.original_price,combos.cover_image,combos.stock',array("cart.user_id" => $user_info['id'], "cart.store_id" => $value['store_id'], "cart.display" => 1));
                foreach ($cart_list as $k => $item) {
                    $tmp_image_arr = filter_image_path($item['cover_image']);
                    $cart_list[$k]['cover_image'] = $tmp_image_arr['path'];
                    $cart_list[$k]['cover_image_thumb'] = $tmp_image_arr['path_thumb'];

                }
                $item_list[$key]['cart_list'] = $cart_list;
            }
        }
        printAjaxData(array('item_list' => $item_list));
    }

    /**
     * 修改购物车购买数量
     * @param buy_number 购买数量
     * @param cart_id 购物车id
     * @return json
     */
    public function change_cart_buy_number() {
        $user_info = $this->_check_login();
        $this->load->model('Cart_model', '', TRUE);
        $this->load->model('Combos_model', '', TRUE);
        if ($_POST) {
            $buy_number = intval($this->input->post('buy_number', TRUE));
            $cart_id = intval($this->input->post('cart_id', TRUE));
            if (!$buy_number || !$cart_id) {
                printAjaxError('fail', '操作异常，刷新重试');
            }
            if ($buy_number <= 0) {
                printAjaxError('buy_number', '购买数量不能小于或等于0');
            }
            $item_info = $this->Cart_model->get_join_combos('combos.stock,combos.type', array("cart.id" => $cart_id));
            if (!$item_info) {
                printAjaxError('fail', '操作异常，刷新重试');
            }

            if ($item_info['type'] == 0 && $buy_number > $item_info['stock']) {
                printAjaxError('fail', "此款商品库存不足，库存为：{$item_info['stock']}");
            }
            
            if (!$this->Cart_model->save(array('buy_number' => $buy_number), array('id' => $cart_id, 'user_id' => $user_info['id']))) {
                printAjaxError('fail', '数量修改失败');
            }
            printAjaxSuccess('success','修改成功');
        }
    }

    /**
     * 删除购物车
     * @param ids 购物车id
     * @return json
     */
    public function delete_cart() {
        $user_info = $this->_check_login();
        $this->load->model('Cart_model', '', TRUE);

        if ($_POST) {
            $ids = $this->input->post('ids', TRUE);
            if (!$ids) {
                printAjaxError('ids', '请选择删除项');
            }
            if ($this->Cart_model->delete("id in ($ids) and user_id = {$user_info['id']}")) {
                printAjaxSuccess('success','删除成功');
            } else {
                printAjaxError('fail', '删除失败');
            }
        }
    }


    /**
     * 订单提交页信息
     */
    public function order_confirm()
    {
        $cart_ids = $this->input->post('cart_ids', TRUE);
        if (!$cart_ids) {
            printAjaxError('fail', '参数异常，请重试');
        }
        $user_info = $this->_check_login();
        $this->load->model('Cart_model', '', TRUE);
        $this->load->model('User_coupons_model', '', TRUE);
        //按店铺分组显示
        $item_list = $this->Cart_model->gets_join_stores('stores.id as store_id,stores.store_name', "cart.user_id = {$user_info['id']} and cart.id in ({$cart_ids})");
        if (!$item_list) {
            printAjaxError('fail', '参数异常，请重试');
        }
        $total = 0;
        $usable_coupon_list = [];
        foreach ($item_list as $key => $value) {
            $store_total = 0;
            $cart_list = $this->Cart_model->gets_join_combos(
                'cart.*,combos.name,combos.price,combos.original_price,combos.cover_image,combos.type,combos.product_id',
                "cart.user_id = {$user_info['id']} and  cart.store_id = {$value['store_id']} and cart.id in ({$cart_ids})");
            foreach ($cart_list as $k => $item) {
                $product_price = $item['price'];
                if ($item['type'] == 1 && $item['product_id']) {
                    $product_info = $this->hbdapiclass->hbd_query_product_info($item['product_id']);
                    if (!$product_info) {
                        printAjaxError('fail', '商品不存在');
                    }
                    if (strtotime($product_info['XJ_DATE']) < time() || strtotime($product_info['END_DATE']) < time()) {
                        printAjaxError('fail', '商品已下架');
                    }
                    if ($product_info['EveryPersonMaxNum'] > 0 && $item['buy_number'] > $product_info['EveryPersonMaxNum']) {
                        printAjaxError('fail', '商品购买数量超过最大限制');
                    }
                    $product_price = $product_info['PRICE'];
                    $cart_list[$k]['price'] = $product_price;
                }
                $tmp_image_arr = filter_image_path($item['cover_image']);
                $cart_list[$k]['cover_image'] = $tmp_image_arr['path'];
                $cart_list[$k]['cover_image_thumb'] = $tmp_image_arr['path_thumb'];
                $product_total = $product_price * $item['buy_number'];
                $store_total += $product_total;

                //优惠券
                $product_coupon_where = "user_coupons.user_id = {$user_info['id']} and user_coupons.status = 0 and coupons.status = 1 and find_in_set('{$item['item_id']}', coupons.usable_goods_ids) and coupons.achieve_amount <= ({$product_total}) and ((coupons.way = 0 and coupons.start_time < NOW() and coupons.end_time > NOW()) or (coupons.way <> 0 and TIMESTAMPDIFF(SECOND, user_coupons.get_time, NOW()) < (coupons.valid_days * 3600 * 24))) ";
                $product_coupon_list = $this->User_coupons_model->gets_join_coupons('coupons.title,coupons.way,coupons.type,coupons.achieve_amount,coupons.used_amount,coupons.start_time,coupons.end_time,coupons.valid_days,coupons.usable_goods_ids,user_coupons.id,user_coupons.get_time', $product_coupon_where);
                $usable_coupon_list = array_merge($usable_coupon_list, $product_coupon_list);
            }
            $item_list[$key]['cart_list'] = $cart_list;
            $item_list[$key]['store_total'] = $store_total;
            $total += $store_total;
        }

        //优惠券
        $coupon_where = "user_coupons.user_id = {$user_info['id']} and user_coupons.status = 0 and coupons.status = 1 and coupons.achieve_amount <= ({$total}) and coupons.usable_goods_ids = '' and ((coupons.way = 0 and coupons.start_time < NOW() and coupons.end_time > NOW()) or (coupons.way <> 0 and TIMESTAMPDIFF(SECOND, user_coupons.get_time, NOW()) < (coupons.valid_days * 3600 * 24)))";
        $coupon_list = $this->User_coupons_model->gets_join_coupons('coupons.title,coupons.way,coupons.type,coupons.achieve_amount,coupons.used_amount,coupons.start_time,coupons.end_time,coupons.valid_days,coupons.usable_goods_ids,user_coupons.id,user_coupons.get_time', $coupon_where);
        if ($usable_coupon_list) {
            $coupon_list = array_merge($usable_coupon_list,$coupon_list);
        }
        if ($coupon_list) {
            foreach ($coupon_list as $key=>$value) {
                if ($value['way'] != 0) {
                    $coupon_list[$key]['start_date'] = date('Y-m-d', strtotime($value['get_time']));
                    $coupon_list[$key]['end_date'] = date('Y-m-d', strtotime("+{$value['valid_days']}days", strtotime($value['get_time'])));
                } else {
                    $coupon_list[$key]['start_date'] = date('Y-m-d', strtotime($value['start_time']));
                    $coupon_list[$key]['end_date'] = date('Y-m-d', strtotime($value['end_time']));
                }
                $coupon_list[$key]['used_amount'] = floatval($value['used_amount']);
            }
        }

        printAjaxData(['item_list'=>$item_list, 'total'=>$total, 'coupon_list'=>$coupon_list, 'bonus'=>$user_info['reward']]);
    }

    public function add_orders()
    {
        $user_info = $this->_check_login();
        if ($_POST) {
            $cart_ids = $this->input->post('cart_ids', TRUE);
            $coupon_id = $this->input->post('coupon_id', TRUE);
            $deduction_amount = $this->input->post('deduction_amount', TRUE);
            if ($deduction_amount > 30) {
                printAjaxError('fail', '奖金一次最多抵扣30元');
            }
            if ($deduction_amount > $user_info['reward']) {
                printAjaxError('fail', '奖金余额不足');
            }
            if (!$cart_ids) {
                printAjaxError('fail', '参数异常，请重试');
            }
        
            $this->load->model('Cart_model', '', TRUE);
            $this->load->model('Orders_model', '', TRUE);
            $this->load->model('Orders_detail_model', '', TRUE);
            $this->load->model('Orders_form_model', '', TRUE);
            $this->load->model('Orders_process_model', '', TRUE);
            $this->load->model('Combos_model', '', TRUE);
            $str_where = "cart.user_id = {$user_info['id']} and cart.id in ({$cart_ids})";
            $item_list = $this->Cart_model->gets_join_combos('cart.*,combos.name,combos.price,combos.original_price,combos.cover_image,combos.type,combos.product_id', $str_where);
            if (!$item_list) {
                printAjaxError('fail', '参数异常，请重试');
            }
            $coupon_is_used = 0;
            $coupon_info = [];
            if ($coupon_id) {
                $this->load->model('User_coupons_model', '', TRUE);
                $this->load->model('Coupons_model', '', TRUE);

                $coupon_where = "user_coupons.id = {$coupon_id} and user_coupons.user_id = {$user_info['id']} and user_coupons.status = 0 and coupons.status = 1 and ((coupons.way = 0 and coupons.start_time < NOW() and coupons.end_time > NOW()) or (coupons.way <> 0 and TIMESTAMPDIFF(SECOND, user_coupons.get_time, NOW()) < (coupons.valid_days * 3600 * 24)))";
                $coupon_info = $this->User_coupons_model->get_join_coupons('user_coupons.coupon_id,coupons.type,coupons.achieve_amount,coupons.used_amount,coupons.usable_goods_ids', $coupon_where);
            }
            $total = 0;
            $order_ids = '';
            foreach ($item_list as $key => $value) {
                $price = $value['price'];
                $fx_price = 0;
                if ($value['type'] == 1 && $value['product_id']) {
                    $product_info = $this->hbdapiclass->hbd_query_product_info($value['product_id']);
                    if (!$product_info) {
                        printAjaxError('fail', '商品不存在');
                    }
                    if (strtotime($product_info['XJ_DATE']) < time() || strtotime($product_info['END_DATE']) < time()) {
                        printAjaxError('fail', '商品已下架');
                    }
                    if ($product_info['EveryPersonMaxNum'] > 0 && $value['buy_number'] > $product_info['EveryPersonMaxNum']) {
                        printAjaxError('fail', '商品购买数量超过最大限制');
                    }
                    $price = $product_info['PRICE'];
                    $fx_price = $product_info['FXPrice'];
                }
                $order_number = $this->_getUniqueOrderNumber();
                $order_total = $price * $value['buy_number'];
                $data = [
                    'order_number' => $order_number,
                    'user_id' => $user_info['id'],
                    'store_id' => $value['store_id'],
                    'mobile' => $user_info['mobile'],
                    'order_type' => $value['type'] ? 1 : 0
                ];
                if ($coupon_info && $order_total >= $coupon_info['achieve_amount']) {
                    if (in_array($value['item_id'], explode(',', $coupon_info['usable_goods_ids'])) || count($item_list) == 1) {
                        $discount = $coupon_info['used_amount'];
                        $order_total -= $discount;
                        $order_total = $order_total > 0 ? $order_total : 0;
                        $data['discount'] = $discount;
                        $data['coupon_id'] = $coupon_id;
                        $coupon_is_used = 1;
                    }
                    
                }
                
                $data['total'] = $order_total;
                $ret_id = $this->Orders_model->save($data);
                if ($ret_id) {
                    $detail_data = [
                        'order_id' => $ret_id,
                        'item_id' => $value['item_id'],
                        'item_type' => 0,
                        'goods_type' => $value['type'],
                        'sell_price' => $price,
                        'fx_price' => $fx_price,
                        'buy_number' => $value['buy_number'],
                        'item_name' => $value['name'],
                        'item_image' => $value['cover_image'],
                    ];
                    if ($this->Orders_detail_model->save($detail_data)) {

                        //减库存
                        if ($value['type'] == 0) {
                            $this->Combos_model->save_column('stock', "stock-{$value['buy_number']}", ['id'=>$value['item_id']]);
                        }
                        $order_ids .= $ret_id . ',';
                        $total += $order_total;
                        //订单跟踪记录
                        $orders_process_data = array(
                            'content' => "订单创建成功",
                            'order_id' => $ret_id,
                            'order_status' => 0,
                            'change_status' => 0
                        );
                        $this->Orders_process_model->save($orders_process_data);

                        
                    } else {
                        $this->Orders_model->delete(['id'=>$ret_id]);
                        printAjaxError('fail', '参数异常，请重试');
                    }
                }
                
            }
            if ($order_ids) {
                $unsettled_total = $total;
                //优惠券
                if ($coupon_info) {
                    if ($coupon_is_used == 0) {
                        foreach (explode(',',$order_ids) as $value){
                            $order_info = $this->Orders_model->get('total',array('id'=>$value));
                            if ($order_info){
                                $discount = $coupon_info['used_amount'] * ($order_info['total'] / $unsettled_total);
                                $data = [
                                    'discount'=> $discount,
                                    'coupon_id'=> $coupon_id,
                                    'total'=> ($order_info['total'] - $discount) >= 0 ? $order_info['total'] - $discount : 0,
                                ];
                                $this->Orders_model->save($data,array('id'=>$value));
                            }

                        }
                        $total -= $coupon_info['used_amount'];
                    }
                    
                    $this->User_coupons_model->save(array('use_time'=>date('Y-m-d H:i:s', time()),'status'=>1),array('id'=>$coupon_id));
                    $this->Coupons_model->save_column('used_number','used_number+1',array('id'=>$coupon_info['coupon_id']));
                }
                //抵扣金
                if ($deduction_amount > 0) {
                    $total -= $deduction_amount;
                    foreach (explode(',',$order_ids) as $value){
                        $order_info = $this->Orders_model->get('total',array('id'=>$value));
                        if ($order_info){
                            $discount = $deduction_amount * ($order_info['total'] / $unsettled_total);
                            $data = [
                                'deduction_amount'=> $discount,
                                'total'=> ($order_info['total'] - $discount) >= 0 ? $order_info['total'] - $discount : 0,
                            ];
                            $this->Orders_model->save($data,array('id'=>$value));
                        }
                    } 
                }
                $total = $total > 0 ? $total : 0;

                $this->Cart_model->delete($str_where);
                $order_number = $this->_getUniqueOrderNumber();
                $order_ids = substr($order_ids, 0, -1);
                $ret = $this->Orders_form_model->save(array('order_ids'=>$order_ids,'order_number'=>$order_number,'total'=>$total));
                if($ret){
                    //抵扣金
                    if ($deduction_amount > 0) {
                        if ($this->Users_model->save(['reward'=>$user_info['reward'] - $deduction_amount], ['id'=>$user_info['id']])) {
                            $fields = [
                                'cause' => '订单抵扣',
                                'price' => $deduction_amount,
                                'balance' => $user_info['reward'] - $deduction_amount,
                                'user_id' => $user_info['id'],
                                'type' => 'order_deduct',
                                'record_type' => 1,
                                'ret_id' => $ret
                            ];
                            $this->load->model('Financial_model', '', TRUE);
                            $this->Financial_model->save($fields);
                        }
                    }
                    printAjaxData(array('orders_id'=>$ret,'total'=>$total));
                }
            }
            
        }
        printAjaxError('fail', '参数异常，请重试');
    }

    //获取唯一的订单号
    private function _getUniqueOrderNumber($column = 'order_number') {
        //一秒钟一万件的量
        $randCode = '';
        while (true) {
            $randCode = getOrderNumber(6);
            $count = $this->Orders_model->count(array($column => $randCode));
            if ($count > 0) {
                $randCode = '';
                continue;
            } else {
                break;
            }
        }
        return $randCode;
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

    public function hbd_order_notify()
    {
        $json = $GLOBALS['HTTP_RAW_POST_DATA'];
        // logs($json);
        $post_data = json_decode($json, true);
        if ($post_data) {
            $this->load->model('Orders_model', '', TRUE);
            $order_number = $post_data['biz_content']['SourceOrderNo'];
            $status = $post_data['biz_content']['CodeState'];
            $order_info = $this->Orders_model->get('id,status,store_id,user_id', ['order_number'=>$order_number]);
            if ($order_info) {
                if ($this->Orders_model->save(['status'=>$status == 1 ? 2 : 10], ['id'=>$order_info['id']])) {
                    //订单跟踪记录
                    $this->load->model('Orders_process_model', '', TRUE);
                    $orders_process_data = array(
                        'content' => $status == 1 ? "订单商家确认成功" : "订单作废",
                        'order_id' => $order_info['id'],
                        'order_status' => $order_info['status'],
                        'change_status' => $status == 1 ? 2 : 9
                    );
                    $this->Orders_process_model->save($orders_process_data);

                    if ($status == 1) {
                        //推广返佣
                        $this->load->model('Orders_detail_model', '', TRUE);
                        $order_detail_info = $this->Orders_detail_model->get('item_id,item_type,buy_number,reward,parent_id', ['order_id'=>$order_info['id']]);
                        if ($order_detail_info['item_type'] == 1) {
                            $this->load->model('Users_model', '', TRUE);
                            $parent_info = $this->Users_model->get('id,total', ['id'=>$order_detail_info['parent_id'], 'display'=>1]);
                            if ($parent_info) {
                                $balance = $parent_info['total'] + $order_detail_info['reward'];
                                $data = [
                                    'user_id' => $parent_info['id'],
                                    'price' => $order_detail_info['reward'],
                                    'balance' => $balance,
                                    'store_id' => $order_info['store_id'],
                                    'type' => 'order_reward',
                                    'ret_id' => $order_info['id'],
                                    'from_user_id' => $order_info['user_id'],
                                    'cause' => '吆喝推广佣金'
                                ];
                                $this->load->model('Financial_model', '', TRUE);
                                if ($this->Financial_model->save($data)) {
                                    $this->Users_model->save(['total'=>$balance], ['id'=>$parent_info['id']]);
                                }
                            }
                        }
                    }

                    echo "Success";
                }
            }  
        }
    }

    public function order_xcx_wx_pay2($order_id = NULL, $type = 0)
    {
        $user_info = $this->_check_login();
        $this->load->model('Orders_model', '', TRUE);
        $this->load->model('Orders_form_model', '', TRUE);
        $this->load->model('Pay_log_model', '', TRUE);

        if (!$order_id) {
            printAjaxError('fail','操作异常');
        }
        $code = $this->input->post("code", true);
        if (!$code) {
            printAjaxError('fail', 'DO NOT ACCESS!');
        }
        $appid = $this->appid;
        $appSecret = $this->appSecret;
        $json = file_get_contents("https://api.weixin.qq.com/sns/jscode2session?appid={$appid}&secret={$appSecret}&js_code={$code}&grant_type=authorization_code");
        $obj = json_decode($json);
        if (isset($obj->errmsg)) {
            printAjaxError('fail', 'invalid code!');
        }
        $openid = $obj->openid;


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

        /********************微信支付**********************/
        require_once "sdk/weixin_pay/lib/WxPay.Api.php";
        require_once "sdk/weixin_pay/WxPay.JsApiPay.php";


        $tools = new JsApiPay();
        $input = new WxPayUnifiedOrder();
        $input->SetAppid($appid);
        $input->SetOpenid($openid);
        $input->SetBody("凑活小程序付款");
        $input->SetAttach("{$order_id}");
        $input->SetTotal_fee($item_info['total'] * 100);
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 600));
        $input->SetNotify_url(base_url() . "index.php/api/napi/order_xcx_wx_pay_notify");
        $input->SetTrade_type("JSAPI");
        $input->SetProduct_id($order_id);
        $input->SetOut_trade_no($out_trade_no);
        $result = WxPayApi::unifiedOrder($input,6,1);
        $jsApiParameters = array();
        if ($result['return_code'] == 'SUCCESS' && $result['result_code'] == 'SUCCESS') {
            $jsApiParameters = $tools->GetJsApiParameters($result);
            //生成支付记录
            if (!$this->Pay_log_model->count(array('out_trade_no' => $out_trade_no, 'payment_type' => 0, 'order_type' => 0))) {
                $fields = array(
                    'user_id' => $user_info['id'],
                    'total_fee' => $item_info['total'],
                    'out_trade_no' => $out_trade_no,
                    'order_number' => $item_info['order_number'],
                    'trade_status' => 'WAIT_BUYER_PAY',
                    'add_time' => time(),
                    'payment_type' => 0,
                    'order_type' => 0,
                    'form_type' => $type
                );
                $this->Pay_log_model->save($fields);
            }

        } else {
            if (array_key_exists('result_code', $result) && $result['result_code'] == "FAIL") {
                //商户号重复时，要重新生成
                if (($result['err_code'] == 'OUT_TRADE_NO_USED' || $result['err_code'] == 'INVALID_REQUEST') && $type == 0) {
                    $out_trade_no = $this->_get_unique_out_trade_no();
                    $this->Orders_model->save(array('out_trade_no' => $out_trade_no), array('id' => $item_info['id']));

                    $input->SetAppid($appid);
                    $input->SetOpenid($openid);
                    $input->SetBody("凑活小程序付款");
                    $input->SetAttach("{$order_id}");
                    $input->SetTotal_fee($item_info['total'] * 100);
                    $input->SetTime_start(date("YmdHis"));
                    $input->SetTime_expire(date("YmdHis", time() + 600));
                    $input->SetNotify_url(base_url() . "index.php/api/napi/order_xcx_wx_pay_notify");
                    $input->SetTrade_type("JSAPI");
                    $input->SetProduct_id($order_id);
                    $input->SetOut_trade_no($out_trade_no);
                    $result = WxPayApi::unifiedOrder($input,6,1);
                    $jsApiParameters = array();
                    if ($result['return_code'] == 'SUCCESS' && $result['result_code'] == 'SUCCESS') {
                        $jsApiParameters = $tools->GetJsApiParameters($result);
                        //生成支付记录
                        if (!$this->Pay_log_model->count(array('out_trade_no' => $out_trade_no, 'payment_type' => 0, 'order_type' => 0))) {
                            $fields = array(
                                'user_id' => $user_info['id'],
                                'total_fee' => $item_info['total'],
                                'out_trade_no' => $out_trade_no,
                                'order_number' => $item_info['order_number'],
                                'trade_status' => 'WAIT_BUYER_PAY',
                                'add_time' => time(),
                                'payment_type' => 0,
                                'order_type' => 0,
                                'form_type' => $type
                            );
                            $this->Pay_log_model->save($fields);
                        }


                    } else {
                        printAjaxError('fail', $result['err_code']);
                    }
                } else {
                    printAjaxError('fail', $result['err_code']);
                }
            } else {
                printAjaxError('fail', '交易失败，请重试');
            }
        }

        $jsApiParameters = json_decode($jsApiParameters, true);

        printAjaxData($jsApiParameters);

    }

    public function order_xcx_wx_pay($order_id = NULL, $type = 0)
    {
        $user_info = $this->_check_login();
        $this->load->model('Orders_model', '', TRUE);
        $this->load->model('Orders_detail_model', '', TRUE);
        $this->load->model('Orders_form_model', '', TRUE);
        $this->load->model('Pay_log_model', '', TRUE);

        if (!$order_id) {
            printAjaxError('fail','操作异常');
        }
        $code = $this->input->post("code", true);
        if (!$code) {
            printAjaxError('fail', 'DO NOT ACCESS!');
        }
        $appid = $this->appid;
        $appSecret = $this->appSecret;
        $json = file_get_contents("https://api.weixin.qq.com/sns/jscode2session?appid={$appid}&secret={$appSecret}&js_code={$code}&grant_type=authorization_code");
        $obj = json_decode($json);
        if (isset($obj->errmsg)) {
            printAjaxError('fail', 'invalid code!');
        }
        $openid = $obj->openid;


        if ($type){
            $item_info = $this->Orders_form_model->get('order_ids,total,order_number,create_time',array('id'=>$order_id));
            $out_trade_no = $item_info['order_number'];
            $order_detail_list = [];
            foreach (explode(',', $item_info['order_ids']) as $order_id) {
                $order_detail = $this->Orders_detail_model->gets('*', ['order_id'=>$order_id]);
                $order_detail_list = array_merge($order_detail_list, $order_detail);
            }
            $item_info['order_detail_list'] = $order_detail_list;
            $item_info['type'] = $type;
        }else{
            $item_info = $this->Orders_model->get('*', "id = {$order_id} and user_id = {$user_info['id']} and status = 0");
            $out_trade_no = $item_info['out_trade_no'] ? $item_info['out_trade_no'] : $item_info['order_number'];
            $order_detail_list = $this->Orders_detail_model->gets('*', ['order_id'=>$order_id]);
            $item_info['order_detail_list'] = $order_detail_list;
            $item_info['type'] = $type;
        }
        if (!$item_info) {
            printAjaxError('fail','订单信息不存在');
        }

        if ($item_info['total'] <= 0) {
            printAjaxError('fail','支付金额异常');
        }

        /********************微信支付**********************/
        require_once "sdk/weixin_pay/lib/WxPay.Api.php";
        require_once "sdk/weixin_pay/WxPay.JsApiPay.php";


        $tools = new JsApiPay();
        $input = new WxPayUnifiedOrder();
        $input->SetAppid($appid);
        $input->SetOpenid($openid);
        $input->SetBody("凑活小程序付款");
        $input->SetAttach("{$order_id}");
        $input->SetTotal_fee($item_info['total'] * 100);
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 600));
        $input->SetNotify_url(base_url() . "index.php/api/napi/order_xcx_wx_pay_notify");
        $input->SetTrade_type("JSAPI");
        $input->SetProduct_id($order_id);
        $input->SetOut_trade_no($out_trade_no);
        $result = WxPayApi::unifiedOrder($input,6,1);
        $jsApiParameters = array();
        if ($result['return_code'] != 'SUCCESS' || $result['result_code'] != 'SUCCESS') {
            if (!array_key_exists('result_code', $result) || $result['result_code'] != "FAIL") {
                printAjaxError('fail', '交易失败，请重试');
            }
            if (!($result['err_code'] == 'OUT_TRADE_NO_USED' || $result['err_code'] == 'INVALID_REQUEST') || $type != 0) {
                printAjaxError('fail', $result['err_code']);
            }
            //商户号重复时，要重新生成
            $out_trade_no = $this->_get_unique_out_trade_no();
            $this->Orders_model->save(array('out_trade_no' => $out_trade_no), array('id' => $item_info['id']));

            $input->SetAppid($appid);
            $input->SetOpenid($openid);
            $input->SetBody("凑活小程序付款");
            $input->SetAttach("{$order_id}");
            $input->SetTotal_fee($item_info['total'] * 100);
            $input->SetTime_start(date("YmdHis"));
            $input->SetTime_expire(date("YmdHis", time() + 600));
            $input->SetNotify_url(base_url() . "index.php/api/napi/order_xcx_wx_pay_notify");
            $input->SetTrade_type("JSAPI");
            $input->SetProduct_id($order_id);
            $input->SetOut_trade_no($out_trade_no);
            $result = WxPayApi::unifiedOrder($input,6,1);
            $jsApiParameters = array();
            if ($result['return_code'] != 'SUCCESS' || $result['result_code'] != 'SUCCESS') {
                printAjaxError('fail', $result['err_code']);
            }

        }

        $jsApiParameters = $tools->GetJsApiParameters($result);
        //生成支付记录
        if (!$this->Pay_log_model->count(array('out_trade_no' => $out_trade_no, 'payment_type' => 0, 'order_type' => 0))) {
            $fields = array(
                'user_id' => $user_info['id'],
                'total_fee' => $item_info['total'],
                'out_trade_no' => $out_trade_no,
                'order_number' => $item_info['order_number'],
                'trade_status' => 'WAIT_BUYER_PAY',
                'add_time' => time(),
                'payment_type' => 0,
                'order_type' => 0,
                'form_type' => $type
            );
            $this->Pay_log_model->save($fields);
        }

        $jsApiParameters = json_decode($jsApiParameters, true);

        //自定义交易组件生成订单
        $order_info = $item_info;
        $this->load->library('wxapiclass');
        $order_info['openid'] = $openid;
        $order_info['prepay_id'] = $result['prepay_id'];
        $order_info['prepay_time'] = time();
        $return_order_info = $this->wxapiclass->add_order($order_info);
        $jsApiParameters['order_info'] = $return_order_info;

        printAjaxData($jsApiParameters);

    }

    //微信支付异步通知
    public function order_xcx_wx_pay_notify(){
        /********************微信支付**********************/
        require_once "sdk/weixin_pay/lib/WxPay.Api.php";
        $msg = '支付通知失败';
        $appid = $this->appid;
        $appSecret = $this->appSecret;
        // $xml = $GLOBALS['HTTP_RAW_POST_DATA'];
        $xml = file_get_contents('php://input');
        try {
            $data = WxPayResults::Init($xml);
            if (array_key_exists("transaction_id", $data)) {
                $input = new WxPayOrderQuery();
                $input->SetTransaction_id($data["transaction_id"]);
                $input->SetAppid($appid);
                $result = WxPayApi::orderQuery($input,6,1);
                if (array_key_exists("return_code", $result)
                    && array_key_exists("result_code", $result)
                    && $result["return_code"] == "SUCCESS"
                    && $result["result_code"] == "SUCCESS"
                ) {
                    $this->load->model('Users_model', '', TRUE);
                    $this->load->model('Orders_model', '', TRUE);
                    $this->load->model('Orders_form_model', '', TRUE);
                    $this->load->model('Orders_process_model', '', TRUE);
                    $this->load->model('Pay_log_model', '', TRUE);

                    //订单号
                    $order_id = $result['attach'];
                    //商户订单号
                    $out_trade_no = $result['out_trade_no'];
                    //微信交易号
                    $trade_no = $result['transaction_id'];
                    //通知时间
                    $notify_time = $result['time_end'];
                    $total_fee = $result['total_fee'];

                    $params = [
                        'id' => $out_trade_no,
                        'user_openid' => $result['openid'],
                        'action_type' => 1,
                        'transaction_id' => $trade_no,
                        'pay_time' => date('Y-m-d H:i:s', strtotime($notify_time))
                    ];
                    $pay_log_info = $this->Pay_log_model->get('*', array('out_trade_no' => $out_trade_no, 'order_type' => 0, 'payment_type' => 0));
                    if ($pay_log_info && $total_fee == $pay_log_info['total_fee'] * 100) {
                        if ($pay_log_info['trade_status'] != 'TRADE_FINISHED' && $pay_log_info['trade_status'] != 'TRADE_SUCCESS' && $pay_log_info['trade_status'] != 'TRADE_CLOSED') {
                            //支付记录
                            $fields = array(
                                'trade_no' => $trade_no,
                                'trade_status' => 'TRADE_SUCCESS',
                                'notify_time' => strtotime($notify_time)
                            );
                            if ($this->Pay_log_model->save($fields, array('id' => $pay_log_info['id']))) {
                                if ($pay_log_info['form_type']) {
                                    $orders_form_info = $this->Orders_form_model->get('order_ids,total,order_number', array('order_number' => $out_trade_no));
                                    $order_id_arr = explode(',', $orders_form_info['order_ids']);
                                } else {
                                    $order_id_arr = [$order_id];
                                }
                                //交易组件同步订单
                                // $params = [
                                //     'id' => $out_trade_no,
                                //     'openid' => $result['openid'],
                                //     'action_type' => 1,
                                //     'transaction_id' => $trade_no,
                                //     'pay_time' => date('Y-m-d H:i:s', strtotime($notify_time))
                                // ];
                                // $this->load->library('wxapiclass');
                                // $this->wxapiclass->sync_order_pay($params);
                                // $params = [
                                //     'order_number' => $out_trade_no,
                                //     'openid' => $result['openid'],
                                // ];
                                // $this->wxapiclass->delivery_send($params);
                                // $this->wxapiclass->delivery_recieve($params);

                                $user_info = $this->Users_model->get('id, total, username, nickname, mobile', array('id' => $pay_log_info['user_id']));
                                if ($order_id_arr && $user_info) {
                                    $this->load->model('Orders_detail_model', '', TRUE);
                                    $this->load->model('Combos_model', '', TRUE);
                                    foreach ($order_id_arr as $id) {
                                        $item_info = $this->Orders_model->get('*', array('id' => $id, 'status' => 0));
                                        if ($item_info) {
                                            
                                            //修改订单状态
                                            $fields = array(
                                                'status' => 1,
                                                'trade_no' => $trade_no,
                                                'payment_type' => 0);
                                            if ($this->Orders_model->save($fields, array('id' => $item_info['id']))) {
                                                //订单追踪记录
                                                $fields = array(
                                                    'content' => "订单付款成功[小程序微信支付]",
                                                    'order_id' => $item_info['id'],
                                                    'order_status' => $item_info['status'],
                                                    'change_status' => 1
                                                );
                                                $this->Orders_process_model->save($fields);
                                                $order_detail_info = $this->Orders_detail_model->get('item_id,item_type,buy_number,reward,parent_id', ['order_id'=>$item_info['id']]);
                                                if ($order_detail_info) {
                                                    //加销量
                                                    if ($order_detail_info['item_type'] == 1) {
                                                        $this->load->model('Share_goods_model', '', TRUE);
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
                                                $this->load->model('System_model', '', TRUE);
                                                $system_info = $this->System_model->get('bonus_amount,platform_commission', ['id'=>1]);
                                                $amount = sprintf("%.2f",substr(sprintf("%.3f", $item_info['total'] * $system_info['platform_commission']), 0, -1));
                                                $balance = $system_info['bonus_amount'] + $amount;
                                                if ($this->System_model->save(['bonus_amount'=>$balance], ['id'=>1])) {
                                                    $this->load->model('Bonus_record_model', '', TRUE);
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
                                                
                                                echo $this->returnInfo("SUCCESS", "OK");
                                            }
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
        echo $this->returnInfo("FAIL", $msg);
    }


    private function returnInfo($type, $msg) {
        $return_xml="<xml>"
            ."<return_code><![CDATA[{$type}]]></return_code>"
            ."<return_msg><![CDATA[{$msg}]]></return_msg>"
            ."</xml>";
        return $return_xml;
    }

    /**
     * 余额支付
     */
    public function balance_pay($order_id = null, $type = 0)
    {
        $user_info = $this->_check_login();
        if (!$order_id) {
            printAjaxError('fail', '参数异常');
        }
        if ($type) {
            $this->load->model('Orders_form_model', '', TRUE);
            $orders_form_info = $this->Orders_form_model->get('order_ids,total,order_number', array('id' => $order_id));
            $order_id_arr = explode(',', $orders_form_info['order_ids']);
        } else {
            $order_id_arr = [$order_id];
        }
        if ($order_id_arr) {
            $this->load->model('Orders_model', '', TRUE);
            $this->load->model('Orders_detail_model', '', TRUE);
            $this->load->model('Orders_process_model', '', TRUE);
            $this->load->model('Combos_model', '', TRUE);
            foreach ($order_id_arr as $id) {
                $item_info = $this->Orders_model->get('*', array('id' => $id, 'status' => 0));
                if ($item_info && $item_info['total'] == 0) {
                    
                    //修改订单状态
                    $fields = array(
                        'status' => 1,
                        'payment_type' => 2);
                    if ($this->Orders_model->save($fields, array('id' => $item_info['id']))) {
                        //订单追踪记录
                        $fields = array(
                            'content' => "订单付款成功[余额支付]",
                            'order_id' => $item_info['id'],
                            'order_status' => $item_info['status'],
                            'change_status' => 1
                        );
                        $this->Orders_process_model->save($fields);
                        $order_detail_info = $this->Orders_detail_model->get('item_id,item_type,buy_number,reward,parent_id', ['order_id'=>$item_info['id']]);
                        if ($order_detail_info) {
                            //加销量
                            if ($order_detail_info['item_type'] == 1) {
                                $this->load->model('Share_goods_model', '', TRUE);
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
                        $this->load->model('System_model', '', TRUE);
                        $system_info = $this->System_model->get('bonus_amount,platform_commission', ['id'=>1]);
                        $amount = sprintf("%.2f",substr(sprintf("%.3f", $item_info['total'] * $system_info['platform_commission']), 0, -1));
                        $balance = $system_info['bonus_amount'] + $amount;
                        if ($this->System_model->save(['bonus_amount'=>$balance], ['id'=>1])) {
                            $this->load->model('Bonus_record_model', '', TRUE);
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
                        
                        printAjaxSuccess('success','支付成功');
                    }
                }
            }
        }

        printAjaxError('fail', '支付失败');
    }

    /**
     * 订单列表
     */
    public function get_orders_list($status = 'all', $per_page = 20, $page = 0)
    {
        $user_info = $this->_check_login();
        $this->load->model('Orders_model', '', TRUE);
        $this->load->model('Orders_detail_model', '', TRUE);
        $this->Orders_model->save(['status'=>10], ['status'=>0, 'create_time <'=>time()-24*3600]);
        $str_where = "user_id = {$user_info['id']}";
        if ($status != 'all') {
            $str_where .= " and status = {$status}";
        }
        $item_list = $this->Orders_model->gets('*', $str_where, $per_page, $per_page * ($page - 1));

        if ($item_list) {
            foreach ($item_list as $key=>$value) {
                $order_detail = $this->Orders_detail_model->get('*', ['order_id'=>$value['id']]);
                if ($order_detail) {
                    $image_arr = filter_image_path($order_detail['item_image']);
                    $order_detail['item_image'] = $image_arr['path'];
                    $order_detail['item_image_thumb'] = $image_arr['path_thumb'];
                }
                $item_list[$key]['order_detail'] = $order_detail;
                $item_list[$key]['create_time'] = date('Y-m-d H:i', strtotime($value['create_time']));
                $item_list[$key]['status_str'] = $this->_orders_status_arr[$value['status']];
            }
        }

        //是否有下一页
        $cur_count = $per_page * ($page - 1) + count($item_list);
        $total_count = $this->Orders_model->count($str_where);
        $is_next_page = 0;
        if ($total_count > $cur_count) {
            $is_next_page = 1;
        }

        printAjaxData(['item_list'=>$item_list, 'is_next_page'=>$is_next_page]);
    }

    /**
     * 取消订单
     */
    public function cancel_order()
    {
        $user_info = $this->_check_login();
        $this->load->model('Orders_model', '', TRUE);

        if ($_POST) {
            $order_id = $this->input->post('order_id', TRUE);
            if (!$order_id) {
                printAjaxError('fail', '参数异常');
            }
            if ($this->Orders_model->save(['status'=>10], ['user_id'=>$user_info['id'], 'id'=>$order_id, 'status'=>0])) {
                $this->load->model('Orders_detail_model', '', TRUE);
                $this->load->model('Combos_model', '', TRUE);
                $this->load->model('Share_goods_model', '', TRUE);
                $orders_detail_list = $this->Orders_detail_model->gets('item_id,item_type,buy_number', ['order_id'=>$order_id]);
                if ($orders_detail_list) {
                    foreach ($orders_detail_list as $key => $value) {
                        if ($value['item_type']) {
                            $this->Share_goods_model->save_column('stock', "stock+{$value['buy_number']}", ['id'=>$value['item_id']]);
                        } else {
                            $this->Combos_model->save_column('stock', "stock+{$value['buy_number']}", ['id'=>$value['item_id']]);
                        }
                    }
                }
                printAjaxSuccess('success', '取消成功');
            }
        }

        printAjaxError('fail', '操作异常');
    }




    /**
     * 吆喝商品列表
     * @param int $per_page
     * @param int $page
     */
    public function get_share_goods_list($type_id = 0, $per_page = 20, $page = 1)
    {
        $this->load->model('Share_goods_model', '', TRUE);
        $this->load->model('Combos_model', '', TRUE);

        $str_where = "display = 1 and status = 1";
        if ($type_id) {
            $str_where .= " and category_id = {$type_id}";
        }
        $item_list = $this->Share_goods_model->gets('id,name,cover_image,price,reward,stock,stock_total,type,goods_id', $str_where, $per_page, $per_page * ($page - 1));
        if ($item_list) {
            foreach ($item_list as $key=>$value) {
                $image_arr = filter_image_path($value['cover_image']);
                $item_list[$key]['cover_image'] = $image_arr['path'];
                $item_list[$key]['cover_image_thumb'] = $image_arr['path_thumb'];
                $item_list[$key]['surplus'] = $value['stock_total'] ? ceil($value['stock']/$value['stock_total']*100) : "0";
            }
        }

        //是否有下一页
        $cur_count = $per_page * ($page - 1) + count($item_list);
        $total_count = $this->Share_goods_model->count($str_where);
        $is_next_page = 0;
        if ($total_count > $cur_count) {
            $is_next_page = 1;
        }

        printAjaxData(['item_list'=>$item_list, 'is_next_page'=>$is_next_page]);
    }

    /**
     * 吆喝商品详情
    */
    public function get_share_goods_detail($id = null)
    {
        if (!$id) {
            printAjaxError('fail','参数异常');
        }
        $this->load->model('Share_goods_model', '', TRUE);
        $item_info = $this->Share_goods_model->get('*', ['id'=>$id]);

        if ($item_info) {
            $item_info['is_collected'] = 0;
            $this->Share_goods_model->save_column('browse_num', 'browse_num+1', ['id'=>$id]);
            $user_id = $this->session->userdata("user_id");
            

            if ($item_info['type'] > 0) {
                $this->load->model('Combos_model', '', TRUE);
                $combos_info = $this->Combos_model->get('*', ['id'=>$item_info['goods_id']]);
                if ($combos_info) {
                    $item_info['address'] = $combos_info['address'];
                    $item_info['location'] = ['lat'=>$combos_info['lat'], 'lng'=>$combos_info['lng']];
                    if ($combos_info['address'] && $combos_info['lat'] == 0 && $combos_info['lng'] == 0) {
                        $location = get_lat_lng($combos_info['address']);
                        if ($location) {
                            $item_info['location'] = $location;
                        }
                    }
                    if ($combos_info['type'] == 1) {
                        $goods_type = 1;
                        $product_info = $this->hbdapiclass->hbd_query_product_info($combos_info['product_id']);
                        $sold_out = (strtotime($product_info['END_DATE']) > time() && strtotime($product_info['XJ_DATE']) > time()) ? 0 : 1;
                        $product_info['sold_out'] = $sold_out;
                        if ($sold_out && $item_info['display'] == 1) {
                            $this->Share_goods_model->save(['display'=>0], ['id'=>$item_info['id']]);
                            $item_info['display'] = 0;
                        }
                        $item_info['product_info'] = $product_info;
                    } else {
                        $goods_type = 2;
                        $this->load->model('Attachment_model', '', TRUE);
                        $attachment_list = [];
                        if ($combos_info['image_ids']) {
                            $tmp_atm_ids = preg_replace(array('/^_/', '/_$/', '/_/'), array('', '', ','), $combos_info['image_ids']);
                            $attachment_list = $this->Attachment_model->gets('id,path',"id in ($tmp_atm_ids)");
                            if ($attachment_list) {
                                foreach ($attachment_list as $key => $value) {
                                    $tmp_image = filter_image_path($value['path']);
                                    $attachment_list[$key]['path'] = $tmp_image['path'];
                                    $attachment_list[$key]['path_thumb'] = $tmp_image['path_max'];
                                }
                            }
                        }
                        $item_info['attachment_list'] = $attachment_list;
            
                        $item_info['content'] = filter_content(html($combos_info['content']), base_url());
                        $item_info['usage_rules'] = filter_content(html($combos_info['usage_rules']), base_url());
            
                        $dishes_list = [];
                        if ($combos_info['dishes_ids']) {
                            $this->load->model('Dishes_model', '', TRUE);
                            $dishes_list = $this->Dishes_model->gets('name, price', "id in ({$combos_info['dishes_ids']})");
                        }
                        $item_info['dishes_list'] = $dishes_list;
                    }
                }

            } else {
                $goods_type = 0;
                $this->load->model('Attachment_model', '', TRUE);
                $attachment_list = [];
                if ($item_info['image_ids']) {
                    $tmp_atm_ids = preg_replace(array('/^_/', '/_$/', '/_/'), array('', '', ','), $item_info['image_ids']);
                    $attachment_list = $this->Attachment_model->gets('id,path',"id in ($tmp_atm_ids)");
                    if ($attachment_list) {
                        foreach ($attachment_list as $key => $value) {
                            $tmp_image = filter_image_path($value['path']);
                            $attachment_list[$key]['path'] = $tmp_image['path'];
                            $attachment_list[$key]['path_thumb'] = $tmp_image['path_max'];
                        }
                    }
                }
                $item_info['attachment_list'] = $attachment_list;
    
                $item_info['content'] = filter_content(html($item_info['content']), base_url());
            }

            $item_info['goods_type'] = $goods_type;
            $item_info['cover_image'] = filter_image_path($item_info['cover_image'])['path_thumb'];
            $this->load->model('Witticisms_model', '', TRUE);
            $witticism_info = $this->Witticisms_model->gets('title', ['display'=>1], 1, 0, 'id', 'RANDOM');
            $item_info['share_title'] = $witticism_info ? $witticism_info[0]['title'].' '.$item_info['name'] : $item_info['name'];


            if ($user_id) {
                $this->load->model('Browsing_histories_model','',TRUE);
                $this->Browsing_histories_model->delete(['user_id'=>$user_id, 'item_id'=>$item_info['id'], 'item_type'=>1]);
                $this->Browsing_histories_model->save(['user_id'=>$user_id, 'item_id'=>$item_info['id'], 'item_type'=>1, 'goods_type'=>$item_info['type']]);
                //是否收藏
                $this->load->model('Collections_model', '', TRUE);
                if ($this->Collections_model->count(['item_id'=>$item_info['id'], 'item_type'=>1, 'user_id'=>$user_id])) {
                    $item_info['is_collected'] = 1;
                }
            }

        }
        printAjaxData(['item_info'=>$item_info]);
    }

    /**
     * 单独商品购买--订单提交页信息
     * @param int $goods_id
     */
    public function single_order_confirm($item_id = null)
    {
        if (!$item_id) {
            printAjaxError('fail', '参数异常');
        }
        $user_info = $this->_check_login();
        $this->load->model('Share_goods_model', '', TRUE);
        $this->load->model('User_coupons_model', '', TRUE);
        $item_info = $this->Share_goods_model->get('*', ['id'=>$item_id, 'display'=>1, 'status'=>1]);
        if (!$item_info) {
            printAjaxError('fail', '参数异常');
        }
        $image_arr = filter_image_path($item_info['cover_image']);
        $item_info['cover_image'] = $image_arr['path'];
        $item_info['cover_image_thumb'] = $image_arr['path_thumb'];

        //优惠券
        $coupon_where = "user_coupons.user_id = {$user_info['id']} and user_coupons.status = 0 and coupons.status = 1 and coupons.achieve_amount <= ({$item_info['price']}) and coupons.usable_goods_ids = '' and ((coupons.way = 0 and coupons.start_time < NOW() and coupons.end_time > NOW()) or (coupons.way <> 0 and TIMESTAMPDIFF(SECOND, user_coupons.get_time, NOW()) < (coupons.valid_days * 3600 * 24)))";
        $coupon_list = $this->User_coupons_model->gets_join_coupons('coupons.title,coupons.way,coupons.type,coupons.achieve_amount,coupons.used_amount,coupons.start_time,coupons.end_time,coupons.valid_days,coupons.usable_goods_ids,user_coupons.id,user_coupons.get_time', $coupon_where);
        if ($coupon_list) {
            foreach ($coupon_list as $key=>$value) {
                if ($value['way'] != 0) {
                    $coupon_list[$key]['start_date'] = date('Y-m-d', strtotime($value['get_time']));
                    $coupon_list[$key]['end_date'] = date('Y-m-d', strtotime("+{$value['valid_days']}days", strtotime($value['get_time'])));
                } else {
                    $coupon_list[$key]['start_date'] = date('Y-m-d', strtotime($value['start_time']));
                    $coupon_list[$key]['end_date'] = date('Y-m-d', strtotime($value['end_time']));
                }
            }
        }

        printAjaxData(['item_info'=>$item_info, 'coupon_list'=>$coupon_list]);
    }

    /**
     * 单独商品购买--订单提交
     */
    public function single_add_orders()
    {
        $user_info = $this->_check_login();
        if ($_POST) {
            $item_id = $this->input->post('item_id', TRUE);
            $buy_number = $this->input->post('buy_number', TRUE);
            $parent_id = $this->input->post('parent_id', TRUE);
            $coupon_id = $this->input->post('coupon_id', TRUE);
            if (!$item_id) {
                printAjaxError('fail', '参数异常');
            }
            $this->load->model('Share_goods_model', '', TRUE);
            $this->load->model('Orders_model', '', TRUE);
            $this->load->model('Orders_process_model', '', TRUE);
            $this->load->model('Orders_detail_model', '', TRUE);
            $item_info = $this->Share_goods_model->get('*', ['id'=>$item_id, 'display'=>1, 'status'=>1]);
            if (!$item_info) {
                printAjaxError('fail', '参数异常');
            }
            if ($item_info['stock'] < $buy_number) {
                printAjaxError('fail', '已售罄');
            }
            $goods_type = 0;
            if ($item_info['type'] > 0) {
                $goods_type = 2;
                $this->load->model('Combos_model', '', TRUE);
                $combos_info = $this->Combos_model->get('*', ['id'=>$item_info['goods_id']]);
                if ($combos_info['type'] == 1 && $combos_info['product_id']) {
                    $goods_type = 1;
                    $product_info = $this->hbdapiclass->hbd_query_product_info($combos_info['product_id']);
                    if (!$product_info) {
                        printAjaxError('fail', '商品不存在');
                    }
                    if (strtotime($product_info['XJ_DATE']) < time() || strtotime($product_info['END_DATE']) < time()) {
                        printAjaxError('fail', '商品已下架');
                    }
                    if ($product_info['EveryPersonMaxNum'] > 0 && $buy_number > $product_info['EveryPersonMaxNum']) {
                        printAjaxError('fail', '商品购买数量超过最大限制');
                    }
                }
            }
            
            
            //优惠券
            $coupon_info = [];
            if ($coupon_id) {
                $this->load->model('User_coupons_model', '', TRUE);
                $this->load->model('Coupons_model', '', TRUE);

                $coupon_where = "user_coupons.id = {$coupon_id} and user_coupons.user_id = {$user_info['id']} and user_coupons.status = 0 and coupons.status = 1 and coupons.achieve_amount <= ({$item_info['price']}) and coupons.usable_goods_ids = '' and ((coupons.way = 0 and coupons.start_time < NOW() and coupons.end_time > NOW()) or (coupons.way <> 0 and TIMESTAMPDIFF(SECOND, user_coupons.get_time, NOW()) < (coupons.valid_days * 3600 * 24)))";
                $coupon_info = $this->User_coupons_model->get_join_coupons('user_coupons.coupon_id,coupons.type,coupons.achieve_amount,coupons.used_amount,coupons.usable_goods_ids', $coupon_where);
            }

            $order_number = $this->_getUniqueOrderNumber();
            $order_total = $item_info['price'] * $buy_number;
            $data = [
                'order_number' => $order_number,
                'user_id' => $user_info['id'],
                'store_id' => $item_info['store_id'],
                // 'total' => $order_total,
                'mobile' => $user_info['mobile'],
                'order_type' => $goods_type == 1 ? 1 : 0
            ];
            if ($coupon_info) {
                $discount = $coupon_info['used_amount'];
                $order_total -= $discount;
                $order_total = $order_total > 0 ? $order_total : 0;
                $data['discount'] = $discount;
                $data['coupon_id'] = $coupon_id;
                $this->User_coupons_model->save(array('use_time'=>date('Y-m-d H:i:s', time()),'status'=>1),array('id'=>$coupon_id));
                $this->Coupons_model->save_column('used_number','used_number+1',array('id'=>$coupon_info['coupon_id']));
            }
            $data['total'] = $order_total;
            $ret_id = $this->Orders_model->save($data);
            if ($ret_id) {
                $detail_data = [
                    'order_id' => $ret_id,
                    'item_id' => $item_info['id'],
                    'item_type' => 1,
                    'goods_type' => $goods_type,
                    'sell_price' => $item_info['price'],
                    'buy_number' => $buy_number,
                    'item_name' => $item_info['name'],
                    'item_image' => $item_info['cover_image']
                ];
                if ($parent_id) {
                    $detail_data['reward'] = $item_info['reward'] * $buy_number;
                    $detail_data['parent_id'] = $parent_id;
                }
                if ($this->Orders_detail_model->save($detail_data)) {
                    //订单跟踪记录
                    $orders_process_data = array(
                        'content' => "订单创建成功",
                        'order_id' => $ret_id,
                        'order_status' => 0,
                        'change_status' => 0
                    );
                    $this->Orders_process_model->save($orders_process_data);
                    $this->Share_goods_model->save_column('stock', "stock-{$buy_number}", ['id'=>$item_id]);
                } else {
                    $this->Orders_model->delete(['id'=>$ret_id]);
                }

                printAjaxData(array('orders_id'=>$ret_id,'total'=>$order_total));
            }
        }

        printAjaxError('fail', '参数异常');
    }

    /**
     * 订单详情
     */
    public function get_order_detail($order_id = null)
    {
        $user_info = $this->_check_login();
        if (!$order_id) {
            printAjaxError('fail', '参数异常');
        }
        $this->load->model('Orders_model', '', TRUE);
        $this->load->model('Orders_detail_model', '', TRUE);
        $order_info = $this->Orders_model->get('*', ['id'=>$order_id, 'user_id'=>$user_info['id']]);
        if (!$order_info) {
            printAjaxError('fail', '参数异常');
        }
        $order_detail_info = $this->Orders_detail_model->get('*', ['order_id'=>$order_id]);
        $order_detail_info['item_image'] = filter_image_path($order_detail_info['item_image'])['path_thumb'];
        $qrcode_path = '';
        if ($order_info['status'] == 1) {
            if ($order_info['order_type'] == 1) {
                $path = "./uploads/order_qrcode/order_{$order_info['order_number']}.png";
                get_qr_code($order_info['hbd_code_no'], $path, 20);
                $qrcode_path = base_url() . substr($path, 2);
            } else {
                $path = "./uploads/order_qrcode/order_{$order_info['order_number']}.png";
                get_qr_code($order_info['order_number'], $path, 20);
                $qrcode_path = base_url() . substr($path, 2);
            }
            
        }
        
        printAjaxData(['order_info'=>$order_info, 'order_detail_info'=>$order_detail_info, 'qrcode_path'=>$qrcode_path]);
    }

    /**
     * 商家查看订单详情
     */
    public function get_store_order_detail($order_number = null)
    {
        $user_info = $this->_check_login(true);
        if (!$order_number) {
            printAjaxError('fail', '参数异常');
        }
        $this->load->model('Orders_model', '', TRUE);
        $this->load->model('Orders_detail_model', '', TRUE);
        $order_info = $this->Orders_model->get('*', ['store_id'=>$user_info['store_id'], 'order_number'=>$order_number, 'status'=>1]);
        if (!$order_info) {
            printAjaxError('fail', '非本店订单');
        }
        $order_detail_info = $this->Orders_detail_model->get('*', ['order_id'=>$order_info['id']]);
        $order_detail_info['item_image'] = filter_image_path($order_detail_info['item_image'])['path_thumb'];

        printAjaxData(['order_info'=>$order_info, 'order_detail_info'=>$order_detail_info]);
    }

    /**
     * 商家确认订单--扫码验券
     */
    public function business_confirm_order($order_id = null)
    {
        $user_info = $this->_check_login(true);
        if (!$order_id) {
            printAjaxError('fail', '参数异常');
        }
        $this->load->model('Orders_model', '', TRUE);
        $order_info = $this->Orders_model->get('*', ['store_id'=>$user_info['store_id'], 'id'=>$order_id, 'status'=>1]);
        if (!$order_info) {
            printAjaxError('fail', '参数异常');
        }

        if ($this->Orders_model->save(['status'=>2], ['id'=>$order_id])) {
            //订单跟踪记录
            $this->load->model('Orders_process_model', '', TRUE);
            $orders_process_data = array(
                'content' => "订单商家确认成功",
                'order_id' => $order_id,
                'order_status' => $order_info['status'],
                'change_status' => 2
            );
            $this->Orders_process_model->save($orders_process_data);

            //交易组件同步订单
            $this->load->model('Orders_form_model', '', TRUE);
            $this->load->model('Pay_log_model', '', TRUE);

            $orders_form_info = $this->Orders_form_model->get('order_number', "find_in_set('{$order_info['id']}', order_ids)");
            $order_number = $orders_form_info ? $orders_form_info['order_number'] : $order_info['order_number'];
            $pay_log_info = $this->Pay_log_model->get('*', ['order_number'=>$order_number]);
            $out_trade_no = $pay_log_info['out_trade_no'];
            $order_user_info = $this->Users_model->get('wx_openid', ['id'=>$pay_log_info['user_id']]);
            $params = [
                'id' => $out_trade_no,
                'openid' => $order_user_info['wx_openid'],
                'action_type' => 1,
                'transaction_id' => $pay_log_info['trade_no'],
                'pay_time' => date('Y-m-d H:i:s', $pay_log_info['notify_time'])
            ];
            $this->load->library('wxapiclass');
            $this->wxapiclass->sync_order_pay($params);
            $params = [
                'order_number' => $out_trade_no,
                'openid' => $order_user_info['wx_openid'],
            ];
            $this->wxapiclass->delivery_send($params);
            $this->wxapiclass->delivery_recieve($params);

            // //推广返佣
            // $this->load->model('Orders_detail_model', '', TRUE);
            // $order_detail_info = $this->Orders_detail_model->get('item_id,item_type,buy_number,reward,parent_id', ['order_id'=>$order_info['id']]);
            // if ($order_detail_info['item_type'] == 1) {
            //     $parent_info = $this->Users_model->get('id,total', ['id'=>$order_detail_info['parent_id'], 'display'=>1]);
            //     if ($parent_info) {
            //         $balance = $parent_info['total'] + $order_detail_info['reward'];
            //         $data = [
            //             'user_id' => $parent_info['id'],
            //             'price' => $order_detail_info['reward'],
            //             'balance' => $balance,
            //             'store_id' => $order_info['store_id'],
            //             'type' => 'order_reward',
            //             'ret_id' => $order_info['id'],
            //             'from_user_id' => $order_info['user_id'],
            //             'cause' => '吆喝推广佣金'
            //         ];
            //         $this->load->model('Financial_model', '', TRUE);
            //         if ($this->Financial_model->save($data)) {
            //             $this->Users_model->save(['total'=>$balance], ['id'=>$parent_info['id']]);
            //         }
            //     }
            // }

            printAjaxSuccess('success', '确认成功！');
        }

        printAjaxError('fail', '操作异常');
    }

    /**
     * 发布圈子
     */
    public function release_life_news()
    {
        $user_info = $this->_check_login();

        if ($_POST) {
            $this->load->model('Life_news_model', '', TRUE);
            $content = strip_tags($this->input->post('content', TRUE));
            $image_ids = $this->input->post('image_ids', TRUE);
            $lng = $this->input->post('lng', TRUE);
            $lat = $this->input->post('lat', TRUE);
            // $this->load->library('wxapiclass');
            // $errcode = $this->wxapiclass->msg_sec_check($content);
            // if ($errcode == 87014){
            //     printAjaxError('fail','内容违法违规！');
            // }
            $this->load->library('baiduapiclass');
            $result = $this->baiduapiclass->text_review($content);
            if (!$result) {
                printAjaxError('fail', '文本存在不合规字符');
            }
            $data = [
                'user_id' => $user_info['id'],
                'content' => $content,
                'image_ids' => $image_ids,
                'lng' => $lng,
                'lat' => $lat
            ];

            if ($this->Life_news_model->save($data)) {
                printAjaxSuccess('success', '发布成功');
            }
        }

        printAjaxError('fail', '发布失败');
    }

    /**
     * 圈子列表
     */
    public function get_life_news_list($per_page = 20, $page = 1)
    {
        $this->load->model('Life_news_model', '', TRUE);
        $this->load->model('Attachment_model', '', TRUE);

        $str_where = "life_news.display = 1";
        $item_list = $this->Life_news_model->gets_join_user('life_news.*, user.path, user.nickname', $str_where, $per_page, $per_page * ($page - 1));
        if ($item_list) {
            foreach ($item_list as $key=>$value) {
                $item_list[$key]['user_path'] = filter_image_path($value['path'])['path_thumb'];
                $attachment_list = [];
                if ($value['image_ids']) {
                    $tmp_atm_ids = preg_replace(array('/^_/', '/_$/', '/_/'), array('', '', ','), $value['image_ids']);
                    $attachment_list = $this->Attachment_model->gets('id,path',"id in ($tmp_atm_ids)");
                    if ($attachment_list) {
                        foreach ($attachment_list as $k => $v) {
                            $tmp_image = filter_image_path($v['path']);
                            $attachment_list[$k]['path'] = $tmp_image['path_max'];
                            $attachment_list[$k]['path_thumb'] = $tmp_image['path_thumb'];
                        }
                    }
                }
                $item_list[$key]['attachment_list'] = $attachment_list;
            }
        }

        //是否有下一页
        $cur_count = $per_page * ($page - 1) + count($item_list);
        $total_count = $this->Life_news_model->count_join_user($str_where);
        $is_next_page = 0;
        if ($total_count > $cur_count) {
            $is_next_page = 1;
        }

        printAjaxData(['item_list'=>$item_list, 'is_next_page'=>$is_next_page]);
    }

    /**
     * 个人主页
     */
    public function get_user_homepage($user_id = NULL, $per_page = 20, $page = 1)
    {
        if (!$user_id) {
            printAjaxError('fail', '参数异常');
        }
        $user_info = $this->_check_login();
        $this->load->model('Life_news_model', '', TRUE);
        $this->load->model('Attachment_model', '', TRUE);

        //头像
        $users_info = $this->Users_model->get('id,nickname,path', ['id'=>$user_id]);
        if (!$users_info) {
            printAjaxError('fail', '参数异常');
        }
        $tmp_image_arr = filter_image_path($users_info['path']);
        $users_info['path'] = $tmp_image_arr['path_thumb'];


        $str_where = "life_news.display = 1 and life_news.user_id = {$user_id}";
        $item_list = $this->Life_news_model->gets_join_user('life_news.*, user.path, user.nickname', $str_where, $per_page, $per_page * ($page - 1));
        if ($item_list) {
            foreach ($item_list as $key=>$value) {
                $item_list[$key]['user_path'] = filter_image_path($value['path'])['path_thumb'];
                $attachment_list = [];
                if ($value['image_ids']) {
                    $tmp_atm_ids = preg_replace(array('/^_/', '/_$/', '/_/'), array('', '', ','), $value['image_ids']);
                    $attachment_list = $this->Attachment_model->gets('id,path',"id in ($tmp_atm_ids)");
                    if ($attachment_list) {
                        foreach ($attachment_list as $k => $v) {
                            $tmp_image = filter_image_path($v['path']);
                            $attachment_list[$k]['path'] = $tmp_image['path_max'];
                            $attachment_list[$k]['path_thumb'] = $tmp_image['path_thumb'];
                        }
                    }
                }
                $item_list[$key]['attachment_list'] = $attachment_list;
            }
        }

        //是否有下一页
        $cur_count = $per_page * ($page - 1) + count($item_list);
        $total_count = $this->Life_news_model->count_join_user($str_where);
        $is_next_page = 0;
        if ($total_count > $cur_count) {
            $is_next_page = 1;
        }

        $users_info['news_count'] = $total_count;
        printAjaxData(['user_info'=> $users_info, 'item_list'=>$item_list, 'is_next_page'=>$is_next_page]);
    }

    /**
     * 我的评价列表
     */
    public function get_user_comments_list($per_page = 10, $page = 1)
    {
        $user_info = $this->_check_login();
        $this->load->model('Comments_model', "", TRUE);
        $this->load->model('Attachment_model', "", TRUE);
        $strWhere = "comments.user_id = {$user_info['id']} and comments.display = 1";

        $comment_list = $this->Comments_model->gets_join_user('comments.*, user.nickname, user.path as user_path', $strWhere, $per_page, $per_page * ($page - 1));
        if ($comment_list){
            foreach ($comment_list as $key=>$value){
                $tmp_image_arr = filter_image_path($value['user_path']);
                $comment_list[$key]['user_path'] = $tmp_image_arr['path_thumb'];
                $comment_list[$key]['create_time'] = date('Y-m-d', strtotime($value['create_time']));
                $attachment_list = [];
                if ($value['image_ids']) {
                    $tmp_atm_ids = preg_replace(array('/^_/', '/_$/', '/_/'), array('', '', ','), $value['image_ids']);
                    $attachment_list = $this->Attachment_model->gets('id,path',"id in ($tmp_atm_ids)");
                    foreach ($attachment_list as $k => $ls) {
                        $tmp_image_arr = filter_image_path($ls['path']);
                        $attachment_list[$k]['path'] = $tmp_image_arr['path_max'];
                        $attachment_list[$k]['path_thumb'] = $tmp_image_arr['path_thumb'];
                    }
                }
                $comment_list[$key]['attachment_list'] = $attachment_list;
            }
        }

        //是否有下一页
        $cur_count = $per_page * ($page - 1) + count($comment_list);
        $total_count = $this->Comments_model->count_join_user($strWhere);
        $is_next_page = 0;
        if ($total_count > $cur_count) {
            $is_next_page = 1;
        }
        $item_info['is_next_page'] = $is_next_page;
        printAjaxData(array('item_list' => $comment_list, 'is_next_page' => $is_next_page));
    }

    /**
     * 足迹
     */
    public function get_browsing_histories_list($per_page = 10, $page = 1)
    {
        $user_info = $this->_check_login();
        $this->load->model('Browsing_histories_model', "", TRUE);
        $str_where = "browsing_histories.user_id = {$user_info['id']} and browsing_histories.display = 1";
        $item_list = $this->Browsing_histories_model->gets('*', $str_where, $per_page, $per_page * ($page - 1));
        $cur_count = $per_page * ($page - 1) + count($item_list);

        if ($item_list) {
            $this->load->model('Attachment_model', "", TRUE);
            $this->load->model('Stores_model', "", TRUE);
            $this->load->model('Store_type_model', "", TRUE);
            $this->load->model('Combos_model', "", TRUE);
            $this->load->model('Share_goods_model', "", TRUE);
            foreach ($item_list as $key=>$value) {
                if ($value['item_type'] == 0) {
                    $item_info = $this->Combos_model->get('name,cover_image,original_price,price', ['id'=>$value['item_id'], 'display'=>1]);
                    if ($item_info) {
                        $item_info['cover_image'] = filter_image_path($item_info['cover_image'])['path_thumb'];
                    } else {
                        unset($item_list[$key]);
                        continue;
                    }
                } elseif ($value['item_type'] == 1) {
                    $item_info = $this->Share_goods_model->get('name,cover_image,original_price,price', ['id'=>$value['item_id'], 'status'=>1, 'display'=>1]);
                    if ($item_info) {
                        $item_info['cover_image'] = filter_image_path($item_info['cover_image'])['path_thumb'];
                    } else {
                        unset($item_list[$key]);
                        continue;
                    }
                } elseif ($value['item_type'] == 2) {
                    $item_info = $this->Stores_model->get('store_name,logo,type_id,per_amount', ['id'=>$value['item_id'], 'status'=>1]);
                    if ($item_info) {
                        $item_info['logo'] = filter_image_path($item_info['logo'])['path_thumb'];
                        $type_info = $this->Store_type_model->get('name', ['id'=>$item_info['type_id']]);
                        $item_info['type_name'] = $type_info ? $type_info['name'] : '';
                    } else {
                        unset($item_list[$key]);
                        continue;
                    }
                } else {
                    continue;
                }
                $item_list[$key]['item_info'] = $item_info;
            }
            $item_list = array_values($item_list);
        }

        //是否有下一页
        $total_count = $this->Browsing_histories_model->count($str_where);
        $is_next_page = 0;
        if ($total_count > $cur_count) {
            $is_next_page = 1;
        }
        $item_info['is_next_page'] = $is_next_page;
        printAjaxData(array('item_list' => $item_list, 'is_next_page' => $is_next_page));
    }

    /**
     * 添加、取消收藏
     * @param int item_id
     * @param int item_type  0=套餐 1=吆喝 2=商户
     */
    public function save_collections($item_id = 0, $item_type = 0)
    {
        $user_info = $this->_check_login();
        $this->load->model('Collections_model', "", TRUE);
        $this->load->model('Combos_model', "", TRUE);
        $this->load->model('Stores_model', "", TRUE);
        $this->load->model('Share_goods_model', "", TRUE);

        if (!$item_id) {
            printAjaxError('fail', '参数异常');
        }
        $item_info = '';
        $goods_type = 0;
        if ($item_type == 0) {
            $item_info = $this->Combos_model->get('id,type', ['id'=>$item_id]);
            $goods_type = $item_info['type'];
        } elseif ($item_type == 1) {
            $item_info = $this->Share_goods_model->get('id,type', ['id'=>$item_id]);
            $goods_type = $item_info['type'];
        } elseif ($item_type == 2) {
            $item_info = $this->Stores_model->get('id', ['id'=>$item_id]);
        }
        if (!$item_info) {
            printAjaxError('fail', '参数异常');
        }
        $collections_info = $this->Collections_model->get('*', ['user_id'=>$user_info['id'], 'item_id'=>$item_id, 'item_type'=>$item_type]);
        if ($collections_info) {
            if ($this->Collections_model->delete(['id'=>$collections_info['id']])) {
                if ($item_type == 0) {
                    $this->Combos_model->save_column('collections_num', 'collections_num-1', ['id'=>$item_id]);
                }
                if ($item_type == 1) {
                    $this->Share_goods_model->save_column('collections_num', 'collections_num-1', ['id'=>$item_id]);
                }
                printAjaxSuccess('success', '取消收藏成功');
            }
        }else{
            if ($this->Collections_model->save(['user_id'=>$user_info['id'], 'item_id'=>$item_id, 'item_type'=>$item_type, 'goods_type'=>$goods_type])) {
                if ($item_type == 0) {
                   $this->Combos_model->save_column('collections_num', 'collections_num+1', ['id'=>$item_id]);
                }
                if ($item_type == 1) {
                    $this->Share_goods_model->save_column('collections_num', 'collections_num+1', ['id'=>$item_id]);
                }
                printAjaxSuccess('success', '收藏成功');
            }
        }
        printAjaxError('fail', '操作失败');
    }


    /**
     * 收藏
     */
    public function get_collections_list($per_page = 10, $page = 1)
    {
        $user_info = $this->_check_login();
        $this->load->model('Collections_model', "", TRUE);
        $str_where = "user_id = {$user_info['id']}";
        $item_list = $this->Collections_model->gets('*', $str_where, $per_page, $per_page * ($page - 1));
        $cur_count = $per_page * ($page - 1) + count($item_list);

        if ($item_list) {
            $this->load->model('Attachment_model', "", TRUE);
            $this->load->model('Stores_model', "", TRUE);
            $this->load->model('Store_type_model', "", TRUE);
            $this->load->model('Combos_model', "", TRUE);
            $this->load->model('Share_goods_model', "", TRUE);
            foreach ($item_list as $key=>$value) {
                if ($value['item_type'] == 0) {
                    $item_info = $this->Combos_model->get('name,cover_image,original_price,price', ['id'=>$value['item_id'], 'display'=>1]);
                    if ($item_info) {
                        $item_info['cover_image'] = filter_image_path($item_info['cover_image'])['path_thumb'];
                    } else {
                        unset($item_list[$key]);
                        continue;
                    }
                } elseif ($value['item_type'] == 1) {
                    $item_info = $this->Share_goods_model->get('name,cover_image,original_price,price,type,goods_id', ['id'=>$value['item_id'], 'status'=>1, 'display'=>1]);
                    if ($item_info) {
                        $item_info['cover_image'] = filter_image_path($item_info['cover_image'])['path_thumb'];
                    } else {
                        unset($item_list[$key]);
                        continue;
                    }
                } elseif ($value['item_type'] == 2) {
                    $item_info = $this->Stores_model->get('store_name,logo,type_id,per_amount', ['id'=>$value['item_id'], 'status'=>1]);
                    if ($item_info) {
                        $item_info['logo'] = filter_image_path($item_info['logo'])['path_thumb'];
                        $type_info = $this->Store_type_model->get('name', ['id'=>$item_info['type_id']]);
                        $item_info['type_name'] = $type_info ? $type_info['name'] : '';
                    } else {
                        unset($item_list[$key]);
                        continue;
                    }
                } else {
                    continue;
                }
                $item_list[$key]['item_info'] = $item_info;
            }

            $item_list = array_values($item_list);
        }

        //是否有下一页
        $total_count = $this->Collections_model->count($str_where);
        $is_next_page = 0;
        if ($total_count > $cur_count) {
            $is_next_page = 1;
        }
        $item_info['is_next_page'] = $is_next_page;
        printAjaxData(array('item_list' => $item_list, 'is_next_page' => $is_next_page));
    }

    /**
     * 种草内容列表
     * @param int $per_page
     * @param int $page
     */
    public function get_grasses_list($per_page = 20, $page = 1)
    {
        $this->load->model('Grasses_model', '', TRUE);

        $str_where = "grasses.display = 1";
        $item_list = $this->Grasses_model->gets_join_stores('grasses.id,grasses.title,grasses.cover_image,grasses.create_time,stores.store_name,stores.logo', $str_where, $per_page, $per_page * ($page - 1), 'grasses.browse_num');
        if ($item_list) {
            foreach ($item_list as $key=>$value) {
                $image_arr = filter_image_path($value['cover_image']);
                $item_list[$key]['cover_image'] = $image_arr['path_thumb'];
                $item_list[$key]['logo'] = filter_image_path($value['logo'])['path_thumb'];
                $item_list[$key]['create_time'] = date('Y-m-d', strtotime($value['create_time']));
            }
        }

        //是否有下一页
        $cur_count = $per_page * ($page - 1) + count($item_list);
        $total_count = $this->Grasses_model->count_join_stores($str_where);
        $is_next_page = 0;
        if ($total_count > $cur_count) {
            $is_next_page = 1;
        }

        printAjaxData(['item_list'=>$item_list, 'is_next_page'=>$is_next_page]);
    }

    /**
     * 种草内容详情
     * @param int $id
    */
    public function get_grasses_detail($id = null)
    {
        if (!$id) {
            printAjaxError('fail','参数异常');
        }
        $this->load->model('Grasses_model', '', TRUE);
        $item_info = $this->Grasses_model->get('*', ['id'=>$id]);

        if ($item_info) {
            $item_info['is_collected'] = 0;
            $this->Grasses_model->save_column('browse_num', 'browse_num+1', ['id'=>$id]);
            
            $this->load->model('Attachment_model', '', TRUE);
            $attachment_list = [];
            if ($item_info['image_ids']) {
                $tmp_atm_ids = preg_replace(array('/^_/', '/_$/', '/_/'), array('', '', ','), $item_info['image_ids']);
                $attachment_list = $this->Attachment_model->gets('id,path',"id in ($tmp_atm_ids)");
                if ($attachment_list) {
                    foreach ($attachment_list as $key => $value) {
                        $tmp_image = filter_image_path($value['path']);
                        $attachment_list[$key]['path'] = $tmp_image['path'];
                        $attachment_list[$key]['path_thumb'] = $tmp_image['path_max'];
                    }
                }
            }
            $item_info['attachment_list'] = $attachment_list;

            $item_info['content'] = filter_content(html($item_info['content']), base_url());
            $item_info['create_time'] = date('Y-m-d', strtotime($item_info['create_time']));


            $this->load->model('Stores_model', '', TRUE);
            $this->load->model('User_comments_model', '', TRUE);
            $store_info = $this->Stores_model->get('id,logo,store_name', ['id'=>$item_info['store_id']]);
            $store_info['logo'] = filter_image_path($store_info['logo'])['path_thumb'];
            $item_info['store_info'] = $store_info;
            $item_info['comment_num'] = $this->User_comments_model->count(['item_id'=>$id, 'item_type'=>0]);
            $user_id = $this->session->userdata("user_id");
            if ($user_id) {
                //是否收藏
                $this->load->model('Collections_model', '', TRUE);
                if ($this->Collections_model->count(['item_id'=>$item_info['store_id'], 'item_type'=>2, 'user_id'=>$user_id])) {
                    $item_info['is_collected'] = 1;
                }
            }

        }
        printAjaxData(['item_info'=>$item_info]);
    }

    /**
     * 推广记录
     * @param int $per_page
     * @param int $page
     */
    public function get_reward_order_list($status = 0, $per_page = 20, $page = 1)
    {
        $user_info = $this->_check_login();
        $this->load->model('Orders_detail_model', '', TRUE);

        $str_where = "orders_detail.parent_id = {$user_info['id']} and orders_detail.order_id in (select ret_id from financial where user_id = {$user_info['id']} and type = 'order_reward')";
        if ($status) {
            if ($status == 1) {
                $str_where .= " and orders.status = 1";
            } else {
                $str_where .= " and orders.status > 1";
            }
        }
        $item_list = $this->Orders_detail_model->gets_join_orders('orders_detail.*, orders.user_id, orders.create_time, orders.status', $str_where, $per_page, $per_page * ($page - 1));
        if ($item_list) {
            foreach ($item_list as $key=>$value) {
                $item_list[$key]['item_image'] = filter_image_path($value['item_image'])['path_thumb'];
                $item_list[$key]['create_time'] = date('Y-m-d H:i', strtotime($value['create_time']));
                $cur_user_info = $this->Users_model->get('nickname,path', ['id'=>$value['user_id']]);
                $item_list[$key]['nickname'] = $cur_user_info ? $cur_user_info['nickname'] : '';
                $item_list[$key]['user_avatar'] = $cur_user_info ? filter_image_path($cur_user_info['path'])['path_thumb'] : '';
                $item_list[$key]['status'] = $value['status'] > 1 ? 2 : 1;
            } 
        }

        //是否有下一页
        $cur_count = $per_page * ($page - 1) + count($item_list);
        $total_count = $this->Orders_detail_model->count_join_orders($str_where);
        $is_next_page = 0;
        if ($total_count > $cur_count) {
            $is_next_page = 1;
        }

        printAjaxData(['item_list'=>$item_list, 'is_next_page'=>$is_next_page]);
    }


    /**
     * 发布评论
     */
    public function add_user_comments()
    {
        $user_info = $this->_check_login();
        $content = strip_tags($this->input->post('content',TRUE));
        $item_id = $this->input->post('item_id',TRUE);
        $item_type = $this->input->post('item_type',TRUE);
        if (!$content){
            printAjaxError('fail','请填写评论内容！');
        }
        $this->load->library('baiduapiclass');
        $result = $this->baiduapiclass->text_review($content);
        if (!$result) {
            printAjaxError('fail', '文本存在不合规字符');
        }
        $this->load->model('User_comments_model', "", TRUE);
        $data = array(
            'user_id' => $user_info['id'],
            'item_id' => $item_id,
            'item_type' => $item_type,
            'content' => $content
        );
        if ($this->User_comments_model->save($data)) {
            printAjaxSuccess('success', '评论成功');
        }
        printAjaxError('fail', '评论失败');
    }

    /**
     * 评论列表
     */
    public function get_news_comments_list($item_id = NULL, $item_type = 0, $per_page = 10, $page = 1)
    {
        $this->load->model('User_comments_model', "", TRUE);
        $this->load->model('Attachment_model', "", TRUE);

        if (!$item_id){
            printAjaxError('fail','参数异常！');
        }

        $strWhere = "user_comments.item_id = {$item_id} and user_comments.item_type = {$item_type}";

        $comment_list = $this->User_comments_model->gets_join_user('user_comments.*, user.nickname, user.path as user_path', $strWhere, $per_page, $per_page * ($page - 1));
        if ($comment_list){
            foreach ($comment_list as $key=>$value){
                $tmp_image_arr = filter_image_path($value['user_path']);
                $comment_list[$key]['user_path'] = $tmp_image_arr['path_thumb'];
                $comment_list[$key]['create_time'] = date('Y-m-d', strtotime($value['create_time']));
            }
        }

        //是否有下一页
        $cur_count = $per_page * ($page - 1) + count($comment_list);
        $total_count = $this->User_comments_model->count_join_user($strWhere);
        $is_next_page = 0;
        if ($total_count > $cur_count) {
            $is_next_page = 1;
        }
        $item_info['is_next_page'] = $is_next_page;
        printAjaxData(array('item_list' => $comment_list, 'is_next_page' => $is_next_page));
    }

    /**
     * 提现
     */
    public function withdrawal()
    {
        $user_info = $this->_check_login();
        if ($_POST) {
            $amount = $this->input->post('amount', TRUE);
            $bank_card_id = $this->input->post('bank_card_id', TRUE);
            if (!$amount || $amount <= 0) {
                printAjaxError('fail', '请填写正确的提现金额！');
            }

            if ($amount > $user_info['total']) {
                printAjaxError('fail', '账户金额不足！');
            }

            if (!$bank_card_id) {
                printAjaxError('fail', '请选择银行卡！');
            }
            $this->load->model('Bank_card_model', "", TRUE);
            $bank_card_info = $this->Bank_card_model->get('*',array('id'=>$bank_card_id,'user_id'=>$user_info['id']));
            if (!$bank_card_info) {
                printAjaxError('fail','参数异常');
            }
            $this->load->model('Withdrawal_record_model', "", TRUE);
            if ($this->Withdrawal_record_model->count("DATEDIFF(now(), create_time) = 0 and user_id = {$user_info['id']}")) {
                printAjaxError('fail', '每日仅能提现一次！');
            }

            $data = [
                'user_id' => $user_info['id'],
                'amount' => $amount,
                'balance' => $user_info['total'] - $amount,
                'realname' => $bank_card_info['realname'],
                'account' => $bank_card_info['card_number'],
                'bank' => $bank_card_info['bank'],
                'location' => $bank_card_info['location'],
                'type' => '银行卡'
            ];

            $ret_id = $this->Withdrawal_record_model->save($data);
            if ($ret_id) {
                if ($this->Users_model->save_column('total', "total-{$amount}", ['id'=>$user_info['id']])) {
                    $f_data = [
                        'user_id' => $user_info['id'],
                        'price' => -$amount,
                        'balance' => $user_info['total'] - $amount,
                        'type' => 'recharge_out',
                        'ret_id' => $ret_id,
                        'cause' => '提现'
                    ];
                    $this->load->model('Financial_model', '', TRUE);
                    if ($this->Financial_model->save($f_data)) {
                        printAjaxSuccess('success', '已提交申请');
                    }
                }
                
            }
        }
        
    }

    public function save_bank_card()
    {
        $user_info = $this->_check_login();
        if ($_POST) {
            $id = $this->input->post('id', TRUE);
            $realname = $this->input->post('realname', TRUE);
            $bank = $this->input->post('bank', TRUE);
            $location = $this->input->post('location', TRUE);
            $card_number = $this->input->post('card_number', TRUE);
            $card_number = str_replace(' ', '', $card_number);
            if (!preg_match('/^([1-9]{1})(\d{15}|\d{16}|\d{18})$/', $card_number)){
                printAjaxError('fail', '银行卡号格式不正确');
            }
            $data = [
                'user_id' => $user_info['id'],
                'realname' => $realname,
                'bank' => $bank,
                'location' => $location,
                'card_number' => $card_number,
            ];
            $this->load->model('Bank_card_model', '', TRUE);
            if ($this->Bank_card_model->save($data, $id ? array('id'=>$id) : NULL)){
                printAjaxSuccess('success','添加成功');
            }
        }
        printAjaxError('fail','参数异常');
    }


    public function get_bank_card_list()
    {
        $user_info = $this->_check_login();
        $this->load->model('Bank_card_model', '', TRUE);
        $bank_card_list = $this->Bank_card_model->gets('*',array('user_id'=>$user_info['id']));
        printAjaxData(array('item_list'=>$bank_card_list));
    }

    public function delete_bank_card($id = null)
    {
        $user_info = $this->_check_login();
        $this->load->model('Bank_card_model', '', TRUE);
        if ($this->Bank_card_model->delete(['id'=>$id, 'user_id'=>$user_info['id']])) {
            printAjaxSuccess('success', '删除成功');
        }
        printAjaxError('fail', '删除失败');
    }

    /**
     * 我的资金(分页)
     * @param type $per_page
     * @param type $page
     */
    public function get_financial_list($per_page = 20, $page = 1) {
        $user_info = $this->_check_login();
        $this->load->model('Financial_model', '', TRUE);

        $strWhere = 'user_id = ' . $user_info['id'] . ' and record_type = 0';
        $item_list = $this->Financial_model->gets('cause,price,create_time', $strWhere, $per_page, $per_page * ($page - 1));
        foreach ($item_list as $key => $value) {
            $item_list[$key]['create_time'] = date('Y-m-d H:i', strtotime($value['create_time']));
        }

        $cur_count = $per_page * ($page - 1) + count($item_list);
        $total_count = $this->Financial_model->count($strWhere);
        $is_next_page = 0;
        if ($total_count > $cur_count) {
            $is_next_page = 1;
        }
        printAjaxData(array('item_list' => $item_list, 'total' => $user_info['total'], 'is_next_page' => $is_next_page));
    }

    //搜索结果
    public function get_search_list($keyword = NULL, $per_page = 20, $page = 1)
    {
        $keyword = urldecode($keyword);
        if ($keyword) {
            $this->load->model('Stores_model', '', TRUE);
            $this->load->model('Combos_model', '', TRUE);
            $this->load->model('Share_goods_model', '', TRUE);
            $this->load->model('Store_type_model', '', TRUE);
        
            $str_where = "store_type in (0,2) and status = 1 and ((store_name regexp '{$keyword}') or (id in (select store_id from combos where name regexp '{$keyword}' and display = 1)) or (id in (select store_id from share_goods where name regexp '{$keyword}' and display = 1 and status = 1)))";
            $item_list = $this->Stores_model->gets('id, store_name, logo, per_amount, type_id', $str_where, $per_page, $per_page * ($page - 1), 'browse_num');

            if ($item_list) {
                foreach ($item_list as $key => $value) {
                    $item_list[$key]['logo'] =  filter_image_path($value['logo'])['path_thumb'];
                    $type_info = $this->Store_type_model->get('name',array('id'=>$value['type_id']));
                    $item_list[$key]['type_str'] = $type_info ? $type_info['name'] : '';
                    $combos_list = $this->Combos_model->gets("id,name,price,original_price,cover_image,browse_num,type as goods_type, 'combos' as type", "store_id = {$value['id']} and name regexp '{$keyword}' and display = 1");
                    $share_goods_list = $this->Share_goods_model->gets("id,name,price,original_price,cover_image,browse_num, 'share_goods' as type", "store_id = {$value['id']} and name regexp '{$keyword}' and display = 1 and status = 1");
                    $goods_list = array_merge($combos_list,$share_goods_list);
                    if ($goods_list) {
                        $browse_num = [];
                        foreach ($goods_list as $k=>$goods) {
                            $browse_num[$k] = $goods['browse_num'];
                            $goods_list[$k]['cover_image'] =  filter_image_path($goods['cover_image'])['path_thumb'];
                        }
                        array_multisort($browse_num, SORT_DESC, $goods_list);
                    }

                    $item_list[$key]['goods_list'] = $goods_list;
                }
            }

            //是否有下一页
            $cur_count = $per_page * ($page - 1) + count($item_list);
            $total_count = $this->Stores_model->count($str_where);
            $is_next_page = 0;
            if ($total_count > $cur_count) {
                $is_next_page = 1;
            }
            $item_info['is_next_page'] = $is_next_page;
            printAjaxData(['item_list'=>$item_list, 'is_next_page' => $is_next_page]);

        }
        printAjaxData(['item_list'=>[]]);
    }

    public function search_result_list($keyword = NULL, $per_page = 20, $page = 1)
    {
        $item_list = [];
        $is_next_page = 0;
        $keyword = urldecode($keyword);
        if ($keyword) {
            $this->load->model('Combos_model', '', TRUE);
            $this->load->model('Share_goods_model', '', TRUE);
            $combos_list = $this->Combos_model->gets("id,name,price,original_price,cover_image,browse_num,type as goods_type, 'combos' as type", "name regexp '{$keyword}' and display = 1");
            $share_goods_list = $this->Share_goods_model->gets("id,name,price,original_price,cover_image,browse_num,type as goods_type, 'share_goods' as type", "name regexp '{$keyword}' and display = 1 and status = 1");
            $goods_list = array_merge($combos_list,$share_goods_list);

            if ($goods_list) {
                $browse_num = [];
                foreach ($goods_list as $k=>$goods) {
                    $browse_num[$k] = $goods['browse_num'];
                    $goods_list[$k]['cover_image'] =  filter_image_path($goods['cover_image'])['path_thumb'];
                }
                array_multisort($browse_num, SORT_DESC, $goods_list);
                $item_list = array_slice($goods_list, $per_page * ($page - 1), $per_page);
            }

             //是否有下一页
             $cur_count = $per_page * ($page - 1) + count($item_list);
             $total_count = count($goods_list);
             $is_next_page = 0;
             if ($total_count > $cur_count) {
                 $is_next_page = 1;
             }
        }
        printAjaxData(['item_list'=>$item_list, 'is_next_page' => $is_next_page]);

    }

    /**
     * 申请退款
     */
    public function order_refund()
    {
        $user_info = $this->_check_login();
        printAjaxError('fail','退款失败');
        if ($_POST) {
            $this->load->model('Orders_model', '', TRUE);
            $this->load->model('Order_refunds_model', '', TRUE);
            $order_id = $this->input->post('order_id', TRUE);
            $reason_id = $this->input->post('reason_id', TRUE);
            $reason = $this->input->post('reason', TRUE);
            $orther_reason = $this->input->post('orther_reason', TRUE);

            if (!$order_id) {
                printAjaxError('fail', '参数异常');
            }
            $order_info = $this->Orders_model->get('store_id,status,order_number,trade_no,total', ['id'=>$order_id, 'user_id'=>$user_info['id']]);
            if (!$order_info || $order_info['status'] != 1) {
                printAjaxError('fail', '参数异常');
            }
            if ($this->Order_refunds_model->count(['user_id'=>$user_info['id'], 'orders_id'=>$order_id])) {
                printAjaxError('fail', '请勿重复提交');
            }

            $out_refund_no = $this->_getUniqueOrderNumber('out_refund_no');
            require_once "sdk/weixin_pay/lib/WxPay.Api.php";
            $pay_api = new WxPayApi();
            $result = $pay_api->do_refund($order_info['trade_no'],NULL,$out_refund_no,$order_info['total']*100,$order_info['total']*100,1);
            // logs($result);
            if ($result['return_code'] != 'SUCCESS' || $result['result_code'] != 'SUCCESS'){
                printAjaxError('fail','退款失败');
            }
            $data = [
                'user_id' => $user_info['id'],
                'store_id' => $order_info['store_id'],
                'orders_id' => $order_id,
                'reson_id' => $reason_id,
                'reason' => $reason,
                'orther_reason' => $orther_reason,
                'status' => 1,
                'out_refund_no'=> $out_refund_no,
                'refund_trade_no'=> $result['refund_id'],
                'refund_money'=> $result['refund_fee']*100,
            ];

            if ($this->Order_refunds_model->save($data)) {
                $this->Orders_model->save(['status'=>9, 'out_refund_no'=>$out_refund_no, 'refund_trade_no'=>$result['refund_id']], ['id'=>$order_id]);

                //撤销平台奖金
                $this->load->model('Bonus_record_model', '', TRUE);
                $bonus_record = $this->Bonus_record_model->get('id,amount', ['ret_id'=>$order_id, 'type'=>0]);
                if ($bonus_record) {
                    if ($this->Bonus_record_model->delete(['id'=>$bonus_record['id']])) {
                        $this->load->model('System_model', '', TRUE);
                        $this->System_model->save_column('bonus_amount', 'bonus_amount-'.$bonus_record['amount'], ['id'=>1]);
                    }
                }

                printAjaxSuccess('success', '退款申请已提交');
            }
        }

        printAjaxError('fail','退款失败');
    }

    /**
     * 更新商品列表
     */
    public function hbd_query_product_list($page = 1)
    {
        $this->hbdapiclass->hbd_query_product_list($page);
        printAjaxSuccess('success','更新成功');
    }

    /**
     * 更新商品信息
     */
    public function check_combos($page = 1)
    {
        // $page = $this->input->get('page', TRUE);
        // if (!$page) {
        //     $page = 1;
        // }
        $this->load->model('Combos_model', '', TRUE);
        $this->load->model('Share_goods_model', '', TRUE);

        $item_list = $this->Combos_model->gets('id,stock,type,product_id', ['display'=>1], 100, 100*($page - 1));
        foreach ($item_list as $key=>$item_info) {
            if ($item_info['type'] == 1) {
                $product_info = $this->hbdapiclass->hbd_query_product_info($item_info['product_id']);
                if ($product_info) {
                    $sold_out = (strtotime($product_info['END_DATE']) > time() && strtotime($product_info['XJ_DATE']) > time()) ? 0 : 1;
                    if ($sold_out) {
                        $this->Combos_model->save(['display'=>0, 'content'=>''], ['id'=>$item_info['id']]);
                        $this->Share_goods_model->save(['display'=>0, 'content'=>''], ['goods_id'=>$item_info['id']]);
                    }
                } else {
                    $this->Combos_model->save(['display'=>0, 'type'=>0, 'content'=>''], ['id'=>$item_info['id']]);
                    $this->Share_goods_model->save(['display'=>0, 'content'=>''], ['goods_id'=>$item_info['id']]);
                }
                
            } else {
                if ($item_info['stock'] <= 0) {
                    $this->Combos_model->save(['display'=>0, 'content'=>''], ['id'=>$item_info['id']]);
                }
            }
        }
        if (count($item_list) == 100) {
            echo 1;
            // $url='http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?page='. ($page + 1);
            // http_curl($url);
            // file_get_contents($url);
            // echo '<script>url="'.$url.'";window.location.href=url;</script>';
        } else {
            echo 0;
        }
        
    }

    /**
     * 我的优惠券列表
     */
    public function my_coupons_list($per_page = 20, $page = 1)
    {
        $user_info = $this->_check_login();
        $this->load->model('User_coupons_model', '', TRUE);

        $str_where = "user_coupons.user_id = {$user_info['id']} and user_coupons.status <> 1 and coupons.status = 1 and ((coupons.way = 0 and coupons.start_time < NOW() and coupons.end_time > NOW()) or (coupons.way <> 0 and TIMESTAMPDIFF(SECOND, user_coupons.get_time, NOW()) < (coupons.valid_days * 3600 * 24)))";
        $item_list = $this->User_coupons_model->gets_join_coupons('coupons.title,coupons.way,coupons.type,coupons.achieve_amount,coupons.used_amount,coupons.start_time,coupons.end_time,coupons.valid_days,coupons.usable_goods_ids,user_coupons.id,user_coupons.get_time,user_coupons.status', $str_where, $per_page, $per_page * ($page - 1));

        if ($item_list) {
            foreach ($item_list as $key=>$value) {
                if ($value['way'] != 0) {
                    $item_list[$key]['start_date'] = date('Y-m-d', strtotime($value['get_time']));
                    $item_list[$key]['end_date'] = date('Y-m-d', strtotime("+{$value['valid_days']}days", strtotime($value['get_time'])));
                } else {
                    $item_list[$key]['start_date'] = date('Y-m-d', strtotime($value['start_time']));
                    $item_list[$key]['end_date'] = date('Y-m-d', strtotime($value['end_time']));
                }
                
                $item_list[$key]['used_amount'] = floatval($value['used_amount']);
            }
        }
        $cur_count = $per_page * ($page - 1) + count($item_list);
        $total_count = $this->User_coupons_model->count_join_coupons($str_where);
        $is_next_page = 0;
        if ($total_count > $cur_count) {
            $is_next_page = 1;
        }

        printAjaxData(['item_list'=>$item_list, 'is_next_page' => $is_next_page]);
    }

    /**
     * 平台奖励金详情
     */
    public function bonus_info()
    {
        $this->load->model('System_model', '', TRUE);
        $this->load->model('Bonus_record_model', '', TRUE);
        $system_info = $this->System_model->get('bonus_amount', ['id'=>1]);

        $expend_bonus_amount = $this->Bonus_record_model->sum('amount', ['amount <' => 0]);

        printAjaxData(['bonus_amount'=>$system_info['bonus_amount'], 'expend_bonus_amount' => -$expend_bonus_amount]);
    }

    /**
     * 平台奖金明细(分页)
     * @param type $per_page
     * @param type $page
     */
    public function get_bonus_record_list($per_page = 20, $page = 1) {
        $this->load->model('Bonus_record_model', '', TRUE);

        $strWhere = NULL;
        $item_list = $this->Bonus_record_model->gets_join_user('bonus_record.cause,bonus_record.amount,bonus_record.create_time,user.nickname,user.path', $strWhere, $per_page, $per_page * ($page - 1));
        foreach ($item_list as $key => $value) {
            $item_list[$key]['create_time'] = date('Y-m-d H:i', strtotime($value['create_time']));
            $item_list[$key]['path'] = filter_image_path($value['path'])['path_thumb'];
            $item_list[$key]['nickname'] = mb_substr($value['nickname'], 0, 1, 'UTF-8') . '**' . mb_substr($value['nickname'], -1, 1, 'UTF-8');
            if ($value['nickname'] === NULL) {
                $item_list[$key]['nickname'] = '用户已注销';
                $item_list[$key]['path'] = base_url().'images/default/user_default.png';
            }
        }

        $cur_count = $per_page * ($page - 1) + count($item_list);
        $total_count = $this->Bonus_record_model->count($strWhere);
        $is_next_page = 0;
        if ($total_count > $cur_count) {
            $is_next_page = 1;
        }
        printAjaxData(array('item_list' => $item_list, 'is_next_page' => $is_next_page));
    }

    /**
     * 个人奖金明细(分页)
     * @param number $per_page
     * @param number $page
     */
    public function get_user_reward_record_list($per_page = 20, $page = 1) {
        $user_info = $this->_check_login();
        $this->load->model('Financial_model', '', TRUE);

        $strWhere = 'user_id = ' . $user_info['id'] . ' and record_type = 1';
        $item_list = $this->Financial_model->gets('cause,price,create_time', $strWhere, $per_page, $per_page * ($page - 1));
        foreach ($item_list as $key => $value) {
            $item_list[$key]['create_time'] = date('Y-m-d H:i', strtotime($value['create_time']));
        }

        $cur_count = $per_page * ($page - 1) + count($item_list);
        $total_count = $this->Financial_model->count($strWhere);
        $is_next_page = 0;
        if ($total_count > $cur_count) {
            $is_next_page = 1;
        }
        printAjaxData(array('item_list' => $item_list, 'total' => $user_info['reward'], 'is_next_page' => $is_next_page));
    }

    /**
     * 个人邀请记录(分页)
     * @param number $per_page
     * @param number $page
     */
    public function invite_users_list($per_page = 20, $page = 1) {
        $user_info = $this->_check_login();

        $strWhere = 'parent_id = ' . $user_info['id'] . ' and display = 1';
        $item_list = $this->Users_model->gets('nickname,path,add_time', $strWhere, $per_page, $per_page * ($page - 1));
        foreach ($item_list as $key => $value) {
            $item_list[$key]['create_time'] = date('Y-m-d H:i', $value['add_time']);
            $item_list[$key]['path'] = filter_image_path($value['path'])['path_thumb'];
            $item_list[$key]['nickname'] = mb_substr($value['nickname'], 0, 1, 'UTF-8') . '**' . mb_substr($value['nickname'], -1, 1, 'UTF-8');
        }

        $cur_count = $per_page * ($page - 1) + count($item_list);
        $total_count = $this->Users_model->count($strWhere);
        $is_next_page = 0;
        if ($total_count > $cur_count) {
            $is_next_page = 1;
        }
        printAjaxData(array('item_list' => $item_list, 'is_next_page' => $is_next_page));
    }


        //获取转发的小程序码
        public function get_wx_code($item_id = 0, $item_type = 0, $page = null)
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

            $scene = $item_id.'_'.$user_id;
            $this->load->library('wxapiclass');
            $result = $this->wxapiclass->get_wx_qr_code($page, $scene);
    
            file_put_contents($file_name, $result);
    
            $tmp_image_arr = filter_image_path($file_name);
            $path = $tmp_image_arr['path'];
            printAjaxData(['path'=>$path]);
    
        }


}

/* End of file Napi.php */
/* Location: ./application/client/controllers/api/Napi.php */