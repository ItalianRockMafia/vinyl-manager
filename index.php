<?php
session_start();
?>
<!doctype html>
<html>
	<head>
		<meta charset="utf-8">
 	   <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
			<link rel="stylesheet" href="../global/main.css">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
		<script src="https://use.fontawesome.com/c414fc2c21.js"></script>
		<title>IRM - Vinyl Library</title>
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
	</div>
</nav>
<div class="topspacer"></div>
<main role="main">
	<div class="container">

<?php

require '../global/functions/apicalls.php';
require '../global/functions/irm.php';
require '../global/functions/telegram.php';
$config = require "../config.php";


$tg_user = getTelegramUserData();

if ($tg_user !== false) {
	$_SESSION['tgID'] = $tg_user['id'];
	$irm_users = json_decode(getCall($config->api_url . "users?transform=1&filter=telegramID,eq," . $tg_user['id']), true);
	foreach($irm_users['users'] as $user){
		$irm_user['id'] = $user['userID'];
	}
	
	$_SESSION['irmID'] = $irm_user['id'];
	saveSessionArray($tg_user);
	?>
	<h1>IRM-Record Library</h1>
<p class="desc">With this tool, you can manage your Record Library. Also you have access to your friends library</p>
<h2>Your records <a href="new.php"><i class="fa fa-plus-circle righticon" aria-hidden="true"></i></a></h2>
<?php
	$my_records = json_decode(getCall($config->api_url ."userAlbums?transform=1&filter=telegramID,eq," . $tg_user['id'] . "&order[]=artist&order[]=album_title"), true);
	echo '<div class="card-columns">';
	foreach($my_records['userAlbums'] as $record){
		$artist = $record['artist'];
		$album = $record['album_title'];
		$mbid = $record['mbid'];
		
		if(empty($mbid)){
			
		$last_album = json_decode(getCall($config->lastfm['api_root'] . "2.0/?method=album.getinfo&api_key=" . $config->lastfm['api_key'] . "&album=" . $album . "&artist=" . $artist . "&format=json"), true);
	} else {
			$last_album = json_decode(getCall($config->lastfm['api_root'] . "2.0/?method=album.getinfo&api_key=" . $config->lastfm['api_key'] . "&mbid=" . $mbid ."&format=json"),true);
		}
		
		for ($i=0; $i < count($last_album['album']['image']); $i++) { 
			
            if($last_album['album']['image'][$i]['size'] == 'mega') {
              $largeImg = $last_album['album']['image'][$i]['#text'];
              
            }
          }
?>
<div class="card" style="">
  <img class="card-img-top" src="<?php echo $largeImg ?>" alt="Card image cap">
  <div class="card-body">
    <h5 class="card-title"><?php echo $last_album['album']['artist']; ?> </h5>
    <p class="card-text"><?php echo $last_album['album']['name']; ?> </p>
    <a href="<?php echo $last_album['album']['url']; ?> " class="btn btn-primary" target="_blank" >View album</a>
  </div>
</div>
<?php

  
	}

} else {
	echo '
	<div class="alert alert-danger" role="alert">
	<strong>Error.</strong> You need to <a href="https://italianrockmafia.ch/login.php">login</a> first.
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
