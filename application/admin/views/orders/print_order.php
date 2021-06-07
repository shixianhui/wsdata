<table class="table_form" cellpadding="0" cellspacing="1">
  <caption>订单信息</caption>
 	<tbody>
 	<tr>
      <th width="20%">
      <strong>购买方式</strong> <br/>
      </th>
      <td style="color: red;">
      <?php if(! empty($item_info)){ echo $item_info['pay_mode']?'积分换购':'现金支付';} ?>
	</td>
    </tr>
	<tr>
      <th width="20%">
      <strong>订单编号</strong> <br/>
      </th>
      <td>
      <?php if(! empty($item_info)){ echo $item_info['order_number'];} ?>
	</td>
    </tr>
    <tr>
      <th> <strong>下单时间</strong> <br/>
	  </th>
      <td>
      <?php if(! empty($item_info)){ echo date('Y-m-d H:i:s', $item_info['add_time']);} ?>
      </td>
    </tr>
    <tr>
      <th> <strong>订单状态</strong> <br/>
	  </th>
      <td>
      <?php if(! empty($item_info)){ echo $status_arr[$item_info['status']];} ?>
      </td>
    </tr>
    <?php if(! empty($item_info) && $item_info['status'] == 5){ ?>
    <tr>
      <th> <strong>交易关闭原因</strong> <br/>
	  </th>
      <td>
      <?php if(! empty($item_info)){ echo $item_info['cancel_cause'];} ?>
      </td>
    </tr>
    <?php } ?>
</tbody>
</table>
<table class="table_form" cellpadding="0" cellspacing="1">
  <caption>买家信息</caption>
 	<tbody>
	<tr>
      <th width="20%">
      <strong>会员名</strong> <br/>
	  </th>
      <td width="30%">
      <?php if($user_info){ echo $user_info['username'];} ?>
	</td>
	<th width="20%">
      <strong>会员等级</strong> <br/>
	  </th>
      <td>
      <?php if($user_info){ echo $user_info['group_name'];} ?>
	</td>
    </tr>
    <tr>
      <th> <strong>昵称</strong> <br/>
	  </th>
      <td>
      <?php if($user_info){ echo $user_info['nickname'];} ?>
      </td>
      <th>
      <strong>真实姓名</strong> <br/>
	  </th>
      <td>
      <?php if($user_info){ echo $user_info['real_name'];} ?>
	</td>
    </tr>
    <tr>
      <th> <strong>手机</strong> <br/>
	  </th>
      <td>
      <?php if($user_info){ echo $user_info['mobile'];} ?>
      </td>
      <th>
      <strong>性别</strong> <br/>
	  </th>
      <td>
      <?php if($user_info){ echo $sex_arr[$user_info['sex']];} ?>
	</td>
    </tr>
    <tr>
      <th> <strong>QQ号</strong> <br/>
	  </th>
      <td>
      <?php if($user_info){ echo $user_info['qq_number'];} ?>
      </td>
      <th>
      <strong>邮件</strong> <br/>
	  </th>
      <td>
      <?php if($user_info){ echo $user_info['email'];} ?>
	</td>
    </tr>
