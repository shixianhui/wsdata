<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <base href="<?php echo base_url(); ?>" />
    <title><?php echo $title; ?></title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="stylesheet" href="js/admin/lib/layui-v2.6.3/css/layui.css" media="all">
    <link rel="stylesheet" href="css/admin/public.css" media="all">
    <script>
        var controller = '<?php echo $this->uri->segment(1); ?>';
        var method = '<?php echo $this->uri->segment(2); ?>';
        var base_url = '<?php echo base_url(); ?>';
    </script>
</head>
<body>
<?php echo $content; ?>
</body>
</html>