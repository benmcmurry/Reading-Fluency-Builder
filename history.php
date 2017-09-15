<?php
session_start();
include_once('../../connectFiles/connect_sr.php');

if(isset($_POST['time'])){
  echo "Time: ".$_POST['time']." , WPM: ".$_POST['wpm'];
  $history_update = $db->prepare("UPDATE History SET
    timed_reading_time = ?,
    timed_reading_wpm = ?
    WHERE user_id = ? AND passage_id = ?");

  $history_update->bind_param("ssss", $_POST['time'], $_POST['wpm'], $_SESSION['user_id'], $_SESSION['passage_id']);

  $history_update->execute();
  $history_entry = $history_update->get_result();
}

if(isset($_POST['userSpeed'])){

  echo $_POST['userSpeed'];
  $history_update = $db->prepare("UPDATE History SET
    scrolled_reading = ?
    WHERE user_id = ? AND passage_id = ?");

  $history_update->bind_param("sss", $_POST['userSpeed'], $_SESSION['user_id'], $_SESSION['passage_id']);

  $history_update->execute();
  $history_entry = $history_update->get_result();

}
if(isset($_POST['score'])){

  echo $_POST['score'];
  $history_update = $db->prepare("UPDATE History SET
    comprehension_quiz = ?
    WHERE user_id = ? AND passage_id = ?");

  $history_update->bind_param("sss", $_POST['score'], $_SESSION['user_id'], $_SESSION['passage_id']);

  $history_update->execute();
  $history_entry = $history_update->get_result();

}
 ?>
