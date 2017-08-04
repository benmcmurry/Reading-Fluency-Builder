<?php
include_once('../../../connectFiles/connect_sr.php');

$question_id = $_POST['question_id'];
$question_id = substr($question_id, 7);

$add_question = "Delete from Questions where question_id=$question_id";

if(!$result = $db->query($add_question)){
  die('There was an error running the query [' . $db->error . ']');
} else {
  echo "Question Deleted.";
}

 ?>
