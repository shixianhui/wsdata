<?php
class Ws_data extends CI_Controller {
	private $_title = '标题';
	private $_tool = '';
	private $_table = '';
	private $_controller = '';
	public function __construct() {
		parent::__construct();
		//获取表名
		$this->_table = $this->uri->segment(1);
		$this->_controller = $this->uri->segment(1);
		//快捷方式
		$this->_tool = $this->load->view("element/save_list_tool", array('table'=>$this->_controller, 'parent_title'=>'父级标题', 'title'=>$this->_title), TRUE);
		//获取表对象
		$this->load->model(ucfirst($this->_table).'_model', 'tableObject', TRUE);
		$this->load->model('Ws_data_model', '', true);
		$this->load->model('Category_model', '', true);
		$this->load->model('Attachment_model', '', true);
		$this->load->model('Area_model', '', true);

    }

	public function index($clear = 1,$page = 0) {
        checkPermission("{$this->_controller}_index");
        if ($clear) {
            $clear = 0;
            $this->session->unset_userdata('search');
            $this->session->unset_userdata('category');
            $this->session->unset_userdata('keyword');
        }
        $this->session->set_userdata(array("{$this->_controller}RefUrl"=>base_url().'admincp.php/'.uri_string()));
        $condition = "{$this->_table}.id > 0";
        $strWhere = $this->session->userdata('search')?$this->session->userdata('search'):$condition;

		//初始化导入数据
		// $data = json_decode(http_curl('https://www.alipei.vip/pricequery/search?category=&keyword='), true);
		// foreach ($data as $value) {
		// 	$category = $this->Category_model->get('id,name', ['name'=>$value['category']]);
		// 	if (!$category) {
		// 		$category_id = $this->Category_model->save(['name'=>$value['category']]);
		// 	} else {
		// 		$category_id = $category['id'];
		// 	}
		// 	$category_1 = $this->Category_model->get('id,name', ['name'=>$value['keyword']]);
		// 	if (!$category_1) {
		// 		$category_1_id = $this->Category_model->save(['name'=>$value['keyword'], 'parent_id'=>$category_id]);
		// 	} else {
		// 		$category_1_id = $category_1['id'];
		// 	}
		// 	$price_arr = explode('/', $value['price']);
		// 	$price = $price_arr[0] ?? ''; 
		// 	$unit = $price_arr[1] ?? ''; 
		// 	$url_arr = explode('/', $value['url']);
		// 	$img_id = '';
		// 	if ($url_arr && $value['url']) {
		// 		$img_name = $url_arr[count($url_arr) - 1];
		// 		$path = 'https://www.alipei.vip/'.$value['url'];
		// 		$img_id = $this->Attachment_model->save(['name'=>$img_name, 'path'=>$path, 'thumb'=>$path, 'type'=>'net']);
		// 	}
		// 	$model_data[] = [
		// 		'category' => $category_id,
		// 		'category_1' => $category_1_id,
		// 		'price' => $price,
		// 		'unit' => $unit,
		// 		'remark' => $value['description'],
		// 		'img' => $img_id
		// 	];
		// }
		// $this->Ws_data_model->save_batch($model_data);
		
	    if ($_POST) {
			$strWhere = $condition;
			$keyword = $this->input->post('keyword');
			$category = $this->input->post('parent_id');
			$display = $this->input->post('display');
		    $startTime = $this->input->post('inputdate_start');
		    $endTime = $this->input->post('inputdate_end');
		    if (! empty($keyword) ) {
		        $strWhere .= " and ({$this->_table}.brand REGEXP '{$keyword}' or a.name REGEXP '{$keyword}' or b.name REGEXP '{$keyword}' or c.name REGEXP '{$keyword}')";
				$this->session->set_userdata('keyword', $keyword);
		    }
		    if ($display != "") {
		        $strWhere .= " and {$this->_table}.display = {$display} ";
		    }
			if ($category) {
		        $strWhere .= " and ({$this->_table}.category = {$category} or {$this->_table}.category_1 = {$category} or {$this->_table}.category_2 = {$category}) ";
				$this->session->set_userdata('category', $category);
			}
		    // if (! empty($startTime) && ! empty($endTime)) {
		    // 	$strWhere .= " and {$this->_table}.add_time > ".strtotime($startTime.' 00:00:00')." and {$this->_table}.add_time < ".strtotime($endTime." 23:59:59")." ";
		    // }
			if (! empty($startTime) && ! empty($endTime)) {
		    	$strWhere .= " and {$this->_table}.create_time > '".$startTime." 00:00:00' and {$this->_table}.create_time < '".$endTime." 23:59:59'";
		    }
            $this->session->set_userdata('search', $strWhere);
		}


        //分页
        $this->config->load('pagination_config', TRUE);
        $paginationCount = $this->tableObject->count_join_category($strWhere);
        $paginationConfig = $this->config->item('pagination_config');
        $paginationConfig['base_url'] = base_url() . "admincp.php/{$this->_controller}/index/{$clear}/";
        $paginationConfig['total_rows'] = $paginationCount;
        $paginationConfig['uri_segment'] = 4;
        $this->pagination->initialize($paginationConfig);
        $pagination = $this->pagination->create_links();

		$item_list = $this->tableObject->gets_join_category('ws_data.*, a.name as category, b.name as category_1, c.name as category_2', $strWhere, $paginationConfig['per_page'], $page);
		foreach ($item_list as $key=>$value) {
			// $path = '';
			// $thumb = '';
			$photos_list = [];
			if ($value['img']) {
				$photos_list = $this->Attachment_model->gets('id,path,thumb', "id in ({$value['img']})");

				// $img_info = $this->Attachment_model->get('id,path,thumb', ['id'=>$value['img']]);
				// $path = $img_info ? $img_info['path'] : '';
				// $thumb = $img_info ? $img_info['thumb'] : '';
			}
			// $item_list[$key]['path'] = $path;
			// $item_list[$key]['thumb'] = $thumb;
			$item_list[$key]['photos_list'] = $photos_list;
		}

		$province_list = $this->Area_model->gets('id,name', ['parent_id'=>0]);
		$datas = $this->Category_model->gets('*', NULL, NULL, NULL, 'id', 'ASC');
		$menus_list = $this->Category_model->generateTree($datas);

		//筛选条件
		$category_name = '';
		$category = $this->session->userdata('category') ?: 0;
		if ($category) {
			$category_info = $this->Category_model->get('*', ['id'=>$category]);
			$category_name = $category_info ? $category_info['name'] : '';
		}
		$keyword = $this->session->userdata('keyword') ?: '';

		$filter = compact('category', 'category_name', 'keyword');

		$data = array(
		        'tool'=>$this->_tool,
		        'table'=>$this->_table,
		        'template'=>$this->_table,
		        'item_list'=>json_encode($item_list),
		        'menus_list'=>json_encode($menus_list),
				'table_limit' => $paginationConfig['per_page'],
                'pagination'=>$pagination,
                'paginationCount'=>$paginationCount,
                'pageCount'=>ceil($paginationCount/$paginationConfig['per_page']),
				'filter' => json_encode($filter)
		        );
	    $layout = array(
			      'title'=>$this->_title,
				  'content'=>$this->load->view("{$this->_controller}/index", $data, TRUE)
			      );
	    $this->load->view('layout/default', $layout);
	}


