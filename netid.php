<?php
session_start();
include_once('../../connectFiles/connect_sr.php');
$_SESSION['cas'] = $_POST['netid'];
$attach_netid = $db->prepare("UPDATE Users SET
  cas = ?
  WHERE user_id = ?");

$attach_netid->bind_param("ss", $_POST['netid'], $_POST['user_id']);

$attach_netid->execute();
$attach_netid_action = $attach_netid->get_result();
echo "Your netid has successfully been attached to your account. Please refresh to see new passages.";
?>
