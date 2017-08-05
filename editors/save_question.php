<?php
include_once('../../../connectFiles/connect_sr.php');
$question_id = $_POST['question_id'];
$question_text = mysqli_real_escape_string($db, $_POST['question_text']);
$correct_answer = mysqli_real_escape_string($db, $_POST['correct_answer']);
$distractor_1 = mysqli_real_escape_string($db, $_POST['distractor_1']);
$distractor_2 = mysqli_real_escape_string($db, $_POST['distractor_2']);
$distractor_3 = mysqli_real_escape_string($db, $_POST['distractor_3']);

$modified_by = $_POST['modified_by'];

$question_id = explode("_", $question_id);
$question_id = $question_id[0];

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

// $backup_question = "Insert into Questions_backup (passage_id, question_id, question_text, question_order, correct_answer, distractor_1, distractor_2, distractor_3, date_created, date_modified, modified_by) values
// ()"
  echo "Data saved for question $question_id.";
}

 ?>
