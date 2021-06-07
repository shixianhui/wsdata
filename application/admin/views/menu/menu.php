<html>
<head>
	<base href="<?php echo base_url(); ?>">
	<link rel="stylesheet" href="css/admin/menu.css">
	<script src="js/admin/jquery-1.4.2.min.js"></script>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<!-- <script type="text/javascript">
	  var cookieName = 'cwsaMenuCookie';

	  function setCookie(Cookie, value, expiredays)
	  {
	          var ExpireDate = new Date ();
	          ExpireDate.setTime(ExpireDate.getTime() + (expiredays * 24 * 3600 * 1000));
	          document.cookie = Cookie + "=" + escape(value) +
	          ((expiredays == null) ? "" : "; expires=" + ExpireDate.toGMTString());
	  }

	  function getCookie(Cookie)
	  {
	          if (document.cookie.length > 0)
	          {
	                  begin = document.cookie.indexOf(Cookie+"=");
	                  if (begin != -1)
	                  {
	                          begin += Cookie.length+1;
	                          end = document.cookie.indexOf(";", begin);
	                          if (end == -1) end = document.cookie.length;
	                          return unescape(document.cookie.substring(begin, end));
	                  }
	          }
	          return null;
	  }

	function SaveMenu()
	  {
	          var cookiestring = '';

	          for(i = 0; i < divNames.length; i++)
	          {
	                  var block = document.getElementById('div_' + divNames[i]);

	                  if(block.style.display != 'none')
	                  {
	                          cookiestring += divNames[i] + '|';
	                  }
	          }

	          setCookie(cookieName,cookiestring,1);
	  }

	function nav_goto(targeturl)
	  {
	          parent.frames.mainFrame.location = targeturl;
	  }


	 </script> -->
	<script type="text/javascript">
		$(function () {
		  //左侧菜单效果
		    $('#nav li').click(function (event) {
				if($(this).children('.sub-menu').length){
		            if($(this).hasClass('open')){
		                $(this).removeClass('open');
		                $(this).children('.sub-menu').stop(true,true).slideUp();
//		                $(this).siblings().children('.sub-menu').slideUp();
		            }else{
		                $(this).addClass('open');
		                $(this).children('.sub-menu').stop(true,true).slideDown();
//		                $(this).siblings().children('.sub-menu').stop(true,true).slideUp();
//		                $(this).siblings().removeClass('open');
		            }
		        }
		        event.stopPropagation();
		    })
		})
        function add_class(obj) {
            $("#nav").find("a").removeClass("a_cur");
            $(obj).attr("class", "a_cur");
        }
	</script>
