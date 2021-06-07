<?php
class Menu extends CI_Controller {
	private $_title = '添加栏目';
	private $_tool = '';
	private $_table = '';
	private $_template = '';
	private $_position = array('head', 'navigation', 'footer');

	public function __construct() {
		parent::__construct();
		//获取表名
		$this->_table = $this->uri->segment(1);
		//模型名
		$this->_template = $this->uri->segment(1);
		//获取表对象
		$this->load->model(ucfirst($this->_template).'_model', 'tableObject', TRUE);
		$this->load->model('System_model', '', TRUE);
		$this->load->library('ChineseToPinyinclass');
		$this->load->helper('url');
	}

    public function index()	{
        $systemInfo = $this->System_model->get('*', array('id'=>1));
		$data = [
			'username'  =>get_cookie('admin_username'),
			'group_name'=>get_cookie('admin_group_name'),
			'index_site_name'=>$systemInfo['index_site_name']
		];
		$this->load->view($this->_template.'/index', $data);
	}

    public function main() {
		$this->load->view($this->_template.'/main');
	}

    public function drag() {
		$this->load->view($this->_template.'/drag');
	}

    public function top() {
        $systemInfo = $this->System_model->get('*', array('id'=>1));

        $data = array(
    	        'username'  =>get_cookie('admin_username'),
    	        'group_name'=>get_cookie('admin_group_name'),
            'index_site_name'=>$systemInfo['index_site_name']
    	        );
		$this->load->view($this->_template.'/top', $data);
	}

    public function menus() {

        $menu_list = $this->Permission_menus_model->menu_tree(array('display'=>1));

		$this->load->view($this->_template.'/menu', array('menu_list'=>$menu_list));
	}

	public function menuList() {
	    checkPermission("{$this->_template}_menuList");
		 if (! $this->uri->segment(2)) {
		    $this->session->unset_userdata("search");
		}
		$this->session->set_userdata(array("{$this->_template }RefUrl"=>base_url().'admincp.php/'.uri_string()));

		$menuList = $this->tableObject->menuTree('*');
		$menuType = array('0'=>'内部栏目', '1'=>'<font color="#077AC7">频道封面</font>', '2'=>'<font color="#0000FF">单网页</font>', '3'=>'<font color="#FF0000">外部链接</font>');
		$model = array();
		$patternList = $this->Pattern_model->gets('title, file_name, title_color', array('status'=>1));
		foreach ($patternList as $key=>$value) {
		    $model[$value['file_name']] = "<font color='{$value['title_color']}'>".$value['title'].'</font>';
		}
		$model[''] = '';

		$data = array(
		             'menuList'=>$menuList,
		             'menuType'=>$menuType,
		             'template'=>$this->_template,
		             'model'=>$model
		             );
		$layout = array(
			      'title'=>$this->_title,
				  'content'=>$this->load->view($this->_template.'/menu_list', $data, TRUE)
			      );
	    $this->load->view('layout/default', $layout);
	}

	public function save($childId = 0, $id = NULL) {
	    if ($id) {
	        checkPermission("{$this->_template}_edit");
	    } else {
	        checkPermission("{$this->_template}_create");
	    }
		$prfUrl = $this->session->userdata("{$this->_template }RefUrl")?$this->session->userdata("{$this->_template }RefUrl"):base_url()."admincp.php/{$this->_template}/menulist";
		if ($_POST) {
			$menuName = $this->input->post('menu_name', TRUE);
			$menuType = $this->input->post('menu_type');
			$model = $this->input->post('model');
			$template = $this->input->post('template');
			$parent = $this->input->post('parent');
			$menu_name = $this->input->post('menu_name', TRUE);
			$en_menu_name = $this->input->post('en_menu_name', TRUE);
			$html_path = $this->input->post('html_path', TRUE);
			$hide = $this->input->post('hide');
			$position = implode(',', $this->input->post('position'));
			$url = $this->input->post('url');
			$sort = $this->input->post('sort');
			$cover_function = $this->input->post('cover_function', TRUE);
			$list_function = $this->input->post('list_function', TRUE);
			$detail_function = $this->input->post('detail_function', TRUE);
			$seo_menu_name = $this->input->post('seo_menu_name', TRUE);
			$keyword = $this->input->post('keyword', TRUE);
			$abstract = $this->input->post('abstract', TRUE);
			$content = unhtml($this->input->post('content'));

			if (! $this->form_validation->required($menuName)) {
				printAjaxError('fail', '栏目名称不能为空 ！');
			}
			if ($menuType != 3 && ! $this->form_validation->required($model)) {
				printAjaxError('fail', '请绑定模型 ！');
			}
		    if ($menuType != 3 && ! $this->form_validation->required($template)) {
				printAjaxError('fail', '请绑定模板 ！');
			}
			if ($menuType == 3 && ! $this->form_validation->required($url)) {
			    printAjaxError('fail', '链接地址不能为空！');
			}
		    if ($this->input->post('parent') == $id) {
			    printAjaxError('fail', '自己不能是自己的上级，请换个上级栏目！');
			}

			if ($id != NULL) {//修改
				$fields = array(
			              'parent'=>$parent,
					      'menu_name'=>$menu_name,
			              'en_menu_name'=>$en_menu_name,
			              'html_path'=>$html_path,
					      'hide'=>$hide,
			              'position'=>$position,
					      'menu_type'=>$menuType,
					      'url'=>$url,
					      'sort'=>$sort,
					      'model'=>$menuType=='3'?'':$model,
			    		  'template'=>$menuType=='3'?'':$template,
						  'cover_function'=>$cover_function,
						  'list_function'=>$list_function,
						  'detail_function'=>$detail_function,
				          'seo_menu_name'=>$seo_menu_name,
			    		  'keyword'=>$keyword,
			    		  'abstract'=>$abstract,
				          'content'=>$content
			              );
			    if ($this->tableObject->save($fields, array('id'=>$id))) {
			        printAjaxSuccess($prfUrl);
				} else {
					printAjaxError('fail', "操作失败！");
				}
			} else {
				$i = 0;
				$menu_name = preg_replace(array('/^\|+/', '/\|+$/'), array('', ''), $menu_name);
				$menu_name_items = explode("|", $menu_name);
				foreach ($menu_name_items as $key=>$menu_name_item) {
					$fields = array(
				              'parent'=>$parent,
						      'menu_name'=>$menu_name_item,
				              'en_menu_name'=>$en_menu_name,
				              'html_path'=>$html_path,
						      'hide'=>$hide,
				              'position'=>$position,
						      'menu_type'=>$menuType,
						      'url'=>$url,
						      'sort'=>$sort + $key,
						      'model'=>$menuType=='3'?'':$model,
				    		  'template'=>$menuType=='3'?'':$template,
							  'cover_function'=>$cover_function,
							  'list_function'=>$list_function,
							  'detail_function'=>$detail_function,
					          'seo_menu_name'=>$seo_menu_name,
				    		  'keyword'=>$keyword,
				    		  'abstract'=>$abstract,
					          'content'=>$content
				              );
				    if ($this->tableObject->save($fields)) {
				        $i++;
					}
				}
			    if ($i > 0) {
				    printAjaxSuccess($prfUrl);
				} else {
				    printAjaxError('fail', "操作失败！");
				}
			}
		}

		$menuList = $this->tableObject->menuTree('id, menu_name, parent');
		$menuInfo = $this->tableObject->gets('*', array('id'=>$id));
		$patternList = $this->Pattern_model->gets('title, file_name', array('status'=>1));

		$data = array(
		        'menuList'=>$menuList,
		        'menuInfo'=>$menuInfo?$menuInfo[0]:$menuInfo,
		        'childId'=>$childId,
		        'position'=>$this->_position,
		        'patternList'=>$patternList,
		        'prfUrl'=>$prfUrl
		        );
	    $layout = array(
	              'tool'=>$this->_tool,
			      'title'=>$this->_title,
				  'content'=>$this->load->view($this->_template.'/save', $data, TRUE)
			      );
	    $this->load->view('layout/default', $layout);
	}

