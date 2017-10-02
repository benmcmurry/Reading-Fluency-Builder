<?php
session_start();
include_once('../../connectFiles/connect_sr.php');

$email_query = $sr_db->prepare("SELECT * from Scores INNER JOIN Users on Scores.user_id=Users.user_id INNER JOIN Passages on Scores.passage_id=Passages.passage_id where Scores.user_id=? and Scores.passage_id=?");
$email_query->bind_param("ss", $_POST['user_id'], $_POST['passage_id']);
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
$to1 = $_POST['email'];
$to2 = $email;
if ($to1 === $to2) {$different = FALSE;} else {$different = TRUE;}
$subject = "SoftRead: $title - Results for $full_name";
$message = <<<EOT
<html><body>
<h1>SoftRead: $title - Results for $full_name </h1>
<h2>Passage Info: </h2>
<p style='font-size:1.3em'>
<strong>Title:</strong> $title <br />
<strong>Author:</strong> $author <br />
<strong>Length:</strong> $length <br />
<strong>Lexile:</strong> $lexile <br />
<strong>Flesch Reading Ease:</strong> $flesch_reading_ease <br />
<strong>Flesch Kincaid Level:</strong> $flesch_kincaid_level <br />
</p>
<h2>Student Results for $full_name </h2>
<p style='font-size:1.3em'>
<strong>Date Last Accessed:</strong> $date_modified <br />
<strong>Timed Reading WPM:</strong> $timed_reading_wpm <br />
<strong>Timed Reading Time: </strong>$timed_reading_time <br />
<strong>Scrolled Reading: </strong>$scrolled_reading <br />
<strong>Quiz Results:</strong> $comprehension_quiz <br />
</p>
</body></html>


EOT;

$headers[] = 'MIME-Version: 1.0';
$headers[] = 'Content-type: text/html; charset=iso-8859-1';

// Additional headers
$headers[] = 'From: SoftRead <no-reply@elc.byu.edu>';



if(mail($to1, $subject, $message, implode("\r\n", $headers)))
{
  echo "<p>Mail Sent Successfully</p>";
  if($different) { mail($to2, $subject, $message, implode("\r\n", $headers));}
}else{
  echo "Mail Not Sent";
}

//
// echo "Message Sent."
?>