</head>
<body>
<?php $admingroupInfo = $this->advdbclass->getPermissionsStr(get_cookie('admin_group_id'));
if ($admingroupInfo) {
?>
	<ul id="nav">
	 	<li style="display: none;">
			<!-- 频道管理开始 -->
		    <?php if (isPermissions($admingroupInfo, 'menu_add') || isPermissions($admingroupInfo, 'menu_menuList')) { ?>
			<a href="javascript:;" class="menu-title">
				<i class="menu ico-size"></i>
				<span>频道管理</span>
				<i class="icon-right"></i>
			</a>
			<!-- 二级菜单 -->
			<ul class="sub-menu" style="display: none;">
			 	<li>
					<?php if (isPermissions($admingroupInfo, 'menu_add')) { ?>
					<a href="admincp.php/menu/save"  target="main-frame" onclick="add_class(this)">添加栏目</a>
					<?php } ?>
			 	</li>
				<li>
					<?php if (isPermissions($admingroupInfo, 'menu_menuList')) { ?>
					<a href="admincp.php/menu/menuList"  target="main-frame" onclick="add_class(this)">栏目列表</a>
					<?php } ?>
			  	</li>
			</ul>
			<?php } ?>
	  	</li>
	 	<li>
		    <!-- 内容管理开始 -->
		    <?php if ($patternList) {
		        $is_ok = false;
		        foreach ($patternList as $key=>$value) {
		            if (isPermissions($admingroupInfo, $value['file_name'].'_index')) {
		                $is_ok = true;
		                break;}}
		        if ($is_ok) {
		        ?>
				<a href="javascript:;" class="menu-title">
					<i class="content ico-size"></i>
					<span>内容管理</span>
					<i class="icon-right"></i>
				</a>
				<ul class="sub-menu" style="display: none;">
					<?php if ($patternList) { ?>
					<?php foreach ($patternList as $pattern) { ?>
					<?php if (isPermissions($admingroupInfo, $pattern['file_name'].'_index')) { ?>
					<li>
						<a href="admincp.php/<?php echo $pattern['file_name']; ?>" target="main-frame" onclick="add_class(this)"><?php echo $pattern['title']; ?></a>
					</li>
					<?php }}} ?>
			    </ul>
			<?php }} ?>
		</li>
		<li class="open">
            <!-- 交易管理开始 -->
                <a href="javascript:;" class="menu-title">
                    <i class="product ico-size"></i>
                    <span>交易管理</span>
                    <i class="icon-right"></i>
                </a>
                <ul class="sub-menu">
                        <li>
                            <a href="admincp.php/orders/index" target="main-frame" onclick="add_class(this)">订单管理</a>
                        </li>
						<li>
                            <a href="admincp.php/withdrawal_record/index" target="main-frame" onclick="add_class(this)">提现记录</a>
                        </li>
						<li>
                            <a href="admincp.php/withdrawal_record/store_index" target="main-frame" onclick="add_class(this)">商家提现记录</a>
                        </li>
						<li>
                            <a href="admincp.php/coupons/index" target="main-frame" onclick="add_class(this)">优惠券管理</a>
                        </li>
                </ul>
        </li>
        <li class="open">
            <!-- 商品活动开始 -->
                <a href="javascript:;" class="menu-title">
                    <i class="product ico-size"></i>
                    <span>商户管理</span>
                    <i class="icon-right"></i>
                </a>
                <ul class="sub-menu">
                        <li>
                            <a href="admincp.php/store_type/index" target="main-frame" onclick="add_class(this)">商户分类</a>
                        </li>
                        <li>
                            <a href="admincp.php/stores/index" target="main-frame" onclick="add_class(this)">商户管理</a>
						</li>
						<li>
                            <a href="admincp.php/combos/index" target="main-frame" onclick="add_class(this)">套餐商品管理</a>
                        </li>
						<li>
                            <a href="admincp.php/share_goods/index" target="main-frame" onclick="add_class(this)">吆喝商品管理</a>
                        </li>
						<li>
                            <a href="admincp.php/tags/index" target="main-frame" onclick="add_class(this)">商品标签</a>
                        </li>
                </ul>
        </li>

	 	<li>
			 <!-- 会员管理开始 -->
			 <?php if (isPermissions($admingroupInfo, 'user_index') || isPermissions($admingroupInfo, 'usergroup_index') || isPermissions($admingroupInfo, 'store_index')) { ?>
			 <a href="javascript:;" class="menu-title">
			 	<i class="user ico-size"></i>
		        <span>会员管理</span>
				<i class="icon-right"></i>
			</a>
			<ul class="sub-menu" style="display: none;">
				<?php if (isPermissions($admingroupInfo, 'user_index')) { ?>
				<li>
					<a href="admincp.php/user" target="main-frame" onclick="add_class(this)">会员列表</a>
				</li>
				<?php } ?>
				<?php if (isPermissions($admingroupInfo, 'usergroup_index')) { ?>
<!--				<li>-->
<!--					<a href="admincp.php/usergroup" target="main-frame" onclick="add_class(this)">会员组列表</a>-->
<!--				</li>-->
				<?php } ?>
                <?php if (isPermissions($admingroupInfo, 'store_index')) { ?>
                    <li>
                        <a href="admincp.php/store/index/1" target="main-frame" onclick="add_class(this)">商户列表</a>
                    </li>
                <?php } ?>

                <?php if (isPermissions($admingroupInfo, 'withdraw_cash_record_index')) { ?>
                    <li>
                        <a href="admincp.php/withdraw_cash_record/index/1" target="main-frame" onclick="add_class(this)">提现申请列表</a>
                    </li>
                <?php } ?>
			</ul>
			<?php } ?>
		</li>
 	    <li>
			<!-- 管理员开始 -->
			<?php if (isPermissions($admingroupInfo, 'admin_index') || isPermissions($admingroupInfo, 'admingroup_index')) { ?>
			    <a href="javascript:;" class="menu-title">
			    	<i class="admin ico-size"></i>
					<span>超管管理</span>
					<i class="icon-right"></i>
				</a>
				<ul class="sub-menu" style="display: none;">
				<?php if (isPermissions($admingroupInfo, 'admin_index')) { ?>
					<li>
						<a href="admincp.php/admin" target="main-frame" onclick="add_class(this)">管理员列表</a>
					</li>
				<?php } ?>
				<?php if (isPermissions($admingroupInfo, 'admingroup_index')) { ?>
					<li class="menulink-normal">
						<a href="admincp.php/admingroup" target="main-frame" onclick="add_class(this)">管理组列表</a>
					</li>
				<?php } ?>
			</ul>
			<?php } ?>
		</li>
	 	<li style="display: none">
			<!-- 生成静态开始 -->
			<?php if (isPermissions($admingroupInfo, 'html_index')) { ?>
				<a href="javascript:;" class="menu-title">
					<i class="html ico-size"></i>
					<span>生成静态</span>
				    <i class="icon-right"></i>
				</a>
				<ul class="sub-menu" style="display: none;">
					<li>
						<a href="admincp.php/html" target="main-frame" onclick="add_class(this)">生成html页面</a>
					</li>
				</ul>
			<?php } ?>
		</li>
	 	<li>
			<!-- 广告管理开始 -->
			<?php if (isPermissions($admingroupInfo, 'ad_index') || isPermissions($admingroupInfo, 'adgroup_index')) { ?>
			  
				<a href="javascript:;" class="menu-title">
					<i class="ad ico-size"></i>
				   	<span>广告管理</span>
					<i class="icon-right"></i>
				</a>
				<ul class="sub-menu" style="display: none;">
				    <?php if (isPermissions($admingroupInfo, 'ad_index')) { ?>
					<li>
						<a href="admincp.php/ad" target="main-frame" onclick="add_class(this)">广告内容管理</a>
					</li>
					<?php } ?>
					<?php if (isPermissions($admingroupInfo, 'adgroup_index')) { ?>
					<li class="menulink-normal">
						<a href="admincp.php/adgroup" target="main-frame" onclick="add_class(this)">广告位管理</a>
					</li>
					<?php } ?>
				</ul>
			<?php } ?>
		</li>
	 	<li>
			<!-- 系统设置开始 -->
			<?php if (isPermissions($admingroupInfo, 'system_save') || isPermissions($admingroupInfo, 'watermark_save') || isPermissions($admingroupInfo, 'range_value_save') || isPermissions($admingroupInfo, 'trade_type_index')) { ?>
	     		<a href="javascript:;" class="menu-title">
	     			<i class="system_settings ico-size"></i>
				  	<span>系统设置</span>
				  	<i class="icon-right"></i>
			  	</a>
	        	<ul class="sub-menu" style="display: none;">
	         		<?php if (isPermissions($admingroupInfo, 'system_save')) { ?>
					<li>
					    <a href="admincp.php/system/save" target="main-frame" onclick="add_class(this)">基本设置</a>
					</li>
			    	<?php } ?>

			    	<?php if (isPermissions($admingroupInfo, 'watermark_save')) { ?>
					<li>
					    <a href="admincp.php/watermark/save" target="main-frame" onclick="add_class(this)">图片水印设置</a>
					</li>
			    	<?php } ?>
			    </ul>
	        <?php } ?>
		</li>
	 	<li>
			<!-- 系统维护开始 -->
			<?php if (isPermissions($admingroupInfo, 'backup_index') || isPermissions($admingroupInfo, 'file_index')) { ?>
			    <a href="javascript:;" class="menu-title">
			    	<i class="system_maintenance ico-size"></i>
					<span>系统维护</span>
					<i class="icon-right"></i>
				</a>
		        <ul class="sub-menu" style="display: none;">
		            <?php if (isPermissions($admingroupInfo, 'backup_index')) { ?>
					<li>
						<a href="admincp.php/backup" target="main-frame" onclick="add_class(this)">数据库备份</a>
					</li>
					<?php } ?>
					<?php if (isPermissions($admingroupInfo, 'file_index')) { ?>
					<li>
						<a href="admincp.php/file" target="main-frame" onclick="add_class(this)">文件管理</a>
					</li>
				    <?php } ?>
				</ul>
		    <?php } ?>
		</li>
	 	<li>
			<!-- 系统登录日志开始 -->
			<?php if (isPermissions($admingroupInfo, 'systemloginlog_index')) { ?>
		     	<a href="javascript:;" class="menu-title">
		     		<i class="system_login_log ico-size"></i>
					<span>登录日志</span>
					<i class="icon-right"></i>
				</a>
		        <ul class="sub-menu" style="display: none;">
				    <li>
				   		<a href="admincp.php/systemloginlog" target="main-frame" onclick="add_class(this)">登录日志</a>
				    </li>
				</ul>
		    <?php } ?>
		</li>
	</ul>  	
<?php } ?>
</body>
</html>