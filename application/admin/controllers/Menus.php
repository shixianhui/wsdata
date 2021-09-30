<?php
class Menus extends CI_Controller {
	private $_title = '菜单管理';
	private $_tool = '';
	private $_table = '';
	public function __construct() {
		parent::__construct();
		//获取表名
		$this->_table = $this->uri->segment(1);
		//获取表对象
		$this->load->model(ucfirst($this->_table).'_model', 'tableObject', TRUE);
		$this->load->model('System_model', '', TRUE);
	}

	public function index() {
		$item_list = $this->tableObject->get_sub_tree();

		// $json_string = file_get_contents('api/menus.json');
		// $item_list = json_encode(json_decode($json_string, TRUE)['data']);
		$item_list = json_encode($item_list);

		$data = array(
		        'table'=>$this->_table,
		        'item_list'=>$item_list,
		        );
	    $layout = array(
			      'title'=>$this->_title,
				  'content'=>$this->load->view("{$this->_table}/index", $data, TRUE)
			      );
	    $this->load->view('layout/default', $layout);
	}


	public function save($id = NULL) {
		$prfUrl = $this->session->userdata("{$this->_table}RefUrl")?$this->session->userdata("{$this->_table}RefUrl"):base_url()."admincp.php/{$this->_table}/index";
		if ($_POST) {
			$parent_id = $this->input->post('parent_id', TRUE);
			$title = $this->input->post('title', TRUE);
			$component = $this->input->post('component', TRUE);
			$permissions = $this->input->post('permissions', TRUE);
			$status = $this->input->post('status', TRUE);
			$icon = $this->input->post('icon', TRUE);
			$type = $this->input->post('type', TRUE);
			
			$fields = array(
				'parent_id'=>  $parent_id,
				'title'=>         $title,
				'permissions'=>         $permissions,
				'component'=>         $component,
				'status'=>       $status ? 1 : 0,
				'sort'=>         $this->input->post('sort', TRUE),
				'type'=>         $type,
				'href'=>         $this->input->post('href', TRUE),
				'icon'=>         $icon,
			);
		
			$ret = $this->tableObject->save($fields, $id ? array('id'=>$id) : NULL);
			if ($ret) {
				if (!$id) {
					$systemInfo = $this->System_model->get('*', array('id' => 1));
					if (!$this->tableObject->count(['component'=>$component, 'id <>'=>$ret]) && $systemInfo['html_level'] == 1) {
						//控制器
						$c_src_file = './'.APPPATH."controllers/init_controller.php";
						$c_tag_file = './'.APPPATH."controllers/{$component}.php";
						if (copy($c_src_file, $c_tag_file)) {
							$old_con = file_get_contents($c_tag_file);
							$new_con = str_replace([ucfirst('init_controller'), 'init_controller'], [ucfirst($component), $component], $old_con);
							file_put_contents($c_tag_file, $new_con);
						} else {
							printAjaxError('fail', '新增控制器文件失败');
						}
						//模型
						$m_src_file = './'.APPPATH."models/init_model.php";
						$m_tag_file = './'.APPPATH."models/{$component}_model.php";
						if (copy($m_src_file, $m_tag_file)) {
							$old_con = file_get_contents($m_tag_file);
							$new_con = str_replace(array(ucfirst('init'), 'init'), array(ucfirst($component), $component), $old_con);
							file_put_contents($m_tag_file, $new_con);
						} else {
							@unlink($c_tag_file);
							printAjaxError('fail', '新增模型文件失败');
						}
						//视图
						$v_src_file = './'.APPPATH."views/init";
						$v_tag_file = './'.APPPATH."views/{$component}";
						$this->_recurse_copy($v_src_file, $v_tag_file);
					}
					if ($type == 1 && $permissions == 'index') {
						$params = [
							['type'=>2, 'parent_id'=>$ret, 'title'=>'新增', 'component'=>$component, 'permissions'=>'create'],
							['type'=>2, 'parent_id'=>$ret, 'title'=>'编辑', 'component'=>$component, 'permissions'=>'edit'],
							['type'=>2, 'parent_id'=>$ret, 'title'=>'删除', 'component'=>$component, 'permissions'=>'delete']
						];
						$this->tableObject->save_batch($params);
					}
				}

				printAjaxSuccess('close_layer', $id ? '修改成功' : '添加成功');
			} else {
				printAjaxError("操作失败！");
			}
		}

		$item_info = $this->tableObject->get('*', array("{$this->_table}.id"=>$id));
		if ($item_info) {
			$parent_menu = $this->tableObject->get('title', ['id'=>$item_info['parent_id']]);
			$item_info['parent_menu'] = $parent_menu ? $parent_menu['title'] : '';
		}

        $datas = $this->tableObject->gets('*', NULL, NULL, NULL, 'id', 'ASC', 'ASC');
		$menus_list = $this->tableObject->generateTree($datas);


	    $data = array(
		        'tool'=>$this->_tool,
	            'item_info'=>$item_info,
	            'item_info_json'=>json_encode($item_info),
	            'menus_list'=>json_encode($menus_list),
	    		'table'=>$this->_table,
	            'prfUrl'=>$prfUrl
		        );
		$layout = array(
		          'title'=>$this->_title,
				  'content'=>$this->load->view("{$this->_table}/save", $data, TRUE)
		          );
		$this->load->view('layout/default', $layout);
	}

	// 原目录，复制到的目录
	private function _recurse_copy($src, $dst) {
		$dir = opendir ( $src );
		@mkdir ( $dst );
		while ( false !== ($file = readdir ( $dir )) ) {
			if (($file != '.') && ($file != '..')) {
				if (is_dir ( $src . '/' . $file )) {
					$this->_recurse_copy ( $src . '/' . $file, $dst . '/' . $file );
				} else {
					copy ( $src . '/' . $file, $dst . '/' . $file );
				}
			}
		}
		closedir ( $dir );
	}

    public function delete() {
	    $ids = $this->input->post('ids', TRUE);

	    if (! empty($ids)) {
	        if ($this->tableObject->delete("id in ($ids)")) {
	            printAjaxData(array('ids'=>explode(',', $ids)));
	        }
	    }

	    printAjaxError('删除失败！');
	}

    public function sort() {
		$ids = $this->input->post('ids', TRUE);
		$sorts = $this->input->post('sorts', TRUE);

		if (! empty($ids) && ! empty($sorts)) {
			$ids = explode(',', $ids);
			$sorts = explode(',', $sorts);

			foreach ($ids as $key=>$value) {
				$this->tableObject->save(
				                   array('sort'=>$sorts[$key]),
				                   array('id'=>$value));
			}
			printAjaxSuccess('', '排序成功！');
		}
		printAjaxError('排序失败！');
	}

	public function display() {
		$ids = $this->input->post('ids');
		$display = $this->input->post('display');

		if (! empty($ids) && $display != "") {
			if($this->tableObject->save(array('status'=>$display), 'id in ('.$ids.')')) {
				printAjaxSuccess('', '修改状态成功！');
			}
		}

		printAjaxError('fail', '修改状态失败！');
	}
}
/* End of file admingroup.php */
/* Location: ./application/admin/controllers/admingroup.php */