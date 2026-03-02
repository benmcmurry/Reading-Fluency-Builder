<?php
session_start();
include_once('../../connectFiles/connect_fb.php');

$netid = isset($_POST['netid']) ? $_POST['netid'] : '';
$passage_id = isset($_POST['passage_id']) ? (int) $_POST['passage_id'] : 0;
$target_email = isset($_POST['email']) ? trim($_POST['email']) : '';

if (!filter_var($target_email, FILTER_VALIDATE_EMAIL)) {
    echo '<p>Invalid email address.</p>';
    exit;
}

$email_query = $fb_db->prepare('SELECT * FROM Scores INNER JOIN Users ON Scores.netid = Users.netid INNER JOIN Passages ON Scores.passage_id = Passages.passage_id WHERE Scores.netid = ? AND Scores.passage_id = ?');
$email_query->bind_param('si', $netid, $passage_id);
$email_query->execute();
$result = $email_query->get_result();
$data = $result->fetch_assoc();

if (!$data) {
    echo '<p>No results found for this user and passage.</p>';
    exit;
}

$name = $data['full_name'];
$owner_email = $data['email'];
$timed_reading_wpm = $data['timed_reading_wpm'];
$timed_reading_time = $data['timed_reading_time'];
$scrolled_reading = $data['scrolled_reading'];
$comprehension_quiz = $data['comprehension_quiz'];
$date_modified = $data['date_modified'];

$title = $data['title'];
$author = $data['author'];
$length = $data['length'];
$lexile = $data['lexile'];
$flesch_reading_ease = $data['flesch_reading_ease'];
$flesch_kincaid_level = $data['flesch_kincaid_level'];

$send_owner = $target_email !== $owner_email;
$subject = "Reading Fluency Builder: {$title} - Results for {$name}";
$message = "\n<html><body>\n<h1>Reading Fluency Builder: {$title} - Results for {$name}</h1>\n<h2>Passage Info</h2>\n<p style='font-size:1.1em'>\n<strong>Title:</strong> {$title}<br>\n<strong>Author:</strong> {$author}<br>\n<strong>Length:</strong> {$length}<br>\n<strong>Lexile:</strong> {$lexile}<br>\n<strong>Flesch Reading Ease:</strong> {$flesch_reading_ease}<br>\n<strong>Flesch Kincaid Level:</strong> {$flesch_kincaid_level}<br>\n</p>\n<h2>Student Results for {$name}</h2>\n<p style='font-size:1.1em'>\n<strong>Date Last Accessed:</strong> {$date_modified}<br>\n<strong>Timed Reading WPM:</strong> {$timed_reading_wpm}<br>\n<strong>Timed Reading Time:</strong> {$timed_reading_time}<br>\n<strong>Scrolled Reading:</strong> {$scrolled_reading}<br>\n<strong>Quiz Results:</strong> {$comprehension_quiz}<br>\n</p>\n</body></html>\n";

$headers = [];
$headers[] = 'MIME-Version: 1.0';
$headers[] = 'Content-type: text/html; charset=utf-8';
$headers[] = 'From: Reading Fluency Builder <no-reply@elc.byu.edu>';

if (mail($target_email, $subject, $message, implode("\r\n", $headers))) {
    if ($send_owner && filter_var($owner_email, FILTER_VALIDATE_EMAIL)) {
        mail($owner_email, $subject, $message, implode("\r\n", $headers));
    }
    echo '<p>Mail sent successfully.</p>';
} else {
    echo '<p>Mail not sent.</p>';
}
