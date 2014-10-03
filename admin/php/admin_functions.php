<?php

define("ADMIN_SCRIPT_SRC", ROOT_PATH . "js/");

define("UPLOAD_TO_FILE_PATH",dirname(dirname(dirname(__FILE__))) . "/images/uploads/blocks/");
define("UPLOADED_FILE_PATH", dirname(ROOT_PATH) . "/images/uploads/blocks/");

function getPageNames()
{
	dbConnect();
	$query = mysql_query("SELECT * FROM website_pages");
	while($result = mysql_fetch_object($query)):
		echo "<option value='".$result->page_name."'>".$result->page_title."</option>";
	endwhile;
}

function getAdminScript($sourceName)
{
	echo ADMIN_SCRIPT_SRC.$sourceName.".js";
}


?>