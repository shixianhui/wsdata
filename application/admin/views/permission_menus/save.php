<?php echo $tool; ?>
<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>" enctype="multipart/form-data" id="jsonForm" >
<table class="table_form" cellpadding="0" cellspacing="1">
  <caption>基本信息</caption>
 	<tbody>
    <tr>
        <th width="20%"><strong>上级权限</strong> <br/>
        </th>
        <td>
            <select class="input_blur" name="parent_id" id="parent_id">
                <option value="0" >请选择上级权限</option>
                <?php if (!empty($item_list)) { ?>
                    <?php
                    foreach ($item_list as $key => $value) {
                        $selector = '';
                        if ($item_info) {
                            if ($item_info['parent_id'] == $value['id']) {
                                $selector = 'selected="selected"';
                            }
                        } else {
                            if ($value['id'] == $tmp_parent_id) {
                                $selector = 'selected="selected"';
                            }
                        }
                        ?>
                        <option <?php echo $selector; ?> value="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></option>
                    <?php }} ?>
            </select>
        </td>
    </tr>
	<tr>
      <th width="20%">
      <font color="red">*</font> <strong>权限名称</strong> <br/>
	  </th>
      <td>
      <input valid="required" errmsg="权限名称不能为空!" name="name" id="name" value="<?php if(! empty($item_info)){ echo $item_info['name'];} ?>" size="40" type="text">
<!--      <br/><font color="red">注：中间加“|”分隔符可以实现批量添加，格式如：“韩都衣舍|南极人|秋水伊人|初语|欧时力”</font>-->
	</td>
    </tr>
    <tr>
      <th width="20%">
      <strong>英文代码</strong> <br/>
	  </th>
      <td>
      <input name="en_name" id="en_name" value="<?php if(! empty($item_info)){ echo $item_info['en_name'];} ?>" size="40" type="text">
      </td>
    </tr>
    <tr>
        <th width="20%">
            <strong>目录显示</strong> <br/>
        </th>
        <td>
            <label><input name="display" value="0" type="radio" size="10" <?php if(empty($item_info) || $item_info['display'] == 0){ echo 'checked="checked"';} ?>> 不显示</label>
            <label><input name="display" value="1" type="radio" size="10" <?php if(! empty($item_info) && $item_info['display'] == 1){ echo 'checked="checked"';} ?>> 显示</label>
        </td>
    </tr>
    <tr>
        <th width="20%">
            <strong>方法名</strong> <br/>
        </th>
        <td>
            <input name="index_function" id="index_function" value="<?php if(! empty($item_info)){ echo $item_info['index_function'];}else{ echo 'index';} ?>" size="40" type="text">
        </td>
    </tr>
    <tr>
        <th width="20%">
            <strong>权限列表</strong> <br/>
        </th>
        <td>
            <label><textarea name="permissions_value" cols="30" rows="5"><?php if(! empty($item_info) && $item_info['permissions_value']){ echo $item_info['permissions_value'];}else{ echo '查看列表:index
添加:add
修改:edit
删除:delete';} ?></textarea></label>
        </td>
    </tr>
<!--    <tr>-->
<!--      <th width="20%"><strong>排序</strong> <br/>-->
<!--	  </th>-->
<!--      <td>-->
<!--      <input name="sort" id="sort" value="--><?php //if(! empty($item_info)){ echo $item_info['sort'];}else{ echo '0';} ?><!--" size="20" type="text">-->
<!--	</td>-->
<!--    </tr>-->
    <tr>
      <td>&nbsp;</td>
      <td>
	  <input class="button_style" name="dosubmit" value=" 保存 " type="submit">
	  &nbsp;&nbsp; <input onclick="javascript:window.location.href='<?php echo $prfUrl; ?>';" class="button_style" name="reset" value=" 返回 " type="button">
	  </td>
    </tr>
</tbody>
</table>
</form>
