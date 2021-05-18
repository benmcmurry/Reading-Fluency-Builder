<?php
session_start();
if($_SESSION['editor'] == "1"){
  // echo "logged in";
} else {
  $current_url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
  echo  "<meta HTTP-EQUIV='REFRESH' content='0; url=../index.php?passage_id=$passage_id'>";

}
include_once('../../../connectFiles/connect_fb.php');

$i=1;
foreach ($_POST as $question_id => &$value) {
 
    $update_order_query = $fb_db->prepare("Update Questions set question_order = ? where question_id = ?");
    $update_order_query->bind_param("ss", $i, $question_id);
    $update_order_query->execute();
    $result = $update_order_query->get_result();

    $i++;

}
echo "Question order saved.";


 ?>
