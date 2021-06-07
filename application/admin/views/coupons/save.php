<?php echo $tool; ?>
<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>" enctype="multipart/form-data" id="jsonForm" >
<table class="table_form" cellpadding="0" cellspacing="1">
  <caption>基本信息</caption>
 	<tbody>
	<tr>
      <th width="20%">
          <strong>标题</strong> <br/>
	  </th>
      <td>
      <input name="title" id="title" value="<?php if(! empty($item_info)){ echo $item_info['title'];} ?>" size="80" type="text">
	</td>
    </tr>
    <tr>
        <th width="20%">
            <strong>获取途径</strong> <br/>
        </th>
        <td>
            <?php if ($way_arr){
                foreach ($way_arr as $key=>$value){ ?>
            <label><input type="radio" class="radio_style" name="way" onclick="select_way(<?=$key?>)" value="<?=$key?>" <?php if ((empty($item_info) && $key == 0) || ($item_info && $item_info['way'] == $key)){ echo 'checked="checked"';}?>> <?=$value?></label>
            <?php }}?>
        </td>
    </tr>
    <tr>
        <th width="20%">
            <strong>类型</strong> <br/>
        </th>
        <td>
            <?php if ($type_arr){
                foreach ($type_arr as $key=>$value){ ?>
            <label><input type="radio" class="radio_style" name="type" value="<?=$key?>" <?php if ((empty($item_info) && $key == 0) || ($item_info && $item_info['type'] == $key)){ echo 'checked="checked"';}?>> <?=$value?></label>
            <?php }}?>
        </td>
    </tr>
    <tr>
        <th width="20%">
            <strong>满多少金额</strong> <br/>
        </th>
        <td>
            <input name="achieve_amount" id="achieve_amount" value="<?php if(! empty($item_info)){ echo $item_info['achieve_amount'];} ?>" size="20" type="text"> 元
        </td>
    </tr>
    <tr>
        <th width="20%">
            <strong>券值金额</strong> <br/>
        </th>
        <td>
            <input name="used_amount" id="used_amount" value="<?php if(! empty($item_info)){ echo $item_info['used_amount'];} ?>" size="20" type="text"> 元
        </td>
    </tr>
    <tr id="valid_days_tr" <?php if(empty($item_info) || $item_info['way'] == 0){ echo "style='display:none;'";} ?>>
        <th width="20%">
            <strong>有效天数</strong> <br/>
        </th>
        <td>
            <input name="valid_days" id="valid_days" value="<?php if(! empty($item_info)){ echo $item_info['valid_days'];} ?>" size="20" type="text"> 天
        </td>
    </tr>
    <tr id="valid_time_tr" <?php if(!empty($item_info) && $item_info['way'] != 0){ echo "style='display:none;'";} ?>>
        <th width="20%">
            <strong>有效时间</strong> <br/>
        </th>
        <td>
            <input class="input_blur" name="start_time" id="start_time"  size="21" readonly="readonly" type="text" value="<?php if(! empty($item_info)){ echo $item_info['start_time'] > 0 ? date('Y-m-d', strtotime($item_info['start_time'])) : '';} ?>"/>&nbsp;
            至&nbsp;<input class="input_blur" name="end_time" id="end_time"  size="21" readonly="readonly" type="text" value="<?php if(! empty($item_info)){ echo $item_info['end_time'] > 0 ? date('Y-m-d', strtotime($item_info['end_time'])) : '';} ?>"/>
        </td>
    </tr>
    <tr>
      <th width="20%">
          <strong>适用商品ID</strong> <br/>
	  </th>
      <td>
      <input name="usable_goods_ids" id="usable_goods_ids" value="<?php if(! empty($item_info)){ echo $item_info['usable_goods_ids'];} ?>" size="80" type="text">
      <font color="red">*多个商品id用英文逗号‘,’相隔，如'1,2,3'</font>
	</td>
    </tr>
    <tr>
        <th width="20%">
            <strong>状态</strong> <br/>
        </th>
        <td>
            <select class="input_blur" name="status" id="status">
                <option value="">选择状态</option>
                <option value="1" <?php if ($item_info && $item_info['status'] == 1){ echo 'selected="selected"'; }?>>可用</option>
                <option value="0" <?php if ($item_info && $item_info['status'] == 0){ echo 'selected="selected"'; }?>>禁用</option>
            </select>
        </td>
    </tr>
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
<script>
    function select_way(way) {
        if (way == 0) {
            $('#valid_time_tr').show();
            $('#valid_days_tr').hide();
        } else {
            $('#valid_days_tr').show();
            $('#valid_time_tr').hide();
        }
    }
</script>