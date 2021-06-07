<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<base href="<?php echo base_url(); ?>" />
<title></title>
<style type="text/css">
body {
	margin: 0;
	padding: 0;
	background: #cadff3;
	cursor: E-resize;
	background-color: #222;
}
img{width: 32px;height: 32px;background-color: rgb(56,56,56);margin-left: 7px}
html,body{
    margin:0px;
    height:100%;
}
</style>
<script type="text/javascript" language="JavaScript">
<!--
function toggleMenu()
{
  frmBody = parent.document.getElementById('frame-body');
  imgArrow = document.getElementById('img');

  if (frmBody.cols == "0, *")
  {
    frmBody.cols="221, *";
    imgArrow.src = "images/admin/arrow_left.png";
  }
  else
  {
    frmBody.cols="0, *";
    imgArrow.src = "images/admin/arrow_right.png";
  }
}
//-->
</script>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>
<body onselect="return false;">
<table height="100%" cellspacing="0" cellpadding="0" id="tbl">
  <tr><td><a href="javascript:toggleMenu();"><img align="absmiddle" src="images/admin/arrow_left.png" id="img" border="0" /></a></td></tr>
</table>
</body>
</html>