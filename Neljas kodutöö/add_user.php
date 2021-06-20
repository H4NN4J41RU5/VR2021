<?php
  require_once "../../../../conf.php";
  require_once "fnc_general.php"; //sisendi testimise/puhastamise funktsioon
  require_once "fnc_user.php"; // useri fail
    
  $notice = null;
  $name = null;
  $surname = null;
  $email = null;
  $gender = null;
  $birth_month = null;
  $birth_year = null;
  $birth_day = null;
  $birth_date = null;
  $month_names_et = ["jaanuar", "veebruar", "märts", "aprill", "mai", "juuni","juuli", "august", "september", "oktoober", "november", "detsember"];
  // parooli muutujasse ei kirjuta, vaid saadan salvestades ära lihtsalt $_POST["password_input"]
  
  //muutujad võimalike veateadetega
  $name_error = null;
  $surname_error = null;
  $birth_month_error = null;
  $birth_year_error = null;
  $birth_day_error = null;
  $birth_date_error = null; // $birth_date_error võtab kokku month, day ja yeari, aga neid kolme peab kontrollima, sest neis võib viga olla, mis takistab terve kuupäeva loomist.
  $gender_error = null;
  $email_error = null;
  $password_error = null;
  $confirm_password_error = null;
  
  //kui on uue kasutaja loomise nuppu vajutatud, siis kontrollime, kas on VIGU! Võime sisestada ka loenduri, et kuniks loendur pole 0, siis mõni viga veel esineb ja tuleb teha muutusi:
  if(isset($_POST["user_data_submit"])){

	//kui on sisestatud nimi
	if(isset($_POST["first_name_input"]) and !empty($_POST["first_name_input"])){
		$name = test_input($_POST["first_name_input"]);
	} else {
		$name_error = "Palun sisestage eesnimi!";
	} //eesnime kontrolli lõpp
	
	if (isset($_POST["surname_input"]) and !empty($_POST["surname_input"])){
		$surname = test_input($_POST["surname_input"]);
	} else {
		$surname_error = "Palun sisesta perekonnanimi!";
	}
	
	if(isset($_POST["gender_input"])){
	    $gender = intval($_POST["gender_input"]);
	} else {
		$gender_error = "Palun märgi sugu!";
	}

	// kontrollime kuupäeva sisestust
	if (!empty($_POST["birth_day_input"])) {
		$birth_day = intval($_POST["birth_day_input"]); //võib kontrollida ka filtriga, et oleks kindlasti täisarv
	} else {
		$birth_day_error = "Palun vali sünnikuupäev!";
	}

	if (!empty($_POST["birth_month_input"])) {
		$birth_month = intval($_POST["birth_month_input"]); //võib kontrollida ka filtriga, et oleks kindlasti täisarv
	} else {
		$birth_month_error = "Palun vali sünnikuu!";
	}

	if (!empty($_POST["birth_year_input"])) {
		$birth_year = intval($_POST["birth_year_input"]); //võib kontrollida ka filtriga, et oleks kindlasti täisarv
	} else {
		$birth_year_error = "Palun vali sünniaasta!";
	}

	// kuupäeva valiidsus ehk reaalsuse kontroll
	if (empty($birth_day_error) and empty($birth_month_error) and empty($birth_year_error)) {
		if (checkdate($birth_month, $birth_day, $birth_year)) {
			$temp_date = new DateTime($birth_year ."-" .$birth_month ."-" .$birth_day); //DateTime moodustamisel lisandub muud default infot ka, nagu kellaaeg jm. Tahame sellest saada ainult Y-m-d, mistõttu teeme formati:
			$birth_date = $temp_date->format("Y-m-d");
		} else {
			$birth_date_error = "Valitud kuupäev on vigane!";
		}
	} 


	//email ehk kasutajatunnus
	if (isset($_POST["email_input"]) and !empty($_POST["email_input"])){
		$email = test_input($_POST["email_input"]);
		$email = filter_var($email, FILTER_VALIDATE_EMAIL);
		if ($email === false) {
			$email_error = "Palun sisesta korrektne e-postiaadress!";
		}
	} else {
		$email_error = "Palun sisesta e-postiaadress!";
	}
	
	// parooli ehk salasõna kontroll
	if (!empty($_POST["password_input"])) {
		if (strlen($_POST["password_input"]) < 8) {
			$password_error = "Parooli pikkus peab olema vähemalt 8 tähemärki!";
		}
	} else {
		$password_error = "Palun sisestage parool!";
	}

	if (empty($_POST["confirm_password_input"])) {
		$confirm_password_error = "Palun sisestage parool kaks korda!";
	} else {
		if ($_POST["confirm_password_input"] != $_POST["password_input"]) {
		$confirm_password_error = "Sisestatud paroolid ei ole ühesugused!";
		}
	}

	// kui vigu pole, siis salvestame
	if (empty($name_error) 
		and empty($surname_error) 
		and empty($birth_month_error) 
		and empty($birth_year_error) 
		and empty($birth_day_error) 
		and empty($birth_date_error) 
		and empty($gender_error) 
		and empty($email_error) 
		and empty($password_error) 
		and empty($confirm_password_error)) {
			// salvestus
			$notice = sign_up($name, $surname, $gender, $birth_date, $email, $_POST["password_input"]);
			// $notice muutuja kontrollib, et sign_up funktsioonil oleks reaalne väärtus, mida saame ka kontrollida
			if ($notice == 1) {
				$notice = "Uus kasutaja on edukalt loodud!";
				$name = null;
  				$surname = null;
  				$email = null;
  				$gender = null;
  				$birth_month = null;
  				$birth_year = null;
  				$birth_day = null;
  				$birth_date = null;
			} elseif ($notice == 2) {
				$notice = "Selle emailiga kasutaja on juba olemas!";
			} else {
				$notice = "Kasutaja loomine ebaõnnestus.";
			}
		}


  } //kui "Loo kasutaja" nuppu on vajutatud
  
