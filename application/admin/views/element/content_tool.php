<div id="position" >
<strong>当前位置：</strong>
<a href="javascript:void(0);">内容管理</a>
<?php $patternInfo = $this->advdbclass->getControllerName($template); ?>
<a href="javascript:void(0);"><?php echo $patternInfo['title']; ?></a>
</div>
<br />
<table class="table_form" cellpadding="0" cellspacing="1">
  <caption>快捷方式</caption>
  <tbody>
  <tr>
    <td>
    <?php if ($template == 'guestbook') { ?>
    <a href="javascript:void(0);"><span id="<?php echo $template; ?>_save">回复<?php echo $patternInfo['alias']; ?></span></a> |
    <?php } else { ?>
    <a href="admincp.php/<?php echo $template; ?>/save"><span id="<?php echo $template; ?>_save">添加<?php echo $patternInfo['alias']; ?></span></a> |
    <?php } ?>    
    <a href="admincp.php/<?php echo $template; ?>"><span id="<?php echo $template; ?>_"><?php echo $patternInfo['alias']; ?>列表</span></a>
<?php
    $systemInfo = $this->advdbclass->getSystem();
    if ($template != 'job' && $template != 'link' && $template != 'guestbook' && $template != 'ask' && $systemInfo['html']) { ?>
    |
    <a href="admincp.php/<?php echo $template; ?>/html"><span id="<?php echo $template; ?>_html">更新html</span></a>
    <?php } ?>
    </td>
  </tr>
</tbody>
</table>