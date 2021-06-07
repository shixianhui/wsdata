<?php echo $tool; ?>
<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
<table class="table_form" cellpadding="0" cellspacing="1">
  <caption>信息查询</caption>
<tbody>
<tr>
<td class="align_c">
订单编号 <input class="input_blur" name="order_number" id="order_number" size="20" type="text">&nbsp;
店铺名 <input class="input_blur" name="store_name" id="store_name" size="20" type="text">&nbsp;
<label><input class="input_blur" name="is_reward" id="is_reward" size="20" type="checkbox" value="1"> 佣金订单</label>&nbsp;
<select class="input_blur" name="product_type">
<option value="">选择产品类型</option>
<option value="0">套餐</option>
<option value="1">吆喝</option>
</select>&nbsp;
发布时间 <input class="input_blur" name="inputdate_start" id="inputdate_start" size="10" readonly="readonly" type="text">&nbsp;-&nbsp;<input class="input_blur" name="inputdate_end" id="inputdate_end" size="10"  readonly="readonly" type="text">&nbsp;
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
<th width="60">选中</th>
<th width="120">订单编号</th>
<th width="150">购买人信息</th>
<th>订单描述</th>
<th width="120">总金额</th>
<th width="80">下单时间</th>
<th width="80">订单状态</th>
<th width="100">管理操作</th>
</tr>
<?php if (! empty($item_list)): ?>
<?php foreach ($item_list as $key=>$value): ?>
<tr id="id_<?php echo $value['id']; ?>"  onMouseOver="this.style.backgroundColor='#ECF7FE'" onMouseOut="this.style.background=''">
<td><input  class="checkbox_style" name="ids" value="<?php echo $value['id']; ?>" type="checkbox">  <?php echo $value['id']; ?></td>
<td class="align_c">
    <?php echo $value['order_number']; ?><br/>
