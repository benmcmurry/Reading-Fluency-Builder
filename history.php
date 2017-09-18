<?php
session_start();
include_once('../../connectFiles/connect_sr.php');

if(isset($_POST['time'])){
  echo "Time: ".$_POST['time']." , WPM: ".$_POST['wpm'];
  $score_update = $db->prepare("UPDATE Scores SET
    timed_reading_time = ?,
    timed_reading_wpm = ?
    WHERE user_id = ? AND passage_id = ?");

  $score_update->bind_param("ssss", $_POST['time'], $_POST['wpm'], $_SESSION['user_id'], $_SESSION['passage_id']);

  $score_update->execute();
  $score_entry = $score_update->get_result();

}

if(isset($_POST['userSpeed'])){

  echo $_POST['userSpeed'];
  $score_update = $db->prepare("UPDATE Scores SET
    scrolled_reading = ?
    WHERE user_id = ? AND passage_id = ?");

  $score_update->bind_param("sss", $_POST['userSpeed'], $_SESSION['user_id'], $_SESSION['passage_id']);

  $score_update->execute();
  $score_entry = $score_update->get_result();

}
if(isset($_POST['score'])){

  echo $_POST['score'];
  $score_update = $db->prepare("UPDATE Scores SET
    comprehension_quiz = ?
    WHERE user_id = ? AND passage_id = ?");

  $score_update->bind_param("sss", $_POST['score'], $_SESSION['user_id'], $_SESSION['passage_id']);

  $score_update->execute();
  $score_entry = $score_update->get_result();

}


  $history_record = $db->prepare("Insert into History (user_id, passage_id, timed_reading_wpm, timed_reading_time, scrolled_reading, comprehension_quiz, date_modified) values (?,?,?,?,?,?, now())");
  $history_record->bind_param("ssssss", $_SESSION['user_id'], $_SESSION['passage_id'], $_POST['wpm'], $_POST['time'], $_POST['userSpeed'], $_POST['score']);

  $history_record->execute();
  $history_entry = $history_record->get_result();


 ?>
