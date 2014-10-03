<?php require "../php/global_config.php" ?>
<?php require "php/admin_functions.php" ?>

<?php
if(isset($_POST["submit"])){
	switch ($_POST["fnc"])
	{
		case "page_images_upload":
			if(!empty($_FILES['file']))
			{
				$page_name = $_POST["set_to_page"];
				$belongs_to_page = $_POST["belongs_to_page"];
				$imageTitle = $_POST["imageTitle"];
				$imageName = $_POST["imageDescription"];

				foreach ($_FILES['file']['name'] as $key => $name ) {
					// $_FILES['file']['size'][$key];
					$name = date("Y-m-d-h-i-s_").$name;
					$imagePath = UPLOAD_TO_FILE_PATH.$name;
					$uploadedImagePath = UPLOADED_FILE_PATH.$name;

					if($_FILES['file']['error'][$key] == 0 && move_uploaded_file($_FILES['file']['tmp_name'][$key], $imagePath))
					{
						dbConnect();
						if(mysql_query("INSERT INTO website_blocks VALUES ('', '$page_name', '$uploadedImagePath','$imageTitle','$imageName','$belongs_to_page') ")){
							echo "Upload erfolgreich!";
						}
						
					}
				}

				

			} else {
				echo "Kein Bild ausgewählt!";
			}		
		break;

		case "create_pages":
			echo "Seite anlegen";
		break;

		default:
			echo "Invalid Data";
		break;
	}
}

?>


<html>
<head>
	<title><?php getTitle(); ?></title>
	<script src="<?php getScript("jquery") ?>"></script>
	<script src="<?php getAdminScript("ajax.functions") ?>"></script>
	<link href="<?php getImage("favicon", "png") ?>" rel="shortcut icon">
	<!-- <link rel="stylesheet" type="text/css" href="<?php getStylesheet("main") ?>" /> -->
</head>
<body>
	<div class="wrapper">
		<div class="page_images_upload">
			<h2>Page Files Upload!</h2>
			<form action="" method="post" enctype="multipart/form-data">

				<input type="hidden" name="fnc" value="page_images_upload">
				<input type="hidden" name="page_name" value="">
				<input type="hidden" name="belongs_to_page" value="">

				<input type="text" name="imageTitle" value="" placeholder="Titel"><br>
				<input type="text" name="imageDescription" value="" placeholder="Beschreibung"><br>
				<input type="file" name="file[]" accept="image/*" value=""><br>
				<select name="set_to_page" id="select_page">
					<?php getPageNames(); ?>
				</select><br>
				<input type="submit" name="submit" value="Hochladen">
			</form>
		</div>
		<p></p>
		<div class="page_images_upload">
			<h2>Page Files Upload!</h2>
			<form action="" method="post" enctype="multipart/form-data">
				<input type="hidden" name="fnc" value="page_images_upload">
				<input type="file" name="file[]" accept="image/*" value="">
				<input type="submit" name="submit" value="Anlegen">
			</form>
		</div>
	</div>
</body>
</html>
