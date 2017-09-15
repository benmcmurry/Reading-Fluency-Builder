<?php
session_start();
include_once('../../connectFiles/connect_sr.php');

$email_query = $db->prepare("SELECT * from History INNER JOIN Users on History.google_id=Users.google_id INNER JOIN Passages on History.passage_id=Passages.passage_id where History.google_id=? and History.passage_id=?");
$email_query->bind_param("ss", $_POST['google_id'], $_POST['passage_id']);
$email_query->execute();
$result = $email_query->get_result();
while ($data = $result->fetch_assoc()) {
  $full_name = $data['full_name'];
  $email = $data['email'];
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

}
$to = $_POST['email'].", ".$email;
$subject = "SoftRead: $title - Results for $full_name";
$message = <<<EOT
SoftRead: $title - Results for $full_name <br />
Passage Info: <br />
Title: $title <br />
Author: $author <br />
Length: $length <br />
Lexile: $lexile <br />
Flesch Reading Ease: $flesch_reading_ease <br />
Flesch Kincaid Level: $flesch_kincaid_level <br />

Student Results for $full_name <br />
Date Last Accessed: $date_modified <br />
Timed Reading WPM: $timed_reading_wpm <br />
Timed Reading Time: $timed_reading_time <br />
Scrolled Reading: $scrolled_reading <br />
Quiz Results: $comprehension_quiz <br />



EOT;


$headers[] = 'MIME-Version: 1.0';
$headers[] = 'Content-type: text/html; charset=iso-8859-1';

// Additional headers
$headers[] = 'To: <'.$_POST['email'].'>';
$headers[] = 'From: SoftRead <no-reply@elc.byu.edu>';
$headers[] = 'Cc: '.$full_name.' <'.$email.'>';


mail($to, $subject, $message, implode("\r\n", $headers));

if(mail($to, $subject, $message, implode("\r\n", $headers)))
{
  echo "Mail Sent Successfully";
}else{
  echo "Mail Not Sent";
}

//
// echo "Message Sent."
?>
