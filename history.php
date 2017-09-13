<?php
session_start();
include_once('../../connectFiles/connect_sr.php');

if(isset($_POST['time'])){
  echo $_POST['time']." , WPM: ".$_POST['wpm']."<br /> Passage: ".$_SESSION['passage_id']."<br />";
  $history_update = $db->prepare("UPDATE History SET
    timed_reading_time = ?,
    timed_reading_wpm = ?
    WHERE google_id = ? AND passage_id = ?");

  $history_update->bind_param("ssss", $_POST['time'], $_POST['wpm'], $_SESSION['google_id'], $_SESSION['passage_id']);

  $history_update->execute();
  $history_entry = $history_update->get_result();
}

if(isset($_POST['userSpeed'])){

  echo $_POST['userSpeed']."<br />";
  $history_update = $db->prepare("UPDATE History SET
    scrolled_reading = ?
    WHERE google_id = ? AND passage_id = ?");

  $history_update->bind_param("sss", $_POST['userSpeed'], $_SESSION['google_id'], $_SESSION['passage_id']);

  $history_update->execute();
  $history_entry = $history_update->get_result();

}
if(isset($_POST['score'])){

  echo $_POST['score']."<br />";
  $history_update = $db->prepare("UPDATE History SET
    comprehension_quiz = ?
    WHERE google_id = ? AND passage_id = ?");

  $history_update->bind_param("sss", $_POST['score'], $_SESSION['google_id'], $_SESSION['passage_id']);

  $history_update->execute();
  $history_entry = $history_update->get_result();

}
 ?>
