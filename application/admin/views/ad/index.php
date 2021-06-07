<?php echo $tool; ?>
<form name="search" method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
<table class="table_form" cellpadding="0" cellspacing="1">
  <caption>信息查询</caption>
<tbody>
<tr>
<td class="align_c">
<select class="input_blur" name="category_id">
<option value="">--选择广告位置--</option>
<?php if (! empty($adgroupList)): ?>
<?php foreach ($adgroupList as $adgroup): ?>
<option value="<?php echo $adgroup['id'] ?>" ><?php echo $adgroup['group_name'] ?></option>
<?php endforeach; ?>
<?php endif; ?>
</select>
<select class="input_blur" name="ad_type" id="ad_type">
<option value="">--选择广告类型--</option>
<option value="image">图片广告</option>
<option value="flash">Flash广告</option>
<option value="html">Html广告</option>
<option value="text">文字广告</option>
</select>
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
<th width="50">排序</th>
<th>广告位置</th>
<th width="100">广告类型</th>
<th width="100">广告大小</th>
<th width="100">广告状态</th>
<th width="70">管理操作</th>
</tr>
<?php if (! empty($itemList)) { ?>
<?php foreach ($itemList as $key=>$value) { ?>
<tr id="id_<?php echo $value['id']; ?>"  onMouseOver="this.style.backgroundColor='#f2f2f2'" onMouseOut="this.style.background='#FFFFFF'">
<td><input  class="checkbox_style" name="ids" value="<?php echo $value['id']; ?>" type="checkbox"> <?php echo $value['id']; ?></td>
<td class="align_c"><input class="input_blur" name="sort[]" id="sort_<?php echo $value['id']; ?>" value="<?php echo $value['sort']; ?>" size="4" type="text"></td>
<td class="align_c"><?php echo $value['group_name']; ?></td>
<td class="align_c"><?php echo $adType[$value['ad_type']]; ?></td>
<td class="align_c"><?php echo $value['width']; ?><font color="#ff0000">x</font><?php echo $value['height']; ?></td>
<td class="align_c"><?php echo $value['enable']?'开启':'<font color="red">关闭</font>'; ?></td>
<td class="align_c"><a href="admincp.php/<?php echo $template; ?>/save/<?php echo $value['id']; ?>">修改</a></td>
</tr>
<?php }} ?>
</tbody>
</table>
<div class="button_box">
<span style="width: 60px;"><a href="javascript:void(0);" onclick="javascript:$('input[type=checkbox]').prop('checked', true)">全选</a>/
<a href="javascript:void(0);" onclick="javascript:$('input[type=checkbox]').prop('checked', false)">取消</a></span>
<input class="button_style" name="list_order" id="list_order" value=" 排序 "  type="button">
<input class="button_style_red" name="delete" id="delete" value=" 删除 "  type="button">
批量移动至：
<select class="input_blur" name="category_id" id="category_id">
<option value="">--选择广告位置--</option>
<?php if (! empty($adgroupList)) { ?>
<?php foreach ($adgroupList as $adgroup) { ?>
<option value="<?php echo $adgroup['id'] ?>" ><?php echo $adgroup['group_name'] ?></option>
<?php }} ?>
</select>
</div>
<div id="pages">
<?php echo $pagination; ?>
<a>总条数：<?php echo $paginationCount; ?></a>
<!-- <a>总页数：<?php echo $pageCount; ?></!--> -->
</div>