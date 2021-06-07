<?php
class Html extends CI_Controller {
	private $_title = '生成静态';
	private $_tool = '';
	public function __construct() {
		parent::__construct();
		$this->_tool = $this->load->view('element/html_tool', '', TRUE);
		$this->load->model('Menu_model', '', TRUE);
		$this->load->model('System_model', '', TRUE);
		$this->load->helper(array('url', 'my_fileoperate', 'file'));
	}

	public function index() {
	    checkPermission("html_index");
		//首页
		$indexCount = 0;
	    if (file_exists('./index.html')) {
	    	$indexCount = 1;
		}			
		$menuList = $this->Menu_model->menuTree('*');
		foreach ($menuList as $key=>$menu) {
			$fileCount = 0;
			if ($menu['menu_type'] == 3) {
			    $menuList[$key]['menuHtml'] = '<font color="red">0</font>/0';
			} else {
			    $menuList[$key]['menuHtml'] = '<font color="red">0</font>/1';
			    $menuList[$key]['coverMenuHtml'] = '<font color="red">0</font>/1';
			}
			//栏目
			if (file_exists('./'.$menu['html_path']."/index{$menu['id']}.html")) {
				$menuList[$key]['menuHtml'] = '1/1';
				$fileCount = $this->_getFileCount('./'.$menu['html_path']);
			} else {
				if (is_dir('./'.$menu['html_path'])) {
				    $fileCount = $this->_getFileCount('./'.$menu['html_path']);
				}			    
			}
			if ($menu['menu_type'] == 1 && $menu['cover_function']) {
				//封面
				if (file_exists("./{$menu['html_path']}/{$menu['cover_function']}{$menu['id']}.html")) {
					$menuList[$key]['coverMenuHtml'] = '1/1';
				}
			}
			//一级文档数量
			$count = $this->Menu_model->getArticle($menu['id']);
			if ($fileCount) {			    
				$menuList[$key]['detailHtml'] = $fileCount.'/'.$count;
			} else {
				$menuList[$key]['detailHtml'] = '<font color="red">'.$fileCount.'</font>/'.$count;
			}
			//二级
			foreach ($menu['subMenuList'] as $skey=>$subMenu) {
				$sfileCount = 0;
				if ($subMenu['menu_type'] == 3) {
				    $menuList[$key]['subMenuList'][$skey]['menuHtml'] = '<font color="red">0</font>/0';	
				} else {
				    $menuList[$key]['subMenuList'][$skey]['menuHtml'] = '<font color="red">0</font>/1';	
				}							
				//栏目
				if (file_exists('./'.$subMenu['html_path']."/index{$subMenu['id']}.html")) {
					$menuList[$key]['subMenuList'][$skey]['menuHtml'] = '1/1';
					$sfileCount = $this->_getFileCount('./'.$subMenu['html_path']);
				} else {
				    if (is_dir('./'.$subMenu['html_path'])) {
				        $sfileCount = $this->_getFileCount('./'.$subMenu['html_path']);
				    }
				}
				//文档
				$scount = $this->Menu_model->getArticle($subMenu['id']);
				if ($sfileCount) {
					$menuList[$key]['subMenuList'][$skey]['detailHtml'] = $sfileCount.'/'.$scount;
				} else {
					$menuList[$key]['subMenuList'][$skey]['detailHtml'] = '<font color="red">'.$sfileCount.'</font>/'.$scount;
				}				
				//三级
				foreach ($subMenu['subMenuList'] as $sskey=>$sSubMenu) {
					$ssfileCount = 0;
					if ($sSubMenu['menu_type'] == 3) {
						$menuList[$key]['subMenuList'][$skey]['subMenuList'][$sskey]['menuHtml'] = '<font color="red">0</font>/0';
					} else {
						$menuList[$key]['subMenuList'][$skey]['subMenuList'][$sskey]['menuHtml'] = '<font color="red">0</font>/1';
					}					
					if (file_exists('./'.$sSubMenu['html_path']."/index{$sSubMenu['id']}.html")) {
						$menuList[$key]['subMenuList'][$skey]['subMenuList'][$sskey]['menuHtml'] = '1/1';
                        $ssfileCount = $this->_getFileCount('./'.$sSubMenu['html_path']);
					} else {
						if (is_dir('./'.$sSubMenu['html_path'])) {
						    $ssfileCount = $this->_getFileCount('./'.$sSubMenu['html_path']);
						}					
					}
					//文档
					$sscount = $this->Menu_model->getArticle($sSubMenu['id']);
					if ($ssfileCount) {					
						$menuList[$key]['subMenuList'][$skey]['subMenuList'][$sskey]['detailHtml'] = $ssfileCount.'/'.$sscount;
					} else {
						$menuList[$key]['subMenuList'][$skey]['subMenuList'][$sskey]['detailHtml'] = '<font color="red">'.$ssfileCount.'</font>/'.$sscount;
					}					
				}
			}
		}
		$data = array(
		        'tool'=>$this->_tool,
		        'menuList'=>$menuList,
		        'indexCount'=>$indexCount
		        );		
		$layout = array(				  
			      'title'=>$this->_title,
				  'content'=>$this->load->view('html/index', $data, TRUE)
			      );
	    $this->load->view('layout/default', $layout);
	}
	
