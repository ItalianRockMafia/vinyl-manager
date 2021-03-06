<?php
session_start();
$user2show = $_POST['friend'];

require '../global/functions/apicalls.php';
require '../global/functions/irm.php';
require '../global/functions/telegram.php';
$config = require "../config.php";

require_once '../global/functions/header.php';
require_once '../global/functions/footer.php';

$friend = json_decode(getCall($config->api_url . "users/" . $user2show . "?transform=1"), true);

$menu = renderMenu();
$options['nav'] = $menu;
$options['title'] = "IRM | " . $friend['tgusername'] . "s library";
$header = getHeader($options);

echo $header;
?>

<div class="topspacer"></div>
<main role="main">
	<div class="container">

<?php


$tg_user = getTelegramUserData();

if ($tg_user !== false) {
	$_SESSION['tgID'] = $tg_user['id'];
	$irm_users = json_decode(getCall($config->api_url . "users?transform=1&filter=telegramID,eq," . $tg_user['id']), true);
	foreach($irm_users['users'] as $user){
		$irm_user['id'] = $user['userID'];
	}
	
	$_SESSION['irmID'] = $irm_user['id'];
	saveSessionArray($tg_user);
	$access = $_SESSION['access'];
	if($access >=3){


	?>
	<h1>IRM-Record Library</h1>
	<p class="desc">With this tool, you can manage your Record Library. Also you have access to your friends library</p>
	<h2>Records of <?php echo $friend['tgusername']; ?></h2>

	<?php
	$friend_records = json_decode(getCall($config->api_url ."userAlbums?transform=1&filter=telegramID,eq," . $friend['telegramID'] . "&order[]=artist&order[]=album_title"), true);
	$recordTypes = json_decode(getcall($config->api_url . "records=?transform=1"), true);

	if(empty($friend_records['userAlbums'])){
		die('<div class="alert alert-warning" role="alert">
		'. $friend['tgusername'] . ' has no records.' . '
	  </div>
	  </div>
	  </main>
	  <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
	  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
	  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
		  </body>
	  </html> 
	  ');
	}
	foreach($recordTypes['records'] as $type){
		echo '<div class="form-check form-check-inline">' . "\n\t";
		echo '<input class="form-check-input" type="checkbox" name="check_'. $type['recordType'] .'" id="check_'. $type['recordType'] . '" value="'. $type['recordType'] .'" onChange="handle' . $type['recordType'] . '()" checked>' . "\n\t";
		echo '<label class="form-check-label" for="check_'. $type['recordType'].'">'. $type['recordType'] .'</label>' . "\n";
		echo '</div>' . "\n";
	}
	

	echo '<div class="card-columns" style="display: inline-block;">';
	foreach($friend_records['userAlbums'] as $record){
		$artist = $record['artist'];
		$album = $record['album_title'];
		$recordType = $record['recordType'];
		$mbid = $record['mbid'];
		
		if(empty($mbid)){
			
		$last_album = json_decode(getCall($config->lastfm['api_root'] . "2.0/?method=album.getinfo&api_key=" . $config->lastfm['api_key'] . "&album=" . $album . "&artist=" . $artist . "&format=json"), true);
	} else {
			$last_album = json_decode(getCall($config->lastfm['api_root'] . "2.0/?method=album.getinfo&api_key=" . $config->lastfm['api_key'] . "&mbid=" . $mbid ."&format=json"),true);
		}
		for ($i=0; $i < count($last_album['album']['image']); $i++) { 
			
            if($last_album['album']['image'][$i]['size'] == 'mega') {
							$largeImg = "";
							$largeImg = $last_album['album']['image'][$i]['#text']; 
            } 
          }
?>
<div class="card <?php echo $recordType; ?>" style="">
  <img class="card-img-top" src="<?php echo $largeImg ?>" alt="<?php echo $last_album['album']['name'] . ' album cover';?>">
  <div class="card-body">
		<h5 class="card-title"><?php
		if(!empty($last_album['album']['artist'])){ 
			echo $last_album['album']['artist']; 
		} else {
			echo $artist;
		}
			?> </h5>
		<p class="card-text"><?php
		if(!empty($last_album['album']['name'])){
			echo $last_album['album']['name']; 
		} else {
			echo $album;
		}
		?> </p>
		<?php if(!empty($last_album['album']['url'])){
			echo '<a href="'. $last_album['album']['url'] . '"class="btn btn-primary" target="_blank" >View album</a>';
		} else {
			echo '<span class="d-inline-block" tabindex="0" data-toggle="tooltip" title="Album not found">';
			echo '<a href="#" class="btn btn-primary disabled" target="_blank">View album</a>';
			echo '</span>';
		}
		$largeImg = "http://www.51allout.co.uk/wp-content/uploads/2012/02/Image-not-found.gif";
	
			echo '	
	</div>
	<div class="card-footer">
      <small class="text-muted">'. $recordType  .'</small>
		</div>';
		?>
</div>
<?php

  
	}
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
</div>
			</main>
			<script type="text/javascript">
				<?php
				foreach($recordTypes['records'] as $recordType){
				echo 'function handle' . $recordType['recordType'] . "(){\n\t\t\t\t\t";
				echo 'var ' . $recordType['recordType'] . '_box = document.getElementById("check_' .  $recordType['recordType'] . '");' . "\n\t\t\t\t\t";
				echo 'if(' .  $recordType['recordType'] . "_box.checked == true){\n\t\t\t\t\t\t";
				echo "[].forEach.call(document.querySelectorAll('.". $recordType['recordType']."'), function (el) {
					el.style.display = 'inline-block';
				});\n\t\t\t\t\t";
				echo "} else {\n\t\t\t\t\t\t";
				echo "[].forEach.call(document.querySelectorAll('.". $recordType['recordType']."'), function (el) {
					el.style.display = 'none';
				});
				
			}}";
			}
			?>
			</script>
			<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
			<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
			<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
				</body>
			</html>