<section class="topbar">
<div class="warp">
<?php if ($this->session->userdata('user_id')) { ?>
<div class="wel">您好<?php if (get_cookie('user_username')) { ?><a style="color: #c81624;" href="<?php echo getBaseUrl($html,'seller.html','seller.html',$client_index);?>"><?php echo get_cookie('user_username'); ?></a><?php } ?>，欢迎来到千户万物商家中心！<a href="<?php echo getBaseUrl($html,'user/logout.html','user/logout.html',$client_index);?>">退出</a></div>
<?php } else { ?>
<div class="wel">您好，欢迎来到千户万物商家中心！<a href="<?php echo getBaseUrl($html,'user/login.html','user/login.html',$client_index);?>">请登录</a><span>|</span><a href="<?php echo getBaseUrl($html,'user/reg.html','user/reg.html',$client_index);?>">快速注册</a></div>
<?php } ?>
 <div class="smlink">
 </div>
</div>
</section>