    //生成栏目封面
	public function createCover() {
	    checkPermissionAjax("html_index");
	    $systemInfo = $this->System_model->get('*', array('id'=>1));
		if ($systemInfo['html'] == 0) {
			printAjaxError('fail', '请到"系统设置 > 基本设置"开启静态！');
		}
		$url = '';
		$id = $this->input->post('id');
		if (! empty($id)) {
			$menuInfo = $this->Menu_model->get('*', array('id'=>$id));
			$index = 'index';
			if ($menuInfo['cover_function']) {
				$index = $menuInfo['cover_function'];
			}
			if ($systemInfo['client_index']) {
			    $url = base_url().$systemInfo['client_index'].'/'.$menuInfo['template']."/{$index}/".$menuInfo['id'];
			} else {
				$url = base_url().$systemInfo['client_index'].$menuInfo['template']."/{$index}/".$menuInfo['id'];
			}
			$content = file_get_contents ($url);
			//判断文件夹创建是否成功			
			$path = "./{$menuInfo['html_path']}/";
		    createDirs($path);
			//在这里要对页面内容进入过滤
			if (@write_file($path."{$index}{$menuInfo['id']}.html", $content)) {
				printAjaxSuccess('', '页面生成成功！');
			} else {
				printAjaxError('fail', '页面生成失败！');
			}   
		}
	}
	
	//文件数量
	private function _getFileCount($dir) {
		$handle = opendir ( $dir );
		$i = 0;
		$cover_count = 0;
		$inddex_count = 0;
		while ( false !== $file = (readdir ( $handle )) ) {
			if ($file !== '.' && $file != '..') {
				if (preg_match('/\.html$/', $file) > 0) {
			        $i ++;
			        //封面数
			        if (preg_match('/^cover/', $file) > 0) {
			        	$cover_count ++;
			        }
			        //列表数
				    if (preg_match('/^index/', $file) > 0) {
				    	$inddex_count++;
			        }
			    }
			}
		}
		closedir ( $handle );
		
		return ($i - $cover_count - $inddex_count);
//		$count = 0;
//		$fileList = scandir($dir);
//		foreach ($fileList as $file) {
//		    if (preg_match('/\.html$/', $file) > 0) {
//		        $count++;
//		    }
//		}
//		
//		return $count;
	}
	
	//生成首页静态
	public function createIndex() {
	    checkPermissionAjax("html_index");
		$systemInfo = $this->System_model->get('html', array('id'=>1));
		if ($systemInfo['html'] == 0) {
			printAjaxError('fail', '请到"系统设置 > 基本设置"开启静态！');
		}
		$content = file_get_contents (base_url().'index.php');
		@write_file("./index.html", $content);
		printAjaxSuccess('', "页面生成成功！");
	}
	
