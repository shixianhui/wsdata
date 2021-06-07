<?php
class Area extends CI_Controller {
	private $_title = '配送地区';
	private $_tool = '';
	private $_table = '';
	public function __construct() {
		parent::__construct();
		//获取表名
		$this->_table = $this->uri->segment(1);
		//快捷方式
		$this->_tool = $this->load->view('element/save_list_tool', array('table'=>$this->_table, 'parent_title'=>'系统设置',  'title'=>$this->_title), TRUE);
		//获取表对象
		$this->load->model(ucfirst($this->_table).'_model', 'tableObject', TRUE);
	}

	public function index($parentId = 0) {
		$this->session->set_userdata(array("{$this->_table}RefUrl"=>base_url().'admincp.php/'.uri_string()));
		$itemList = $this->tableObject->gets('*', array('parent_id'=>$parentId));
		//路径
		$location = $this->tableObject->getLocation($parentId, $this->_table);

		$data = array(
		              'tool'=>$this->_tool,
		              'table'=>$this->_table,
		              'location'=>$location,
		              'itemList'=>$itemList
		              );
	    $layout = array(
			      'title'=>$this->_title,
				  'content'=>$this->load->view($this->_table.'/index', $data, TRUE)
			      );
	    $this->load->view('layout/default', $layout);
	}

    public function save($id = NULL) {
    	$prfUrl = $this->session->userdata($this->_table.'RefUrl')?$this->session->userdata($this->_table.'RefUrl'):base_url()."admincp.php/{$this->_table}/index/";
		if ($_POST) {
			$parentId = 0;
			$country = $this->input->post('country', TRUE);
			$province = $this->input->post('province', TRUE);
			$city = $this->input->post('city', TRUE);

		    if ($country == $id && $id) {
			    printAjaxError('fail', '自己不能是自己的上级，请选择一级分类！');
			}
		    if ($province == $id && $id) {
			    printAjaxError('fail', '自己不能是自己的上级，请选择一级分类！');
			}
		    if ($city == $id && $id) {
			    printAjaxError('fail', '自己不能是自己的上级，请选择二级分类！');
			}
			if ($country && $province && $city) {
			    $parentId = $city;
			} else if ($country && $province && !$city) {
			    $parentId = $province;
			} else if ($country && !$province && !$city) {
			    $parentId = $country;
			}

		    if ($id != NULL) {
				$fields = array(
				          'parent_id'=>     $parentId,
				          'name'=>          $this->input->post('name', TRUE)
				          );
			    if ($this->tableObject->save($fields, $id?array('id'=>$id):$id)) {
					printAjaxSuccess($prfUrl);
				} else {
					printAjaxError('fail', "操作失败！");
				}
			} else {
				$i = 0;
				$name = $this->input->post('name', TRUE);
				$name = preg_replace(array('/^\|+/', '/\|+$/'), array('', ''), $name);
				$nameArr = explode("|", $name);
				foreach ($nameArr as $name) {
					$fields = array(
				          'parent_id'=>     $parentId,
				          'name'=>          trim($name)
				          );
			         if ($this->tableObject->save($fields, $id?array('id'=>$id):$id)) {
			             $i++;
			         }
				}
				if (count($nameArr) == $i) {
				    printAjaxSuccess($prfUrl);
				} else {
				    printAjaxError('fail', "操作失败！");
				}
			}
		}

		$itemInfo = $this->tableObject->get('*', array('id'=>$id));
		if ($itemInfo) {
		    $itemInfo['sub_parent_id'] = 0;
		    $itemInfo['sub_sub_parent_id'] = 0;
			if ($itemInfo['parent_id']) {
			    $sub_itemInfo = $this->tableObject->get('parent_id', array('id'=>$itemInfo['parent_id']));
			    if ($sub_itemInfo['parent_id']) {
			    	$sub_sub_itemInfo = $this->tableObject->get('parent_id', array('id'=>$sub_itemInfo['parent_id']));
			    	if ($sub_sub_itemInfo['parent_id']) {
			    		$itemInfo['sub_sub_parent_id'] = $itemInfo['parent_id'];
			    	    $itemInfo['sub_parent_id'] = $sub_itemInfo['parent_id'];
				        $itemInfo['parent_id'] = $sub_sub_itemInfo['parent_id'];
			    	} else {
				    	$itemInfo['sub_parent_id'] = $itemInfo['parent_id'];
				        $itemInfo['parent_id'] =  $sub_itemInfo['parent_id'];
			    	}
			    } else {
			        $itemInfo['parent_id'] = $itemInfo['parent_id'];
			    }
			}
		}
		$treeList = $this->tableObject->gets('*', array('parent_id'=>0));

		$data = array(
		        'tool'=>$this->_tool,
		        'table'=>$this->_table,
		        'itemInfo'=>$itemInfo,
		        'treeList'=>$treeList,
		        'prfUrl'=>$prfUrl
		        );
		$layout = array(
		          'title'=>$this->_title,
				  'content'=>$this->load->view($this->_table.'/save', $data, TRUE)
		          );
		$this->load->view('layout/default', $layout);
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

		printAjaxError('fail', '排序失败！');
	}

    public function delete() {
	    $ids = $this->input->post('ids', TRUE);
	    if (! empty($ids)) {
	    	if ($this->tableObject->getChildTreeCount($ids)) {
	    	    printAjaxError('fail', '存在子级数据，删除失败！');
	    	}
	        if ($this->tableObject->delete('id in ('.$ids.')')) {
	            printAjaxData(array('ids'=>explode(',', $ids)));
	        }
	    }

	    printAjaxError('fail', '删除失败！');
	}

	public function getCity() {
	    $parentId = $this->input->post('parent_id', TRUE);
	    if ($parentId) {
	        $treeList = $this->tableObject->gets('*', array('parent_id'=>$parentId));
	        printAjaxData($treeList);
	    }
	}

    public function display() {
	    $ids = $this->input->post('ids');
		$display = $this->input->post('display');

		if (! empty($ids) && $display != "") {
			if($this->tableObject->save(array('display'=>$display), 'id in ('.$ids.')')) {
			    printAjaxSuccess('', '修改状态成功！');
			}
		}

		printAjaxError('fail', '修改状态失败！');
	}
}
/* End of file link.php */
/* Location: ./application/admin/controllers/link.php */