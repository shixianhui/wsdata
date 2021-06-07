<?php
class News extends CI_Controller {
	private $_table = 'news';
	private $_template = 'news';
	public function __construct() {
		parent::__construct();
		$this->load->model(ucfirst($this->_table).'_model', 'tableObject', TRUE);
		$this->load->model('Menu_model', '', TRUE);
		$this->load->model('System_model', '', TRUE);
	}
	
    //封面
	public function cover($menuId = NULL) {
	    //关键字信息		
		$systemInfo = $this->System_model->get('*', array('id'=>1));
		$menuInfo = $this->Menu_model->get('menu_name, seo_menu_name, keyword, abstract', array('id'=>$menuId));
		//头条
		$ids = $this->Menu_model->getChildMenus($menuId);
		//热点文章
		$hotItemList = $this->tableObject->gets("{$this->_table}.path <> '' and {$this->_table}.category_id in ({$ids}) and {$this->_table}.display=1", 1, 0);
		$strNotWhere = '';
		if ($hotItemList) {
		    $strNotWhere = " and {$this->_table}.id <> {$hotItemList[0]['id']}";
		}
		//顶级ID
		$parent_id = $this->Menu_model->getParentMenuId($menuId);
		
		$data = array(
				'site_name'=>$systemInfo['site_name'],
				'index_name'=>$systemInfo['index_name'],
		        'client_index'=>$systemInfo['client_index'],
				'title'=>$menuInfo['seo_menu_name']?$menuInfo['seo_menu_name']:$menuInfo['menu_name'].$systemInfo['site_name'],
		        'keywords'=>$menuInfo['keyword'],
		        'description'=>$menuInfo['abstract'],
				'site_copyright'=>$systemInfo['site_copyright'],
				'icp_code'=>$systemInfo['icp_code'],
		        'hotItemList'=>$hotItemList,
		        'template'=>$this->_template,
		        'parent_id'=>$parent_id,
				'html'=>$systemInfo['html']
		        );
	    $layout = array(
				  'content'=>$this->load->view("{$this->_template}/cover", $data, TRUE)
			      );
	    $this->load->view('layout/default', $layout);
	    //缓存
	    if ($systemInfo['cache'] == 1) {
	    	$this->output->cache($systemInfo['cache_time']);
	    }
	}
	
	public function index($menuId = NULL, $page = 0) {
		//关键字信息
		$systemInfo = $this->System_model->get('*', array('id'=>1));
		$menuInfo = $this->Menu_model->get('menu_name, seo_menu_name, keyword, abstract', array('id'=>$menuId));
		//当前位置
		$location = '';
		if ($systemInfo['html']) {
			$location = "<a href='index.html'>{$systemInfo['index_name']}</a>&nbsp;&gt;&nbsp;";
		} else {
			$location = "<a href='{$systemInfo['client_index']}'>{$systemInfo['index_name']}</a>&nbsp;&gt;&nbsp;";
		}
		$url = $systemInfo['client_index'];
		$url .= $systemInfo['client_index']?'/':'';
		$url = $this->Menu_model->getLocation($menuId, $systemInfo['html'], $url);
		$location .= $url;
		//新闻列表
		$strWhere = NULL;
		$ids = $this->Menu_model->getChildMenus($menuId);
		$strWhere = "{$this->_table}.category_id in ({$ids}) and {$this->_table}.display=1";
		//分页
		$url = $systemInfo['client_index'];
		if ($systemInfo['client_index']) {
		    $url .= '/';
		}
		$paginationCount = $this->tableObject->rowCount($strWhere);
		$this->config->load('pagination_config', TRUE);
    	$paginationConfig = $this->config->item('pagination_config');
    	$paginationConfig['base_url'] = "{$url}{$this->_template}/index/{$menuId}/";
    	$paginationConfig['total_rows'] = $paginationCount;
    	$paginationConfig['uri_segment'] = 4;
		$this->pagination->initialize($paginationConfig);
		$pagination = $this->pagination->create_links();
		
		$itemList = $this->tableObject->gets($strWhere, $paginationConfig['per_page'], $page);
		
		$data = array(
				'site_name'=>$systemInfo['site_name'],
				'index_name'=>$systemInfo['index_name'],
		        'client_index'=>$systemInfo['client_index'],
				'title'=>$menuInfo['seo_menu_name']?$menuInfo['seo_menu_name']:$menuInfo['menu_name'].$systemInfo['site_name'],
		        'keywords'=>$menuInfo['keyword'],
		        'description'=>$menuInfo['abstract'],
				'site_copyright'=>$systemInfo['site_copyright'],
				'icp_code'=>$systemInfo['icp_code'],
				'html'=>$systemInfo['html'],
		        'itemList'=>$itemList,
		        'menuId'=>$menuId,
		        'pagination'=>$pagination,
		        'paginationCount'=>$paginationCount,
		        'pageCount'=>ceil($paginationCount/$paginationConfig['per_page']),
		        'perPage'=>$paginationConfig['per_page'],
		        'template'=>$this->_template,
		        'location'=>$location
		        );
	    $layout = array(
				  'content'=>$this->load->view("{$this->_template}/index", $data, TRUE)
			      );
	    $this->load->view('layout/default', $layout);
	    //缓存
	    if ($systemInfo['cache'] == 1) {
	    	$this->output->cache($systemInfo['cache_time']);
	    }
	}
	