	public function save($id = 0) {
        if ($id) {
            checkPermission("{$this->_controller}_edit");
        } else {
            checkPermission("{$this->_controller}_create");
        }
		$prfUrl = $this->session->userdata("{$this->_controller}RefUrl")?:base_url()."admincp.php/{$this->_controller}/index";
        $item_info = $id ? $this->tableObject->get('*', array("id"=>$id)) : [];
        if ($_POST) {
            $fields = array(
                'no' => $this->input->post('no', TRUE),
                'category' => $this->input->post('category', TRUE),
                'category_1' => $this->input->post('category_1', TRUE),
                'category_2' => $this->input->post('category_2', TRUE),
                'province' => $this->input->post('province', TRUE),
                'city' => $this->input->post('city', TRUE),
                'brand' => $this->input->post('brand', TRUE),
                'project' => $this->input->post('project', TRUE),
                'model' => $this->input->post('model', TRUE),
                'unit' => $this->input->post('unit', TRUE),
                'num' => $this->input->post('num', TRUE),
                'price' => $this->input->post('price', TRUE),
                'remark' => $this->input->post('remark', TRUE),
                'source' => $this->input->post('source', TRUE),
                'img' => $this->input->post('path', TRUE),
            );
            $ret = $this->tableObject->save($fields, $id ? array('id' => $id) : NULL);
            if ($ret) {
                printAjaxSuccess('close_layer','保存成功');
            } else {
                printAjaxError('fail',"操作失败！");
            }
        }

		$category_list = $this->Category_model->gets('id,name', ['parent_id'=>0]);
		$province_list = $this->Area_model->gets('id,name', ['parent_id'=>0]);
		$category_1_list = [];
		$category_2_list = [];
		$city_list = [];
		$photos_list = [];
		if ($item_info) {
			$item_info['thumb'] = '';
			$item_info['path'] = '';
			if ($item_info['category']) {
				$category_1_list = $this->Category_model->gets('id,name', ['parent_id'=>$item_info['category']]);
			}
			if ($item_info['category_1']) {
				$category_2_list = $this->Category_model->gets('id,name', ['parent_id'=>$item_info['category_1']]);
			}
			if ($item_info['img']) {
				// $img_info = $this->Attachment_model->get('id,path,thumb', ['id'=>$item_info['img']]);
				// $item_info['thumb'] = $img_info ? $img_info['thumb'] : '';
				// $item_info['path'] = $img_info ? $img_info['path'] : '';
				$photos_list = $this->Attachment_model->gets('id,path,thumb', "id in ({$item_info['img']})");
			}
			if ($item_info['province']) {
				$area_info = $this->Area_model->get('id,name', "name regexp '{$item_info['province']}'");
				if ($area_info) {
					$city_list = $this->Area_model->gets('id,name', ['parent_id'=>$area_info['id']]);
				}
			}
			$item_info['photos_list'] = $photos_list;
		}

	    $data = array(
		        'tool'=>$this->_tool,
	            'item_info'=>$item_info,
	            'item_info_json'=>json_encode($item_info),
	    		'table'=>$this->_table,
	            'prfUrl'=>$prfUrl,
		        'category_list'=>$category_list,
		        'province_list'=>$province_list,
		        'category_1_list'=>$category_1_list,
		        'category_2_list'=>$category_2_list,
		        'city_list'=>$city_list,
		        );
		$layout = array(
		          'title'=>$this->_title,
				  'content'=>$this->load->view("{$this->_controller}/save", $data, TRUE)
		          );
		$this->load->view('layout/default', $layout);
	}

