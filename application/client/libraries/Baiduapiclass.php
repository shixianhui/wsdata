<?php
require_once "sdk/aip-php-sdk-4.15.1/AipContentCensor.php";
class Baiduapiclass
{
    private $appId = '16140955';
    private $apiKey = 'oX7f4eDsMzgDXSMmZuRxMRLO';
    private $secretKey = 'RKH4006xs6IZNlrRpWOFXua21T534cgV';
    //百度AI access_token
    function get_baidu_access_token()
    {
        $access_token = '';
        if (isset($_SESSION['baidu_token'])) {
            if ($_SESSION['baidu_token']['expires'] > time()) {
                $access_token = $_SESSION['baidu_token']['data'];
            } else {
                unset($_SESSION['baidu_token']);
            }
        }
        if (!$access_token) {
            $url = 'https://aip.baidubce.com/oauth/2.0/token';
            $post_data['grant_type'] = 'client_credentials';
            $post_data['client_id'] = 'oX7f4eDsMzgDXSMmZuRxMRLO';
            $post_data['client_secret'] = 'RKH4006xs6IZNlrRpWOFXua21T534cgV';
            $o = "";
            foreach ($post_data as $k => $v) {
                $o .= "$k=" . urlencode($v) . "&";
            }
            $post_data = substr($o, 0, -1);

            $res = http_curl($url, $post_data);
            $res = json_decode($res);
            if (!isset($res->error)) {
                $access_token = $res->access_token;
                $expires_in = $res->expires_in;
                $session_data = array(
                    'data'    => $access_token,
                    'expires' => time() + $expires_in - 60,
                );
                $_SESSION['baidu_token'] = $session_data;
//            $this->session->set_tempdata('baidu_token', $access_token, 10);
            }
        }


        return $access_token;
    }

    function anti_spam($content = '')
    {
        $spam = 0;
        $access_token = $this->get_baidu_access_token();
        if ($access_token && $content) {
            $url = 'https://aip.baidubce.com/rest/2.0/solution/v1/text_censor/v2/user_defined?access_token=' . $access_token;
            $res = http_curl($url, http_build_query(array('text' => $content)));
            $obj_arr = json_decode($res, TRUE);
            var_dump($obj_arr);die;
            if (!isset($obj_arr['error_code'])) {
                $spam = $obj_arr['result']['spam'];
                $reject = $obj_arr['result']['reject'];
                if ($reject) {
                    foreach ($reject as $value) {
                        $hits = $value['hit'];
                        if ($hits) {
                            $content = str_replace($hits, '**', $content);
                        }
                    }
                }
            }
        }
        return array('spam' => $spam, 'content' => $content);
    }
    
    /**
     * 文本内容审核
     */
    function text_review($content = null)
    {
        if (!$content) {
            return false;
        }
        if(class_exists('AipContentCensor')){
            $client = new AipContentCensor($this->appId, $this->apiKey, $this->secretKey);
        }
        $result = $client->textCensorUserDefined($content);
        if (isset($obj_arr['error_code']) || $result['conclusionType'] != 1) {
            return false;
        }
        return true;
    }

    /**
     * 图片审核
     */
    function image_review($image = null)
    {
        if (!$image) {
            return false;
        }
        if(class_exists('AipContentCensor')){
            $client = new AipContentCensor($this->appId, $this->apiKey, $this->secretKey);
        }
        $result = $client->imageCensorUserDefined(file_get_contents($image));
        if (isset($obj_arr['error_code']) || $result['conclusionType'] != 1) {
            return false;
        }
        return true;
    }
}
