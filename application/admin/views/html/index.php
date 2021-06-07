<?php echo $tool; ?>
<table cellpadding="0" cellspacing="1" class="table_list">
 <caption>栏目及文档html生成管理<font color="red">(更新文档可去内容管理)</font></caption>
 <tr>
 <th width="5%">ID</th>
 <th>栏目名称</th>
 <th width="12%">栏目页数</th>
 <th width="15%">文档页数</th>
 <th width="25%">管理操作</th>
</tr>
<tr onMouseOver="this.style.backgroundColor='#f2f2f2'" onMouseOut="this.style.background='#FFFFFF'">
 <td class="align_c">0</td>
 <td><a href="javascript:void(0);"><span class="">首页</span></a></td>
 <td class="align_c"><?php echo $indexCount; ?>/1</td>
 <td class="align_c">----</td>
 <td class="align_c"><a href="javascript:createIndex();">更新首页</a></td>
</tr>
<?php if (! empty($menuList)): ?>
<!-- 一级 -->
<?php foreach ($menuList as $menu): ?>
<?php if ($menu['menu_type'] == 1 && $menu['cover_function']) { ?>
<tr onMouseOver="this.style.backgroundColor='#f2f2f2'" onMouseOut="this.style.background='#FFFFFF'">
 <td class="align_c"><?php echo $menu['id']; ?></td>
 <td><a href="javascript:void(0);"><span class=""><?php echo $menu['menu_name']; ?>[封面]</span></a></td>
 <td class="align_c"><?php echo $menu['coverMenuHtml']; ?></td>
 <td class="align_c">----</td>
 <td class="align_c"><a href="javascript:createCover(<?php echo $menu['id']; ?>)">更新封面</a></td>
</tr>
<?php } ?>
<tr onMouseOver="this.style.backgroundColor='#f2f2f2'" onMouseOut="this.style.background='#FFFFFF'">
 <td class="align_c"><?php echo $menu['id']; ?></td>
 <td><a href="javascript:void(0);"><span class=""><?php echo $menu['menu_name']; ?></span></a></td>
 <td class="align_c"><?php echo $menu['menuHtml']; ?></td>
 <td class="align_c"><?php echo $menu['detailHtml']; ?></td>
 <td class="align_c"><?php if ($menu['menu_type'] != '3'){ ?><a href="javascript:createMenu(<?php echo $menu['id']; ?>)">更新栏目</a><?php }else{echo '--------';} ?> | <?php if ( $menu['menu_type'] == '2' || $menu['menu_type'] == '3'){echo '--------';} else { ?> <a href="javascript:createDetail(<?php echo $menu['id']; ?>)" title="确保栏目已更新">更新文档</a><?php } ?></td>
</tr>
<!-- 二级 -->
<?php foreach ($menu['subMenuList'] as $subMenu): ?>
<tr onMouseOver="this.style.backgroundColor='#f2f2f2'" onMouseOut="this.style.background='#FFFFFF'">
 <td class="align_c"><?php echo $subMenu['id']; ?></td>
 <td>&nbsp;&nbsp;|-<a href="javascript:void(0);"><span class=""><?php echo $subMenu['menu_name']; ?></span></a></td>
 <td class="align_c"><?php echo $subMenu['menuHtml']; ?></td>
 <td class="align_c"><?php echo $subMenu['detailHtml']; ?></td>
 <td class="align_c"><?php if ($menu['menu_type'] != '3'){ ?><a href="javascript:createMenu(<?php echo $subMenu['id']; ?>)">更新栏目</a><?php }else{echo '--------';} ?> | <?php if ($menu['menu_type'] == '2' || $menu['menu_type'] == '3'){echo '--------';} else { ?> <a href="javascript:createDetail(<?php echo $subMenu['id']; ?>)" title="确保栏目已更新">更新文档</a><?php } ?></td>
</tr>
<!-- 三级 -->
<?php foreach ($subMenu['subMenuList'] as $sSubMenu): ?>
<tr onMouseOver="this.style.backgroundColor='#f2f2f2'" onMouseOut="this.style.background='#FFFFFF'">
 <td class="align_c"><?php echo $sSubMenu['id']; ?></td>
 <td>&nbsp;&nbsp;&nbsp;&nbsp;|-<a href="javascript:void(0);"><span class=""><?php echo $sSubMenu['menu_name']; ?></span></a></td>
 <td class="align_c"><?php echo $sSubMenu['menuHtml']; ?></td>
 <td class="align_c"><?php echo $sSubMenu['detailHtml']; ?></td>
 <td class="align_c"><?php if ($menu['menu_type'] != '3'){ ?><a href="javascript:createMenu(<?php echo $sSubMenu['id']; ?>)">更新栏目</a><?php }else{echo '--------';} ?> | <?php if ($menu['menu_type'] == '2' || $menu['menu_type'] == '3'){echo '--------';} else { ?> <a href="javascript:createDetail(<?php echo $sSubMenu['id']; ?>)" title="确保栏目已更新">更新文档</a><?php } ?></td>
</tr>
<?php endforeach; ?>
<?php endforeach; ?>
<?php endforeach; ?>
<?php endif; ?>
</table>
<br/>
<br/>
<script language="javascript" type="text/javascript">
//生成首页html
function createIndex() {
	$.post("<?php echo base_url(); ?>admincp.php/html/createIndex",
			{"id": 1},
			function(res){
				if(res.success){
					alert(res.message);
					return false;
				}else{
					alert(res.message);
					return false;
				}
			},
			"json"
		);
}

//更新栏目
function createMenu(id) {
	$.post("<?php echo base_url(); ?>admincp.php/html/createMenu",
		{	"id": id
		},
		function(res){
			if(res.success){
				alert(res.message);
				window.location.reload();
				return false;
			}else{
				alert(res.message);
				return false;
			}
		},
		"json"
	);
}
//更新封面
function createCover(id) {
	$.post("<?php echo base_url(); ?>admincp.php/html/createCover",
		{	"id": id
		},
		function(res){
			if(res.success){
				alert(res.message);
				window.location.reload();
				return false;
			}else{
				alert(res.message);
				return false;
			}
		},
		"json"
	);
}

//更新文档
function createDetail(menuId, model) {
	$.post("<?php echo base_url(); ?>admincp.php/html/createDetail",
		{	"menu_id": menuId
		},
		function(res){
			if(res.success){
				alert(res.message);
				window.location.reload();
				return false;
			}else{
				alert(res.message);
				return false;
			}
		},
		"json"
	);
}
</script>