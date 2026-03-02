<?php
session_start();
include_once('../../connectFiles/connect_fb.php');

$time = isset($_POST['time']) ? $_POST['time'] : null;
$wpm = isset($_POST['wpm']) ? $_POST['wpm'] : null;
$user_speed = isset($_POST['userSpeed']) ? $_POST['userSpeed'] : null;
$score = isset($_POST['score']) ? $_POST['score'] : null;

if ($time !== null) {
    echo 'Time: ' . htmlspecialchars($time, ENT_QUOTES, 'UTF-8') . ' , WPM: ' . htmlspecialchars($wpm, ENT_QUOTES, 'UTF-8');
    $score_update = $fb_db->prepare('UPDATE Scores SET timed_reading_time = ?, timed_reading_wpm = ?, date_modified = NOW() WHERE netid = ? AND passage_id = ?');
    $score_update->bind_param('sssi', $time, $wpm, $_SESSION['netid'], $_SESSION['passage_id']);
    $score_update->execute();
}

if ($user_speed !== null) {
    echo htmlspecialchars($user_speed, ENT_QUOTES, 'UTF-8');
    $score_update = $fb_db->prepare('UPDATE Scores SET scrolled_reading = ?, date_modified = NOW() WHERE netid = ? AND passage_id = ?');
    $score_update->bind_param('ssi', $user_speed, $_SESSION['netid'], $_SESSION['passage_id']);
    $score_update->execute();
}

if ($score !== null) {
    echo htmlspecialchars($score, ENT_QUOTES, 'UTF-8');
    $score_update = $fb_db->prepare('UPDATE Scores SET comprehension_quiz = ?, date_modified = NOW() WHERE netid = ? AND passage_id = ?');
    $score_update->bind_param('ssi', $score, $_SESSION['netid'], $_SESSION['passage_id']);
    $score_update->execute();
}

$history_record = $fb_db->prepare('INSERT INTO History (netid, passage_id, timed_reading_wpm, timed_reading_time, scrolled_reading, comprehension_quiz, date_modified) VALUES (?, ?, ?, ?, ?, ?, NOW())');
$history_record->bind_param('sissss', $_SESSION['netid'], $_SESSION['passage_id'], $wpm, $time, $user_speed, $score);
$history_record->execute();
