<?php
// Load the settings from the central config file
require_once 'config.php';
// Load the CAS lib
require_once 'CAS.php';
// Enable debugging
phpCAS::setDebug();
// Enable verbose error messages. Disable in production!
phpCAS::setVerbose(true);

// Initialize phpCAS
phpCAS::client(CAS_VERSION_2_0, $cas_host, $cas_port, $cas_context);
phpCAS::setNoCasServerValidation();
if (isset($_REQUEST['logout'])) {
  if ($_SERVER['SERVER_NAME'] == 'localhost') {
    $redirect = $_SERVER['SERVER_NAME']."/~Ben/curriculum/editors/";
  } else {$redirect = $_SERVER['SERVER_NAME']."/curriculum/editors/";}
    phpCAS::logout();
}
if (isset($_REQUEST['logout'])) {
  phpCAS::logout();
}

$auth = phpCAS::checkAuthentication();
if (isset($passage_id)) {
  // $additional ="&passage_id=$passage_id";
} else {
  $additional="";
}

if ($auth) {
  $netid = phpCAS::getUser();
  $id = "<span id='user'>$netid</span> | <a href='?logout='>Logout</a>";
} else {    
  phpCAS::forceAuthentication();
  $id = '';
}
$_SESSION['netid'] = $netid;
$_SESSION['name'] = phpCAS::getAttributes()['name'];
$_SESSION['email'] = phpCAS::getAttributes()['email'];

include_once('../../connectFiles/connect_sr.php');

$search_for_id = $sr_db->prepare("Select * from Users where netid= ? ");
$search_for_id->bind_param("s", $netid);
$search_for_id->execute();
$result = $search_for_id->get_result();

if (mysqli_num_rows($result)==0) {

  $add_user = $sr_db->prepare("Insert into Users (netid, full_name, email)
  values (?, ?, ?)");
  $add_user->bind_param("sss", $netid, $_SESSION['name'], $_SESSION['email']);
  $add_user->execute();
  $result = $add_user->get_result();
  $user_id = $sr_db->insert_id;


} else {
  $update_user = $sr_db->prepare("UPDATE Users SET full_name = ?, email = ? WHERE netid = ? ");
  $update_user->bind_param("sss", $_SESSION['name'], $_SESSION['email'], $netid);
  $update_user->execute();
  $update_user_result = $update_user->get_result();
}

$get_user_query = $sr_db->prepare("Select editor from Users where netid = ? ");
$get_user_query->bind_param("s", $google_id);
$get_user_query->execute();
$result = $get_user_query->get_result();

$user = $result->fetch_assoc();
$_SESSION['editor'] = $user['editor'];

?>

<?php
foreach (phpCAS::getAttributes() as $key => $value) {
if (is_array($value)) {
echo '<li>', $key, ':<ol>';
foreach($value as $item) {
      echo '<li><strong>', $item, '</strong></li>';
    }
echo '</ol></li>';
} else {
    echo '<li>', $key, ': <strong>', $value, '</strong></li>';
  }
}
?>