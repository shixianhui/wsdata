<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * print ajax error
 *
 *
 * @access	public
 * @param	string
 * @return	string
 */
if ( ! function_exists('getBaseUrl')) {
	function getBaseUrl($isHtml = false, $htmlUrl = '', $unHtmlUrl = '', $client_index = '') {
		$url = '';
		if ($isHtml) {
		    $url = $htmlUrl;
		} else {
		    $url = $client_index;
    	    $url .= $client_index?'/':'';
    	    $url .= $unHtmlUrl;
		}
		
		return $url;
	}
}

// ------------------------------------------------------------------------
/**
 * 获取字的宽度
 *
 *
 */
if ( ! function_exists('getStrWidth')) {
	function getStrWidth($str) {
		$len = mb_strlen($str);
		$letterCount = 0.0;
		for ($i = 0; $i < $len; $i++) {
		    if (strlen(mb_substr($str, $i, 1)) == 3) {
		        $letterCount += 1.0;
		    } else {
		        $letterCount += 0.5;
		    }
		}
		
		return $letterCount;
	}
}

// ------------------------------------------------------------------------

/**
 * Intercept length of the string
 *
 *
 * @access	public
 * @param	string
 * @param	int
 * @param	string
 * @return	string
 */
if ( ! function_exists('my_substr')) {
function my_substr($string, $length, $dot = '...', $charset = 'utf-8') {
		if(strlen($string) <= $length) return $string;	
		$string = str_replace(array('&amp;', '&quot;', '&lt;', '&gt;'), array('&', '"', '<', '>'), $string);	
		$strcut = '';		
		if(strtolower($charset) == 'utf-8') {
			$n = $tn = $noc = 0;			
			while($n < strlen($string)) {
				$t = ord($string[$n]);	
				// 特别要注意这部分，utf-8是1--6位不定长表示的，这里就是如何
				// 判断utf-8是1位2位还是3位还是4、5、6位,这对其他语言的编程也有用处
				// 具体可以查看rfc3629或rfc2279
				if($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
					$tn = 1; $n++; $noc++;
				} else if(194 <= $t && $t <= 223) {
					$tn = 2; $n += 2; $noc += 2;
				} elseif(224 <= $t && $t < 239) {
					$tn = 3; $n += 3; $noc += 2;
				} elseif(240 <= $t && $t <= 247) {
					$tn = 4; $n += 4; $noc += 2;
				} elseif(248 <= $t && $t <= 251) {
					$tn = 5; $n += 5; $noc += 2;
				} elseif($t == 252 || $t == 253) {
					$tn = 6; $n += 6; $noc += 2;
				} else {
					$n++;
				}
	
				if($noc >= $length) {
					break;
				}
			}
			
			if($noc > $length) $n -= $tn;
			
			$strcut = substr($string, 0, $n);
		} else {
			for($i = 0; $i < $length; $i++) {
				$strcut .= ord($string[$i]) > 127 ? $string[$i] . $string[++$i] : $string[$i];
			}
		}
		//$strcut = str_replace(array('&', '"', '<', '>'), array('&amp;', '&quot;', '&lt;', '&gt;'), $strcut);
		return strlen($string) > strlen($strcut)? $strcut . $dot:$strcut;
    }
}

// ------------------------------------------------------------------------

/**
 * Display html
 *
 */
if ( ! function_exists('html')) {
    function html($str) {
    	return html_entity_decode($str, ENT_NOQUOTES, 'UTF-8');
    }
}

// ------------------------------------------------------------------------

/**
 * Html filter
 *
 */
if ( ! function_exists('unhtml')) {
    function unhtml($str) {
    	return htmlentities($str, ENT_NOQUOTES, 'UTF-8');
    }
}


if ( ! function_exists('text')) {
    function text($str) {
        return str_replace("<br/>","\n",html_entity_decode($str, ENT_QUOTES, 'UTF-8'));
    }
}

//过滤html标签 换行转义
if ( ! function_exists('untext')) {
    function untext($str) {
        $str = str_replace("\n","<br/>",strip_tags($str));

        $res = htmlentities($str, ENT_QUOTES, 'UTF-8');
        return $res;
    }
}
// ------------------------------------------------------------------------

