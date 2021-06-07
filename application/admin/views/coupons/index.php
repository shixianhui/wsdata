<?php echo $tool; ?>
<table class="table_list" id="news_list" cellpadding="0" cellspacing="1">
<caption>信息管理</caption>
<tbody>
<tr class="mouseover">
<th width="70">选中</th>
<th width="150">标题</th>
<th width="150">类型</th>
<th width="150">获取途径</th>
<th>详情</th>
<th width="100">发放数量</th>
<th width="100">使用数量</th>
<th width="100">状态</th>
<th width="150">管理操作</th>
</tr>
<?php if (! empty($item_list)): ?>
<?php foreach ($item_list as $key=>$value): ?>
<tr id="id_<?php echo $value['id']; ?>"  onMouseOver="this.style.backgroundColor='#ECF7FE'" onMouseOut="this.style.background=''">
<td><input  class="checkbox_style" name="ids" value="<?php echo $value['id']; ?>" type="checkbox"> <?php echo $value['id']; ?></td>
<td class="align_c"><?php echo $value['title']; ?></td>
<td class="align_c"><?php echo $type_arr[$value['type']]; ?></td>
<td class="align_c"><?php echo $way_arr[$value['way']]; ?></td>
    <td class="align_c">
        <?php echo $value['type'] ? $value['used_amount'].' 元' : '满'.$value['achieve_amount'].'减'.$value['used_amount']; ?>
        <br/>
        <?php if($value['way'] == 0) { ?>
        有效期：<?=$value['start_time'].'至'.$value['end_time'] ?>
        <?php } else { ?>
        有效天数：<?=$value['valid_days']?>
        <?php } ?>
    </td>
    <td class="align_c"><?php echo $value['get_number']; ?></td>
    <td class="align_c"><?php echo $value['used_number']; ?></td>
    <td class="align_c"><?php echo $value['status'] ? '可用' : '禁用'; ?></td>
<td class="align_c">
<a href="admincp.php/<?php echo $table; ?>/save/<?php echo $value['id']; ?>">修改</a> | 
<a href="admincp.php/<?php echo $table; ?>/send/<?php echo $value['id']; ?>">发放</a>
</td>
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
    <select class="input_blur" name="select_display" id="select_display">
        <option value="">选择状态</option>
        <option value="1">可用</option>
        <option value="0">禁用</option>
    </select>
</div>
<br/><br/>