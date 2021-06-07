<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Detect a user is logged, if not logged in, jump to the login page
 * 
 * @author zeping xiang 2009-10-16
 *
 */
class Validateloginclass {
    
	public function __construct() {
	    $CI =& get_instance();
		$CI->load->helper('cookie');
		$validateUserName = get_cookie('admin_username');
	    $CI->load->helper('url');	    
	    $first = $CI->uri->segment(1);
		$second = $CI->uri->segment(2);
		
		if (! empty($first)) {
			if($second != 'login' && $first != 'message' && $first != 'upload' && $first != 'verifycode') {
			   if (empty($validateUserName)) {
		           redirect('/admin/login');
			   }
			}
		}
	}
}
// END Validateloginclass class

/* End of file Validateloginclass.php */
/* Location: ./system/libraries/Validateloginclass.php */