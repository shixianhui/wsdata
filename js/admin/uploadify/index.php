<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>UploadiFive Test</title>
<base href="http://my.model.com/uploadify/" />
<script src="js/jquery.min.1.7.1.js" type="text/javascript"></script>
<script src="js/jquery.uploadify.min.js" type="text/javascript"></script>
<link rel="stylesheet" type="text/css" href="css/uploadify.css">
<style type="text/css">
body {
	font: 13px Arial, Helvetica, Sans-serif;
}
</style>
</head>

<body>
	<h1>Uploadify Demo</h1>
	<form>
		<div id="queue"></div>
		<input id="file_upload" name="file_upload" type="file" multiple="true">
	</form>
	<script type="text/javascript">
		<?php $timestamp = time();?>
		$(function() {
			$('#file_upload').uploadify({
				'formData'     : {
					'timestamp' : '<?php echo $timestamp;?>',
					'token'     : '<?php echo md5('unique_salt' . $timestamp);?>'
				},
				'fileTypeDesc' : 'Image Files',
		        'fileTypeExts' : '*.gif; *.jpg; *.png',
		        'method'   : 'post',
		        'multi'    : true,
		        'fileSizeLimit' : '100KB',
		        'uploadLimit' : 5,
				'swf'      : 'flash/uploadify.swf',
				'uploader' : 'uploadify.php'
			});
		});
	</script>
</body>
</html>