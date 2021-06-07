<table width='100%' border='0' cellspacing='0' cellpadding='0' >
<tr>
<td width="100px">当前目录：</td>
<td align="left">
<?php if (isset($path)){echo ".".$path;} ?>
</td>
<td align="center">
<?php if (isset($cusPath)) { ?>
<?php if ($cusPath != ":uploads:") { ?>
<a href="admincp.php/upload/select/<?php echo $prePath; ?>">返回上级目录</a>
<?php }} ?>
</td>
</tr>
</table>
<table cellpadding="0" cellspacing="1" class="table_list">
<caption>文件选择</caption>
<tr>
 <th class="align_c" >文件名</th>
 <th width="10%">大小</th>
 <th class="align_c" width="15%">原图(宽x高)</th>
 <th class="align_c" width="15%">缩略图(宽x高)</th>
 <th width="20%">上次修改时间</th>
</tr>
<?php foreach ($files as $key=>$value ): ?>
<tr>
 <td align='left'>
 <?php if ($value["fileType"] == "dir"){?>
 <img src="images/admin/dir.gif" />
 <a href="admincp.php/upload/select/<?php echo $cusPath . $value["fileName"] . ":" ; ?>"><?php echo $value["fileName"];?></a> 
 <?php } else if ($value["fileType"] == "file") {?> 
 <a href="javascript:selectImage('<?php echo substr($path, 1) . $value["fileName"]; ?>');">
 <img src="<?php echo preg_replace('/\./', '_thumb.', substr($path, 1) . $value["fileName"]); ?>" width="80px" />
 </a><br/>
 <?php echo $value["fileName"];?>
 <?php }?>
 </td>
 <td class="align_c">
 <?php if ($value["fileType"] == "dir"){?>
 <目录>
 <?php } else if ($value["fileType"] == "file") {?>
 <?php echo $value["fileSize"];?>
 <?php }?> 
 </td>
 <td class="align_c"><?php echo $value["artworkSize"];?></td>
 <td class="align_c"><?php echo $value["thumbnailSize"];?></td>
 <td class="align_c"><?php echo $value["fileMTime"];?></td>
</tr>
<?php endforeach; ?>
</table>
<table width='100%' border='0' cellspacing='0' cellpadding='0' >
<tr>
<td width="100px">当前目录：</td>
<td align="left">
<?php if (isset($path)){echo ".".$path;} ?>
</td>
<td align="center">
<?php if (isset($cusPath)) { ?>
<?php if ($cusPath != ":uploads:") { ?>
<a href="admincp.php/upload/select/<?php echo $prePath; ?>">返回上级目录</a>
<?php }} ?>
</td>
</tr>
</table>
<script language="javascript" type="text/javascript">
function selectImage(filePath) {
	if (filePath != null && filePath !="") {
		window.opener.document.getElementById("path").value=filePath;
		window.close();
	}	
}
</script>