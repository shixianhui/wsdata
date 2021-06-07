<?php echo $tool; ?>
<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
<table class="table_form" cellpadding="0" cellspacing="1">
  <caption>信息查询</caption>
<tbody>
<tr>
<td class="align_c">标题 <input class="input_blur" name="title" id="title" size="20" type="text">&nbsp;
<select class="input_blur" name="select_category_id" onchange="#">
<option value="">--选择栏目--</option>
<?php if (! empty($menuList)): ?>
<!-- 一级 -->
<?php foreach ($menuList as $menu): ?>
<option value="<?php echo $menu['id']; ?>"><?php echo $menu['menu_name']; ?></option>
<!-- 二级 -->
<?php foreach ($menu['subMenuList'] as $subMenu): ?>
<option value="<?php echo $subMenu['id']; ?>">&nbsp;&nbsp;|-<?php echo $subMenu['menu_name']; ?></option>
<!-- 三级 -->
<?php foreach ($subMenu['subMenuList'] as $sSubMenu): ?>
<option value="<?php echo $sSubMenu['id']; ?>">&nbsp;&nbsp;&nbsp;&nbsp;|-<?php echo $sSubMenu['menu_name']; ?></option>
<?php endforeach; ?>
<?php endforeach; ?>
<?php endforeach; ?>
<?php endif;?>
</select>&nbsp;
发布时间 <input class="input_blur" name="inputdate_start" id="inputdate_start" size="10" readonly="readonly" type="text">&nbsp;<script language="javascript" type="text/javascript">
					date = new Date();
					Calendar.setup({
						inputField     :    "inputdate_start",
						ifFormat       :    "%Y-%m-%d",
						showsTime      :    false,
						timeFormat     :    "24"
					});
				 </script> - <input class="input_blur" name="inputdate_end"
id="inputdate_end" size="10"  readonly="readonly" type="text">&nbsp;<script language="javascript" type="text/javascript">
					date = new Date();
					Calendar.setup({
						inputField     :    "inputdate_end",
						ifFormat       :    "%Y-%m-%d",
						showsTime      :    false,
						timeFormat     :    "24"
					});
				 </script>&nbsp;
<input class="button_style" name="dosubmit" value=" 查询 " type="submit">
</td>
</tr>
</tbody>
</table>
</form>
<table class="table_list" id="news_list" cellpadding="0" cellspacing="1">
<caption>信息管理</caption>
<tbody>
<tr class="mouseover">
<th width="70">选中</th>
<th>标题</th>
<th width="150">栏目</th>
<th width="100">html状态</th>
<th width="120">发布时间</th>
<th width="70">管理操作</th>
</tr>
<?php if (! empty($itemList)): ?>
<?php foreach ($itemList as $key=>$value): ?>
<tr id="id_<?php echo $value['id']; ?>"  onMouseOver="this.style.backgroundColor='#ECF7FE'" onMouseOut="this.style.background='#FFFFFF'">
<td><input  class="checkbox_style" name="ids" value="<?php echo $value['id']; ?>" type="checkbox"> <?php echo $value['id']; ?></td>
<td><?php echo $value['title']; ?></td>
<td class="align_c"><?php echo $value['menu_name'] ?></td>
<td class="align_c"><?php echo $value['display']?'已生成':'<font color="#FF0000">未生成</font>'; ?></td>
<td class="align_c"><?php echo date("Y-m-d H:i", $value['add_time']); ?></td>
<td class="align_c"><a target="_blank" href="<?php echo base_url(); ?>/<?php echo $value['html_path']; ?>/<?php echo $value['id']; ?>.html">查看</a></td>
</tr>
<?php endforeach; ?>
<?php endif; ?>
</tbody>
</table>
<div class="button_box">
<span style="width: 60px;"><a href="javascript:void(0);" onclick="javascript:$('input[type=checkbox]').prop('checked', true)">全选</a>/
<a href="javascript:void(0);" onclick="javascript:$('input[type=checkbox]').prop('checked', false)">取消</a></span>
<input class="button_style" name="html_update" id="html_update" value=" 更新html "  type="button">
<input class="button_style" name="html_delete" id="html_delete" value=" 删除html"  type="button">
</div>
<div id="pages">
<?php echo $pagination; ?>
<a>总条数：<?php echo $paginationCount; ?></a>
<!-- <a>总页数：<?php echo $pageCount; ?></a> -->
</div>