</td>
<td class="align_c"><?php echo $value['mobile']; ?>[ID:<?=$value['user_id']?>]</td>
<td class="align_c">
<table  width="100%" cellpadding="0" cellspacing="1">
<?php if ($value['order_detail_list']) { ?>
<?php foreach ($value['order_detail_list'] as $key=>$orderdetail) {
	  $strClass = 'table_td';
      if ($key+1 == count($value['order_detail_list'])) {
          $strClass = '';
      }
	?>
<tr>
<td class="align_c" width="60" class="<?php echo $strClass; ?>">
<img src="<?php if ($orderdetail['item_image']){ echo preg_match('/^http/', $orderdetail['item_image']) ? $orderdetail['item_image'] : preg_replace('/\./', '_thumb.', $orderdetail['item_image']);}else{echo 'images/admin/nopic.gif';} ?>" width="50px" height="50px" />
</td>
<td class="<?php echo $strClass; ?>">
<span><?php echo $orderdetail['item_name']; ?></span><br/>
<span style="color: #999;"><?=$orderdetail['item_type'] ? '吆喝':'套餐'?></span><br/>
<span>[<?=$value['store_name']?>]</span><br/>

</td>
<td class="<?php echo $strClass; ?>" style="width: 100px;text-align:center;" title="单价">¥<?php echo floatval($orderdetail['sell_price']); ?></td>
<td class="<?php echo $strClass; ?>" style="width: 20px;text-align:center;" title="购买数量"><?php echo $orderdetail['buy_number']; ?></td>
<td class="<?php echo $strClass; ?>" style="width: 100px;text-align:center;" title="佣金"><?php echo floatval($orderdetail['reward']); ?></td>
</tr>
<?php }} ?>
</table>
</td>
<td class="align_c">
<span class="priceColor">¥<?php echo $value['total']; ?></span>
</td>
<td class="align_c"><?php echo $value['create_time']; ?></td>
<td class="align_c"><?php echo $status_arr[$value['status']]; ?></td>
<td class="align_c">
<?php if ($value['status'] == 0) { ?>
<span style="line-height:25px;"><a onclick="javascript:change_pay(<?php echo $value['id']; ?>,'<?php echo $value['order_number']; ?>','<?php echo $value['create_time']; ?>','<?php echo $value['total']; ?>');" href="javascript:void(0);" title="用于线下付款修改订单状态">设为已付款</a></span><br/>
<span style="line-height:25px;"><a onclick="javascript:change_price(<?php echo $value['id']; ?>,'<?php echo $value['order_number']; ?>','<?php echo $value['create_time']; ?>','<?php echo $value['total']; ?>');" href="javascript:void(0);">修改金额</a></span><br/>
<span style="line-height:25px;"><a onclick="javascript:close_order(<?php echo $value['id']; ?>,'<?php echo $value['order_number']; ?>','<?php echo $value['create_time']; ?>','<?php echo $value['total']; ?>');" href="javascript:void(0);">交易关闭</a></span><br/>
<?php } else if ($value['status'] == 1) { ?>
<span style="line-height:25px;"><a onclick="javascript:delivery(<?php echo $value['id']; ?>,'<?php echo $value['order_number']; ?>','<?php echo $value['create_time']; ?>','<?php echo $value['total']; ?>');" href="javascript:void(0);">设为已使用</a></span><br/>
<?php } ?>
<span style="line-height:25px;"><a href="admincp.php/<?php echo $table; ?>/view/<?php echo $value['id']; ?>">详情</a></span>
</td>
</tr>
<?php endforeach; ?>
<?php endif; ?>
</tbody>
</table>
<div class="button_box">
<span style="width: 60px;"><a href="javascript:void(0);" onclick="javascript:$('input[type=checkbox]').prop('checked', true)">全选</a>/
<a href="javascript:void(0);" onclick="javascript:$('input[type=checkbox]').prop('checked', false)">取消</a></span>
<input class="button_style" name="delete" id="delete" value=" 删除 "  type="button">
</div>
<div id="pages" style="margin-top: 5px;">
<?php echo $pagination; ?>
<a>总条数：<?php echo $paginationCount; ?></a>
<!-- <a>总页数：<?php echo $pageCount; ?></a> -->
</div>
<br/><br/>
<script  type="text/javascript">
//修改金额
function change_price(id, order_num, add_time, total) {
	var html = '<table class="table_form" cellpadding="0" cellspacing="1">';
		html += '<tr><th width="30%"><strong>订单编号</strong> <br/></th>';
		html += '<td>'+order_num+'</td></tr>';
		html += '<tr><th width="30%"><strong>下单时间</strong> <br/></th>';
		html += '<td>'+add_time+'</td></tr>';
		html += '<tr><th width="30%"><strong>订单金额</strong> <br/></th>';
		html += '<td>'+total+'元</td></tr>';
		html += '<tr><th width="30%"> <strong>订单金额修改为</strong> <br/></th>';
		html += '<td>';
		html += '<input id="order_total" value="'+total+'" size="10" class="input_blur" type="text" /> 元';
		html += '</td>';
		html += '</tr>';
		html += '</table>';
	var d = dialog({
		width:350,
		fixed: true,
	    title: '修改订单金额提示',
	    content: html,
	    okValue: '确认修改订单金额',
	    ok: function () {
		    var order_total = $('#order_total').val();
		    if (!order_total) {
                return my_alert('order_total', 1, '订单金额不能为空');
			}
			if (total == order_total) {
				return my_alert('order_total', 1, '修改金额不能跟原金额一样');
			}
	        $.post(base_url+"admincp.php/"+controller+"/change_price",
    				{	"id": id,
				        "order_total":order_total
    				},
    				function(res){
    					if(res.success){
                            return my_alert_flush('fail', 0, res.message);
    					} else {
        					if (res.field == 'fail') {
        						return my_alert('fail', 0, res.message);
            				} else {
            					return my_alert(res.field, 1, res.message);
                		    }
    					}
    				},
    				"json"
    		);
		    return false;
	    },
	    cancelValue: '取消',
	    cancel: function () {
	    }
	});
	d.show();
}

//关闭交易
function close_order(id, order_num, add_time, total) {
	var html = '<table class="table_form" cellpadding="0" cellspacing="1">';
		html += '<tr><th width="30%"><strong>订单编号</strong> <br/></th>';
		html += '<td>'+order_num+'</td></tr>';
		html += '<tr><th width="30%"><strong>下单时间</strong> <br/></th>';
		html += '<td>'+add_time+'</td></tr>';
		html += '<tr><th width="30%"><strong>运费</strong> <br/></th>';
		html += '<td>'+total+'元</td></tr>';
		html += '<tr><th width="30%"> <strong>关闭原因</strong> <br/></th>';
		html += '<td>';
		html += '<textarea id="cancel_cause" rows="4" cols="30"  class="textarea_style"></textarea>';
		html += '</td>';
		html += '</tr>';
		html += '</table>';
	var d = dialog({
		width:350,
		fixed: true,
	    title: '关闭交易提示',
	    content: html,
	    okValue: '确认关闭交易',
	    ok: function () {
		    var cancel_cause = $('#cancel_cause').val();
		    if (!cancel_cause) {
                return my_alert('cancel_cause', 1, '请输入关闭原因');
			}
	        $.post(base_url+"admincp.php/"+controller+"/close_order",
    				{	"id": id,
				        "cancel_cause":cancel_cause
    				},
    				function(res){
    					if(res.success){
                            return my_alert_flush('fail', 0, res.message);
    					} else {
        					if (res.field == 'fail') {
        						return my_alert('fail', 0, res.message);
            				} else {
            					return my_alert(res.field, 1, res.message);
                		    }
    					}
    				},
    				"json"
    		);
		    return false;
	    },
	    cancelValue: '取消',
	    cancel: function () {
	    }
	});
	d.show();
}

