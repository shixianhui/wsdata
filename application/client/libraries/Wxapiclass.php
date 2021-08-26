<?php
/**
 * Created by PhpStorm.
 * User: sxh
 * Date: 2019/10/31
 * Time: 15:06
 */
class Wxapiclass
{
    //微信小程序
    private $_appid = 'wxf3079356aedb39a8';
    private $_appSecret = '093decdce3971440e0bd71ba04330306';
    private $_custom_service_errmsg_arr = array(
        '0'=>'成功',
        '65400'=>'API不可用，即没有开通/升级到新客服功能',
        '65401'=>'无效客服帐号',
        '65403'=>'客服昵称不合法',
        '65404'=>'客服帐号不合法',
        '65405'=>'帐号数目已达到上限，不能继续添加',
        '65406'=>'已经存在的客服帐号',
        '65407'=>'邀请对象已经是该公众号客服',
        '65408'=>'本公众号已经有一个邀请给该微信',
        '65409'=>'无效的微信号',
        '65410'=>'邀请对象绑定公众号客服数达到上限',
        '65411'=>'该帐号已经有一个等待确认的邀请，不能重复邀请',
        '65412'=>'该帐号已经绑定微信号，不能进行邀请',
    );

    public function __construct()
    {

    }

    public function get_wx_openid($code = '')
    {
        if (!$code) {
            printAjaxError('fail', 'DO NOT ACCESS!');
        }
        $appid = $this->_appid;
        $appSecret = $this->_appSecret;
        $json = file_get_contents("https://api.weixin.qq.com/sns/jscode2session?appid={$appid}&secret={$appSecret}&js_code={$code}&grant_type=authorization_code");
        $obj = json_decode($json);
        if (isset($obj->errmsg)) {
            printAjaxError('fail', 'invalid code!');
        }
        $session_key = $obj->session_key;
        $openid = $obj->openid;

        return array('openid'=>$openid,'session_key'=>$session_key);
    }

    //获取所有客服账号
    public function get_kf_list()
    {
        $access_token = $this->get_wx_access_token();
        if (!$access_token){
            printAjaxError('fail', 'invalid appid!');
        }
        $url = "https://api.weixin.qq.com/cgi-bin/customservice/getkflist?access_token={$access_token}";

        $result = json_decode(http_curl($url),true);

        if (isset($result['errcode'])){
            return array();
        }else{
            return $result;
        }

    }

    /**
     * 添加客服.
     * @param $kf_account string 完整客服帐号，格式为：帐号前缀@公众号微信号
     * @param $nickname string 客服昵称
     *
     * @return errode int 成功0，失败返回对应的错误码
     * @return errmsg int 成功ok，失败返回对应的错误信息
     */
    public function add_account($kf_account = '',$nickname = '')
    {

        $access_token = $this->get_wx_access_token();
        if (!$access_token){
            printAjaxError('fail', 'invalid appid!');
        }

        $url = "https://api.weixin.qq.com/customservice/kfaccount/add?access_token={$access_token}";

        $data = json_encode(array('kf_account'=>$kf_account,'nickname'=>$nickname));
        $result = json_decode(http_curl($url,$data),true);
        $result['errmsg'] = $this->_custom_service_errmsg_arr[$result['errcode']];
        return $result; //errcode == 0 成功
    }

    /**
     * 邀请客服.（小程序添加客服直接邀请即可）
     * @param $kf_account string 完整客服帐号，格式为：帐号前缀@公众号微信号
     * @param $invite_wx string 客服微信号
     *
     * @return errode int 成功0，失败返回对应的错误码
     * @return errmsg int 成功ok，失败返回对应的错误信息
     */
    public function invite_worker($kf_account = '',$invite_wx = '')
    {

        $access_token = $this->get_wx_access_token();
        if (!$access_token){
            printAjaxError('fail', 'invalid appid!');
        }

        $url = "https://api.weixin.qq.com/customservice/kfaccount/inviteworker?access_token={$access_token}";

        $data = json_encode(array('kf_account'=>$kf_account,'invite_wx'=>$invite_wx));
        $result = json_decode(http_curl($url,$data),true);

        $result['errmsg'] = $this->_custom_service_errmsg_arr[$result['errcode']];
        return $result;
    }

