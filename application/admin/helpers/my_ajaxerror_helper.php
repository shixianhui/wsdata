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
	function printAjaxSuccess($url, $message = null) {
		$messageArr = array(
		              'success' => true,
		              'url'=>$url,
                      'message' => $message
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
/* End of file html_helper.php */
/* Location: ./application/admin/helpers/My_ajaxerror.php */