    public function detail($id = NULL) {		
    	$systemInfo = $this->System_model->get('*', array('id'=>1));
    	$itemInfo = $this->tableObject->get('*', array('id'=>$id, 'display'=>1));
    	//当前位置
		$location = '';
		if ($systemInfo['html']) {
			$location = "<a href='index.html'>{$systemInfo['index_name']}</a>&nbsp;&gt;&nbsp;";
		} else {
			$location = "<a href='{$systemInfo['client_index']}'>{$systemInfo['index_name']}</a>&nbsp;&gt;&nbsp;";
		}
		$url = $systemInfo['client_index'];
		$url .= $systemInfo['client_index']?'/':'';
		$url = $this->Menu_model->getLocation($itemInfo['category_id'], $systemInfo['html'], $url);
		$location .= $url;
    	//栏目关键词
    	$menuInfo = $this->Menu_model->get('html_path', array('id'=>$itemInfo['category_id']));
    	$ids = $this->Menu_model->getChildMenus($itemInfo['category_id']);
		//上下篇
		$prvInfo = $this->tableObject->getPrv($id, $ids);
		$nextInfo = $this->tableObject->getNext($id, $ids);
		//相关文章
		$relationIds = 0;
		if ($itemInfo && $itemInfo['relation']) {
		    $relationIds = preg_replace(array('/^,/', '/^，/', '/,$/', '/，$/', '/，/'), array('', '', '', '', ','), $itemInfo['relation']);
		}
		$relationList = $this->tableObject->gets("{$this->_table}.id in ({$relationIds})");
		
		$data = array(
				'site_name'=>$systemInfo['site_name'],
				'index_name'=>$systemInfo['index_name'],
		        'client_index'=>$systemInfo['client_index'],
				'title'=>$itemInfo['seo_title']?$itemInfo['seo_title'].$systemInfo['site_name']:$itemInfo['title'].$systemInfo['site_name'],
		        'keywords'=>$itemInfo['keyword'],
		        'description'=>$itemInfo['abstract'],
				'site_copyright'=>$systemInfo['site_copyright'],
				'icp_code'=>$systemInfo['icp_code'],
				'html'=>$systemInfo['html'],
				'html_path'=>$menuInfo['html_path'],
				'itemInfo'=>$itemInfo,
		        'prvInfo'=>$prvInfo,
				'nextInfo'=>$nextInfo,
		        'location'=>$location,
		        'template'=>$this->_template,
		        'relationList'=>$relationList
		        );
		$layout = array(
				  'content'=>$this->load->view("{$this->_template}/detail", $data, TRUE)
			      );
	    $this->load->view('layout/default', $layout);
        //缓存
	    if ($systemInfo['cache'] == 1) {
	    	$this->output->cache($systemInfo['cache_time']);
	    }
	}
}
/* End of file main.php */
/* Location: ./application/client/controllers/main.php */