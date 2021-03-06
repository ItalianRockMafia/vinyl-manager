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
$options['title'] = "IRM | Add new record";
$header = getHeader($options);
$footer = renderFooter();

echo $header;


if(isset($_GET['add'])){
	$artist = $_POST['artist'];
	$album = $_POST['album'];
	$type = $_POST['recordtype'];

	$artistName = json_decode(getCall($config->api_url . "artists/" . $artist . "?transform=1"), true);
	$last_album = json_decode(getCall($config->lastfm['api_root'] . "2.0/?method=album.getinfo&api_key=" . $config->lastfm['api_key'] . "&album=" . $album . "&artist=" . $artist . "&format=json"), true);
	$mbid = $last_album['album']['mbid'];
	
	//$postfields = "{\n \t \"album_title\": \"$album\", \n \t \"artistIDFK\": \"$artist\", \n \t \"mbid\": \"$mbid\" \n }";
	$postfields = "{\n \t \"album_title\": \"$album\", \n \t \"artistIDFK\": \"$artist\" \n}";
	$album2db = postCall($config->api_url . "albums", $postfields);
	if(is_numeric($album2db)){
		$irmID = $_SESSION['irmID'];
		$postfields = "{\n \t \"userIDFK\": \"$irmID\", \n \t \"albumIDFK\": \"$album2db\", \n \t \"recordIDFK\": \"$type\" \n }";
		$albumAdded = postCall($config->api_url . "userHasAlbum", $postfields);
		if(is_numeric($albumAdded)){
			
			header('Location: https://italianrockmafia.ch/vinyl/?added=complete');
		} else {
		header('Location: https://italianrockmafia.ch/vinyl/?added=failed');
		}
	} else{
		header('Location: https://italianrockmafia.ch/vinyl/new.php?added=fail');
	}
	echo "<pre>"; print_r($postfields); echo "</pre>";
}

if(isset($_GET['addex'])){
	$album = $_POST['existing'];
	$irmID = $_SESSION['irmID'];
	$type = $_POST['recordtype'];

	$postfields = "{\n \t \"userIDFK\": \"$irmID\", \n \t \"albumIDFK\": \"$album\", \n \t \"recordIDFK\": \"$type\" \n }";
	$albumAdded = postCall($config->api_url . "userHasAlbum", $postfields);
	if(is_numeric($albumAdded)){
		
	header('Location: https://italianrockmafia.ch/vinyl/?added=complete');
	} else {
		header('Location: https://italianrockmafia.ch/vinyl/?added=failed');
	}
}

?>

<div class="topspacer"></div>
<main role="main">
	<div class="container">
	<?php

saveSessionArray($tg_user);
$access = $_SESSION['access'];
if($access >=3){

if ($tg_user !== false) {
	
	$artists = json_decode(getCall($config->api_url . "artists?transform=1"), true);
	$records = json_decode(getCall($config->api_url . "albumArtist?transform=1"), true);

	?>
	<h1>Add recordto your library</h1>
	<h2>Add existing record</h2>
	<form method="POST" action="?addex=1">
  <div class="form-group">
  <label for="existing" class="">Select record</label></a>
		<select class="form-control" id="existing" name="existing"><?php
		
		foreach($records["albumArtist"] as $record){
			echo '<option value="' . $record['albumID'] . '">' . $record['artist'] . ' - ' . $record['album_title'] . '</option>';
		}
	?>
	</select>
	</div>
	<div class="form-group">
		<?php
	$records = json_decode(getCall($config->api_url . "records?transform=1"),true);
	foreach($records['records'] as $record){
		echo '<div class="form-check form-check-inline">';
		echo '<input class="form-check-input" type="radio" name="recordtype" id="record' . $record['recordType'] . '" value="' . $record['recordID'] .'"';
		if($record['recordID'] == "1"){
			echo 'checked';
		}
		echo '>';
		echo '<label class="form-check-label" for="record' . $record['recordType'] . '">'. $record['recordType'] .'</label>';
		echo '</div>';

	}
?>
  </div>
	<button type="submit" class="btn btn-success">Add to my records</button>

</form>
<div class="topspacer"></div>
<h2>Add new record</h2>
<form method="POST" action="?add=1">
<div class="form-group">
		<label for="album">Album title</label>
		<input type="text" class="form-control" name="album" id="album" placeholder="Silver">
	</div>

<div class="form-group">
	  <label for="artist">Select artist</label><a href="add-data.php?artist=1"><i class="fa fa-plus-circle righticon" aria-hidden="true"></i></a>
	  <select id="artist" name="artist" class="form-control"><?php
		
		foreach($artists["artists"] as $artist){
			echo '<option value="' . $artist['artistID'] . '">' . $artist['artist'] . '</option>';
		}
	?>
	</select>
	<div class="form-group">
		<?php
	$records = json_decode(getCall($config->api_url . "records?transform=1"),true);
	foreach($records['records'] as $record){
		echo '<div class="form-check form-check-inline">';
		echo '<input class="form-check-input" type="radio" name="recordtype" id="record' . $record['recordType'] . '" value="' . $record['recordID'] .'"';
		if($record['recordID'] == "1"){
			echo 'checked';
		}
		echo '>';
		echo '<label class="form-check-label" for="record' . $record['recordType'] . '">'. $record['recordType'] .'</label>';
		echo '</div>';

	}
?>
  </div>
	<button type="submit" class="btn btn-success">Add to my records</button>
</form>
<?php

}else {
	echo '
	<div class="alert alert-warning" role="alert">
	<strong>Warning.</strong> You have no access to this page.
	</div>
';
}
} else {
	echo '
	<div class="alert alert-danger" role="alert">
	<strong>Error.</strong> You need to <a href="https://italianrockmafia.ch/login.php">login</a> first.
  </div>
';
}
?>

<!-- Modal coming soon -->
<div class="modal fade" id="comingSoon" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Coming Soon</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        This feature is still in development.
      </div>
      <div class="modal-footer">
			<button type="button" class="btn btn-success" data-dismiss="modal">OK</button>
      </div>
    </div>
  </div>
</div>


<?php

echo $footer;