/**
 * clear string
 *
 */
if ( ! function_exists('clearstring')) {
    function clearstring($str) {
    	return str_replace(array('&', '"', '<', '>'), array('&amp;', '&quot;', '&lt;', '&gt;'), $str);
    }
}


// ------------------------------------------------------------------------

/**
 * 生成密码
 *
 *
 * @access	public
 * @param int 
 * @return	string
 */
if ( ! function_exists('getRandNum')) {
	function getRandNum($len) {
	    $str = '0123456789';
		$maxLen = strlen($str)-1;
		$randStr = '';
		for ($i = 0; $i < $len; $i++) {
		    $randStr .= substr($str, rand(0, $maxLen), 1);
		}
		
		return $randStr;
	}
}

// ------------------------------------------------------------------------

/**
 * 生成密码
 *
 *
 * @access	public
 * @param int 
 * @return	string
 */
if ( ! function_exists('getRandPass')) {
	function getRandPass($len) {
	    $str = 'abcdefghijklmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ123456789';
		$maxLen = strlen($str)-1;
		$randStr = '';
		for ($i = 0; $i < $len; $i++) {
		    $randStr .= substr($str, rand(0, $maxLen), 1);
		}
		
		return $randStr;
	}
}

// --------------------------------获取时间格式----------------------------------------

/**
 * clear string
 *
 */
if ( ! function_exists('getFormatTime')) {
    function getFormatTime($minute = 0) {
    	$retStr = '';
    	if ($minute > 0) {
    	    $day = floor($minute/(60*24));
    	    $hour = floor(($minute - $day*(60*24))/60);
    	    $min = floor(($minute - $day*(60*24) - $hour*60));
    	    
    	    if ($day) {
    	        $retStr .= $day.'天';
    	    }
    	    if ($hour) {
    	        $retStr .= $hour.'小时';
    	    }
    	    if ($min) {
    	        $retStr .= $min.'分钟';
    	    }
    	}
    	
    	return $retStr;
    }
}


// --------------------------------中间星号----------------------------------------

/**
 * clear string
 *
 */
if ( ! function_exists('createMiddleBit')) {
    function createMiddleBit($wangwang = NULL) {
    	$retStr = "";
    	$len = mb_strlen($wangwang);
    	if ($len > 6) {
    	    $retStr = mb_substr($wangwang, 0, 1).'***'.mb_substr($wangwang, $len - 3, 3);
    	} else {
    	    if ($len > 4) {
    	        $retStr = mb_substr($wangwang, 0, 1).'***'.mb_substr($wangwang, $len - 2, 2);
    	    } else {
    	        $retStr = mb_substr($wangwang, 0, 1).'***'.mb_substr($wangwang, $len - 1, 1);
    	    }
    	}
    	
    	return $retStr;
    }
}

// ------------------------------------------------------------------------

/**
 * 获取IP地址
 *
 *
 * @access	public
 * @param int 
 * @return	string
 */
if ( ! function_exists('getUserIPAddress')) {
	function getUserIPAddress($time = 5) {
		$cip = '';
	    if(!empty($_SERVER["HTTP_CLIENT_IP"])) {
		    $cip = $_SERVER["HTTP_CLIENT_IP"];
	    } else if (!empty($_SERVER["HTTP_X_FORWARDED_FOR"])){
		    $cip = $_SERVER["HTTP_X_FORWARDED_FOR"];
		} else if (!empty($_SERVER["REMOTE_ADDR"])){
		    $cip = $_SERVER["REMOTE_ADDR"];
		}
		if (!$cip) {
		    return array('', '');
		}
	    //初始化
		$ch = curl_init();
		//设置选项，包括URL
		curl_setopt($ch, CURLOPT_URL, "http://www.ip138.com/ips138.asp?ip={$cip}");
		curl_setopt($ch, CURLOPT_REFERER, "http://www.yhd.com");
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; rv:37.0) Gecko/20100101 Firefox/37.0");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, $time);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
		//执行并获取HTML文档内容
		$output = curl_exec($ch);
		//释放curl句柄
		curl_close($ch);
		$output = mb_convert_encoding($output, 'utf-8', 'gbk');
		header("Content-type: text/html; charset=utf-8");
		if (!$output) {
		    return array('', '');
		}
		preg_match("/(本站主数据：)+.*(参考数据一：)+/", $output, $matches);
	    if (!$matches || !$matches[0]) {
			return array('', '');
		}
		$address = preg_replace(array('/本站主数据：/', '/参考数据一：/', '/(<\/li>|<li>)/'), array('', '', ''), $matches[0]);
		
		return array($cip, $address);
	}
}

