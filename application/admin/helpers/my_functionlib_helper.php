<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
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
		
		return $strcut . $dot;
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

// ------------------------------------------------------------------------

/**
 * set editer mode
 *
 */
if ( ! function_exists('getEditerMode')) {
    function getEditerMode() {
    	//Default,full,standard,introduce,Basic
    	return 'Default';
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
		return $strcut . $dot;
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


if ( ! function_exists('clearSession')) {
    function clearSession($except_arr = array()) {
        $CI = & get_instance();
        $session_all = $CI->session->all_userdata();
        if ($session_all) {
            foreach ($session_all as $key=>$value) {
                if ($key != 'session_id' && $key != 'ip_address' && $key != 'user_agent' && $key != 'last_activity' && !in_array($key, $except_arr)) {
                    $CI->session->unset_userdata($key);
                }
            }
        }
    }
}



/**
 * Determine access rights
 *
 *
 * @access	public
 * @param	string
 * @return	json
 */
if ( ! function_exists('checkPermission')) {
    function checkPermission($permissionsItem = NULL) {
        $CI = & get_instance();
        if (!$CI->advdbclass->getPermissions(get_cookie('admin_group_id'), $permissionsItem)) {
            $data = array(
                'msg'=>'没有此权限!',
                'url'=>'goback'
            );
            $CI->session->set_userdata($data);
            redirect('/message/index');
        }
    }
}

// ------------------------------------------------------------------------

/**
 * Determine access rights
 *
 *
 * @access	public
 * @param	string
 * @return	json
 */
if ( ! function_exists('checkPermissionAjax')) {
    function checkPermissionAjax($permissionsItem = NULL) {
        $CI = & get_instance();
        if (!$CI->advdbclass->getPermissions(get_cookie('admin_group_id'), $permissionsItem)) {
            printAjaxError('fail', '没有此权限!');
        }
    }
}


// ------------------------------------------------------------------------

/**
 * Determine access rights
 *
 *
 * @access	public
 * @param	string
 * @return	json
 */
if ( ! function_exists('isPermissions')) {
    function isPermissions($admingroupInfo = NULL, $permissionsItem = NULL) {
        $ret = false;
        if ($admingroupInfo) {
            $permissionArr = explode(',', $admingroupInfo['permissions']);
            if (in_array($permissionsItem, $permissionArr)) {
                $ret = true;
            }
        }

        return $ret;
    }
}

// --------------------------------中间星号----------------------------------------

/**
 * clear string
 *
 */
if ( ! function_exists('createMobileBit')) {
	function createMobileBit($mobile = NULL) {
		return  str_replace(mb_substr($mobile, 3, 4), '****', $mobile);
	}
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

        return date('ymdhi', time()).$randStr;
    }
}

/**转换空格等特殊字符为英文逗号
 * match_string
 */
if ( ! function_exists('match_string')) {
    function match_string($str) {
        $result = preg_replace("/(\n)|(\s+)|(\t)|(\')|(')|(，)|(\.)/",',',$str);
        return rtrim($result,',');
    }
}


/* End of file my_functionlib_helper.php */
/* Location: ./application/admin/helpers/my_functionlib_helper.php */