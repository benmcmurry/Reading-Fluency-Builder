<?php
include_once('../../../connectFiles/connect_sr.php');
$question_id = $_POST['question_id'];
$question_text = mysqli_real_escape_string($db, $_POST['question_text']);
$correct_answer = mysqli_real_escape_string($db, $_POST['correct_answer']);
$distractor_1 = mysqli_real_escape_string($db, $_POST['distractor_1']);
$distractor_2 = mysqli_real_escape_string($db, $_POST['distractor_2']);
$distractor_3 = mysqli_real_escape_string($db, $_POST['distractor_3']);

$modified_by = $_POST['modified_by'];



$update_question = "UPDATE Questions SET
    question_text = '$question_text',
    correct_answer = '$correct_answer',
    distractor_1 = '$distractor_1',
    distractor_2 = '$distractor_2',
    distractor_3 = '$distractor_3',
    modified_by = '$modified_by'
 WHERE question_id='$question_id'";
if(!$result = $db->query($update_question)){
  die('There was an error running the query [' . $db->error . ']');
} else {
  echo "Data saved for question $question_id.";
}

 ?>
