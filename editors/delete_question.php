<?php
session_start();
if($_SESSION['logged_in'] == 'yes' && $_SESSION['editor'] == "1" ){
  // echo "logged in";
} else {
  $current_url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
  echo  "<meta HTTP-EQUIV='REFRESH' content='0; url=../start.php?current_url=$current_url'>";

}
include_once('../../../connectFiles/connect_sr.php');

$question_id = $_POST['question_id'];
$question_id = substr($question_id, 7);

$add_question = $sr_db->prepare("Delete from Questions where question_id = ? ");
$add_question->bind_param("s", $question_id);
$add_question->execute();
$result = $add_question->get_result();

  echo "Question Deleted.";

 ?>
