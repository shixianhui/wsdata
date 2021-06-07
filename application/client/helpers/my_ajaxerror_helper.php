<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * print ajax error
 *
 *
 * @access	public
 * @param	string
 * @return	json
 */
if ( ! function_exists('printAjaxError')) {
	function printAjaxError($field =  '', $message = '') {
		$messageArr = array(
		              'success'=> false,
		              'field'=>   $field,
                      'message'=> $message
                      );
        echo json_encode($messageArr);
        exit;
	}
}

// ------------------------------------------------------------------------

/**
 * print ajax success
 *
 *
 * @access	public
 * @param	string
 * @return	json
 */
if ( ! function_exists('printAjaxSuccess')) {
	function printAjaxSuccess($field =  '', $message = '') {
		$messageArr = array(
		              'success' => true,
		              'field'=>   $field,
                      'message'   => $message
                      );
        echo json_encode($messageArr);
        exit;
	}
}

// ------------------------------------------------------------------------

/**
 * print ajax success
 *
 *
 * @access	public
 * @param	array
 * @return	json
 */
if ( ! function_exists('printAjaxData')) {
	function printAjaxData($data) {
		$messageArr = array(
		              'success' => true,
                      'data'   => $data
                      );
        echo json_encode($messageArr);
        exit;
	}
}


/**
 * 请求短信验证码
 * @param string $mobile 手机号
 * @param string $sms_txt 验证码内容
 */
function send_sms($mobile = NULL, $sms_txt = NULL) {
	$sUrl = 'http://api.qirui.com:7891/mt'; // 接入地址
	$apiKey = '1062030026';    // 请替换为你的帐号编号
	$apiSecret = 'e1512e45857becfcef46';  // 请替换为你的帐号密钥
	$nCgid = 1221;   // 请替换为你的通道组编号
	$sMobile = $mobile;    // 请替换为你的手机号码
	$sContent = $sms_txt;   // 请把数字替换为其他4~10位的数字测试，如需测试其他内容，请先联系客服报备发送模板
	$nCsid = 0;    // 签名编号 ,可以为空时，使用系统默认的编号
	$data = array('un' => $apiKey, 'pw' => $apiSecret, 'da' => $sMobile, 'sm' => $sContent,'dc' => 15,'tf' => 3,'rf' => 1,);  //定义参数
	$data = @http_build_query($data);        //把参数转换成URL数据
	$xml = file_get_contents($sUrl . '?' . $data);  // 发送请求
	$xml_val = xmlToArray($xml);
	return $xml_val;
}

function xmlToArray($xml) {
	//禁止引用外部xml实体
	libxml_disable_entity_loader(true);
	$xmlstring = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
	$val = json_decode(json_encode($xmlstring), true);
	return $val;
}



function http_curl($url, $data = '', $is_json = 0)
{
	//2初始化
	$ch = curl_init();
	//3.设置参数
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);    //不验证证书
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);    //不验证证书
	curl_setopt($ch, CURLOPT_TIMEOUT, 60);
	if (!empty($data)) {
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		if ($is_json) {
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		        'Content-Type: application/json',
		        'Content-Length: ' . strlen($data))
			);
		}
		
	}
	//4.调用接口
	$res = curl_exec($ch);
	//5.关闭curl
	curl_close( $ch );
//        if(curl_errno($ch)){
//            return curl_error($ch);
//        }else{
//            //5.关闭curl
//        curl_close( $ch );
//        $arr = json_decode($res, true);
	return $res;
//        }
}

    //腾讯地址解析
    function get_lat_lng($address = null)
    {
        $info = array();
        if ($address) {
			$address = urlencode($address);
            $json = http_curl("https://apis.map.qq.com/ws/geocoder/v1/?address={$address}&key=5U4BZ-MYFK3-HCQ3U-YAKDS-AZMWE-2VFCF");
            $obj = json_decode($json);
            if ($obj->status == 0) {
                $lat = $obj->result->location->lat;
                $lng = $obj->result->location->lng;
                $info = array('lat' => $lat, 'lng' => $lng);
            }
        }

        return $info;
    }

	//腾讯地图逆地址解析
    function get_address($lat,$lng){
        $url = "http://apis.map.qq.com/ws/geocoder/v1/?location={$lat},{$lng}&key=5U4BZ-MYFK3-HCQ3U-YAKDS-AZMWE-2VFCF";
        $data = http_curl($url);
        $data = json_decode($data,TRUE);
        $address = empty($data['status']) ? $data['result']['address'] : '';
        $province = empty($data['status']) ? $data['result']['address_component']['province'] : '';
        $city = empty($data['status']) ? $data['result']['address_component']['city'] : '';
        $district = empty($data['status']) ? $data['result']['address_component']['district'] : '';
        $adcode = empty($data['status']) ? $data['result']['ad_info']['adcode'] : '';
        $res = array('address'=>$address,'province'=>$province,'city'=>$city,'district'=>$district,'adcode'=>$adcode);
        return $res;
    }
/* End of file html_helper.php */
/* Location: ./application/admin/helpers/My_ajaxerror.php */