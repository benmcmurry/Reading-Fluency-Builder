<?php
session_start();
if($_SESSION['logged_in'] == 'yes' && $_SESSION['editor'] == "1"){
  // echo "logged in";
} else {
  $current_url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
  echo  "<meta HTTP-EQUIV='REFRESH' content='0; url=../start.php?current_url=$current_url'>";

}
include_once('../../../connectFiles/connect_sr.php');
$passage_id = mysqli_real_escape_string($db, $_POST['passage_id']);
$passage_title = mysqli_real_escape_string($db, $_POST['passage_title']);
$passage_text = stripslashes(mysqli_real_escape_string($db, $_POST['passage_text']));
$author = mysqli_real_escape_string($db, $_POST['author']);
$source = mysqli_real_escape_string($db, $_POST['source']);
$length = mysqli_real_escape_string($db, $_POST['length']);
$lexile = mysqli_real_escape_string($db, $_POST['lexile']);
$flesch_reading_ease = mysqli_real_escape_string($db, (float)$_POST['flesch_reading_ease']);
$flesch_kincaid_level = mysqli_real_escape_string($db, (float)$_POST['flesch_kincaid_level']);
$library_id = mysqli_real_escape_string($db, $_POST['library_id']);
$vocabulary =  stripcslashes(mysqli_real_escape_string($db, $_POST['vocabulary']));
$modified_by = mysqli_real_escape_string($db, $_POST['modified_by']);


$passage_text = mysqli_real_escape_string($db, $passage_text);
$vocabulary = mysqli_real_escape_string($db, $vocabulary);


if (is_float($flesch_reading_ease)){} else {$flesch_reading_ease = 0;}
if (is_float($flesch_kincaid_level)){} else {$flesch_kincaid_level = 0;}
$update_passage = $db->prepare("UPDATE Passages SET
    passage_text = ?,
    title = ?,
    author = ?,
    source = ?,
    length = ?,
    lexile = ?,
    flesch_reading_ease = ?,
    flesch_kincaid_level = ?,
    library_id = ?,
    vocabulary = ?,
    modified_by = ?
    WHERE passage_id=?");
$update_passage->bind_param("ssssssssssss", $passage_text, $passage_title, $author, $source, $length, $lexile, $flesch_reading_ease, $flesch_kincaid_level, $library_id, $vocabulary, $modified_by, $passage_id);
$update_passage->execute();
$result = $update_passage->get_result();


  echo "Data saved.";


 ?>
