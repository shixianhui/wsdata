<div id="position" >
<strong>当前位置：</strong>
<a href="javascript:void(0);"><?php echo $parent_title; ?></a>
<a href="javascript:void(0);"><?php echo $title; ?>管理</a>
</div>
<br />
<table class="table_form" cellpadding="0" cellspacing="1">
  <caption>快捷方式</caption>
  <tbody>
  <tr>
    <td>
        <?php
        $index_method = empty($index_method) ? 'index' : $index_method;
        $save_method = empty($save_method) ? 'save' : $save_method;
        ?>
        <?php if (empty($is_list)) { ?>
    <?php if (!empty($is_checked)) { ?>
    <a href="javascript:void(0);"><span id="<?php echo $table; ?>_<?=$save_method?>">审核<?php echo $title; ?></span></a> |
    <?php } else { ?>
    <a href="admincp.php/<?php echo $table; ?>/<?=$save_method?><?=empty($parent_id) ? '' : '/'.$parent_id?>"><span id="<?php echo $table; ?>_<?=$save_method?>">添加<?php echo $title; ?></span></a> |
    <?php } ?>
    <?php } ?>
<!--    <a href="admincp.php/--><?php //echo $table; ?><!--/--><?//=$index_method?><!--/--><?//=empty($parent_id) ? 1 : $parent_id?><!--"><span id="--><?php //echo $table; ?><!--_--><?//=$index_method?><!--_--><?//=empty($parent_id) ? 1 : $parent_id?><!--">--><?php //echo $title; ?><!--列表</span></a>-->
    <a href="admincp.php/<?php echo $table; ?>/<?=$index_method?>/<?=empty($parent_id) ? '' : $parent_id?>"><span id="<?php echo $table; ?>_<?=$index_method?>"><?php echo $title; ?>列表</span></a>
        <?php if (!empty($navigation)){
            foreach ($navigation as $value){ ?>
                | <a href="<?=!empty($value['no_url']) ? 'javascript:void(0);' : 'admincp.php/'.$table.'/'.$value['func']?>"><span id="<?php echo $table.'_'.(empty($value['id']) ? $value['func'] : $value['id']); ?>"><?=$value['title']?></span></a>
        <?php }} ?>
    </td>
  </tr>
</tbody>
</table>
<script type="text/javascript">
    $(document).ready(function() {
        if(method == '<?=$index_method?>'){
            var uri = "<?php echo $this->uri->segment(3,'init'); ?>";
            if (uri != 'init') {
                $('#'+controller+'_'+method+'_' + uri + '').addClass('toolColor');
                $('#'+controller+'_'+method+'_' + uri + '').parent('a').siblings('a').each(function () {
                    $(this).find('span').removeClass('toolColor');
                })
            }

        }

    });
</script>