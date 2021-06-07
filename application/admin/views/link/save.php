<?php echo $tool; ?>
<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>" enctype="multipart/form-data" id="jsonForm" >
<table class="table_form" cellpadding="0" cellspacing="1">
  <caption>基本信息</caption>
 	<tbody>
	<tr>
      <th width="20%">
      <font color="red">*</font> <strong>栏目</strong> <br/>
	  </th>
      <td>
      <input name="select_category_id" id="select_category_id" type="hidden" value="<?php if(! empty($itemInfo)){ echo $itemInfo['category_id'];} ?>" >
      <select class="input_blur" name="category_id" id="category" valid="required" errmsg="请选择栏目!">
       <option value="" >请选择栏目</option>
       <?php if (! empty($menuList)) { ?>
       <!-- 一级 -->
       <?php foreach ($menuList as $menu) { ?>
       <option <?php if ($menu['subMenuList']) {echo 'disabled="disabled"';} ?> value="<?php echo $menu['id']; ?>"><?php echo $menu['menu_name']; ?></option>
       <!-- 二级 -->
       <?php foreach ($menu['subMenuList'] as $subMenu) { ?>
       <option <?php if ($subMenu['subMenuList']) {echo 'disabled="disabled"';} ?> value="<?php echo $subMenu['id']; ?>">&nbsp;&nbsp;┣<?php echo $subMenu['menu_name']; ?></option>
       <!-- 三级 -->
       <?php foreach ($subMenu['subMenuList'] as $sSubMenu) { ?>
       <option <?php if ($sSubMenu['subMenuList']) {echo 'disabled="disabled"';} ?> value="<?php echo $sSubMenu['id']; ?>">&nbsp;&nbsp;&nbsp;&nbsp;┣<?php echo $sSubMenu['menu_name']; ?></option>
       <!-- 四级 -->
       <?php foreach ($sSubMenu['subMenuList'] as $sSubMenu4) { ?>
       <option <?php if ($sSubMenu4['subMenuList']) {echo 'disabled="disabled"';} ?> value="<?php echo $sSubMenu4['id']; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;┣<?php echo $sSubMenu4['menu_name']; ?></option>
       <!-- 五级 -->
       <?php foreach ($sSubMenu4['subMenuList'] as $sSubMenu5) { ?>
       <option value="<?php echo $sSubMenu5['id']; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;┣<?php echo $sSubMenu5['menu_name']; ?></option>
       <?php }}}}}} ?>
      </select>
      </td>
    </tr>
    <tr>
      <th width="20%">
      <strong>链接类型</strong> <br/>
	  </th>
      <td>
      <label>
      <input type="radio" value="logo" name="link_type" class="radio_style" <?php if(! empty($itemInfo)){ if($itemInfo['link_type']=='logo'){echo 'checked="checked"';}}else{echo 'checked="checked"';} ?>> 图片链接&nbsp;&nbsp;&nbsp;&nbsp;
	  </label>
	  <label>
	  <input type="radio" value="text" name="link_type" class="radio_style" <?php if(! empty($itemInfo)){ if($itemInfo['link_type']=='text'){echo 'checked="checked"';}} ?>> 文字链接
	  </label>
	</td>
    </tr>
    <tr>
      <th width="20%">
      <font color="red">*</font> <strong>网站名称</strong> <br/>
	  </th>
      <td>
      <input class="input_blur" name="site_name" id="site_name" value="<?php if(! empty($itemInfo)){ echo $itemInfo['site_name'];} ?>" size="50" maxlength="60" valid="required" errmsg="网站名称不能为空!" type="text">
	</td>
    </tr>
    <tr>
      <th width="20%">
      <font color="red">*</font> <strong>网站地址</strong> <br/>
	  </th>
      <td>
     <input class="input_blur" name="url" id="url" value="<?php if(! empty($itemInfo)){ echo $itemInfo['url'];} ?>" size="50" maxlength="100" valid="required" errmsg="网站地址不能为空!" type="text">
	</td>
    </tr>
    <tr id="tr_image_path">
      <th width="20%">
      <strong>网站Logo</strong> <br/>
	  </th>
      <td>
    <input name="model" id="model"  value="<?php echo $template; ?>" type="hidden" />
    <input name="path" id="path" size="50" class="input_blur" value="<?php if(! empty($itemInfo)){ echo $itemInfo['path'];} ?>" type="text" />
    <input class="button_style" name="upload_image" id="upload_image" value="上传图片" style="width: 60px;"  type="button" />  <input class="button_style" value="浏览..."
style="cursor: pointer;" name="select_image" id="select_image" type="button" /> <input class="button_style" style="cursor: pointer;"  name="cut_image" id="cut_image" value="裁剪图片" type="button"  />
    </td>
    </tr>
    <tr>
      <th width="20%">
      <strong>网站介绍</strong> <br/>
	  </th>
      <td>
     <textarea id="description" rows="4" cols="50" name="description"><?php if(! empty($itemInfo)){ echo $itemInfo['description'];} ?></textarea>
	</td>
    </tr>
    <tr>
      <th width="20%">
      <strong>排序</strong> <br/>
	  </th>
      <td>
     <input class="input_blur" name="sort" id="sort" value="<?php if(! empty($itemInfo)){ echo $itemInfo['sort'];}else{echo '0';} ?>" size="50" valid="isNumber" errmsg="排序只能是数字!" type="text">
	</td>
    </tr>
    <tr>
      <th width="20%">
      <strong>QQ号</strong> <br/>
	  </th>
      <td>
     <input class="input_blur" name="qq" id="qq" value="<?php if(! empty($itemInfo)){ echo $itemInfo['qq'];} ?>" size="50" maxlength="15" type="text">
	</td>
    </tr>
    <tr>
      <th width="20%">
      <strong>邮件</strong> <br/>
	  </th>
      <td>
     <input class="input_blur" name="email" id="email" value="<?php if(! empty($itemInfo)){ echo $itemInfo['email'];} ?>" size="50" maxlength="100" valid="isEmail" errmsg="邮件格式错误!" type="text">
	</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>
	  <input class="button_style" name="dosubmit" value=" 保存 " type="submit">
	  &nbsp;&nbsp; <input onclick="javascript:window.location.href='<?php echo $prfUrl; ?>';" class="button_style" name="reset" value=" 返回 " type="button">
	  </td>
    </tr>
</tbody></table>
</div>
</form>