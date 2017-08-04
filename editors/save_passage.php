<?php
include_once('../../../connectFiles/connect_sr.php');
$passage_id = $_POST['passage_id'];
$passage_title = mysqli_real_escape_string($db, $_POST['passage_title']);
$passage_text = stripslashes(mysqli_real_escape_string($db, $_POST['passage_text']));
$author = mysqli_real_escape_string($db, $_POST['author']);
$source = mysqli_real_escape_string($db, $_POST['source']);
$length = mysqli_real_escape_string($db, $_POST['length']);
$lexile = mysqli_real_escape_string($db, $_POST['lexile']);
$flesch_reading_ease = (float)$_POST['flesch_reading_ease'];
$flesch_kincaid_level = (float)$_POST['flesch_kincaid_level'];
$library_id = mysqli_real_escape_string($db, $_POST['library_id']);
$vocabulary =  stripcslashes(mysqli_real_escape_string($db, $_POST['vocabulary']));
$modified_by = $_POST['modified_by'];


$passage_text = mysqli_real_escape_string($db, $passage_text);
$vocabulary = mysqli_real_escape_string($db, $vocabulary);


if (is_float($flesch_reading_ease)){} else {$flesch_reading_ease = 0;}
if (is_float($flesch_kincaid_level)){} else {$flesch_kincaid_level = 0;}
$update_passage = "UPDATE Passages SET
    passage_text = '$passage_text',
    title = '$passage_title',
    author = '$author',
    source = '$source',
    length = '$length',
    lexile = '$lexile',
    flesch_reading_ease = '$flesch_reading_ease',
    flesch_kincaid_level = '$flesch_kincaid_level',
    library_id = '$library_id',
    vocabulary = '$vocabulary',
    modified_by = '$modified_by'
 WHERE passage_id='$passage_id'";
if(!$result = $db->query($update_passage)){
  die('There was an error running the query [' . $db->error . ']');
} else {

  echo "Data saved.";
}

 ?>
