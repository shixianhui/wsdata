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
    <link href="css/default/rest.css?v=1.1" type="text/css" rel="stylesheet">
<link href="css/default/base.css?v=1.1" type="text/css" rel="stylesheet">
<script type="text/javascript" language="javascript" src="js/default/jquery.js"></script>
<link rel="stylesheet" href="js/default/aui-artDialog/css/ui-dialog.css">
<script src="js/default/aui-artDialog/dist/dialog-plus-min.js"></script>
<!--[if lt IE 9]>
<script type="text/javascript" src="js/default/html5.js"></script>
<![endif]-->
<!-- <script type="text/javascript" language="javascript" src="js/default/jquery.SuperSlide.js"></script>
<script type="text/javascript" language="javascript" src="js/default/jquery.lazyload.min.js"></script> -->
<link href="css/default/member.css?v=1.1" type="text/css" rel="stylesheet">

<script src="js/default/jquery.form.js"></script>
<script src="js/default/formvalid.js?v=1.1" type="text/javascript"></script>
<script src="js/default/index.js" type="text/javascript"></script>
<script>
var controller = '<?php echo $this->uri->segment(1); ?>';
var method = '<?php echo $this->uri->segment(2); ?>';
var base_url = '<?php echo base_url(); ?>';
</script>
</head>
<body style="background:#fff">
<header class="header clearfix reg-header">
<div class="warp">
 <a href="<?php echo base_url(); ?>" class="logo"><img src="images/default/logo.png" style="width: 160px"></a>
 <div class="name"><?php echo $action_title; ?></div>
</div>
</header>
<?php echo $content; ?>
<div class="clear"></div>
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
    		$url = getBaseUrl($html, $footerMenu['html_path'], "{$footerMenu['template']}/index/{$footerMenu['id']}.html", $client_index);
        }
	?>
|<a href="<?php echo $url; ?>"><?php echo $footerMenu['menu_name'] ?></a>
<?php }} ?>
</P>
<P><?php echo $site_copyright; ?><?php echo $icp_code; ?></P>
</div>
</body>
</html>
<!-- <script type="text/javascript" language="javascript" src="js/default/main.js"></script>
<script type="text/javascript">
        $(function () {
            $("img.lazy").lazyload({
                placeholder: "images/default/load.jpg", //加载图片前的占位图片
                effect: "fadeIn" //加载图片使用的效果(淡入)
            });
        });
    </script> -->