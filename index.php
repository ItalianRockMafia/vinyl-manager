<?php
session_start();

require '../global/functions/apicalls.php';
require '../global/functions/irm.php';
require '../global/functions/telegram.php';
$config = require "../config.php";
require_once '../global/functions/header.php';
require_once '../global/functions/footer.php';

$menu = renderMenu();
$options['nav'] = $menu;
$options['title'] = "IRM | Record Library";
$header = getHeader($options);

echo $header;
$footer = renderFooter();

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

	
	$allIrmUsers = json_decode(getCall($config->api_url . "users?transform=1"),true);
	?>
	<h1>IRM-Record Library</h1>
	<p class="desc">With this tool, you can manage your Record Library. Also you have access to your friends library</p>
	
	<div class="card-deck">
  <div class="card">
    <div class="card-body">
      <h5 class="card-title">My Library</h5>
      <p class="card-text">Here you can manage your record library. This include view, edit and share your library with friends.</p>
	  <a href="library.php" class="btn btn-success">Open</a>
    </div>
  </div>
  <div class="card">
    <div class="card-body">
      <h5 class="card-title">Wishlist</h5>
      <p class="card-text">Manage your wishlist. You can share it with your friends.</p>
	 <!-- <a href="#" class="btn btn-success">Open</a> -->
	 <button type="button" class="btn btn-success" data-toggle="modal" data-target="#comingSoon">
  Open
</button>

    </div>
  </div>
  <div class="card">
    <div class="card-body">
      <h5 class="card-title">Friends library</h5>
      <p class="card-text">View the library of your friends.</p>
      <form class="form" method="POST" action="friend.php">
        <div class="form-group ">
  	      <label for="friend" class="sr-only">Select friend</label>
		      <select class="form-control" name="friend">
		      <?php
		        foreach($allIrmUsers['users'] as $irm_user){
			        echo '<option value="' . $irm_user['userID'] . '">' . $irm_user['firstname'] . ' ' . $irm_user['lastname'] . ' (' . $irm_user['tgusername'] . ')</option>';
		        }
		      ?>
		      </select>
          </div>
          <div class="form-group">
          <button type="submit" class="btn btn-success mb-2">View</button>

          </div>
	    </form>
    </div>
  </div>
</div>

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
  

}
else {
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

echo $footer;