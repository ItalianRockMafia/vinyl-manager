<?php
session_start();
$date = new DateTime();
require '../global/functions/apicalls.php';
require '../global/functions/telegram.php';
$config = require "../config.php";
require '../global/functions/irm.php';
$tg_user = getTelegramUserData();

require_once '../global/functions/header.php';
require_once '../global/functions/footer.php';


$menu = renderMenu();
$options['nav'] = $menu;
$options['title'] = "IRM | Add data";
$header = getHeader($options);
$footer = renderFooter();
echo $header;




?>

<div class="topspacer"></div>
<main role="main">
	<div class="container">
	<?php

saveSessionArray($tg_user);
if ($tg_user !== false) {

	if(isset($_GET['addartist'])){
		$artist = $_POST['artist'];
		$postfields = "{\"artist\": \"$artist\"}";
		postcall($config->api_url . "artists", $postfields);
		header('Location: new.php?new=1');
	}

	if(isset($_GET['addalbum'])){
		$album = $_POST['album'];
		$artist = $_POST['artist'];
		$postfields = "{\"album_title\": \"$album\",\"artistIDFK\": \"$artist\"}";
		postcall($config->api_url . "albums", $postfields);
		header('Location: new.php?new=1');
	}

	if(isset($_GET['del'])){
	$rec2del = $_GET['del'];
	$deleted = deleteCall($config->api_url . "userHasAlbum/" . $rec2del);
	if(is_numeric($deleted)){
		header('Location: https://italianrockmafia.ch/vinyl/?removed=success');
	} else {
		header('Location: https://italianrockmafia.ch/vinyl/?removed=fail');
		
	}
}



	if(isset($_GET['artist'])){
		?>
	<form method="POST" action="?addartist=1">
	<div class="form-group">
		<label for="artist">New Artist</label>
		<input type="text" class="form-control" name="artist" id="artist" placeholder="Gotthard">
	  </div>
	  <button type="submit" class="btn btn-success">Submit</button>
	  </form>
	<?php
	}

	if(isset($_GET['album'])){
		$artists = json_decode(getCall($config->api_url . "artists?transform=1"), true);
		?>
	<form method="POST" action="?addalbum=1">
	<div class="form-group">
		<label for="artist">New Album</label>
		<input type="text" class="form-control" name="album" id="album" placeholder="Silver">
	  </div>
	  <div class="form-group">
	  <label for="artist" class="">Artist</label>
		<select class="form-control" id="artist" name="artist"><?php
		
		foreach($artists["artists"] as $artist){
			echo '<option value="' . $artist['artistID'] . '">' . $artist['artist'] . '</option>';
		}
	?>
	</select>
  </div>
	  <button type="submit" class="btn btn-success">Submit</button>
	  </form>
	<?php
	}

	
} else {
	echo '
	<div class="alert alert-danger" role="alert">
	<strong>Error.</strong> You need to <a href="login.php>login</a> first
  </div>
';
}

echo $footer;
?>
			