<?php
session_start();
include_once('../../connectFiles/connect_sr.php');

$search_for_netid = $sr_db->prepare("Select netid from Netids where netid=?");
$search_for_netid->bind_param("s", $_POST['netid']);
$search_for_netid->execute();
$results = $search_for_netid->get_result();
if ($results->num_rows == 0) {
  
  echo "<br /><h3>Your netid could not be found.</h3>";

}else {


$_SESSION['cas'] = $_POST['netid'];
$attach_netid = $sr_db->prepare("UPDATE Users SET
  cas = ?
  WHERE user_id = ?");

$attach_netid->bind_param("ss", $_POST['netid'], $_POST['user_id']);

$attach_netid->execute();
$attach_netid_action = $attach_netid->get_result();
echo "<br /><h3>Your netid has successfully been attached to your account.<br /><br /> Your page with refresh automatically. </h3><script>setTimeout(function() {
  window.location.reload(true);}, 2000);</script>";
}
?>
