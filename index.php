<?php 
	
	
	//Check Insert
// 	$action = new Action(Action::$insert, "lang", null);
// 	$action->addField("code", "fn");
// 	$action->addField("lang_name", "French");
// 	$action->execute();
	
	//Check Update
// 	$action = new Action(Action::$update, "lang", null);
// 	$action->setPrimaryKeyName("code");
// 	$action->setPrimaryKeyValue("fn");
// 	$action->addField("lang_name", "French123");
// 	$action->execute();
	
	//Check Delete
// 	$action = new Action(Action::$delete, "lang", null);
// 	$action->addFieldForWhere("code", "=", "fn");
// 	$action->execute();
header('view/uishell?p-admin.html');
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head runat="server">
    <title></title>
    <script src="../ckeditor/ckeditor.js" type="text/javascript"></script>
    <link href="../ckeditor/contents.css" rel="stylesheet" type="text/css" />
    <script src="../ckeditor/config.js" type="text/javascript"></script>
    <link href="../ckeditor/skins/moono/editor.css" rel="stylesheet" type="text/css" />
    <script src="../ckeditor/lang/ar.js" type="text/javascript"></script>
    <script src="../ckeditor/styles.js" type="text/javascript"></script>
    <script src="../ckeditor/build-config.js" type="text/javascript"></script>
</head>
<body>
<div style="width:100%;height: 300px;">
 <textarea class="ckeditor" runat="server" cols='80' id="editor1" name="editor1" rows='10' style="visibility: hidden; display: none;">	</textarea>
</div>
</body>