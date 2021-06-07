<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Operating session
 * 
 * @author zeping xiang 2010-11-25
 *
 */
class Mysession {

    /**
     * Transfer operation session session_id
     * 
     * @return boolean
     */
     public function parseSessionId($sessionId) {
         $this->CI = & get_instance();
    		    
	    if (config_item('sess_driver') == 'database')
		{
			$this->CI->db->where('id', $sessionId);

			$query = $this->CI->db->get(config_item('sess_save_path'));

			// No result?  Kill it!
			if ($query->num_rows() == 0)
			{
                $this->CI->session->sess_destroy();
				return FALSE;
			}

			// Is there custom data?  If so, add it to the main session array
			$row = $query->row();
//			if (isset($row->user_data) AND $row->user_data != '')
//			{
//				$custom_data = $this->CI->session->_unserialize($row->user_data);
//
//				if (is_array($custom_data))
//				{
//					foreach ($custom_data as $key => $val)
//					{
//						$session[$key] = $val;
//					}
//				}
				$session['session_id'] = $row->id;
				$session['ip_address'] = $row->ip_address;
//				$session['user_agent'] = $row->user_agent;
				$session['last_activity'] = $row->timestamp;
				$session['user_id'] = $this->CI->session->userdata('user_id');
				var_dump($session['user_id']);die;
//			}
		}
		
		// Session is valid!
         $this->CI->session->set_userdata($session);
         session_write_close();
				
//		unset($session);

		return TRUE;
	}
	
    /**
	 * Update an existing session
	 *
	 * @access	public
	 * @return	void
	 */
//	public function sess_update()
//	{
//		// We only update the session every five minutes by default
//		if (($this->userdata['last_activity'] + $this->sess_time_to_update) >= $this->now)
//		{
//			return;
//		}
//
//		// Save the old session id so we know which record to
//		// update in the database if we need it
//		$old_sessid = session_id();
//		$new_sessid = $old_sessid;
//
//		// Update the session data in the session data array
//		$this->userdata['session_id'] = $new_sessid;
//		$this->userdata['last_activity'] = time();
//
//		// _set_cookie() will handle this for us if we aren't using database sessions
//		// by pushing all userdata to the cookie.
//		$cookie_data = NULL;
//
//		// Update the session ID and last_activity field in the DB if needed
//		if ($this->sess_use_database === TRUE)
//		{
//			// set cookie explicitly to only have our session data
//			$cookie_data = array();
//			foreach (array('session_id','ip_address','user_agent','last_activity') as $val)
//			{
//				$cookie_data[$val] = $this->userdata[$val];
//			}
//
//			$this->CI->db->query($this->CI->db->update_string($this->sess_table_name, array('last_activity' => $this->now, 'session_id' => $new_sessid), array('session_id' => $old_sessid)));
//		}
//
//		// Write the cookie
//		$this->_set_cookie($cookie_data);
//	}
}
// END Validateloginclass class

/* End of file MY_Session.php */
/* Location: ./system/libraries/MY_Session.php */