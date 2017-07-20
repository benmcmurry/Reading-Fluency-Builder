<?php
include_once('../../../connectFiles/connect_sr.php');
$passage_id = $_POST['passage_id'];
$passage_text = $_POST['passage_text'];
$source = $_POST['source'];
$length = $_POST['length'];
$lexile = $_POST['lexile'];
$flesch_reading_ease = $_POST['flesch_reading_ease'];
$flesch_kincaid_level = $_POST['flesch_kincaid_level'];
$library_id = $_POST['library_id'];
$modified_by = $_POST['modified_by'];

$passage_text = mysqli_real_escape_string($db, $passage_text);

if (is_float($flesch_reading_ease)){} else {$flesch_reading_ease = 0;}
if (is_float($flesch_kincaid_level)){} else {$flesch_kincaid_level = 0;}
$update_passage = "UPDATE Passages SET
    passage_text = '$passage_text',
    source = '$source',
    length = '$length',
    lexile = '$lexile',
    flesch_reading_ease = '$flesch_reading_ease',
    flesch_kincaid_level = '$flesch_kincaid_level',
    library_id = '$library_id',
    modified_by = '$modified_by'
 WHERE passage_id='$passage_id'";
if(!$result = $db->query($update_passage)){
  die('There was an error running the query [' . $db->error . ']');
} else {
  echo "Data saved.";
}

 ?>
