<?php
include_once('../../../connectFiles/connect_sr.php');
session_start();
$new_passage = "Insert into Passages (title, creator, length, lexile, date_created) values('New Passage by ".$_SESSION['given_name']."','".$_SESSION['google_id']."','1','1', now())";
if(!$result = $db->query($new_passage)){
  die('There was an error running the query [' . $db->error . ']');
} else {
  $last_id = $db->insert_id;

  header( 'Location: ../editors/edit.php?passage_id='.$last_id ) ;
}
 ?>
