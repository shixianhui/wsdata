<table cellpadding="0" cellspacing="1" class="table_list">
 <caption>栏目管理</caption>
 <tr>
 <th width="5%">ID</th>
 <th width="5%">排序</th>
 <th>栏目名称[样式代码]</th>
 <th width="8%">文章数</th>
 <th width="12%">栏目类型</th>
 <th width="12%">绑定模型</th>
 <th width="8%">栏目位置</th>
 <th width="8%">显示隐藏</th>
 <th width="16%">管理操作</th>
</tr>
<?php if (! empty($menuList)) { ?>
<!-- 一级 -->
<?php foreach ($menuList as $menu) { ?>
<tr id="id_<?php echo $menu['id']; ?>"  onMouseOver="this.style.backgroundColor='#f2f2f2'" onMouseOut="this.style.background='#FFFFFF'">
 <td class="align_c"><?php echo $menu['id']; ?><input style="display:none;" type="checkbox" checked="checked" name="menu_id" value="<?php echo $menu['id']; ?>" /></td>
 <td class="align_c"><input class="input_blur" name="sort[]" id="sort_<?php echo $menu['id']; ?>"  type="text" size="3" value="<?php echo $menu['sort']; ?>" style="background-color:#FF0000" ></td>
 <td title="<?php echo "静态地址：{$menu['html_path']}\n【SEO栏目名称：】".clearstring($menu['seo_menu_name'])."\n【关键词：】".clearstring($menu['keyword'])."\n【描述：】".clearstring($menu['abstract']); ?>"><a href="javascript:void(0);"><span class=""><?php echo $menu['menu_name']; ?></span></a><?php if ($menu['en_menu_name']){ echo '['.$menu['en_menu_name'].']';} ?></td>
 <td class="align_c"><?php echo $this->advdbclass->getArticle($menu['id']); ?></td>
 <td class="align_c" <?php if ($menu['menu_type'] == 3){echo "title=\"{$menu['url']}\"";} ?>><?php echo $menuType[$menu['menu_type']]; ?></td>
 <td class="align_c"><?php echo $model[$menu['model']]; ?></td>
 <td class="align_c"><?php echo preg_replace(array('/head/', '/navigation/', '/footer/'), array('头部', '导航栏', '底部'), $menu['position']); ?></td>
 <td class="align_c"><?php echo $menu['hide']?'<font color="#FF0000">隐藏</font>':'显示'; ?></td>
 <td class="align_c"><a href="admincp.php/<?php echo $template; ?>/save/<?php echo $menu['id']; ?>">添加子栏目</a> | <a href="admincp.php/<?php echo $template; ?>/save/0/<?php echo $menu['id']; ?>">修改</a> | <a href="javascript:deleteMenu(<?php echo $menu['id']; ?>)">删除</a></td>
</tr>
<!-- 二级 -->
<?php foreach ($menu['subMenuList'] as $subMenu) { ?>
<tr id="id_<?php echo $subMenu['id']; ?>" onMouseOver="this.style.backgroundColor='#f2f2f2'" onMouseOut="this.style.background='#FFFFFF'">
 <td class="align_c"><?php echo $subMenu['id']; ?><input style="display:none;" type="checkbox" checked="checked" name="menu_id" value="<?php echo $subMenu['id']; ?>" /></td>
 <td class="align_c"><input class="input_blur" name="sort[]" id="sort_<?php echo $subMenu['id']; ?>" type="text" size="3" value="<?php echo $subMenu['sort']; ?>" ></td>
 <td title="<?php echo "静态地址：{$subMenu['html_path']}\n【SEO栏目名称：】".clearstring($subMenu['seo_menu_name'])."\n【关键词：】".clearstring($subMenu['keyword'])."\n【描述：】".clearstring($subMenu['abstract']); ?>">&nbsp;&nbsp;┣<a href="javascript:void(0);"><span class=""><?php echo $subMenu['menu_name']; ?></span></a></td>
 <td class="align_c"><?php echo $this->advdbclass->getArticle($subMenu['id']); ?></td>
 <td class="align_c" <?php if ($subMenu['menu_type'] == 3){echo "title=\"{$subMenu['url']}\"";} ?>><?php echo $menuType[$subMenu['menu_type']]; ?></td>
 <td class="align_c"><?php echo $model[$subMenu['model']]; ?></td>
 <td class="align_c"><?php echo preg_replace(array('/head/', '/navigation/', '/footer/'), array('头部', '导航栏', '底部'), $subMenu['position']); ?></td>
 <td class="align_c"><?php echo $subMenu['hide']?'<font color="#FF0000">隐藏</font>':'显示'; ?></td>
 <td class="align_c"><a href="admincp.php/<?php echo $template; ?>/save/<?php echo $subMenu['id']; ?>">添加子栏目</a> | <a href="admincp.php/<?php echo $template; ?>/save/0/<?php echo $subMenu['id']; ?>">修改</a> | <a href="javascript:deleteMenu(<?php echo $subMenu['id']; ?>)">删除</a></td>
</tr>
<!-- 三级 -->
<?php foreach ($subMenu['subMenuList'] as $sSubMenu) { ?>
<tr id="id_<?php echo $sSubMenu['id']; ?>" onMouseOver="this.style.backgroundColor='#f2f2f2'" onMouseOut="this.style.background='#FFFFFF'">
 <td class="align_c"><?php echo $sSubMenu['id']; ?><input style="display:none;" type="checkbox" checked="checked" name="menu_id" value="<?php echo $sSubMenu['id']; ?>" /></td>
 <td class="align_c"><input class="input_blur" name="sort[]" id="sort_<?php echo $sSubMenu['id']; ?>" type="text" size="3" value="<?php echo $sSubMenu['sort']; ?>"></td>
 <td title="<?php echo "静态地址：{$sSubMenu['html_path']}\n【SEO栏目名称：】".clearstring($sSubMenu['seo_menu_name'])."\n【关键词：】".clearstring($sSubMenu['keyword'])."\n【描述：】".clearstring($sSubMenu['abstract']); ?>">&nbsp;&nbsp;&nbsp;&nbsp;┣<a href="javascript:void(0);"><span class=""><?php echo $sSubMenu['menu_name']; ?></span></a></td>
 <td class="align_c"><?php echo $this->advdbclass->getArticle($sSubMenu['id']); ?></td>
 <td class="align_c" <?php if ($sSubMenu['menu_type'] == 3){echo "title=\"{$sSubMenu['url']}\"";} ?>><?php echo $menuType[$sSubMenu['menu_type']]; ?></td>
 <td class="align_c"><?php echo $model[$sSubMenu['model']]; ?></td>
 <td class="align_c"><?php echo preg_replace(array('/head/', '/navigation/', '/footer/'), array('头部', '导航栏', '底部'), $sSubMenu['position']); ?></td>
 <td class="align_c"><?php echo $sSubMenu['hide']?'<font color="#FF0000">隐藏</font>':'显示'; ?></td>
 <td class="align_c"><a href="admincp.php/<?php echo $template; ?>/save/<?php echo $sSubMenu['id']; ?>">添加子栏目</a> | <a href="admincp.php/<?php echo $template; ?>/save/0/<?php echo $sSubMenu['id']; ?>">修改</a> | <a href="javascript:deleteMenu(<?php echo $sSubMenu['id']; ?>)">删除</a></td>
</tr>
<!-- 四级 -->
<?php foreach ($sSubMenu['subMenuList'] as $sSubMenu4) { ?>
<tr id="id_<?php echo $sSubMenu4['id']; ?>" onMouseOver="this.style.backgroundColor='#f2f2f2'" onMouseOut="this.style.background='#FFFFFF'">
 <td class="align_c"><?php echo $sSubMenu4['id']; ?><input style="display:none;" type="checkbox" checked="checked" name="menu_id" value="<?php echo $sSubMenu4['id']; ?>" /></td>
 <td class="align_c"><input class="input_blur" name="sort[]" id="sort_<?php echo $sSubMenu4['id']; ?>" type="text" size="3" value="<?php echo $sSubMenu4['sort']; ?>"></td>
 <td title="<?php echo "静态地址：{$sSubMenu4['html_path']}\n【SEO栏目名称：】".clearstring($sSubMenu4['seo_menu_name'])."\n【关键词：】".clearstring($sSubMenu4['keyword'])."\n【描述：】".clearstring($sSubMenu4['abstract']); ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;┣<a href="javascript:void(0);"><span class=""><?php echo $sSubMenu4['menu_name']; ?></span></a></td>
 <td class="align_c"><?php echo $this->advdbclass->getArticle($sSubMenu4['id']); ?></td>
 <td class="align_c" <?php if ($sSubMenu4['menu_type'] == 3){echo "title=\"{$sSubMenu4['url']}\"";} ?>><?php echo $menuType[$sSubMenu4['menu_type']]; ?></td>
 <td class="align_c"><?php echo $model[$sSubMenu4['model']]; ?></td>
 <td class="align_c"><?php echo preg_replace(array('/head/', '/navigation/', '/footer/'), array('头部', '导航栏', '底部'), $sSubMenu4['position']); ?></td>
 <td class="align_c"><?php echo $sSubMenu4['hide']?'<font color="#FF0000">隐藏</font>':'显示'; ?></td>
 <td class="align_c"><a href="admincp.php/<?php echo $template; ?>/save/<?php echo $sSubMenu4['id']; ?>">添加子栏目</a> | <a href="admincp.php/<?php echo $template; ?>/save/0/<?php echo $sSubMenu4['id']; ?>">修改</a> | <a href="javascript:deleteMenu(<?php echo $sSubMenu4['id']; ?>)">删除</a></td>
</tr>
<!-- 五级 -->
<?php foreach ($sSubMenu4['subMenuList'] as $sSubMenu5) { ?>
<tr id="id_<?php echo $sSubMenu5['id']; ?>" onMouseOver="this.style.backgroundColor='#f2f2f2'" onMouseOut="this.style.background='#FFFFFF'">
 <td class="align_c"><?php echo $sSubMenu5['id']; ?><input style="display:none;" type="checkbox" checked="checked" name="menu_id" value="<?php echo $sSubMenu5['id']; ?>" /></td>
 <td class="align_c"><input class="input_blur" name="sort[]" id="sort_<?php echo $sSubMenu5['id']; ?>" type="text" size="3" value="<?php echo $sSubMenu5['sort']; ?>"></td>
 <td title="<?php echo "静态地址：{$sSubMenu5['html_path']}\n【SEO栏目名称：】".clearstring($sSubMenu5['seo_menu_name'])."\n【关键词：】".clearstring($sSubMenu5['keyword'])."\n【描述：】".clearstring($sSubMenu5['abstract']); ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;┣<a href="javascript:void(0);"><span class=""><?php echo $sSubMenu5['menu_name']; ?></span></a></td>
 <td class="align_c"><?php echo $this->advdbclass->getArticle($sSubMenu5['id']); ?></td>
 <td class="align_c" <?php if ($sSubMenu5['menu_type'] == 3){echo "title=\"{$sSubMenu5['url']}\"";} ?>><?php echo $menuType[$sSubMenu5['menu_type']]; ?></td>
 <td class="align_c"><?php echo $model[$sSubMenu5['model']]; ?></td>
 <td class="align_c"><?php echo preg_replace(array('/head/', '/navigation/', '/footer/'), array('头部', '导航栏', '底部'), $sSubMenu5['position']); ?></td>
 <td class="align_c"><?php echo $sSubMenu5['hide']?'<font color="#FF0000">隐藏</font>':'显示'; ?></td>
 <td class="align_c"><a style="margin-left:80px;" href="admincp.php/<?php echo $template; ?>/save/0/<?php echo $sSubMenu5['id']; ?>">修改</a> | <a href="javascript:deleteMenu(<?php echo $sSubMenu5['id']; ?>)">删除</a></td>
</tr>
<?php }}}}}} ?>
</table>
<div class="button_box">
<input class="button_style" name="menu_list_order" id="menu_list_order" value=" 排序 "  type="button">
</div>
<script language="javascript" type="text/javascript">
function deleteMenu(id) {
	var con = confirm("你确定要删除[ID:"+id+"]吗？删除后将不可恢复！");
	if (con == true) {
		$.post("admincp.php/"+controller+"/delete",
			{	"id": id
			},
			function(res){
				if(res.success){
					$("#id_"+res.data.id).remove();
					return false;
				}else{
					alert(res.message);
					return false;
				}
			},
			"json"
		);
	}
}
</script>