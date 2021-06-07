<?php echo $tool; ?>
<form name="search" method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
<table class="table_form" cellpadding="0" cellspacing="1">
  <caption>信息查询</caption>
<tbody>
<tr>
<td class="align_c">
关键字
<input class="input_blur" name="site_name" id="site_name" value="" size="20" type="text">&nbsp;
<select class="input_blur" name="category_id">
<option value="">--选择栏目--</option>
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
<!-- 五级 -->
<?php foreach ($sSubMenu4['subMenuList'] as $sSubMenu5) { ?>
<option value="<?php echo $sSubMenu5['id']; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;┣<?php echo $sSubMenu5['menu_name']; ?></option>
<?php }}}}}} ?>
</select>
<select class="input_blur" name="link_type" id="link_type">
<option value="">--链接类型--</option>
<option value="text">文字</option>
<option value="logo">图片</option>
</select>
<select class="input_blur" name="display">
<option value="">选择状态</option>
<option value="1">显示</option>
<option value="0">隐藏</option>
</select>&nbsp;
<input class="button_style" name="dosubmit" value=" 查询 " type="submit">
</td>
</tr>
</tbody></table>
</form>
<table class="table_list" id="news_list" cellpadding="0" cellspacing="1">
<caption>信息管理</caption>
<tbody>
<tr class="mouseover">
<th width="70">选中</th>
<th>网站名称</th>
<th width="100">所属分类</th>
<th width="50">状态</th>
<th width="100">链接类型</th>
<th width="70">管理操作</th>
</tr>
<?php if (! empty($itemList)): ?>
<?php foreach ($itemList as $key=>$value): ?>
<tr id="id_<?php echo $value['id']; ?>"  onMouseOver="this.style.backgroundColor='#f2f2f2'" onMouseOut="this.style.background='#FFFFFF'">
<td><input  class="checkbox_style" name="ids" value="<?php echo $value['id']; ?>" type="checkbox"> <?php echo $value['id']; ?></td>
<td><a href="admincp.php/<?php echo $template; ?>/save/<?php echo $value['id']; ?>" ><?php echo $value['site_name']; ?></a></td>
<td class="align_c"><?php echo $value['menu_name']; ?></td>
<td class="align_c"><?php echo $value['display']?'显示':'<font color="#FF0000">隐藏</font>'; ?></td>
<td class="align_c"><?php if ($value['link_type']=='text'){echo '文字';}else{echo '<font color="#ff0000">图片</font>';} ?></td>
<td class="align_c"><a href="admincp.php/<?php echo $template; ?>/save/<?php echo $value['id']; ?>">修改</a></td>
</tr>
<?php endforeach; ?>
<?php endif; ?>
</tbody>
</table>
<div class="button_box">
<span style="width: 60px;"><a href="javascript:void(0);" onclick="javascript:$('input[type=checkbox]').prop('checked', true)">全选</a>/
<a href="javascript:void(0);" onclick="javascript:$('input[type=checkbox]').prop('checked', false)">取消</a></span>
<input class="button_style_red" name="delete" id="delete" value=" 删除 "  type="button">
批量移动至：
<select class="input_blur" name="category_id" id="category_id" onchange="#">
<option value="">--选择栏目--</option>
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
<select class="input_blur" name="select_display" id="select_display" onchange="#">
<option value="">选择状态</option>
<option value="1">显示</option>
<option value="0">隐藏</option>
</select>
</div>
<div id="pages">
<?php echo $pagination; ?>
<a>总条数：<?php echo $paginationCount; ?></a>
<!-- <a>总页数：<?php echo $pageCount; ?></a> -->
</div>