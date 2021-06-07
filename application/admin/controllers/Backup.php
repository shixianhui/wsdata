<?php
class Backup extends CI_Controller {
	private $_title = '系统维护';
	private $_tool = '';
	private $_table = '';
	private $_template = 'backup';
	
	public function __construct() {
		parent::__construct();
		$this->_tool = $this->load->view('element/backup_tool', '', TRUE);
		$this->load->helper('my_fileoperate');
		$this->load->dbutil();
	}

	public function index() {
	    checkPermission("{$this->_template}_index");
	    
		$allTableList = array();
		$tableList = $this->db->list_tables();
		foreach ($tableList as $key=>$value) {
			$allTableList[$key]['table_name'] = $value;
			$allTableList[$key]['table_rows'] = $this->db->count_all_results($value);
		}
		$data = array(
		        'tool'=>$this->_tool,
				'allTableList'=>$allTableList
		        );
	    $layout = array(
			      'title'=>$this->_title,
				  'content'=>$this->load->view('backup/index', $data, TRUE)
			      );
	    $this->load->view('layout/default', $layout);
	}

	public function optimize() {
	    checkPermissionAjax("{$this->_template}_optimize");
	    
		$tables = $this->input->post('tables', TRUE);
		if (! empty($tables)) {
			$tables = explode(',', $tables);
			foreach ($tables as $key=>$value) {
				$this->dbutil->optimize_table($value);
			}
			printAjaxSuccess('', '优化成功！');
		}

		printAjaxError('fail', '优化失败！');
	}

	public function repair() {
	    checkPermissionAjax("{$this->_template}_repair");
	    
		$tables = $this->input->post('tables', TRUE);
		if (! empty($tables)) {
			$tables = explode(',', $tables);
			foreach ($tables as $key=>$value) {
				$this->dbutil->repair_table($value);
			}
			printAjaxSuccess('', '修复成功！');
		}

		printAjaxError('fail', '修复失败！');
	}

	public function backupDatabase() {
	    checkPermission("{$this->_template}_backupDatabase");
	    
		$tables = $this->input->post('ids', TRUE);
		if (! empty($tables)) {
			$prefs = array(
                'tables'      => $tables,           // 包含了需备份的表名的数组.
                'ignore'      => array(),           // 备份时需要被忽略的表
                'format'      => 'zip',             // gzip, zip, txt
                'filename'    => 'backup.sql',    // 文件名 - 如果选择了ZIP压缩,此项就是必需的
                'add_drop'    => TRUE,              // 是否要在备份文件中添加 DROP TABLE 语句
                'add_insert'  => TRUE,              // 是否要在备份文件中添加 INSERT 语句
                'newline'     => "\n"               // 备份文件中的换行符
            );
            $backup  = $this->dbutil->backup($prefs);
			// 加载下载辅助函数并将文件发送到你的桌面
			$this->load->helper('download');
			force_download('backup.zip', $backup);
		}
	}
}
/* End of file news.php */
/* Location: ./application/admin/controllers/news.php */