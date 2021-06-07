<?php echo $tool; ?>
<table class="table_list" id="news_list" cellpadding="0" cellspacing="1">
<caption>信息管理</caption>
<tbody>
<tr class="mouseover">
<th width="70">选中</th>
    <th width="50">排序</th>
    <th>名称</th>
<th>英文代码</th>
<th width="70">管理操作</th>
</tr>
<?php if (! empty($item_list)): ?>
<?php foreach ($item_list as $key=>$value): ?>
        <tr id="id_<?php echo $value['id']; ?>"  onMouseOver="this.style.backgroundColor='#ECF7FE'" onMouseOut="this.style.background=''">
            <td><input  class="checkbox_style" name="ids" value="<?php echo $value['id']; ?>" type="checkbox"> <?php echo $value['id']; ?></td>
            <td class="align_c"><input style="background-color:#E0E0E0;" class="input_blur" name="sort[]" id="sort_<?php echo $value['id']; ?>" value="<?php echo $value['sort']; ?>" size="4" type="text"></td>
            <td style="padding-left: <?php echo $value['level'] * 15; ?>px;" ><?php if ($value['level'] > 1){ echo '┣';} ?><?php echo $value['name']; ?></td>
<td class="align_c"><?php echo $value['en_name']; ?></td>
<td class="align_c"><a href="admincp.php/<?php echo $table; ?>/save/<?php echo $value['id']; ?>">修改</a></td>
</tr>
<?php endforeach; ?>
<?php endif; ?>
</tbody>
</table>
<div class="button_box">
<span style="width: 60px;"><a href="javascript:void(0);" onclick="javascript:$('input[type=checkbox]').prop('checked', true)">全选</a>/
<a href="javascript:void(0);" onclick="javascript:$('input[type=checkbox]').prop('checked', false)">取消</a></span>
<input style="margin-left: 10px;" class="button_style" name="list_order" id="list_order" value=" 排序 "  type="button">
<input style="margin-left: 10px;" class="button_style" name="delete" id="delete" value=" 删除 "  type="button">
</div>
<br/><br/>