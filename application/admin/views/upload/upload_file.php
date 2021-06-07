<form name="upload" method="post" action="admincp.php/upload/uploadFile/<?php echo $domId;?>" enctype="multipart/form-data" onSubmit="return doCheck();">
<table cellpadding="2" cellspacing="1" class="table_form">
    <caption>附件上传</caption>
  <tr>
   <td>
     <input class="input_blur"  name="filePath" id="filePath" type="file" size="30" />
     <input class="button_style" type="submit" name="dosubmit" value=" 上传 " />
	</td>
   </tr>
  <tr>
     <td>
	 允许上传类型：<?php echo $ext; ?><br />
	 允许上传大小：<?php echo $size; ?><br />
    </td>
   </tr>
  <tr>
     <td>     
     <?php if (isset($filePath)){echo $filePath;}?>
	 </td>
   </tr>
</table>
</form>
<script language="javascript" type="text/javascript">
<?php if (! empty($filePath)){ ?>
window.opener.document.getElementById("<?php echo $domId; ?>").value="<?php echo $filePath; ?>";
<?php } ?>
</script>