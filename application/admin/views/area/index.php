<?php echo $tool; ?>
<table cellspacing="1" cellpadding="0">
  <tbody>
  <tr>
    <td>
    <strong>当前分类：</strong><?php echo $location; ?>
    </td>
  </tr>
</tbody>
</table>
<table class="table_list" id="news_list" cellpadding="0" cellspacing="1">
<caption>信息管理</caption>
<tbody>
<tr class="mouseover">
<th width="100">选中</th>
<th width="100">排序</th>
<th>名称</th>
<th width="50">状态</th>
<th width="70">管理操作</th>
</tr>
<?php if (! empty($itemList)): ?>
<?php foreach ($itemList as $key=>$value): ?>
<tr id="id_<?php echo $value['id']; ?>"  onMouseOver="this.style.backgroundColor='#ECF7FE'" onMouseOut="this.style.background='#FFFFFF'">
<td><input  class="checkbox_style" name="ids" value="<?php echo $value['id']; ?>" type="checkbox"> <?php echo $value['id']; ?></td>
<td class="align_c"><input class="input_blur" name="sort[]" id="sort_<?php echo $value['id']; ?>" value="<?php echo $value['sort']; ?>" size="4" type="text"></td>
<td><a href="admincp.php/<?php echo $table; ?>/index/<?php echo $value['id']; ?>"><?php echo $value['name'] ?></a></td>
<td class="align_c"><?php echo $value['display']?'显示':'<font color="#FF0000">隐藏</font>'; ?></td>
<td class="align_c"><a href="admincp.php/<?php echo $table; ?>/save/<?php echo $value['id']; ?>">修改</a></td>
</tr>
<?php endforeach; ?>
<?php endif; ?>
</tbody>
</table>
<div class="button_box">
<span style="width: 60px;"><a href="javascript:void(0);" onclick="javascript:$('input[type=checkbox]').prop('checked', true)">全选</a>/
<a href="javascript:void(0);" onclick="javascript:$('input[type=checkbox]').prop('checked', false)">取消</a></span>
<input class="button_style" name="list_order" id="list_order" value=" 排序 "  type="button">
<input class="button_style" name="delete" id="delete" value=" 删除 "  type="button">
<select class="input_blur" name="select_display" id="select_display" onchange="#">
<option value="">选择状态</option>
<option value="1">显示</option>
<option value="0">隐藏</option>
</select>
</div>
<br/>
<br/>