    public function delete() {
        checkPermissionAjax("{$this->_controller}_delete");

        $ids = $this->input->post('ids', TRUE);

	    if (! empty($ids)) {
	        if ($this->tableObject->delete("id in ($ids)")) {
	            printAjaxData(array('ids'=>explode(',', $ids)));
	        }
	    }

	    printAjaxError('删除失败！');
	}

    public function sort() {
        checkPermissionAjax("{$this->_controller}_edit");

        $ids = $this->input->post('ids', TRUE);
		$sorts = $this->input->post('sorts', TRUE);

		if (! empty($ids) && ! empty($sorts)) {
			$ids = explode(',', $ids);
			$sorts = explode(',', $sorts);

			foreach ($ids as $key=>$value) {
				$this->tableObject->save(array('sort'=>$sorts[$key]),array('id'=>$value));
			}
			printAjaxSuccess('', '排序成功！');
		}
		printAjaxError('排序失败！');
	}

	public function display() {
        checkPermissionAjax("{$this->_controller}_edit");

        $ids = $this->input->post('ids');
		$display = $this->input->post('display');

		if (! empty($ids) && $display != "") {
			if($this->tableObject->save(array('display'=>$display), 'id in ('.$ids.')')) {
				printAjaxSuccess('', '修改状态成功！');
			}
		}

		printAjaxError('fail', '修改状态失败！');
	}

	public function attribute() {
        checkPermissionAjax("{$this->_controller}_edit");
        if ($_POST) {
        	$ids = $this->input->post('ids', TRUE);
        	$customAttribute = $this->input->post('custom_attribute', TRUE);

        	if (! empty($ids) && ! empty($customAttribute)) {
        		if ($customAttribute == 'clear'){
        			$customAttribute = '';
        		}
        		if($this->tableObject->save(array('custom_attribute'=>$customAttribute), 'id in ('.$ids.')')) {
        			printAjaxSuccess('success', '属性修改成功！');
        		}
        	}

        	printAjaxError('fail', '属性修改失败！');
        }
	}

	public function getCity($name)
	{
		$name = urldecode($name);
		$item_list = [];
		$item_info = $this->Area_model->get('id,name', "name regexp '{$name}'");
		if ($item_info) {
			$item_list = $this->Area_model->gets('id,name', ['parent_id'=>$item_info['id']]);
		}

		printAjaxData(compact('item_list'));
	}

	public function getCategory($id)
	{
		$item_list = $this->Category_model->gets('id,name', ['parent_id'=>$id]);

		printAjaxData(compact('item_list'));
	}


}
/* End of file init_controller.php */
/* Location: ./application/admin/controllers/init_controller.php */