    /**
     * 删除客服.
     * @param $kf_account string 完整客服帐号，格式为：帐号前缀@公众号微信号
     *
     * @return errode int 成功0，失败返回对应的错误码
     * @return errmsg int 成功ok，失败返回对应的错误信息
     */
    public function del_account($kf_account = '')
    {

        $access_token = $this->get_wx_access_token();
        if (!$access_token){
            printAjaxError('fail', 'invalid appid!');
        }

        $url = "https://api.weixin.qq.com/customservice/kfaccount/del?access_token={$access_token}";

        $data = json_encode(array('kf_account'=>$kf_account));
        $result = json_decode(http_curl($url,$data),true);
        $result['errmsg'] = $this->_custom_service_errmsg_arr[$result['errcode']];
        return $result;
    }

    /**
     * 文件存储access_token
     */
    private function get_wx_access_token()
    {
        $this->CI->load->driver('cache');
        $cache = $this->CI->cache->file->get_metadata('wx_token');
        if ($cache && $cache['expire'] > time()) {
            $access_token = $this->CI->cache->file->get('wx_token');
        } else {
            $access_token = '';
            $appid = $this->_appid;
            $appSecret = $this->_appSecret;
            $json = http_curl("https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$appid}&secret={$appSecret}");
            $obj = json_decode($json);
            if (!isset($obj->errmsg)) {
                $access_token = $obj->access_token;
                $expires_in = $obj->expires_in;
                $this->CI->cache->file->save('wx_token', $access_token, $expires_in-60);
            }
        }

        return $access_token;
    }


    //获取小程序码
    public function get_wx_qr_code($page,$scene)
    {
        $access_token = $this->get_wx_access_token();
        if (!$access_token){
            printAjaxError('fail', 'invalid appid!');
        }
        $url = "https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token={$access_token}";
        $encode_id = urlencode($scene);
        $data = array(
            'page'=>"{$page}",
            'scene'=>"{$encode_id}",
            'width'=>280,
            'auto_color'=>false,
        );
        $data=json_encode($data);
        $result = http_curl($url,$data);
        if (json_decode($result)){
            printAjaxError('fail', 'invalid result!');
        }
        return $result;
    }


    //小程序内容安全审核
    public function msg_sec_check($data = '')
    {
        $access_token = $this->get_wx_access_token();
        if (!$access_token){
            printAjaxError('fail', 'invalid appid!');
        }
        $url = "https://api.weixin.qq.com/wxa/msg_sec_check?access_token={$access_token}";

        $result = json_decode(http_curl($url,json_encode(array('content' => $data),JSON_UNESCAPED_UNICODE)));

        return $result->errcode;
    }


    //发送订阅消息
    public function msg_sub_send($openid = '')
    {
        $access_token = $this->get_wx_access_token();
        if (!$access_token){
            printAjaxError('fail', 'invalid appid!');
        }

        $template_id = 'eHYeLR-oK0tLuAESKidVwUrIgJIuchlEEgAHYf2ef9g';
        $url = "https://api.weixin.qq.com/cgi-bin/message/subscribe/send?access_token={$access_token}";
        $data = [
            'touser'=>$openid,
            'template_id'=>$template_id,
            'page'=>'index',
            'data'=>[
                'character_string9'=>['value'=>1111111],
                'thing4'=>['value'=>'测试'],
                'thing5'=>['value'=>'蚁立'],
                'amount2'=>['value'=>1.00],
                'thing11'=>['value'=>'测试数据'],
            ]
        ];
        $data = json_encode($data);
        $result = json_decode(http_curl($url,$data),true);
        var_dump($result);
    }

}