	//生成栏目静态
	public function createMenu() {
	    checkPermissionAjax("html_index");
	    $systemInfo = $this->System_model->get('*', array('id'=>1));
		if ($systemInfo['html'] == 0) {
			printAjaxError('fail', '请到"系统设置 > 基本设置"开启静态！');
		}
		$url = '';
		$id = $this->input->post('id');
		if (! empty($id)) {
			$menuInfo = $this->Menu_model->get('*', array('id'=>$id));
			if ($menuInfo && $menuInfo['parent'] != 0 && $menuInfo['model'] == 'picture') {
				if ($systemInfo['client_index']) {
				    $url = base_url().$systemInfo['client_index'].'/'.$menuInfo['template'].'/index/'.$menuInfo['parent'].'/'.$menuInfo['id'];
				} else {
				    $url = base_url().$systemInfo['client_index'].$menuInfo['template'].'/index/'.$menuInfo['parent'].'/'.$menuInfo['id'];
				}				
			} else {
				if ($systemInfo['client_index']) {
					$url = base_url().$systemInfo['client_index'].'/'.$menuInfo['template'].'/index/'.$menuInfo['id'];
				} else {
				    $url = base_url().$systemInfo['client_index'].$menuInfo['template'].'/index/'.$menuInfo['id'];
				}
			}
			$content = file_get_contents ($url);
			//判断文件夹创建是否成功			
			$path = "./{$menuInfo['html_path']}/";
		    createDirs($path);
			//在这里要对页面内容进入过滤
		    if (@write_file ( $path . "index{$menuInfo['id']}.html", $content )) {
				printAjaxSuccess ( '', '页面生成成功！' );
			} else {
				printAjaxError ( 'fail', '页面生成失败！' );
			}
		}
	}
	
	//生成文档
	public function createDetail() {
	    checkPermissionAjax("html_index");
	    $systemInfo = $this->System_model->get('*', array('id'=>1));
		if ($systemInfo['html'] == 0) {
			printAjaxError('fail', '请到"系统设置 > 基本设置"开启静态！');
		}
		$menuId = $this->input->post('menu_id');
		if (! empty($menuId)) {
			$menuInfo = $this->Menu_model->get('*', array('id'=>$menuId));
			if ($menuInfo) {
			    switch ($menuInfo['model']) {
			    	case 'page':
			    		$this->load->model(ucfirst($menuInfo['model']).'_model', 'tableObject', TRUE);
			    		$ids = $this->Menu_model->getChildIds($menuId);
			    		$itemList = $this->tableObject->gets("category_id in ({$ids})");
			    		$i = 0;
			    		foreach ($itemList as $item) {
			    			if ($systemInfo['client_index']) {
			    			    $url = base_url().$systemInfo['client_index'].'/'.$menuInfo['template'].'/index/'.$menuId.'/'.$item['id'];
			    			} else {
			    			    $url = base_url().$systemInfo['client_index'].$menuInfo['template'].'/index/'.$menuId.'/'.$item['id'];
			    			}
			    			$content = file_get_contents ($url);
			    			$path = "./{$menuInfo['html_path']}/";
		                    createDirs($path);
			    			//在这里要对页面内容进入过滤
							if (@write_file($path.$item['id'].'.html', $content)) {
							    $i++;
							}
			    		}
			            if ($i > 0) {
			    		    printAjaxSuccess('', '页面生成成功！');
			    		} else {
			    			printAjaxError('fail', '页面生成失败！');
			    		}
			    		break;
			    	default:
			    		$this->load->model(ucfirst($menuInfo['model']).'_model', 'tableObject', TRUE);
			    		$ids = $this->Menu_model->getChildIds($menuId);
			    		$itemList = $this->tableObject->gets("category_id in ({$ids})");
			    		$i = 0;
			    		foreach ($itemList as $item) {
			    			if ($systemInfo['client_index']) {
			    			    $url = base_url().$systemInfo['client_index'].'/'.$menuInfo['template'].'/detail/'.$item['id'];
			    			} else {
			    			    $url = base_url().$systemInfo['client_index'].$menuInfo['template'].'/detail/'.$item['id'];
			    			}
			    			$content = file_get_contents ($url);
			    			$path = "./{$menuInfo['html_path']}/";
		                    createDirs($path);
			    			//在这里要对页面内容进入过滤
							if (write_file($path.$item['id'].'.html', $content)) {
							    $i++;
							}
			    		}
			    		if ($i > 0) {
			    		    printAjaxSuccess('', '页面生成成功！');
			    		} else {
			    			printAjaxError('fail', '页面生成失败！');
			    		}			    		
			    }
			}			
		}
	}
}
/* End of file link.php */
/* Location: ./application/admin/controllers/link.php */