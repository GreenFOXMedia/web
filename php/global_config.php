<?php 

// define Params

$root = $_SERVER["REQUEST_URI"];

define("ROOT_PATH", $root);
define("SCRIPT_SRC", ROOT_PATH . "js/");
define("STYLE_SRC", ROOT_PATH . "css/");
define("IMAGE_SRC", ROOT_PATH . "images/");
define("GALERY_IMAGE_PATH", ROOT_PATH . "images/slides/");

define("HOSTNAME",  "localhost");
define("USERNAME", "root");
define("PASSWORD", "25190411");
define("DATABASE", "zagonser");


function dbConnect()
{
	mysql_connect(HOSTNAME, USERNAME, PASSWORD);
	mysql_select_db(DATABASE);
}

function checkOnlineState($check)
{

	if($check):
		dbConnect();
		$query = mysql_query("SELECT * FROM website_informations WHERE id = '1'");
		$result = mysql_fetch_object($query);

		$active = $result->active;
		if($active == "0"):
			header("location: offline.php");
		endif;
		
		if($active == "1"):
			header("location: offline.php");
		endif;
	endif;
	
}

function getScript($sourceName)
{
	echo SCRIPT_SRC.$sourceName.".js";
}


function getImage($sourceName, $type)
{
	echo IMAGE_SRC.$sourceName. "." .$type;
}

function getStylesheet($sourceName)
{
	echo STYLE_SRC.$sourceName.".css";
}


function getTitle()
{
	dbConnect();
	$query = mysql_query("SELECT * FROM website_informations WHERE id = '1'");
	$result = mysql_fetch_object($query);

	$title = $result->title;
	echo $title;
}

function getHeadline()
{
	dbConnect();
	$query = mysql_query("SELECT * FROM website_informations WHERE id = '1'");
	$result = mysql_fetch_object($query);

	$title = $result->headline;
	echo $title;
}

function getSubTitle()
{
	dbConnect();
	$query = mysql_query("SELECT * FROM website_informations WHERE id = '1'");
	$result = mysql_fetch_object($query);

	$subtitle = $result->subtitle;
	echo $subtitle;
}


function loadSlideshowImages()
{
	$path = "images/slides/";
	$images_png = glob($path . "*.png");
	$images_jpg = glob($path . "*.jpg");
	

	foreach ($images_png as $image) {
		echo "<img height='270' width='1000' src='".$image."'>";
	}


	foreach ($images_jpg as $image) {
		echo "<img height='270' width='1000' src='".$image."'>";
	}
}

function getBlocks($blocksName)
{
	dbConnect();
	$query = mysql_query("SELECT * FROM website_blocks WHERE belongs_to_page = '$blocksName'");
	echo '<div class="blockWrapper">';
		$i = 0;
		$class_add = "";
		while($result = mysql_fetch_object($query)):
			if($i % 3 == 1):
				$class_add = "second";
			else:
				$class_add = "";
			endif;
				echo '<div class="image_container '.$class_add.'"><div class="image"><img data-title="'.$result->image_title.'" data-description="'.$result->image_description.'" src="'.$result->link_to_image.'" alt="'.$result->image_title.'"/></div></div>';
			
			$i++;
		endwhile;
	echo '</div>';
}

function loadContactForm()
{
	$form = "";
	$form .= "<form action='' method='post' autocomplete='off'>";
		$form .= "<input name='name' placeholder='Vor- und Nachname' type='text'><br />";
		$form .= "<input name='mail' placeholder='E-Mail' type='text'><br />";
		$form .= "<input name='phone' placeholder='Telefon' type='text'><br />";
		$form .= "<textarea name='message' placeholder='Ihre Nachricht'></textarea><br />";
		$form .= "<input name='send_form' value='Absenden' type='button'>";
	$form .= "</form>";

	echo $form;
}

?>