?>

<!DOCTYPE html>
<html lang="et">
  <head>
    <meta charset="utf-8">
	<title>Konto loomine</title>
	<link rel="stylesheet" type="text/css" href="../news.css">
  </head>
  <body>
    <h1>Loo endale konto</h1>
	<p>See leht on valminud aine "Veebirakendused ja nende loomine" õppetöö raames.</p>
	<hr>
	
	<form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
	  <label>Eesnimi:</label><br>
	  <input name="first_name_input" type="text" value="<?php echo $name; ?>"><span><?php echo $name_error; ?></span><br>
      <label>Perekonnanimi:</label><br>
	  <input name="surname_input" type="text" value="<?php echo $surname; ?>"><span><?php echo $surname_error; ?></span>
	  <br><br>
	  
	  <input type="radio" name="gender_input" value="2" <?php if($gender == "2"){echo " checked";} ?>><label>Naine</label>
	  <input type="radio" name="gender_input" value="1" <?php if($gender == "1"){echo " checked";} ?>><label>Mees</label><br>
	  <span><?php echo $gender_error; ?></span>
	  <br>
	  
	  <label>Sünnikuupäev: </label>
	  <?php
	    echo '<select name="birth_day_input">' ."\n";
		echo "\t \t" .'<option value="" selected disabled>päev</option>' ."\n"; // selected - alguses valitud. disabled - hiljem valida ei saa
		for($i = 1; $i < 32; $i ++){
			echo "\t \t" .'<option value="' .$i .'"';
			if($i == $birth_day){
				echo " selected";
			}
			echo ">" .$i ."</option> \n";
		}
		echo "\t </select> \n";
	  ?>
	
		<label>Sünnikuu: </label>
	  <?php
	    echo '<select name="birth_month_input">' ."\n";
		echo "\t \t" .'<option value="" selected disabled>kuu</option>' ."\n";
		for ($i = 1; $i < 13; $i ++){
			echo "\t \t" .'<option value="' .$i .'"';
			if ($i == $birth_month){
				echo " selected ";
			}
			echo ">" .$month_names_et[$i - 1] ."</option> \n";
		}
		echo "</select> \n";
	  ?>
	
	  <label>Sünniaasta: </label>
	  <?php
	    echo '<select name="birth_year_input">' ."\n";
		echo "\t \t" .'<option value="" selected disabled>aasta</option>' ."\n";
		for ($i = date("Y") - 15; $i >= date("Y") - 110; $i --){
			echo "\t \t" .'<option value="' .$i .'"';
			if ($i == $birth_year){
				echo " selected ";
			}
			echo ">" .$i ."</option> \n";
		}
		echo "</select> \n";
	  ?>
	

	  <span><?php echo $birth_date_error ." " .$birth_day_error ." " .$birth_month_error ." " .$birth_year_error; ?></span>
	  
	  <br> <br>
	  <label>E-mail (kasutajatunnus):</label><br>
	  <input type="email" name="email_input" value="<?php echo $email; ?>"><span><?php echo $email_error; ?></span><br>
	   <br>
	  <label>Salasõna (min 8 tähemärki):</label><br>
	  <input type="password" name="password_input" value=""><span><?php echo $password_error; ?></span><br>
        <br>
	  <label>Korrake salasõna:</label><br>
	  <input type="password" name="confirm_password_input" ><span><?php echo $confirm_password_error; ?></span><br>
        <br>
	  <input type="submit" name="user_data_submit"  value="Loo kasutaja"><span><?php echo $notice; ?></span>
	</form>
	<hr>
	<p>Tagasi <a href="page.php">avalehele</a></p>
    <hr>
  </body>
</html>