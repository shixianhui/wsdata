<?php echo $tool; ?>
<table class="table_list" id="news_list" cellpadding="0" cellspacing="1">
<caption>数据库表信息管理</caption>
<tbody>
<tr class="mouseover">
<th width="6%">选中</th>
<th>表名</th>
<th width="10%">记录数</th>
</tr>
<form method="post" action="admincp.php/backup/backupDatabase" enctype="multipart/form-data">
<?php if (! empty($allTableList)): ?>
<?php foreach ($allTableList as $key=>$value): ?>
<tr onMouseOver="this.style.backgroundColor='#f2f2f2'" onMouseOut="this.style.background='#FFFFFF'">
<td><input  class="checkbox_style" name="ids[]" value="<?php echo $value['table_name']; ?>" type="checkbox"> <?php echo ($key+1); ?></td>
<td><?php echo $value['table_name']; ?></td>
<td class="align_c"><?php echo $value['table_rows'] ?></td>
</tr>
<?php endforeach; ?>
<?php endif; ?>
</tbody>
</table>
<div class="button_box">
<span style="width: 60px;"><a href="javascript:void(0);" onclick="javascript:$('input[type=checkbox]').prop('checked', true)">全选</a>/
<a href="javascript:void(0);" onclick="javascript:$('input[type=checkbox]').prop('checked', false)">取消</a></span>
<input class="button_style" id="optimize_tables" value=" 批量优化 "  type="button">
<input class="button_style" id="repair_tables" value=" 批量修复 "  type="button">
<input class="button_style" name="backup_tables" value=" 备份所选表 "  type="submit">
</form>
</div>