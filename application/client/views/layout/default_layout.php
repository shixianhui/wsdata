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
<script src="js/default/jquery.form.js"></script>
<script src="js/default/formvalid.js?v=1.1" type="text/javascript"></script>
<script src="js/default/index.js?v=1.1" type="text/javascript"></script>
<link href="css/default/member.css?v=1.1" type="text/css" rel="stylesheet">
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