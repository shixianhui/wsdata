<style>
	.b_form li {
		margin-top: 15px;
	}

	.store_view {
		background: #c81624 none repeat scroll 0 0;
		color: #fff;
		cursor: pointer;
		font-size: 16px;
		margin-right: 6px;
		padding: 6px 8px;
		margin-left: 20px;
	}
</style>
<div class="member_right mt20">
	<div class="box_shadow clearfix m_border">
		<div class="b_title">
			<div class="hd">
				<ul>
					<li>我的店铺</li>
				</ul>
			</div>
			<div class="bd">
				<?php if ($item_info) { ?>
				<?php if ($item_info['status'] == 0) { ?>
				<ul class="b_form">
					<Li><span>申请时间：</span>
						<font>
							<?php echo date('Y-m-d H:i', $item_info['create_time']); ?>
						</font>
					</Li>
					<Li><span>审核进程：</span>
						<font>审核中[我们会尽快处理您的申请]</font>
					</Li>
				</ul>
				<?php } else if ($item_info['status'] == 2) { ?>
				<ul class="b_form">
					<Li><span>审核进度：</span>
						<font>审核暂未通过</font>
					</Li>
					<Li><span>拒绝原因：</span>
						<font>
							<?php echo $item_info['close_reason']; ?>
						</font>
					</Li>
					<Li><span></span>
						<a href="<?php echo getBaseUrl(false," ","seller/my_join/{$item_info[ 'id']}.html ",$client_index);?>" class="b_btn">重新申请</a>
					</Li>
				</ul>
				<?php } else if ($item_info['status'] == 3) { ?>
				<ul class="b_form">
					<Li><span>店铺状态：</span>
						<font>关闭</font>
					</Li>
					<Li><span>关闭原因：</span>
						<font>
							<?php echo $item_info['close_reason']; ?>
						</font>
					</Li>
				</ul>
				<?php } else { ?>
				<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" name="jsonForm" id="jsonForm">
					<ul class="b_form">
                        <style>
                            #mcxq-1 li .main .img{width: 138px;height:138px;float: left;}
                            #mcxq-1 li .main .img img{width: 138px;height:138px;}
                            #mcxq-1 li .main .dec{margin-left: 10px;padding:0;float: left;width: 461px;}
                            #mcxq-1 li .main .up_load{position: absolute;top: 110px;left: 150px;}

                            .mcxq-2 li{width: 100%;}
                            .mcxq-2 li .title{width: 10%;overflow: hidden;float: left;}
                            .mcxq-2 li .title h5{color: #676767;font-size: 16px;font-weight: 400;margin-top: 50px;}
                            .mcxq-2 li .title h6{color: #676767;font-size: 16px;font-weight: 400;}
                            .mcxq-2 li .main{width: 90%;overflow: hidden;float: left;position: relative;}
                            .mcxq-2 li .main .img{width: 500px;height: 150px;}
                            .mcxq-2 li .main .img img{width: 500px;height: 150px;}
                            .mcxq-2 li .main .up_load{position: absolute;top: 120px;right: 240px;}
                            .mcxq-2 li .main .dec{margin-left: 0;padding:10px 210px 10px 0;width: auto;}
                        </style>
                        <div class="mcxq-2" id="mcxq-1">
                            <li>
                                <div class="title">
                                    <h5>店铺LOGO:</h5>
                                </div>
                                <div class="main">
                                    <div class="img"><a data-lightbox="image_list_group_1" data-title="店铺logo" href="<?php if($item_info){ echo $item_info['logo'];}?>"><img style="width: 138px;height:138px;" src="<?php if($item_info){ echo $item_info['logo'];}?>" onerror="this.src='images/default/default.png';" id="path"></a></div>
                                    <div class="dec">更换店标：此处为您的列表页店铺LOGO，将显示在商家店铺列表里，建议原图尺寸138*138像素。 支持图片格式 jpg、gif、png，且文件小于5M。</div>
                                    <a href="javascript:void(0)" class="up_load fl">本地上传<input style="filter:alpha(opacity=0);-moz-opacity:0;opacity:0;" type="file" accept=".gif,.jpg,.jpeg,.png" name="store_logo" model="store_logo" class="path_file"></a>
                                    <input type="hidden" name="path" value="<?php if($item_info){ echo $item_info['logo'];}?>">
                                </div>
                            </li>
                        </div>
                        <!-- <div class="mcxq-2">
                            <li>
                                <div class="title">
                                    <h5>店铺条幅:</h5>
                                    <h6>（PC端）</h6>
                                </div>
                                <div class="main">
                                    <div class="img"><a data-lightbox="image_list_group_2" data-title="广告图片效果" href="<?php if($item_info){ echo $item_info['store_banner'];}?>"><img src="<?php if($item_info){ echo $item_info['store_banner'];}?>" onerror="this.src='images/default/mall_intro_bg.jpg';" id="store_banner"></a></div>
                                    <a href="javascript:void(0)" class="up_load fl">本地上传<input style="filter:alpha(opacity=0);-moz-opacity:0;opacity:0;" type="file" accept=".gif,.jpg,.jpeg,.png" name="banner" model="store_banner" class="path_file"></a>
                                    <div class="dec">更换店铺条幅：此处为您的 店铺条幅，将显示在店铺导航上方的banner位置，建议原图尺寸1920*150像素，具体根据店铺主题对应调整。 支持图片格式 jpg、gif、png，且文件小于5M。</div>
                                    <input type="hidden" name="store_banner" value="<?php if($item_info){ echo $item_info['store_banner'];}?>">
                                </div>
                            </li>
                            <li>
                                <div class="title">
                                    <h5>店铺条幅:</h5>
                                    <h6>（移动端）</h6>
                                </div>
                                <div class="main">
                                <div class="img">
                                    <a data-lightbox="image_list_group_2" data-title="广告图片效果" href="<?php if($item_info){ echo $item_info['app_banner'];}?>"><img src="<?php if($item_info){ echo $item_info['app_banner'];}?>" onerror="this.src='images/default/mall_intro_bg.jpg';" id="app_banner"></a></div>
                                    <a href="javascript:void(0)" class="up_load fl">本地上传<input style="filter:alpha(opacity=0);-moz-opacity:0;opacity:0;" type="file" accept=".gif,.jpg,.jpeg,.png" name="banner" model="app_banner" class="path_file"></a>
                                <div class="dec">更换店铺条幅：此处为您的 APP和小程序店铺条幅，将显示在店铺导航上方的banner位置，建议原图尺寸750*300像素，具体根据店铺主题对应调整。 支持图片格式 jpg、gif、png，且文件小于5M。</div>
                                <input type="hidden" name="app_banner" value="<?php if($item_info){ echo $item_info['app_banner'];}?>">
                                </div>
                            </li>
                        </div> -->
						<Li><span>店铺名称：</span><input id="store_name" name="store_name" type="text" value="<?php if ($item_info) {echo $item_info['store_name']; } ?>" class="input_1" maxlength="100" disabled>
						</Li>
						<Li><span>店铺介绍：</span><textarea name="description" id="description" cols="" rows="" class="textarea_1"><?php if ($item_info) {echo $item_info['description'];} ?></textarea><em class="tip">还可输入50字</em></Li>
                        <!-- <Li><span>经营范围：</span><input type="text" id="business_scope" name="business_scope" class="input_1" style="width:560px;" value="<?php if ($item_info) {echo $item_info['business_scope']; } ?>" maxlength="255"></Li> -->
                        <!-- <Li><span>所在地区：</span>
							<div class="b_select">
								<div class="dropdown">
									<select id="province_id" name="province_id" onchange="javascript:get_city('province_id','city_id',0,0,1);" valid="required" errmsg="请选择省">
										<option value="">-省份-</option>
										<?php if ($province_list) { ?>
										<?php foreach ($province_list as $province) {
								              	$selector = '';
								              	if ($item_info) {
								              		if ($item_info['province_id'] == $province['id']) {
								              			$selector = 'selected="selected"';
								              		}
								              	}
							              	?>
										<option <?php echo $selector; ?> value="<?php echo $province['id']; ?>"><?php echo $province['name']; ?></option>
										<?php }} ?>
									</select>
								</div>
							</div>
							<div class="b_select">
								<div class="dropdown">
									<select id="city_id" name="city_id" valid="required" errmsg="请选择市" onchange="javascript:get_city('city_id','area_id',0,0,0);">
										<option value="">-市/县-</option>
									</select>
								</div>
							</div>
							<div class="b_select">
								<div class="dropdown">
									<select id="area_id" name="area_id" valid="required" errmsg="请选择区">
										<option value="">-区-</option>
									</select>
								</div>
							</div>
							<font>*</font>
						</Li> -->
						<Li><span>详细地址：</span><input type="text" id="address" name="address" class="input_1" style="width:560px;" value="<?php if ($item_info) {echo $item_info['address']; } ?>" maxlength="255"></Li>
						</Li>
						<Li><span>联系电话：</span><input id="phone" name="phone" type="text" value="<?php if ($item_info) {echo $item_info['phone'];} ?>" class="input_1" valid="isContact" errmsg="联系电话格式有误" placeholder="手机或者固话"></Li>
                        <Li><span>营业时间：</span><input id="business_hours" name="business_hours" type="text" value="<?php if ($item_info) {echo $item_info['business_hours'];} ?>" class="input_1" placeholder="示例：9:00 - 24:00"></Li>
						<Li><span>营业状态：</span>
						<?php if($business_status_arr){ 
							foreach($business_status_arr as $key=>$value){?>
						<label><input name="business_status" value="<?=$key?>" type="radio" size="10" <?php if($item_info['business_status'] == $key){ echo 'checked="checked"';} ?>> <?=$value?></label>
						<?php }} ?>
                </li>
					</Li>
                        <Li><span>人均消费：</span><input id="per_amount" name="per_amount" type="text" value="<?php if ($item_info) {echo $item_info['per_amount'];} ?>" class="input_1" placeholder="示例：50元/人"> <font style="display: inline-block;color:#676767;font-size:12px;">元/人</font></Li>


<!--                        <li><span style="float: none;vertical-align: bottom">身份证正面照片：</span>-->
<!--                            <div style="width: 142px;height:80px;float: none;display: inline-block" class="picture"><a data-lightbox="image_list_group_1" data-title="身份证正面照片" href="--><?php //if($item_info){ echo $item_info['id_card_path_1'];}?><!--"><img style="width: 142px;height: 80px;" src="--><?php //if($item_info){ echo $item_info['id_card_path_1'];}?><!--" onerror="this.src='images/default/default.png';" id="id_card_path_1"></a></div>-->
<!--                            <a href="javascript:void(0)" class="up_load" style="vertical-align: bottom">本地上传<input style="filter:alpha(opacity=0);-moz-opacity:0;opacity:0;" type="file" accept=".gif,.jpg,.jpeg,.png" name="id_card_1" model="id_card_path_1" class="path_file"></a>-->
<!--                            <input type="hidden" name="id_card_path_1" value="--><?php //if($item_info){ echo $item_info['id_card_path_1'];}?><!--">-->
<!--                        </li>-->
<!--                        <li><span style="float: none;vertical-align: bottom">身份证反面照片：</span>-->
<!--                            <div style="width: 142px;height: 80px;float: none;display: inline-block" class="picture"><a data-lightbox="image_list_group_1" data-title="身份证反面照片" href="--><?php //if($item_info){ echo $item_info['id_card_path_2'];}?><!--"><img style="width: 142px;height: 80px;" src="--><?php //if($item_info){ echo $item_info['id_card_path_2'];}?><!--" onerror="this.src='images/default/default.png';" id="id_card_path_2"></a></div>-->
<!--                            <a href="javascript:void(0)" class="up_load" style="vertical-align: bottom">本地上传<input style="filter:alpha(opacity=0);-moz-opacity:0;opacity:0;" type="file" accept=".gif,.jpg,.jpeg,.png" name="id_card_2" model="id_card_path_2" class="path_file"></a>-->
<!--                            <input type="hidden" name="id_card_path_2" value="--><?php //if($item_info){ echo $item_info['id_card_path_2'];}?><!--">-->
<!--                        </li>-->

						<Li style="margin-top: 30px">
							<a href="javascript:void(0)" class="b_btn" onclick="$('#jsonForm').submit();">确认提交</a>
						</Li>
					</ul>
				</form>
				<?php }
} ?>
			</div>
		</div>
	</div>
</div>
<link rel="stylesheet" href="js/admin/lightbox2-master/src/css/lightbox.css">
<script src="js/admin/lightbox2-master/src/js/lightbox.js"></script>
<script>
    lightbox.option({
      'resizeDuration': 500,
      'wrapAround': true
    })
</script>
<script language="javascript" type="text/javascript">
	<?php if ($item_info) { ?>
	get_city('province_id', 'city_id', '<?php echo $item_info['city_id']; ?>', '<?php echo $item_info['province_id']; ?>', 1);
	<?php } ?>
	//上传图像
	$(".path_file").wrap("<form class='path_upload' action='" + base_url + "index.php/upload/uploadImage' method='post' enctype='multipart/form-data'></form>");
	$(".path_file").change(function() { //选择文件
		var model = $(this).attr('model');
		var field = $(this).attr('name');
		$(this).parents('.path_upload').ajaxSubmit({
			dataType: 'json',
			data: {
				'model': model,
				'field': field
			},
			beforeSend: function() {
				$('body').append($('<div id="loading"></div>'));
			},
			uploadProgress: function(event, position, total, percentComplete) {},
			success: function(res) {
				$("#loading").remove();
				if(res.success) {
					model = model == 'store_logo' ? 'path' : model;
					$("#" + model).attr('src', res.data.file_path);
					$("#" + model).parent().attr('href', res.data.file_path);
					$('input[name="' + model + '"]').val(res.data.file_path);
				} else {
					var d = dialog({
						fixed: true,
						title: '提示',
						content: res.message
					});
					d.show();
					setTimeout(function() {
						d.close().remove();
					}, 2000);
				}
			},
			error: function(xhr) {}
		});
	});
 
</script>