// ------------------------------------------------------------------------

/**
 * 获取中文字符的长度
 *
 *
 * @access	public
 * @param int 
 * @return	string
 */
if ( ! function_exists('getChineseLen')) {
	function getChineseLen($str = '') {
		$mb_len = mb_strlen($str);
	    $mb_count = 0;
	    for ($i = 0; $i < $mb_len; $i++) {
	    	$tmp_str = mb_substr($str, $i, 1);
	    	if (preg_match("/[\x7f-\xff]/", $tmp_str)) {
	    		$mb_count++;
	    	}
	    }
	    
	    return floor(($mb_count/$mb_len)*100);
	}
}

// --------------------------------替换内容图片的路径－相对路径换成绝对路径----------------------------------------

/**
 * clear string
 *
 */
if ( ! function_exists('filter_content')) {
    function filter_content($content = '', $url = '') {
        preg_match_all("/<img(.*)src=\"([^\"]+)\"[^>]+>/isU", $content, $matches);
        if($matches){
            $img_url_arr = $matches[2];
            $img_url_arr = array_unique($img_url_arr);
            if ($img_url_arr) {
                foreach($img_url_arr as $val){
                    if (!preg_match('/^http/', $val)) {
                        $content = str_replace($val, $url.$val, $content);
                    }
                }
            }
        }

        return $content;
    }
}

	/**
	 * 过滤图片路径
	 *
	 * @param string $image_path
	 * @return array
	 */
	function filter_image_path($image_path = NULL) {
		$path = '';
		$path_thumb = '';
		$path_max = '';
		if ($image_path) {
			if (!preg_match('/^http/', $image_path)) {
				$path = base_url() . $image_path;
				$path_thumb = base_url() . preg_replace('/\./', '_thumb.', $image_path);
				$path_max = base_url() . preg_replace('/\./', '_max.', $image_path);
			} else {
				$path = $image_path;
				$path_thumb = $image_path;
				$path_max = $image_path;
			}
		}
		return array('path' => $path, 'path_thumb' => $path_thumb, 'path_max' => $path_max);
	}

/**
 * get order number
 *
 *
 * @access	public
 * @param int
 * @return	string
 */
if ( ! function_exists('getOrderNumber')) {
    function getOrderNumber($len) {
        $str = '0123456789';
        $maxLen = strlen($str)-1;
        $randStr = '';
        for ($i = 0; $i < $len; $i++) {
            $randStr .= substr($str, rand(0, $maxLen), 1);
        }

        return date('ymdHis', time()).$randStr;
    }
}

/**
 * 加盐算法
 *
 *
 */
if (!function_exists('create_password_salt')) {
    function create_password_salt($user = NULL, $salt = NULL, $password = NULL) {
        return md5(strtolower($user) . $salt . $password);
    }
}

/**
 * 生成随机码(纯数字);
 *
 *
 * @access	public
 * @param int
 * @return	string
 */
