<?php
session_start();
if($_SESSION['logged_in'] == 'yes' && $_SESSION['editor'] == "1"){
  // echo "logged in";
} else {
  $current_url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
  echo  "<meta HTTP-EQUIV='REFRESH' content='0; url=../start.php?current_url=$current_url'>";

}
include_once('../../../connectFiles/connect_sr.php');
$question_id = mysqli_real_escape_string($db, $_POST['question_id']);
$question_text = mysqli_real_escape_string($db, $_POST['question_text']);
$correct_answer = mysqli_real_escape_string($db, $_POST['correct_answer']);
$distractor_1 = mysqli_real_escape_string($db, $_POST['distractor_1']);
$distractor_2 = mysqli_real_escape_string($db, $_POST['distractor_2']);
$distractor_3 = mysqli_real_escape_string($db, $_POST['distractor_3']);

$modified_by = mysqli_real_escape_string($db, $_POST['modified_by']);



$update_question = $db->prepare("UPDATE Questions SET
    question_text = ?,
    correct_answer = ?,
    distractor_1 = ?,
    distractor_2 = ?,
    distractor_3 = ?,
    modified_by = ?
 WHERE question_id=?");
 $update_question->bind_param("sssssss", $question_text, $correct_answer, $distractor_1, $distractor_2, $distractor_3, $modified_by, $question_id );
 $update_question->execute();
 $result = $update_question->get_result();

  echo "Data saved for question $question_id.";

 ?>
