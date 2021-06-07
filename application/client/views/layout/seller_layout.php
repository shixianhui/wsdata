<!DOCTYPE html>
<html >
<head>
<meta charset="utf-8">
<title><?php echo $title; ?></title>
<base href="<?php echo base_url(); ?>" />
<meta name="title" content="<?php echo clearstring($title); ?>" />
<meta name="keywords" content="<?php echo clearstring($keywords); ?>" />
<meta name="description" content="<?php echo clearstring($description); ?>" />
    <link rel="shortcut icon" href="images/default/favicon.ico" type="image/x-icon" />
    <script>
var controller = '<?php echo $this->uri->segment(1); ?>';
var method = '<?php echo $this->uri->segment(2); ?>';
var base_url = '<?php echo base_url(); ?>';
</script>
<link href="css/default/rest.css?v=1.1" type="text/css" rel="stylesheet">
<link href="css/default/base.css?v=1.1" type="text/css" rel="stylesheet">
<script type="text/javascript" language="javascript" src="js/default/jquery.js"></script>
<link rel="stylesheet" href="js/default/aui-artDialog/css/ui-dialog.css">
<script src="js/default/aui-artDialog/dist/dialog-plus-min.js"></script>
<!--[if lt IE 9]>
<script type="text/javascript" src="js/default/html5.js"></script>
<![endif]-->
<!-- <script type="text/javascript" language="javascript" src="js/default/jquery.SuperSlide.js"></script> -->
<script type="text/javascript" language="javascript" src="js/default/jquery.lazyload.min.js"></script>
<script src="js/default/jquery.form.js"></script>
<script src="js/default/formvalid.js?v=1.1" type="text/javascript"></script>
<script src="js/default/index.js?v=1.1" type="text/javascript"></script>
<script src="js/default/jquery.jedate.js" type="text/javascript"></script>
<script src="js/default/jedate_save.js?v=1.1" type="text/javascript"></script>
<link href="css/default/member.css?v=1.1" type="text/css" rel="stylesheet">
<link href="css/default/jedate.css?v=1.1" type="text/css" rel="stylesheet">
</head>
<body>
<?php echo $this->load->view('element/topbar_tool', '', TRUE); ?>
<header class="header clearfix b_header">
<div class="warp">
 <a href="<?php echo base_url(); ?>" class="logo"><img src="images/default/logo.png" style="width: 160px"></a>
 <div class="name">商家中心</div>
</div>
<div class="clear"></div>
<div class="b_head_menu">
<ul>
 <Li><a href="<?php echo getBaseUrl(false,'','seller.html',$client_index);?>" class="current">商家中心</a></Li>
