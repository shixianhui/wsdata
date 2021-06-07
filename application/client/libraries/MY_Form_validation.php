<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Form_validation extends CI_Form_validation {

    // public function __construct($rules = array())
    // {
    //     parent::__construct($rules);
    // }

    /**
	 * Valid Mobile
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	function valid_mobile($str)
	{
		return ( ! preg_match("/^((\(\d{2,3}\))|(\d{3}\-))?(13|14|15|16|17|18|19)\d{9}$/", $str)) ? FALSE : TRUE;
	}
	
    /**
	 * Valid Phone
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	function valid_phone($str)
	{
		return ( ! preg_match("/^((\(\d{2,3}\))|(\d{3}\-))?(\(0\d{2,3}\)|0\d{2,3}-)?[1-9]\d{6,7}(\-\d{1,4})?$/", $str)) ? FALSE : TRUE;
	}
	
    /**
	 * Valid Bank Card
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	function valid_bank_card($str)
	{
		return ( ! preg_match("/^([1-9]{1})(\d{14}|\d{15}|\d{16}|\d{18})$/", $str)) ? FALSE : TRUE;
    }
    
     /**
	 * Valid Password
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	function valid_password($str)
	{
		return ( ! preg_match("/^(?=.*([a-zA-Z].*))(?=.*[0-9].*)[a-zA-Z0-9-*\/\+.,~!@#$%^&*()]{6,20}$/", $str)) ? FALSE : TRUE;
    }

    /**
	 * Valid URL
	 *
	 * @param	string	$str
	 * @return	bool
	 */
	public function valid_url($str)
	{
		return ( ! preg_match("/^http[s]?:\/\/[A-Za-z0-9]+\.[A-Za-z0-9]+[\/=\?%\-&_~`@[\]\\':+!]*([^<>\"\"])*$/", $str)) ? FALSE : TRUE;
	}
}