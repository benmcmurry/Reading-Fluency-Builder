<?php
session_start();
if($_SESSION['logged_in'] == 'yes' && $_SESSION['editor'] == "1"){
  // echo "logged in";
} else {
  $current_url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
  echo  "<meta HTTP-EQUIV='REFRESH' content='0; url=../start.php?current_url=$current_url'>";

}
include_once('../../../connectFiles/connect_sr.php');
$passage_id = $_POST['passage_id'];


$add_question = $db->prepare("Insert into Questions (passage_id) values ( ? )");
$add_question->bind_param("s", $passage_id);
$add_question->execute();
$result = $add_question->get_result();

  echo "Data saved.";

 ?>
