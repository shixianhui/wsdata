<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>" enctype="multipart/form-data" id="jsonForm" >
<div id="basics" style="" >
<input name="id" value="" type="hidden">
<table class="table_form" cellpadding="0" cellspacing="1">
  <caption>添加栏目</caption>
 	<tbody>
 	<tr>
      <th width="20%"> <strong>上级栏目</strong> <br/>
	  </th>
      <td>
      <input name="parentSelectMenu" id="parentSelectMenu" type="hidden" value="<?php if ($menuInfo){echo $menuInfo['parent'];} else if($childId !="0"){echo $childId;} ?>" >
      <select class="input_blur" name="parent" id="parent">
       <option value="0">无(作为一级栏目)</option>
       <?php if (! empty($menuList)) { ?>
       <!-- 一级 -->
       <?php foreach ($menuList as $menu) { ?>
       <option value="<?php echo $menu['id']; ?>"><?php echo $menu['menu_name']; ?></option>
       <!-- 二级 -->
       <?php foreach ($menu['subMenuList'] as $subMenu) { ?>
       <option value="<?php echo $subMenu['id']; ?>">&nbsp;&nbsp;┣<?php echo $subMenu['menu_name']; ?></option>
       <!-- 三级 -->
       <?php foreach ($subMenu['subMenuList'] as $sSubMenu) { ?>
       <option value="<?php echo $sSubMenu['id']; ?>">&nbsp;&nbsp;&nbsp;&nbsp;┣<?php echo $sSubMenu['menu_name']; ?></option>
       <!-- 四级 -->
       <?php foreach ($sSubMenu['subMenuList'] as $sSubMenu4) { ?>
       <option value="<?php echo $sSubMenu4['id']; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;┣<?php echo $sSubMenu4['menu_name']; ?></option>
       <?php }}}}} ?>
      </select>
      </td>
    </tr>
    <tr>
      <th width="20%">
      <font color="red">*</font> <strong>栏目名称</strong> <br/>
	  </th>
      <td>
      <input name="menu_name" id="menu_name" size="50" value="<?php if ($menuInfo){echo $menuInfo['menu_name'];} ?>" class="input_blur" type="text" valid="required" errmsg="栏目名称不能为空!" />
      </td>
    </tr>
    <tr>
      <th width="20%"><strong>英文栏目名称</strong> <br/>
	  </th>
      <td>
      <input name="en_menu_name" id="en_menu_name" size="50" value="<?php if ($menuInfo){echo $menuInfo['en_menu_name'];} ?>" class="input_blur" type="text" />
      </td>
    </tr>
    <tr>
      <th width="20%"> <strong>静态地址</strong> <br/>
	  </th>
      <td>
      <input name="html_path" id="html_path" size="50" value="<?php if ($menuInfo){echo $menuInfo['html_path'];} ?>" class="input_blur" type="text" /> <font color="red">格式："a/demo"</font>
      </td>
    </tr>
    <tr>
      <th width="20%">
      <strong>是否隐藏栏目</strong> <br/>
	  </th>
      <td>
      <input type="radio" value="0" name="hide" class="radio_style" <?php if($menuInfo){if($menuInfo['hide']=='0'){echo 'checked="checked"';}}else{echo 'checked="checked"';} ?> > 显示
      <input type="radio" value="1" name="hide" class="radio_style" <?php if($menuInfo){if($menuInfo['hide']=='1'){echo 'checked="checked"';}} ?>> 隐藏
	  </td>
    </tr>
    <tr>
      <th width="20%">
      <strong>栏目显示位置</strong> <br/>
	  </th>
      <td>
      <input type="checkbox" name="position[]" value="head" <?php if($menuInfo){if(in_array('head', explode(',', $menuInfo['position']))){echo 'checked="checked"';}} ?> /> 头部
      <input type="checkbox" name="position[]" value="navigation" <?php if($menuInfo){if(in_array('navigation', explode(',', $menuInfo['position']))){echo 'checked="checked"';}}else{echo 'checked="checked"';} ?> /> 导航栏
      <input type="checkbox" name="position[]" value="footer" <?php if($menuInfo){if(in_array('footer', explode(',', $menuInfo['position']))){echo 'checked="checked"';}} ?> /> 底部
	  </td>
    </tr>
    <tr id="menu_type">
      <th width="20%">
      <strong>栏目类型</strong> <br/>
	  </th>
      <td>
      <input type="radio" value="0" name="menu_type" class="radio_style" <?php if($menuInfo){if($menuInfo['menu_type']=='0'){echo 'checked="checked"';}}else{echo 'checked="checked"';} ?> > 内部栏目（可绑定内容模型，并支持在栏目下建立子栏目或发布信息）<br/>
      <input type="radio" value="1" name="menu_type" class="radio_style" <?php if($menuInfo){if($menuInfo['menu_type']=='1'){echo 'checked="checked"';}} ?> > 频道封面<br/>
	  <input type="radio" value="2" name="menu_type" class="radio_style" <?php if($menuInfo){if($menuInfo['menu_type']=='2'){echo 'checked="checked"';}} ?> > 单网页（选择page模型，要不然不起作用）<br/>
	  <input type="radio" value="3" name="menu_type" class="radio_style" <?php if($menuInfo){if($menuInfo['menu_type']=='3'){echo 'checked="checked"';}} ?> > 外部链接（可建立一个链接并指向任意网址）
      </td>
    </tr>
    <tr id="link_url" style="display:none;">
      <th width="20%">
      <strong>链接地址</strong> <br/>
	  </th>
      <td>
      <input name="url" id="url" size="50" value="<?php if ($menuInfo){echo $menuInfo['url'];} ?>" class="input_blur" type="text" />
      </td>
    </tr>
    <tr>
      <th width="20%">
      <strong>栏目排序</strong> <br/>
	  </th>
      <td>
      <input name="sort" id="sort" size="5" value="<?php if ($menuInfo){echo $menuInfo['sort'];}else{echo 0;} ?>" class="input_blur" type="text" valid="required|isNumber" errmsg="栏目排序不能为空！|只能是数字！"  />
      </td>
    </tr>
    <tr id="selectModelTr">
      <th width="20%"> <strong>绑定模型</strong> <br/>
	  </th>
      <td><input name="selectModel" id="selectModel" type="hidden" value="<?php if ($menuInfo){echo $menuInfo['model'];} ?>" >
      <select class="input_blur" name="model" id="model">
      <option value="">请选择模型</option>
      <?php if ($patternList): ?>
      <?php foreach ($patternList as $pattern): ?>
       <option value="<?php echo $pattern['file_name']; ?>"><?php echo $pattern['title']; ?>|<?php echo $pattern['file_name']; ?></option>
      <?php endforeach; ?>
      <?php endif; ?>
      </select>
      </td>
    </tr>
    <tr id="select_template">
      <th width="20%"> <strong>绑定模板</strong> <br/>
	  </th>
      <td>
      <input name="template" id="template" size="50" value="<?php if ($menuInfo){echo $menuInfo['template'];} ?>" class="input_blur" type="text" /> <font color="red">注：必须与模型对应</font>
      </td>
    </tr>
    <tr>
      <th width="20%"> <strong>封面方法</strong> <br/>
	  </th>
      <td>
      <input name="cover_function" id="cover_function" size="50" value="<?php if ($menuInfo){echo $menuInfo['cover_function'];}else{echo 'cover';} ?>" class="input_blur" type="text" /> <font color="red">注：栏目类型为“频道封面”时才起作用</font>
      </td>
    </tr>
    <tr>
      <th width="20%"> <strong>列表方法</strong> <br/>
	  </th>
      <td>
      <input name="list_function" id="list_function" size="50" value="<?php if ($menuInfo){echo $menuInfo['list_function'];}else{echo 'index';} ?>" class="input_blur" type="text" /> <font color="red">注：一般默认就行</font>
      </td>
    </tr>
    <tr>
      <th width="20%"> <strong>正文方法</strong> <br/>
	  </th>
      <td>
      <input name="detail_function" id="detail_function" size="50" value="<?php if ($menuInfo){echo $menuInfo['detail_function'];}else{echo 'detail';} ?>" class="input_blur" type="text" /> <font color="red">注：一般默认就行</font>
      </td>
    </tr>
    <tr>
      <th width="20%"> <strong>SEO标题</strong> <br/>
	  优化后，源里显示这个标题
	  </th>
      <td>
      <input name="seo_menu_name" id="seo_menu_name" size="80"  value="<?php if(! empty($menuInfo)){ echo $menuInfo['seo_menu_name'];} ?>"  maxlength="100" class="input_blur" type="text" />
      </td>
    </tr>
    <tr>
      <th width="20%"> <strong>SEO关键词</strong> <br/>
	  多关键词之间用逗号隔开
	  </th>
      <td>
      <input name="keyword" id="keyword" size="80"  value="<?php if(! empty($menuInfo)){ echo $menuInfo['keyword'];} ?>"  maxlength="100" class="input_blur" type="text" />
      </td>
    </tr>
	<tr>
      <th width="20%"> <strong>SEO摘要</strong> <br/>
	  </th>
      <td>还可以输入 <font id="ls_description" color="#ff0000">255</font> 个字符！<br/>
      <textarea name="abstract" id="abstract" rows="4" cols="50"  class="textarea_style" style="width: 80%;" ><?php if(! empty($menuInfo)){ echo $menuInfo['abstract'];} ?></textarea>
      </td>
    </tr>
    <tr>
      <th width="20%"> <strong>单页面栏目内容</strong></th>
      <td>
	  <?php echo $this->load->view('element/ckeditor_tool', NULL, TRUE); ?>
<script id="content" name="content" type="text/plain" style="width:800px;height:300px;"><?php if(! empty($menuInfo)){ echo html($menuInfo["content"]);}else{echo "";} ?></script>
<script type="text/javascript">
    var ue = UE.getEditor('content');
</script>
      </td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>
	  <input class="button_style" name="dosubmit" value=" 保存 " type="submit">
	  &nbsp;&nbsp; <input onclick="javascript:window.location.href='<?php echo $prfUrl; ?>';" class="button_style" name="reset" value=" 返回 " type="button">
	  </td>
    </tr>
  </tbody>
</table>
</div>
</form>