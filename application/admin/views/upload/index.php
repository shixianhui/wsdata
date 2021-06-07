<form name="upload" method="post" action="admincp.php/upload/uploadImage" enctype="multipart/form-data" onSubmit="return doCheck();">
<table cellpadding="2" cellspacing="1" class="table_form">
  <tr>
   <td>
     <input class="input_blur" name="filePath" id="filePath" type="file" size="30" />
     <input class="button_style" type="submit" name="dosubmit" value=" 上传图片 " />
	</td>
   </tr>
  <tr>
     <td>
      <input type="hidden" name="model" value="<?php echo $model; ?>" />
	 允许上传类型：<?php echo $ext; ?><br />
	 允许上传大小：<?php echo $size; ?><br />
	 缩略图大小：宽 <?php echo $width; ?>px，高 <?php echo $height; ?> px
    </td>
   </tr>
  <tr>
     <td>     
     <img id="previewpic" src="<?php if (isset($filePath)){echo $filePath;}else{echo "images/admin/nopic.gif";} ?>"  />
     <?php if ($model == 'product') { ?>
     <img style="display:none;" id="max_previewpic" src="<?php if (isset($filePath)){echo preg_replace("/_thumb/", "_max", $filePath);}else{echo "images/admin/nopic.gif";} ?>"  />
	 <?php } ?>
	 </td>
   </tr>
</table>
</form>
<script language="javascript" type="text/javascript">
<?php if (! empty($filePath)){ ?>
window.parent.document.getElementById("path").value="<?php echo preg_replace(array("/_thumb/", "/_/"), array("", "."), $filePath); ?>";
<?php } ?>
</script>