	public function delete() {
	    checkPermissionAjax("{$this->_template}_delete");
		$id = $this->input->post('id');

		if (! $this->form_validation->required($id)) {
		    printAjaxError('fail', '参数不能为空！');
		} else if (! $this->form_validation->integer($id)) {
		    printAjaxError('fail', '参数错误！');
		}
		if($this->tableObject->getChildMenu($id)) {
		    printAjaxError('fail', '删除失败，请先删除子级！');
		}
		if ($this->tableObject->getArticle($id) > 0) {
		    printAjaxError('fail', '删除失败，请先删除关联的文章！');
		}
		if ($this->tableObject->delete(array('id'=>$id))) {
		    printAjaxData(array('id'=>$id));
		} else {
			printAjaxError('fail', '删除失败！');
		}
	}

    public function sort() {
        checkPermissionAjax("{$this->_template}_sort");

		$menuIds = $this->input->post('menuIds', TRUE);
		$menuSorts = $this->input->post('menuSorts', TRUE);

		if (! empty($menuIds) && ! empty($menuSorts)) {
			$ids = explode(',', $menuIds);
			$sorts = explode(',', $menuSorts);

			foreach ($ids as $key=>$value) {
				$this->tableObject->save(
				                   array('sort'=>$sorts[$key]),
				                   array('id'=>$value));
			}
			printAjaxSuccess('', '排序成功！');
		}
		printAjaxError('fail', '排序失败！');
	}

	//中文转换成拼音
	public function getPinyin() {
		$systemInfo = $this->System_model->get('*', array('id'=>1));
		$menuName = $this->input->post('menu_name', TRUE);
		$parentId = $this->input->post('parent_id');
		$path = $systemInfo['html_folder']?$systemInfo['html_folder'].'/':'a/';
		if (!$systemInfo['html_level']) {
			if (! empty($parentId)) {
				$menuInfo = $this->tableObject->get('html_path', array('id'=>$parentId));
				$path = $menuInfo['html_path'].'/';
			}
			if (! empty($menuName)) {
				$chineseToPinyinclass = new ChineseToPinyinclass();
			    $pinyin = $chineseToPinyinclass->Pinyin(strtolower($menuName), 'utf-8');
			    printAjaxData(array('pinyin'=>$path.$pinyin));
			} else {
			    printAjaxError('fail', "栏目名称不能为空!");
			}
		} else {
			if ($parentId) {
				$parentId = $this->tableObject->getParentMenuId($parentId);
				$parentMenuInfo = $this->tableObject->get('html_path', array('id'=>$parentId));
				printAjaxData(array('pinyin'=>$parentMenuInfo['html_path']));
			} else {
				if (! empty($parentId)) {
					$menuInfo = $this->tableObject->get('html_path', array('id'=>$parentId));
					$path = $menuInfo['html_path'].'/';
				}
				if (! empty($menuName)) {
					$chineseToPinyinclass = new ChineseToPinyinclass();
				    $pinyin = $chineseToPinyinclass->Pinyin(strtolower($menuName), 'utf-8');
				    printAjaxData(array('pinyin'=>$path.$pinyin));
				} else {
				    printAjaxError('fail', "栏目名称不能为空!");
				}
			}
		}
	}
}

/* End of file menu.php */
/* Location: ./application/admin/controllers/menu.php */