//状态设为已付款
function change_pay(id, order_num, add_time, total) {
	var html = '<font color="red">注：确定要将订单状态修改为已付款吗，请确认已线下收到客户的打款？</font>';
	    html += '<table class="table_form" cellpadding="0" cellspacing="1">';
		html += '<tr><th width="30%"><strong>订单编号</strong> <br/></th>';
		html += '<td>'+order_num+'</td></tr>';
		html += '<tr><th width="30%"><strong>下单时间</strong> <br/></th>';
		html += '<td>'+add_time+'</td></tr>';
		html += '<tr><th width="30%"><strong>订单金额</strong> <br/></th>';
		html += '<td>'+total+'元</td></tr>';
		html += '<tr><th width="30%"> <strong>备注</strong> <br/></th>';
		html += '<td>';
		html += '<textarea id="remark" placeholder="请输入打款凭证" rows="4" cols="35"  class="textarea_style"></textarea>';
		html += '</td>';
		html += '</tr>';
		html += '</table>';
	var d = dialog({
		width:400,
		fixed: true,
	    title: '修改订单状态提示',
	    content: html,
	    okValue: '确认',
	    ok: function () {
		    var remark = $('#remark').val();
		    if (!remark) {
                return my_alert('remark', 1, '请输入确认');
			}
	        $.post(base_url+"admincp.php/"+controller+"/change_pay",
    				{	"id": id,
				        "remark":remark
    				},
    				function(res){
    					if(res.success){
                            return my_alert_flush('fail', 0, res.message);
    					} else {
        					if (res.field == 'fail') {
        						return my_alert('fail', 0, res.message);
            				} else {
            					return my_alert(res.field, 1, res.message);
                		    }
    					}
    				},
    				"json"
    		);
		    return false;
	    },
	    cancelValue: '取消',
	    cancel: function () {
	    }
	});
	d.show();
}

//发货
function delivery(id, order_num, add_time, total) {
	var html = '<table class="table_form" cellpadding="0" cellspacing="1">';
		html += '<tr><th width="25%"><strong>订单编号</strong> <br/></th>';
		html += '<td>'+order_num+'</td></tr>';
		html += '<tr><th width="25%"><strong>下单时间</strong> <br/></th>';
		html += '<td>'+add_time+'</td></tr>';
		html += '<tr><th width="25%"><strong>订单金额</strong> <br/></th>';
		html += '<td>'+total+'元</td></tr>';
		html += '<tr><th width="25%"> <strong>备注</strong> <br/></th>';
		html += '<td>';
		html += '<textarea id="remark" placeholder="请输入备注" rows="4" cols="35"  class="textarea_style"></textarea>';
		html += '</td>';
		html += '</tr>';
		html += '</table>';
	var d = dialog({
		width:400,
		fixed: true,
	    title: '修改订单状态提示',
	    content: html,
	    okValue: '确认',
	    ok: function () {
		    var remark = $('#remark').val();
	        $.post(base_url+"admincp.php/"+controller+"/delivery",
    				{	"id": id,
				        "remark":remark
    				},
    				function(res){
    					if(res.success){
                            return my_alert_flush('fail', 0, res.message);
    					} else {
        					if (res.field == 'fail') {
        						return my_alert('fail', 0, res.message);
            				} else {
            					return my_alert(res.field, 1, res.message);
                		    }
    					}
    				},
    				"json"
    		);
		    return false;
	    },
	    cancelValue: '取消',
	    cancel: function () {
	    }
	});
	d.show();
}

//确认收货
function receiving(id, order_num, add_time, total) {
	var html = '<font color="red">注：确认对此订单进行收货操作？</font>';
    html += '<table class="table_form" cellpadding="0" cellspacing="1">';
	html += '<tr><th width="25%"><strong>订单编号</strong> <br/></th>';
	html += '<td>'+order_num+'</td></tr>';
	html += '<tr><th width="25%"><strong>下单时间</strong> <br/></th>';
	html += '<td>'+add_time+'</td></tr>';
	html += '<tr><th width="25%"><strong>订单金额</strong> <br/></th>';
	html += '<td>'+total+'元</td></tr>';
	html += '</table>';
	var d = dialog({
		width:300,
		fixed: true,
	    title: '确认收货提示',
	    content: html,
	    okValue: '确认收货',
	    ok: function () {
			$.post(base_url+"admincp.php/"+controller+"/receiving",
					{	"id":id
					},
					function(res){
						if(res.success) {
							return my_alert_flush('fail', 0, res.message);
						} else {
							if (res.field == 'fail') {
        						return my_alert('fail', 0, res.message);
            				} else {
            					return my_alert(res.field, 1, res.message);
                		    }
						}
					},
					"json"
			);
	    },
	    cancelValue: '取消',
	    cancel: function () {
	    }
	});
	d.show();
}
</script>