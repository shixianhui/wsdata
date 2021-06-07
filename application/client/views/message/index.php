<link href="css/default/member.css?v=1.1" type="text/css" rel="stylesheet">
<section class="warp">
<div  class="cart_border mt30">
 <div class="border_d clearfix">
 <div class="cart_success clearfix">
  <span class="icon"></span><p class="fl"><b><?php if (isset($msg)){echo $msg;} ?></b>
  <br>
  <?php if ($url == "goback") { ?>
    <a href="javascript:history.go(-1);" >[ 点这里返回上一页 ]</a>
    <?php } else if (isset($url)) {?>
    如果您的浏览器没有自动跳转，<a style="color: #666;" href="<?php echo $url;?>">请点击这里</a>
    <script>window.setTimeout("window.location.href='<?php echo $url;?>'",3000);</script>
    <?php } ?>
  </p>
 </div>
</div>
</div>
</section>