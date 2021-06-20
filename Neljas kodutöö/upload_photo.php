<?php

	
	$photo_upload_error = null;
	$image_file_type = null;
	$image_file_name = null;
	$file_name_prefix = "vr_";
	$file_size_limit = 1 * 1024 * 1024;
	$image_max_w = 600;
	$image_max_h = 400;
	$watermark = "../images/vr_watermark.png";
	$path_small = "../upload_photos_small/";
	$path_normal ="../upload_photos_normal/";
	$path_orig = "../upload_photos_orig/";

	if(isset($_POST["photo_submit"])){


			$photo_upload = new Upload_photo($_FILES["file_input"],$file_size_limit);
			var_dump($_FILES["file_input"]);


			if(empty($photo_upload->error)){
			//-- loome normaalsuuruses pildi säilitades külgede proportsiooni
				$photo_upload->resize_photo(600, 400, true);
				

			
			// Lisan vesimärgi
				$photo_upload->add_watermark($watermark);
				$photo_upload->date_to_pic();
				$photo_upload->save_image_to_file($path_normal); 

			//-- loome pisipildi ruuduna lõigates selle originaalpildi keskelt kahandades 100 pixlile

				$photo_upload->resize_photo( 100, 100, false );
				$photo_upload->save_image_to_file($path_small); 
					
			//-- säilitame ka üleslaetud originaalfaili eraldi kasutas  
				$photo_upload->save_orig_image($path_orig);
				$check = insert_pic_db($photo_upload->image_new_filename,Input::str($_FILES["file_input"]["name"]),Input::str($_POST['alt_text']),Input::int($_POST['privacy_input']));

				if ($check ==1){
				$photo_upload_error .= "  Foto andmete lisamine andmebaasi õnnestus";
				} else {
				$photo_upload_error .= "  Foto andmete lisamine ebaõnnestus";
				}
			} else {
				$photo_upload_error .= $photo_upload->error; 
				$photo_upload_error .= " Foto üleslaadimine ebaõnnestus!";
				} 
	}

	



?>
<!DOCTYPE html>
<html lang="et">
<head>
	<meta charset="utf-8">
	<title>Veebirakendused ja nende loomine 2021</title>
</head>
<body>
	<h1>Fotode üleslaadimine</h1>
	<p>See leht on valminud aine "Veebirakendused ja nende loomine" õppetöö raames!</p>
	<hr>
	<p><a href="?logout=1">Logi välja</a></p>
	<p><a href="home.php">Avalehele</a></p>
	<hr>
	<form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" enctype="multipart/form-data">
		<label for="file_input">Vali foto fail: </label>
		<input id="file_input" name="file_input" type="file">
		<br>
        <br>
		<label for="alt_input">Pildi selgitus</label>
		<input id="alt_text" name="alt_text" type="text" placeholder="Pildil on...">
		<br>
        <br>
		<label>Privaatsustase: </label>
		<br>
		<input id="privacy_input_1" name="privacy_input" type="radio" value="3" checked>
		<label for="privacy_input_1">Privaatne</label>
		<br>
		<input id="privacy_input_2" name="privacy_input" type="radio" value="2">
		<label for="privacy_input_2">Registreeritud kasutajatele</label>
		<br>
		<input id="privacy_input_3" name="privacy_input" type="radio" value="1">
		<label for="privacy_input_3">Avalik</label>
		<br>
        <br>
		<input type="submit" name="photo_submit" value="Lae pilt üles!">
	</form>
	<p><?php echo $photo_upload_error; ?></p>
</body>
</html>