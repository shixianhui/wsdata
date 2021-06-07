<script src="js/default/jquery.form.js"></script>
<script src="js/default/formvalid.js" type="text/javascript"></script>
<script src="js/default/index.js" type="text/javascript"></script>
<section class="warp">
<div class="log_box">
<h3>有账号，快速登录</h3>
 <ul class="log_form">
 <form action="<?php echo getBaseUrl(false, "user/login.html", "user/login.html", $client_index); ?>" method="post" id="jsonForm" name="jsonForm">
    <Li><input valid="required" errmsg="用户名不能为空" name="username" id="username" type="text" placeholder="手机号/邮箱" class="log_txt"></Li>
    <Li><input valid="required" errmsg="登录密码不能为空" name="password" id="password" type="password" placeholder="请输入密码" class="log_txt"></Li>
    <li ><a onclick="javascript:$('form').submit();" href="javascript:void(0);" class="btn_r">立即登录</a>
    <a href="<?php echo getBaseUrl($html,'user/reg.html','user/reg.html',$client_index);?>" class="btn_r" style="margin-left: 20px;background:#fff;color:#cc0011;border:1px solid #cc0011;">立即注册</a>
   </li>
    <li>
       <!-- <label><input type="checkbox" name="remember" value="1">记住用户名</label> <a href="<?php echo getBaseUrl($html,'user/get_pass.html','user/get_pass.html',$client_index);?>" class="f12 c9">忘记密码?</a><font color="#ccc"> |</font>  -->
   </li>
 </form>
    <!-- <div class="other_log mt20" ><span class="c9 f14">—  第三方账号登录  —</span> -->
    <!-- <p> -->
    <!-- <a href="sdk/authlogin/qqlogin/oauth" class="qq_icon icon"></a>
    <a href="https://open.weixin.qq.com/connect/qrconnect?appid=wx3ee6137b544586f2&redirect_uri=<?php echo urlencode(base_url().'index.php/user/weixin_login')?>&response_type=code&scope=snsapi_login&state=1#wechat_redirect" class="wechat_icon icon"></a>
    </p>
    </div> -->
    </ul>
</div>
<div class="log_img">
<?php $adList = $this->advdbclass->getAd(5, 1);
if ($adList) {
foreach ($adList as $key=>$value) {
	?>
<img alt="<?php echo clearstring($value['ad_text']); ?>" src="<?php echo $value['path']; ?>">
<?php }} ?>
</div>
</section>