<?php	
    //session_start();
	require_once "classes/SessionManager.class.php";
	SessionManager::sessionStart("vr", 0, "/~hanna.jairus/", "tigu.hk.tlu.ee");
	
	require_once "../../../../conf.php";
	//require_once "fnc_general.php";
	require_once "fnc_user.php";

    $myname = "Hanna Jairus";
	$currenttime = date("d.m.Y H:i:s");
	$timehtml = "\n <p>Lehe avamise hetkel on: " .$currenttime .".</p> \n";
	$semesterbegin = new DateTime("2021-1-25");
	$semesterend = new DateTime("2021-6-30");
	$semesterduration = $semesterbegin->diff($semesterend);
	$semesterdurationdays = $semesterduration->format("%r%a");
	$semesterdurhtml = "\n <p>2021 kevadsemestri kestus on " .$semesterdurationdays ." päeva.</p> \n";
	$today = new DateTime("now");
	$fromsemesterbegin = $semesterbegin->diff($today);
	$fromsemesterbegindays = $fromsemesterbegin->format("%r%a");

	//eestikeelne nädalapäev
	setlocale(LC_TIME, 'et_EE.utf8');
	$todayname ="<p> Täna on ". strftime('%A.');
	
	//semestri edenemine
	if($today < $semesterbegin){
		$semesterprogress = "\n <p>Semester pole veel alanud.</p> \n";
	}

	elseif($fromsemesterbegindays <= $semesterdurationdays){
		$semesterprogress = "\n"  .'<p>Semester edeneb: <meter min="0" max="' .$semesterdurationdays .'" value="' .$fromsemesterbegindays .'"></meter>.</p>' ."\n";
	} 

	else {
		$semesterprogress = "\n <p>Semester on lõppenud.</p> \n";
	}
	
	//piltide kataloog
	$picsdir = "./img/";
	$allfiles = array_slice(scandir($picsdir), 2);

	//lubatud faililaiendid
	$allowedphototypes = ["image/jpeg", "image/png", "image/jpg"];
	$picfiles = [];
	
	//tsükkel pildifailide leidmiseks
	foreach($allfiles as $file){
		$fileinfo = getimagesize($picsdir .$file);
		if(isset($fileinfo["mime"])){
			if(in_array($fileinfo["mime"], $allowedphototypes)){
				array_push($picfiles, $file);
			}
		}
	}

	//juhuslikud pildid
	$randomphotofunc = array_rand($picfiles,3);

// sisselogimine – toon välja need muutujad, mida küsin htmli osas logi sisse lahtrites:
	$notice = null;
	$email = null;
	$email_error = null;
	$password_error = null;
	
	//kontrollime, kas email ja parool on põhimõtteliselt olemas: e-mail tingimused, parooli pikkuse tingimus, pole tühi ja midagi vb veel)
	if (isset($_POST["login_submit"])) {
		// kontrollime, kas email ja parool on olemas:
		$notice = sign_in($_POST["email_input"], $_POST["password_input"]);
    }

?>
<!DOCTYPE html>
<html lang="et">
<head>
	<meta charset="utf-8">
	<title>Veebirakendused ja nende loomine 2021</title>
</head>
<body>
	<h1>
	<?php
		echo $myname;
	?>
	</h1>
	<p>See leht on valminud aine "Veebirakendused ja nende loomine" õppetöö raames.</p>
    	<hr>
	<h2>Logi sisse</h2>
	<form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
		<label>E-mail (kasutajatunnus):</label><br>
		<input type="email" name="email_input" value="<?php echo $email; ?>"><span><?php echo $email_error; ?></span><br><br>
		<label>Salasõna:</label><br>
		<input type="password" name="password_input"><span><?php echo $password_error; ?></span><br><br>
		<input name="login_submit" type="submit" value="Logi sisse!"><span><?php echo $notice; ?></span>
	</form>
	<p>Loo endale <a href="add_user.php">kasutajakonto</a>!</p>
	<hr>
	<?php
		echo $timehtml;
		echo $semesterdurhtml;
		echo $semesterprogress;
		echo $todayname;
		echo "</p>";
	?>
	<img src="<?php echo $picsdir .$picfiles[$randomphotofunc[0]] ?>" width="300px" height="300px" alt="bambus">
	<img src="<?php echo $picsdir .$picfiles[$randomphotofunc[1]] ?>" width="300px" height="300px" alt="bambus">
	<img src="<?php echo $picsdir .$picfiles[$randomphotofunc[2]] ?>" width="300px" height="300px" alt="bambus">
	<!--https://tigu.hk.tlu.ee/~hanna.jairus/Veebirakendused/veebirakendused/1.png-->
</body>
</html>