if ( ! function_exists('getRandCode')) {
    function getRandCode($len) {
        $str = '0123456789';
        $maxLen = strlen($str)-1;
        $randStr = '';
        for ($i = 0; $i < $len; $i++) {
            $randStr .= substr($str, rand(0, $maxLen), 1);
        }

        return $randStr;
    }
}

	function logs($data = null){
		$file  = 'logs/log.txt';
		$content = date('Y-m-d H:i:s')."\r\n".var_export($data,true)."\r\n";
		file_put_contents($file,$content,FILE_APPEND);

		return true;
	}


	function checkIdentity($num, $checkSex = '')
	{
		// 不是15位或不是18位都是无效身份证号
		if (strlen($num) != 15 && strlen($num) != 18) {
			return false;
		}
		// 是数值
		if (is_numeric($num)) {
			// 如果是15位身份证号
			if (strlen($num) == 15) {
				// 省市县（6位）
				$areaNum = substr($num, 0, 6);
				// 出生年月（6位）
				$dateNum = substr($num, 6, 6);
				// 性别（3位）
				$sexNum = substr($num, 12, 3);
			} else {
				// 如果是18位身份证号
				// 省市县（6位）
				$areaNum = substr($num, 0, 6);
				// 出生年月（8位）
				$dateNum = substr($num, 6, 8);
				// 性别（3位）
				$sexNum = substr($num, 14, 3);
				// 校验码（1位）
				$endNum = substr($num, 17, 1);
			}
		} else {
			// 不是数值
			if (strlen($num) == 15) {
				return false;
			} else {
				// 验证前17位为数值，且18位为字符x
				$check17 = substr($num, 0, 17);
				if (!is_numeric($check17)) {
					return false;
				}
				// 省市县（6位）
				$areaNum = substr($num, 0, 6);
				// 出生年月（8位）
				$dateNum = substr($num, 6, 8);
				// 性别（3位）
				$sexNum = substr($num, 14, 3);
				// 校验码（1位）
				$endNum = substr($num, 17, 1);
				if ($endNum != 'x' && $endNum != 'X') {
					return false;
				}
			}
		}

		if (isset($areaNum)) {
			if (!checkArea($areaNum)) {
				return false;
			}
		}

		if (isset($dateNum)) {
			if (!checkDates($dateNum)) {
				return false;
			}
		}

		// 性别1为男，2为女
		if ($checkSex == 1) {
			if (isset($sexNum)) {
				if (!checkSex($sexNum)) {
					return false;
				}
			}
		} else if ($checkSex == 2) {
			if (isset($sexNum)) {
				if (checkSex($sexNum)) {
					return false;
				}
			}
		}

		if (isset($endNum)) {
			if (!checkEnd($endNum, $num)) {
				return false;
			}
		}
		return true;
	}

	// 验证城市
	function checkArea($area)
	{
		$num1 = substr($area, 0, 2);
		$num2 = substr($area, 2, 2);
		$num3 = substr($area, 4, 2);
		// 根据GB/T2260—999，省市代码11到65
		if (10 < $num1 && $num1 < 66) {
			return true;
		} else {
			return false;
		}
		//============
		// 对市 区进行验证
		//============
	}

	// 验证出生日期
	function checkDates($date)
	{
		if (strlen($date) == 6) {
			$date1 = substr($date, 0, 2);
			$date2 = substr($date, 2, 2);
			$date3 = substr($date, 4, 2);
			$statusY = checkY('19' . $date1);
		} else {
			$date1 = substr($date, 0, 4);
			$date2 = substr($date, 4, 2);
			$date3 = substr($date, 6, 2);
			$nowY = date("Y", time());
			if (1900 < $date1 && $date1 <= $nowY) {
				$statusY = checkY($date1);
			} else {
				return false;
			}
		}
		if (0 < $date2 && $date2 < 13) {
			if ($date2 == 2) {
				// 润年
				if ($statusY) {
					if (0 < $date3 && $date3 <= 29) {
						return true;
					} else {
						return false;
					}
				} else {
					// 平年
					if (0 < $date3 && $date3 <= 28) {
						return true;
					} else {
						return false;
					}
				}
			} else {
				$maxDateNum = getDateNum($date2);
				if (0 < $date3 && $date3 <= $maxDateNum) {
					return true;
				} else {
					return false;
				}
			}
		} else {
			return false;
		}
	}

	// 验证性别
	function checkSex($sex)
	{
		if ($sex % 2 == 0) {
			return false;
		} else {
			return true;
		}
	}

	// 验证18位身份证最后一位
	function checkEnd($end, $num)
	{
		$checkHou = array(1, 0, 'X', 9, 8, 7, 6, 5, 4, 3, 2);
		$checkGu = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
		$sum = 0;
		for ($i = 0; $i < 17; $i++) {
			$sum += (int)$checkGu[$i] * (int)$num[$i];
		}
		$checkHouParameter = $sum % 11;
		if ($checkHou[$checkHouParameter] != strtoupper($num[17])) {
			return false;
		} else {
			return true;
		}
	}

	// 验证平年润年，参数年份,返回 true为润年  false为平年
	function checkY($Y)
	{
		if (getType($Y) == 'string') {
			$Y = (int)$Y;
		}
		if ($Y % 100 == 0) {
			if ($Y % 400 == 0) {
				return true;
			} else {
				return false;
			}
		} else if ($Y % 4 == 0) {
			return true;
		} else {
			return false;
		}
	}

	// 当月天数 参数月份（不包括2月）  返回天数
	function getDateNum($month)
	{
		if ($month == 1 || $month == 3 || $month == 5 || $month == 7 || $month == 8 || $month == 10 || $month == 12) {
			return 31;
		} else {
			return 30;
		}
	}


	if (!function_exists('checkLogin')) {
		function checkLogin($is_check_store = false)
		{
			$CI = &get_instance();
			$CI->load->library('session');
			if (!$CI->session->userdata('user_id')) {
				$data = array(
					'user_msg' => '你还未登录,请登录',
					'user_url' => base_url() . "index.php/user/login.html"
				);
	
				$CI->session->set_userdata($data);
				redirect(base_url() . 'index.php/message/index');
				exit;
			}
			$CI->load->model('User_model', '', TRUE);
			$userInfo = $CI->User_model->get('display', array('id' => $CI->session->userdata('user_id')));
			if (!$userInfo) {
				$data = array(
					'user_msg' => '您的账号不存在或被管理员删除',
					'user_url' => base_url() . "index.php/user/logout"
				);
				$CI->session->set_userdata($data);
				redirect(base_url() . 'index.php/message/index');
				exit;
			}
			if ($userInfo['display'] == 0) {
				$data = array(
					'user_msg' => '您的账号已禁用，请联系网站客服',
					'user_url' => base_url() . "index.php"
				);
	
				$CI->session->set_userdata($data);
				redirect(base_url() . 'index.php/message/index');
				exit;
			}
			if ($is_check_store) {
				$CI->load->model('Stores_model', '', TRUE);
				$CI->load->model('Seller_group_model', '', TRUE);
				if (!get_cookie('seller_group_id')) {
					$data = array(
						'user_msg' => '您还未申请店铺,请申请店铺',
						'user_url' => base_url() . "index.php/seller/my_join.html"
					);
	
					$CI->session->set_userdata($data);
					redirect(base_url() . 'index.php/message/index');
					exit;
				}
				$seller_group_info = $CI->Seller_group_model->get('user_id', array('id' => get_cookie('seller_group_id')));
				if (!$seller_group_info) {
					$data = array(
						'user_msg' => '您还未申请店铺,请申请店铺',
						'user_url' => base_url() . "index.php/seller/my_join.html"
					);
	
					$CI->session->set_userdata($data);
					redirect(base_url() . 'index.php/message/index');
					exit;
				}
				$storeInfo = $CI->Stores_model->get('id,status', array('user_id' => $seller_group_info['user_id']));
				if (!$storeInfo) {
					$data = array(
						'user_msg' => '您还未申请店铺,请申请店铺',
						'user_url' => base_url() . "index.php/seller/my_join.html"
					);
	
					$CI->session->set_userdata($data);
					redirect(base_url() . 'index.php/message/index');
					exit;
				}
				if ($storeInfo['status'] == 0) {
					$data = array(
						'user_msg' => '店铺审核中，请耐心等待',
						'user_url' => base_url()
					);
	
					$CI->session->set_userdata($data);
					redirect(base_url() . 'index.php/message/index');
					exit;
				} else if ($storeInfo['status'] == 2) {
					$data = array(
						'user_msg' => '店铺审核暂未通过',
						'user_url' => base_url() . "index.php/seller/my_join.html"
					);
	
					$CI->session->set_userdata($data);
					redirect(base_url() . 'index.php/message/index');
					exit;
				} else if ($storeInfo['status'] == 3) {
					$data = array(
						'user_msg' => '店铺已关闭',
						'user_url' => base_url() . "index.php/seller.html"
					);
	
					$CI->session->set_userdata($data);
					redirect(base_url() . 'index.php/message/index');
					exit;
				}
			}
		}
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * clear string
	 *
	 */
	if (!function_exists('checkLoginAjax')) {
		function checkLoginAjax($is_check_store = false)
		{
			$CI = &get_instance();
			$CI->load->library('session');
			if (!$CI->session->userdata('user_id')) {
				$messageArr = array(
					'success' => false,
					'field'   => 'fail',
					'message' => '你还未登录,请登录'
				);
				echo json_encode($messageArr);
				exit;
			}
			$CI->load->model('User_model', '', TRUE);
			$userInfo = $CI->User_model->get('display', array('id' => $CI->session->userdata('user_id')));
			if (!$userInfo) {
				$messageArr = array(
					'success' => false,
					'field'   => 'fail',
					'message' => '您的账号不存在或被管理员删除'
				);
				echo json_encode($messageArr);
				exit;
			}
			if ($userInfo['display'] == 0) {
				$messageArr = array(
					'success' => false,
					'field'   => 'fail',
					'message' => '您的账号已禁用，请联系网站客服'
				);
				echo json_encode($messageArr);
				exit;
			}
			if ($is_check_store) {
				$CI->load->model('Stores_model', '', TRUE);
				$CI->load->model('Seller_group_model', '', TRUE);
				if (!get_cookie('seller_group_id')) {
					$messageArr = array(
						'success' => false,
						'field'   => 'fail',
						'message' => '您还未申请店铺'
					);
					echo json_encode($messageArr);
					exit;
				}
				$seller_group_info = $CI->Seller_group_model->get('user_id', array('id' => get_cookie('seller_group_id')));
				if (!$seller_group_info) {
					$messageArr = array(
						'success' => false,
						'field'   => 'fail',
						'message' => '您还未申请店铺'
					);
					echo json_encode($messageArr);
					exit;
				}
				$storeInfo = $CI->Stores_model->get('status', array('user_id' => $seller_group_info['user_id']));
				if (!$storeInfo) {
					$messageArr = array(
						'success' => false,
						'field'   => 'fail',
						'message' => '您还未申请店铺'
					);
					echo json_encode($messageArr);
					exit;
				}
				if ($storeInfo['status'] == 0) {
					$messageArr = array(
						'success' => false,
						'field'   => 'fail',
						'message' => '店铺审核中，请耐心等待'
					);
					echo json_encode($messageArr);
					exit;
				} else if ($storeInfo['status'] == 2) {
					$messageArr = array(
						'success' => false,
						'field'   => 'fail',
						'message' => '店铺审核暂未通过'
					);
					echo json_encode($messageArr);
					exit;
				} else if ($storeInfo['status'] == 3) {
					$messageArr = array(
						'success' => false,
						'field'   => 'fail',
						'message' => '店铺已关闭，原因：' . $storeInfo['close_reason']
					);
					echo json_encode($messageArr);
					exit;
				}
			}
		}
	}


	if (!function_exists('checkPermission')) {
		function checkPermission($permissionsItem = NULL)
		{
			$CI = &get_instance();
			if (!$CI->advdbclass->getPermissions(get_cookie('seller_group_id'), $permissionsItem)) {
				$data = array(
					'user_msg' => '没有权限',
					'user_url' => "goback"
				);
				$CI->session->set_userdata($data);
				redirect(base_url() . 'index.php/message/index');
				exit;
			}
		}
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Determine access rights
	 *
	 *
	 * @access    public
	 * @param    string
	 * @return    json
	 */
	if (!function_exists('checkPermissionAjax')) {
		function checkPermissionAjax($permissionsItem = NULL)
		{
			$CI = &get_instance();
			if (!$CI->advdbclass->getPermissions(get_cookie('seller_group_id'), $permissionsItem)) {
				printAjaxError('fail', '没有此权限!');
			}
		}
	}

	if (!function_exists('alert_message')) {
		function alert_message($msg = '', $url = 'index.php')
		{
			$data = array(
				'user_msg' => $msg,
				'user_url' => $url
			);
			$CI = &get_instance();
			$CI->session->set_userdata($data);
			redirect(base_url() . 'index.php/message/index');
			exit;
		}
	}
    
/* End of file my_functionlib_helper.php */
/* Location: ./application/admin/helpers/my_functionlib_helper.php */