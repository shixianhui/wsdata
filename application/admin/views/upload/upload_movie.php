<form name="upload" method="post" action="admincp.php/upload/uploadMovie" enctype="multipart/form-data" onSubmit="return doCheck();">
<table cellpadding="2" cellspacing="1" class="table_form">
    <caption>视频上传</caption>
  <tr>
   <td>
     <input name="filePath" id="filePath" type="file" size="15" />
     <input type="submit" name="dosubmit" value=" 上传 " />
	</td>
   </tr>
  <tr>
     <td>
	 允许上传类型：<?php echo $ext; ?><br />
	 允许上传大小：<?php echo $size; ?><br />
	 缩略图大小：宽 <input type="text" name="width" value="<?php echo $width; ?>" size="3" /> px，高 <input type="text" name="height" value="<?php echo $height; ?>" size="3" /> px
    </td>
   </tr>
  <tr>
     <td>     
     <img id="previewpic" src="<?php if (isset($filePath)){echo $filePath;}else{echo "images/admin/nopic.gif";} ?>"  />
	 </td>
   </tr>
</table>
</form>
<script language="javascript" type="text/javascript">
<?php if (! empty($filePath)){ ?>
window.opener.document.getElementById("path").value="<?php echo preg_replace("/_thumb/", "", $filePath); ?>";
<?php } ?>
</script>