<!-- <Li><a href="<?php echo getBaseUrl(false,'','page/index/292.html',$client_index);?>" target="_blank">入驻规则</a></Li> -->
<!-- <Li><a href="<?php echo getBaseUrl(false,'','page/index/293.html',$client_index);?>" target="_blank">安全中心</a></Li> -->
</ul>
</div>
</header>
<?php $segment_uri = $this->uri->segment(2); ?>
<section class="warp">
 <div class="member_left mt20">
  <div class="member_nav box_shadow">
   <dl><dt>店铺管理<em class="icon icon_arrow"></em></dt>
   <dd>
 
   <a href="<?php echo getBaseUrl($html,"{$template}/index.html","{$template}/index.html",$client_index);?>" <?php echo ($segment_uri=='index' || !$segment_uri) ? 'class="current"' : '';?>>我的店铺</a>
   </dd>
   </dl>

   <dl><dt>菜品管理<em class="icon icon_arrow"></em></dt>
   <dd>
    <a href="<?php echo getBaseUrl($html,"{$template}/my_save_dishes.html","{$template}/my_save_dishes.html",$client_index);?>" <?php echo ($segment_uri=='my_save_dishes') ? 'class="current"' : '';?>>发布菜品</a>
    <a href="<?php echo getBaseUrl($html,"{$template}/my_get_dishes_list.html","{$template}/my_get_dishes_list/1.html",$client_index);?>" <?php echo ($segment_uri=='my_get_dishes_list') ? 'class="current"' : '';?>>菜品列表</a>
    <a href="<?php echo getBaseUrl($html,"{$template}/my_get_dishes_category_list.html","{$template}/my_get_dishes_category_list.html",$client_index);?>" <?php echo ($segment_uri=='my_get_dishes_category_list' || $segment_uri=='my_save_dishes_category') ? 'class="current"' : '';?>>分类设置</a>
    <a href="<?php echo getBaseUrl($html,"{$template}/my_save_combos.html","{$template}/my_save_combos.html",$client_index);?>" <?php echo ($segment_uri=='my_save_combos') ? 'class="current"' : '';?>>新增套餐</a>
    <a href="<?php echo getBaseUrl($html,"{$template}/my_get_combos_list.html","{$template}/my_get_combos_list.html",$client_index);?>" <?php echo ($segment_uri=='my_get_combos_list') ? 'class="current"' : '';?>>套餐列表</a>
    <a href="<?php echo getBaseUrl($html,"{$template}/my_save_share_goods.html","{$template}/my_save_share_goods.html",$client_index);?>" <?php echo ($segment_uri=='my_save_share_goods') ? 'class="current"' : '';?>>新增吆喝商品</a>
    <a href="<?php echo getBaseUrl($html,"{$template}/my_get_share_goods_list.html","{$template}/my_get_share_goods_list.html",$client_index);?>" <?php echo ($segment_uri=='my_get_share_goods_list') ? 'class="current"' : '';?>>吆喝商品列表</a>
    <a href="<?php echo getBaseUrl($html,"{$template}/my_get_grasses_list.html","{$template}/my_get_grasses_list.html",$client_index);?>" <?php echo ($segment_uri=='my_get_grasses_list') ? 'class="current"' : '';?>>种草内容列表</a>

   </dd>
   </dl>
    <dl>
        <dt>交易管理<em class="icon icon_arrow"></em></dt>
        <dd>
            <a href="<?php echo getBaseUrl($html,"{$template}/order_index.html","{$template}/order_index.html",$client_index);?>" <?php echo ($segment_uri=='order_index') ? 'class="current"' : '';?>>订单列表</a>

        </dd>
    </dl>
    <dl>
        <dt>财务管理<em class="icon icon_arrow"></em></dt>
        <dd>
            <a href="<?php echo getBaseUrl($html,"{$template}/store_account.html","{$template}/store_account.html",$client_index);?>" <?php echo ($segment_uri=='store_account') ? 'class="current"' : '';?>>财务概况</a>

        </dd>
    </dl>

      <dl><dt>子账号管理<em class="icon icon_arrow"></em></dt>
          <dd>
              <a href="<?php echo getBaseUrl($html,"{$template}/my_seller_group_list.html","{$template}/my_seller_group_list.html",$client_index);?>" <?php echo ($segment_uri=='my_seller_group_list') ? 'class="current"' : '';?>>部门设置</a>
              <a href="<?php echo getBaseUrl($html,"{$template}/my_get_seller_list.html","{$template}/my_get_seller_list.html",$client_index);?>" <?php echo ($segment_uri=='my_get_seller_list') ? 'class="current"' : '';?>>账号管理</a>

          </dd>
      </dl>
  </div>
 </div>
 <?php echo $content; ?>
</section>
<div class="clear"></div>
<footer class="mt20">
	<div class="clear"></div>
	<div class="copyright">
<P>
<a href="<?php echo base_url(); ?>">首页</a>
<?php
    $footerMenuList = $this->advdbclass->getFooterMenu();
    if ($footerMenuList) {
	foreach ($footerMenuList as $footerMenu) {
        if ($footerMenu['menu_type'] == '3') {
            $url = $footerMenu['url'];
        } else {
            if ($footerMenu['menu_type'] == 1 && $footerMenu['cover_function']) {
                $url = getBaseUrl($html, "{$footerMenu['html_path']}/{$footerMenu['cover_function']}{$footerMenu['id']}.html", "{$footerMenu['template']}/{$footerMenu['cover_function']}/{$footerMenu['id']}.html", '');
            } else {
                $url = getBaseUrl($html, "{$footerMenu['html_path']}/index{$footerMenu['id']}.html", "{$footerMenu['template']}/index/{$footerMenu['id']}.html", '');
            }
        }
	?>
|<a href="<?php echo $url; ?>"><?php echo $footerMenu['menu_name'] ?></a>
<?php }} ?>
</P>
		<P><?php echo $site_copyright; ?><?php echo $icp_code; ?></P>
	</div>
</footer>
</body>
</html>
<script type="text/javascript">
(function(a){
	a.fn.hoverClass=function(b){
		var a=this;
		a.each(function(c){
			a.eq(c).hover(function(){
				$(this).addClass(b)
			},function(){
				$(this).removeClass(b)
			})
		});
		return a
	};
})(jQuery);

$(function(){
	$("#link1").hoverClass("current");
	$("#link2").hoverClass("current");
	$("#link3").hoverClass("current");
	$("#link4").hoverClass("current");
	$("#activity").hoverClass("current");
});

$(function () {
   $("img.lazy").lazyload({
      placeholder: "images/default/load.jpg", //加载图片前的占位图片
      effect: "fadeIn" //加载图片使用的效果(淡入)
   });
});

    </script>