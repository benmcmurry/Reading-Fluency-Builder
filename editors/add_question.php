<?php
include_once('../../../connectFiles/connect_sr.php');
$passage_id = $_POST['passage_id'];


$add_question = "Insert into Questions (passage_id) values ($passage_id)";

if(!$result = $db->query($add_question)){
  die('There was an error running the query [' . $db->error . ']');
} else {
  echo "Data saved.";
}

 ?>
