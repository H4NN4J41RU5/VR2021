<?php

	require_once "../../../../conf.php";
	// echo $server_host; kuvab localhosti
	$news_input_error = null;
	$clean_news_title = "";
	$clean_news_content = "";
	$clean_news_author = "";
	//var_dump($_POST); // on olemas ka $_GET, mõlemaga saab kätte massiivi

	if (isset($_POST["news_submit"])) {
		if (empty($_POST["news_title_input"])) {
			$news_input_error = "Uudise pealkiri on puudu! ";
		}	else {
			$clean_news_title = Input::str($_POST['news_title_input']);
		}
		if (empty($_POST["news_content_input"])) {
			$news_input_error .= "Uudise sisu on puudu! ";
		} 	else {
			$clean_news_content = Input::str($_POST['news_content_input']);
			$clean_news_author = Input::str($_POST['news_author_input']);
		}
		//if (empty($_POST["news_author_input"])) {
			//$news_input_error .= "Uudise autor on puudu! ";
		//} jätan praegu tingimuse välja, sest siis saab autor olla ka anonüümne

		if (empty($news_input_error)){
			//kui tingimised täidetud, siis salvestame andmebaasi
			store_news($clean_news_title,$clean_news_content,$clean_news_author);
			$clean_news_title = "";
			$clean_news_content = "";
			$clean_news_author = "";
		}
	}
	
	function store_news($news_title, $news_content,$news_author){
		//echo $news_title .$news_content .$news_author;
		// trükib välja
		//echo $GLOBALS["server_host"];
		// kuvab ikkagi localhosti
		// loome andmebaasis serveri ja baasiga ühenduse ---->
		$conn = new mysqli($GLOBALS["server_host"], $GLOBALS["server_user_name"], $GLOBALS["server_password"], $GLOBALS["database"]);
		// määrame suhtluseks kodeeringu
		$conn -> set_charset ("utf8");
		//valmistame ette SQL käsu
		$stmt = $conn -> prepare("INSERT INTO vr2021_news (vr2021_news_title, vr2021_news_content, vr2021_news_author) VALUES (?,?,?)");
		echo $conn -> error;
		// i - integer	s - string	d-decimal valin s - varchar nagu php adminis veeru tüübiks määrasime
		$stmt -> bind_param("sss", $news_title, $news_content,$news_author);
		// küsimärgid saavad päris väärtused
		$stmt -> execute();
		//login andmebaasist välja
		$stmt -> close();
		$conn -> close();
	}

	class  Input {
		static $errors = true;
	
		static function check($arr, $on = false) {
			if ($on === false) {
				$on = $_REQUEST;
			}
			foreach ($arr as $value) {	
				if (empty($on[$value])) {
					self::throwError('Data is missing', 900);
				}
			}
		}
	
		static function int($val) {
			$val = filter_var($val, FILTER_VALIDATE_INT);
			if ($val === false) {
				self::throwError('Invalid Integer', 901);
			}
			return $val;
		}
	
		static function str($val) {
			if (!is_string($val)) {
				self::throwError('Invalid String', 902);
			}
			$val = trim(htmlspecialchars($val));
			return $val;
		}
	
		static function bool($val) {
			$val = filter_var($val, FILTER_VALIDATE_BOOLEAN);
			return $val;
		}
	
		static function email($val) {
			$val = filter_var($val, FILTER_VALIDATE_EMAIL);
			if ($val === false) {
				self::throwError('Invalid Email', 903);
			}
			return $val;
		}
	
		static function url($val) {
			$val = filter_var($val, FILTER_VALIDATE_URL);
			if ($val === false) {
				self::throwError('Invalid URL', 904);
			}
			return $val;
		}
	
		static function throwError($error = 'Error In Processing', $errorCode = 0) {
			if (self::$errors === true) {
				throw new Exception($error, $errorCode);
			}
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
	<h1>Uudiste lisamine</h1>
	<p>See leht on valminud aine "Veebirakendused ja nende loomine" õppetöö raames.</p>
	<hr>
	<form method="POST">
    <label for="news_title_input">Uudise pealkiri</label>
    <br>
    <input type="text" id="news_title_input" name="news_title_input" value="<?php echo $clean_news_title ; ?>" placeholder="Pealkiri" >
    <br>
	<br>
    <label for="news_content_input">Uudise tekst</label>
    <br>
    <textarea id="news_content_input" name="news_content_input" placeholder="Uudise tekst" rows="6" cols="40"><?php echo $clean_news_content ; ?></textarea>
    <br>
	<br>
    <label for="news_author">Uudise lisaja nimi</label>
    <br>
    <input type="text" id="news_author_input" name="news_author_input" value="<?php echo $clean_news_author ; ?>" placeholder="Nimi" >
    <br>
	<br>   
    <input type="submit" name="news_submit" value="Saada">
</form>
	<p><?php echo $news_input_error; ?></p>
</body>
</html>