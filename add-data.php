<?php
session_start();
$date = new DateTime();
require '../global/functions/apicalls.php';
require '../global/functions/telegram.php';
$config = require "../config.php";
require '../global/functions/irm.php';
$tg_user = getTelegramUserData();



?>
<!doctype html>
<html>
	<head>
		<meta charset="utf-8">
 	   <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
			<link rel="stylesheet" href="../global/main.css">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
		<script src="https://use.fontawesome.com/c414fc2c21.js"></script>
		<title>IRM - Meetup planer</title>
	</head>
	<body>


		<nav class="navbar navbar-expand-lg navbar-dark bg-danger">
	<a class="navbar-brand" href="https://italianrockmafia.ch">ItalianRockMafia</a>
	  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
		<span class="navbar-toggler-icon"></span>
	  </button>
	<div class="collapse navbar-collapse" id="navbarSupportedContent">
		<ul class="navbar-nav mr-auto">
		<li class="nav-item">
        				<a class="nav-link" href="https://italianrockmafia.ch/main.php">Home</a>
      				</li>
							<li class="nav-item">
        				<a class="nav-link" href="https://italianrockmafia.ch/settings.php">Settings</a>
      				</li>
			  <li class="nav-item">
				<a class="nav-link" href="https://italianrockmafia.ch/meetup">Events</a>
			  </li>
			  <li class="nav-item">
				<a class="nav-link" href="../emp">EMP</a>
			  </li>
			  <li class="nav-item active">
				<a class="nav-link" href="https://italianrockmafia.ch/vinyl">Vinyl <span class="sr-only">(current)</span></a>
			  </li>
				</ul>
				<ul class="nav navbar-nav navbar-right">
				<li class="nav-item">
        			<a class="nav-link" href="https://italianrockmafia.ch/login.php?logout=1">Logout</a>
      			</li>
		</ul>
</nav>
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
?>
			</div>
			</main>
			<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
			<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
			<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
				</body>
			</html>
