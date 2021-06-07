<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * get unique file name
 *
 * @access	public
 * @param	string example './uploads/'
 * @return	string
 */
if ( ! function_exists('getUniqueFileName')) {
    function getUniqueFileName($savePath) {
		$randFileName = date('YmdHis', time()) . rand(100000, 999999);
		if (file_exists ( $savePath . '/' . $randFileName )) {
		    while ( true ) {
		    	$randFileName = date('YmdHis', time()) . rand(100000, 999999);
				if (! file_exists ( $savePath . '/' . $randFileName )) {
					break;
				}
			}
		}
		
		return $randFileName;
	}
}

// ------------------------------------------------------------------------

/**
 * get file size
 *
 * @access	public
 * @param	int example:1073741824
 * @return	string
 */
if ( ! function_exists('getFileSize')) {
    function getFileSize($fileSize) {
		$ret = 0;
		if ($fileSize >= 1073741824) {//G
		    $ret = round($fileSize/1073741824) . ' G';
		} else if ($fileSize >= 1048576) {//mb
		    $ret = round($fileSize/1048576) . ' MB';
		} else if ($fileSize >= 1024) {//kb
		    $ret = round($fileSize/1024) . ' KB';
		} else {//byte
		    $ret = $fileSize.' byte';
		}
	    return $ret;
	}
}

// ------------------------------------------------------------------------

/**
 * get file size
 *
 * @access	public
 * @param	string example:g.zip
 * @return	string
 */
if ( ! function_exists('getFileExt')) {
    function getFileExt($fileName) {
	    $fileInfo = pathinfo($fileName);
		return $fileInfo['extension'];
	}
}

// ------------------------------------------------------------------------

/**
 * create dir
 *
 * @access	public
 * @param	string example:'./uploads/'
 * @return	string
 */
if ( ! function_exists('createDir')) {
    function createDir($filePath) {
	    if (! file_exists ( $filePath )) {
			if (! mkdir($filePath, 0777)) {
			    return false;
			} else {
			    chmod ($filePath, 0777);
			}
		}
		
		return $filePath;
	}
}

// ------------------------------------------------------------------------

/**
 * create dirs
 *
 * @access	public
 * @param	string example:'./uploads/'
 * @return	string
 */
if ( ! function_exists('mkdirs')) {
	function mkdirs($dir) {
		if(!is_dir($dir)){
			if(!mkdirs(dirname($dir))) {
				return false;
			}
			if(!mkdir($dir, 0777)) {
				return false;
			} else {
			    chmod ($dir, 0777);
			}
		}
		
		return true;
	}
}

// ------------------------------------------------------------------------

/**
 * create dir of date time
 *
 * @access	public
 * @param	string example:'./uploads/'
 * @return	string
 */
if ( ! function_exists('createDateTimeDir')) {
    function createDateTimeDir($savePath) {
		$filePath = $savePath. '/' . date ('Y', time());
		if (createDir($filePath)) {
		    $filePath = $filePath . '/' . date ('md', time());
		    if (createDir($filePath))
		    	return $filePath;
		}
		
	    return false;
	}
}

// ------------------------------------------------------------------------

/**
 * create dir of date time
 *
 * @access	public
 * @param	string example:'./uploads/'
 * @return	string
 */
if ( ! function_exists('createDirs')) {
    function createDirs($path, $mode = 0777) {
		$dirs = explode('/',$path);
		$pos = strrpos($path, ".");
		if ($pos === false) {
		    $subamount=0;
		} else {
		    $subamount=1;
		}
		
		for ($c=0; $c < count($dirs) - $subamount; $c++) {
			$thispath="";
			for ($cc=0; $cc <= $c; $cc++) {
			    $thispath.=$dirs[$cc].'/';
			}
			if (!file_exists($thispath)) {
			    mkdir($thispath, $mode);
			    chmod ($thispath, $mode);
			}
		}
	}
}

	function get_qr_code($content, $path, $size)
    {
        require_once 'sdk/phpqrcode/phpqrcode.php';
        $errorCorrectionLevel = 'H';//容错级别
        //生成二维码图片
        QRcode::png($content, $path, $errorCorrectionLevel, $size, 2);

    }
/* End of file html_helper.php */
/* Location: ./application/admin/helpers/My_firstletter.php */