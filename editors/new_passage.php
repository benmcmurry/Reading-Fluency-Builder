<?php
include_once('../../../connectFiles/connect_fb.php');
session_start();
$title = "New Passage by ".$_SESSION['given_name'];
$new_passage = $fb_db->prepare("Insert into Passages (title, creator, length, lexile, date_created) values ( ? , ? ,'1','1', now())");
$new_passage->bind_param("ss", $title, $_SESSION['google_id']);
$new_passage->execute();
$result = $new_passage->get_result();


  $last_id = $fb_db->insert_id;

  header( 'Location: ../editors/edit.php?passage_id='.$last_id ) ;

 ?>