</tbody>
</table>
<table class="table_form" cellpadding="0" cellspacing="1">
  <caption>商品信息</caption>
 	<tbody>
	<tr>
	<th class="align_c" width="60" scope="col">&nbsp;</th>
    <th class="align_c" scope="col"><strong>宝贝</strong></th>
    <th class="align_c" scope="col" width="150"><strong>宝贝属性</strong></th>
    <th class="align_c" scope="col" width="90"><strong>数量</strong></th>
    <th class="align_c" scope="col" width="90"><strong>单价</strong></th>
    <th class="align_c" scope="col" width="90"><strong>换购积分</strong></th>
  </tr>
  <?php if ($orders_detail_list) {
        foreach ($orders_detail_list as $key=>$orderdetail) {
  	?>
  <tr>
    <td class="align_c">
    <img src="<?php if ($orderdetail['path']){ echo preg_replace('/\./', '_thumb.', $orderdetail['path']);;}else{echo 'images/admin/nopic.gif';} ?>" width="50px" height="50px" />
    </td>
    <td>
    <?php echo $orderdetail['product_title']; ?><br/>产品编号：<?php echo $orderdetail['product_num']; ?></td>
    <td>
    <?php if ($orderdetail['color_size_open']) { ?>
    <?php echo $orderdetail['product_color_name']; ?>：<?php echo $orderdetail['color_name']; ?><br/><?php echo $orderdetail['product_size_name']; ?>：<?php echo $orderdetail['size_name']; ?>
    <?php } ?>
    </td>
     <td class="align_c"><?php echo $orderdetail['buy_number']; ?></td>
    <td class="align_c">¥<?php echo $orderdetail['buy_price']; ?></td>
    <td class="align_c"><?php echo $orderdetail['consume_score']; ?></td>
  </tr>
  <?php }} ?>
  <?php if ($item_info) { ?>
  <?php if ($item_info['pay_mode'] == 0) { ?>
  <tr>
    <td colspan="6" style="text-align:right;height:25px;">商品总价：<span style="font-size:14px;margin-right:20px;font-family:微软雅黑,Helvetica,Arial,sans-serif;">+ ￥<?php if(! empty($item_info)){ echo $item_info['product_total'];} ?></span></td>
  </tr>
  <?php } else { ?>
  <tr>
    <td colspan="6" style="text-align:right;height:25px;" title="应付：<?php if ($item_info) {echo $item_info['use_deductible_score_gold'];} ?>金象积分、<?php if ($item_info) {echo $item_info['use_deductible_score_silver'];} ?>银象积分">积分换购：<span style="font-size:14px;margin-right:20px;font-family:微软雅黑,Helvetica,Arial,sans-serif;"><?php if(! empty($item_info)){ echo $item_info['deductible_score'];} ?>积分</span></td>
  </tr>
  <?php }} ?>
  <tr>
    <td colspan="6" style="text-align:right;height:25px;">运费：<span style="font-size:14px;margin-right:20px;font-family:微软雅黑,Helvetica,Arial,sans-serif;">+ ￥<?php if(! empty($item_info)){ echo $item_info['postage_price'];} ?></span></td>
  </tr>
  <tr>
    <td colspan="6" style="text-align:right;height:25px;">优惠：<span style="font-size:14px;margin-right:20px;font-family:微软雅黑,Helvetica,Arial,sans-serif;">- ￥<?php if(! empty($item_info)){ echo $item_info['discount_total'];} ?></span></td>
  </tr>
  <tr>
    <td colspan="6" style="text-align:right;height:25px;">支付手续费：<span style="font-size:14px;margin-right:20px;font-family:微软雅黑,Helvetica,Arial,sans-serif;">+ ￥<?php if(! empty($item_info)){ echo $item_info['payment_price'];} ?></span></td>
  </tr>
  <tr>
    <td colspan="6" style="text-align:right;height:40px;">订单交易成功可获赠积分：<span style="color:#E36439;margin-right:50px;"><?php if ($item_info) {echo $item_info['gold_score'];} ?>金象积分、<?php if ($item_info) {echo $item_info['silver_score'];} ?>银象积分</span>实收款：
    <?php if ($item_info) { ?>
    <?php if ($item_info['pay_mode'] > 0) { ?>
    <span style="font-size:18px; font-weight:bold;color:#E36439;font-family:微软雅黑,Helvetica,Arial,sans-serif;">￥<?php if(! empty($item_info)){ echo $item_info['total'];} ?></span><span style="font-size:18px; font-weight:bold;color:#E36439;margin-right:20px;font-family:微软雅黑,Helvetica,Arial,sans-serif;" title="实付：<?php if ($item_info){echo $item_info['deductible_score_gold'].'金象积分、'.$item_info['deductible_score_silver'].'银象积分';} ?>"><?php echo "+{$item_info['deductible_score']}积分"; ?></span>
    <?php } else { ?>
    <span style="font-size:18px; font-weight:bold;color:#E36439;margin-right:20px;font-family:微软雅黑,Helvetica,Arial,sans-serif;">￥<?php if(! empty($item_info)){ echo $item_info['total'];} ?></span>
    <?php }} ?>
    </td>
  </tr>
</tbody>
</table>
<table class="table_form" cellpadding="0" cellspacing="1">
  <caption>收货与物流信息</caption>
 	<tbody>
	<tr>
      <th width="20%"> <strong>收货人姓名</strong> <br/>
	  </th>
      <td>
      <?php if(! empty($item_info)){ echo $item_info['buyer_name'];} ?>
      </td>
    </tr>
    <tr>
      <th width="20%"> <strong>手机号</strong> <br/>
	  </th>
      <td>
      <?php if(! empty($item_info)){ echo $item_info['mobile'];} ?>
      </td>
    </tr>
    <tr>
      <th width="20%"> <strong>固定电话</strong> <br/>
	  </th>
      <td>
      <?php if(! empty($item_info)){ echo $item_info['phone'];} ?>
      </td>
    </tr>
    <tr>
      <th width="20%"> <strong>邮编</strong> <br/>
	  </th>
      <td>
      <?php if(! empty($item_info)){ echo $item_info['zip'];} ?>
      </td>
    </tr>
	<tr>
      <th width="20%"><strong>收货地址</strong> <br/>
	  </th>
      <td>
      <?php if(! empty($item_info)){ echo $item_info['txt_address'].$item_info['address'];} ?>&nbsp;
     </td>
    </tr>
    <?php if(! empty($item_info) && $item_info['invoice']){ ?>
    <tr>
      <th width="20%"> <strong><font color="#ff4200">发票抬头</font></strong> <br/>
	  </th>
      <td>
      <?php echo $item_info['invoice']; ?>
      </td>
    </tr>
    <?php } ?>
    <tr>
      <th width="20%"> <strong>订单附言</strong> <br/>
	  </th>
      <td>
      <?php if(! empty($item_info)){ echo $item_info['remark'];} ?>
      </td>
    </tr>
    <tr>
      <th width="20%"> <strong>运送方式</strong> <br/>
	  </th>
      <td>
      <?php if(! empty($item_info)){ echo $item_info['postage_title'];} ?>
      </td>
    </tr>
	<tr>
      <th width="20%">
      <strong>物流公司名称</strong> <br/>
	  </th>
      <td>
      <?php if(! empty($item_info)){ echo $item_info['delivery_name'];} ?>
      </td>
    </tr>
	<tr>
      <th width="20%"> <strong>运单号</strong> <br/>
	  </th>
      <td>
      <?php if(! empty($item_info)){ echo $item_info['express_number'];} ?>
      </td>
    </tr>
</tbody>
</table>
</div>
<br/>
