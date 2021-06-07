<div class="member_right mt20">
	<div class="box_shadow clearfix m_border">
		<div class="member_title"><span class="bt">新增账号</span><span style="float: right;font-size:16px;"><a href="<?php echo getBaseUrl(false, '', 'seller/my_get_seller_list.html', $client_index) ?>" style="color:#333;">返回</a></span></div>
		<form action="<?php echo $_SERVER['REQUEST_URI'];?>" method="post" name="jsonForm" id="jsonForm">
			<div class="b_shop_update">
				<ul class="m_form fl">
					<li class="clearfix"><span>用户名：</span>
                        <input class="input_txt" name="username" id="username" value="<?php if(! empty($item_info)){ echo $item_info['username'];} ?>" <?php if(! empty($item_info)){ echo 'readonly="true"';} ?> size="50" maxlength="50" valid="required" errmsg="用户名不能为空!" type="text">
					</li>
					<li class="clearfix"><span>密码：</span><input class="input_txt" name="password" id="password" value="" size="50" maxlength="50" <?php if(empty($item_info)){ echo 'valid="required" errmsg="密码不能为空!"';} ?> type="password"></li>
					<li class="clearfix"><span>确认密码：</span><input class="input_txt" name="ref_password" id="ref_password" value="" size="50" maxlength="50" valid="eqaul" eqaulName="password" errmsg="前后密码不一致!" type="password"></li>
					<li class="clearfix"><span>所属部门：</span>
                        <div class="xm-select fl">
                            <div class="dropdown">
                                <label class="iconfont" for="seller_group_id"></label>
                        <select name="seller_group_id"  valid="required" errmsg="请选择部门!">
                            <option value="">选择部门</option>
                            <?php if ($group_name_arr) { ?>
                                <?php foreach ($group_name_arr as $key=>$value) { ?>
                                    <option value="<?php echo $key; ?>" <?php if (! empty($item_info) && $item_info['seller_group_id'] == $key){ echo "selected = 'selected'"; } ?>><?php echo $value; ?></option>
                                <?php }} ?>
                        </select>
                            </div>
                        </div>
                    </li>
                    <li class="clearfix"><span>昵称：</span>
                        <input class="input_txt" name="nickname" id="nickname" value="<?php if(! empty($item_info)){ echo $item_info['nickname'];} ?>" size="50" type="text">
                    </li>
                    <li class="clearfix"><span>真实姓名：</span>
                        <input class="input_txt" name="real_name" id="real_name" value="<?php if(! empty($item_info)){ echo $item_info['real_name'];} ?>" size="50" type="text">
                    </li>
                    <li class="clearfix"><span>手机：</span>
                        <input class="input_txt" name="mobile" id="mobile" value="<?php if(! empty($item_info)){ echo $item_info['mobile'];} ?>" size="50" valid="isMobile" errmsg="手机号格式错误!" type="text">
                    </li>
				</ul>
			</div>
			<div class="clear"></div>
			<div style="margin:20px 0px 20px 200px; clear:both; display:block;">
				<a href="javascript:void(0);" onclick="$('#jsonForm').submit();" class="b_btn">确认提交</a>
			</div>
		</form>
	</div>
</div>