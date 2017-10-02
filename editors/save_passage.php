<?php
session_start();
if($_SESSION['logged_in'] == 'yes' && $_SESSION['editor'] == "1"){
  // echo "logged in";
} else {
  $current_url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
  echo  "<meta HTTP-EQUIV='REFRESH' content='0; url=../start.php?current_url=$current_url'>";

}
include_once('../../../connectFiles/connect_sr.php');
$passage_id =  $_POST['passage_id'];
$passage_title =  $_POST['passage_title'];
$passage_text = $_POST['passage_text'];
$author =  $_POST['author'];
$source =  $_POST['source'];
$length =  $_POST['length'];
$lexile =  $_POST['lexile'];
$flesch_reading_ease =  (float)$_POST['flesch_reading_ease'];
$flesch_kincaid_level =  (float)$_POST['flesch_kincaid_level'];
$library_id =  $_POST['library_id'];
$vocabulary =  stripcslashes( $_POST['vocabulary']);
$modified_by =  $_POST['modified_by'];


// $passage_text =  $passage_text);
// $vocabulary =  $vocabulary);


if (is_float($flesch_reading_ease)){} else {$flesch_reading_ease = 0;}
if (is_float($flesch_kincaid_level)){} else {$flesch_kincaid_level = 0;}
$update_passage = $sr_db->prepare("UPDATE Passages SET
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
