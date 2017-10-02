<?php
session_start();
if($_SESSION['logged_in'] == 'yes'){
  //  echo "logged in";
  // echo $_SESSION['cas'];
} else {
  //echo "not logged in";
  echo  "<meta HTTP-EQUIV='REFRESH' content='0; url=start.php'>";
  return;
}
$google_id = $_SESSION['google_id'];
$user_id = $_SESSION['user_id'];
$netid = $_SESSION['cas'];
include_once('../../connectFiles/connect_sr.php');
if(isset($_GET['passage_id'])) {
  $_SESSION['passage_id'] = $_GET['passage_id'];
  $current_passage = $_GET['passage_id'];
  $passage_query = $sr_db->prepare("Select * from Passages where passage_id=?");
  $passage_query->bind_param("s", $current_passage);
  $passage_query->execute();
  $passage_results = $passage_query->get_result();

  while($passage_results_row = $passage_results->fetch_assoc()){
    $title = "SoftRead 3.0 - ".$passage_results_row['title'];
    $passage_name = $passage_results_row['title'];
    $source = $passage_results_row['source'];
    $passage = $passage_results_row['passage_text'];
    $wordcount=$passage_results_row['length'];
    $vocabulary = $passage_results_row['vocabulary'];
  }
  $passage_results->free(); //free results
$passage_id = $_GET['passage_id'];

$scores_query = $sr_db->prepare("Select * from Scores where user_id=? and passage_id=?");
$scores_query->bind_param("ss", $_SESSION['user_id'], $passage_id);
$scores_query->execute();
$scores_results = $scores_query->get_result();
if (!$scores_results->fetch_assoc())
  {
    $scores_results->free();
    $scores_query = $sr_db->prepare("Insert into Scores (user_id, google_id, passage_id, date_modified) values (?, ?, ?, now())");
    $scores_query->bind_param("sss", $_SESSION['user_id'], $_SESSION['google_id' ], $_SESSION['passage_id']);
    $scores_query->execute();
    $scores_results = $scores_query->get_result();
  } else {
    $scores_results->free();
    }
    $scores_query = $sr_db->prepare("Select * from Scores where user_id=? and passage_id=?");
    $scores_query->bind_param("ss", $_SESSION['user_id'], $_SESSION['passage_id']);
    $scores_query->execute();
    $scores_results = $scores_query->get_result();

    while ($scores_results_rows = $scores_results->fetch_assoc()){
      $timed_reading_wpm = $scores_results_rows['timed_reading_wpm'];
      $timed_reading_time = $scores_results_rows['timed_reading_time'];
      $scrolled_reading = $scores_results_rows['scrolled_reading'];
      $comprehension_quiz = $scores_results_rows['comprehension_quiz'];
      $date_modified = $scores_results_rows['date_modified'];
    }




} else {
$title = "SoftRead 3.0";

}


if($_SESSION['editor'] == "1"){$editor = true;} else {$editor = false;}


?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />

<link href="https://fonts.googleapis.com/css?family=Martel+Sans:400,700" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Roboto+Slab" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600,700" rel="stylesheet">

<link rel="stylesheet" href="style.css">

<script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>

  <?php
    if(isset($_GET['page'])) {
      echo "var page='".$_GET['page']."';";

    } else {echo "var page='instructions';";}
    if(isset($_GET['passage_id'])) {
      echo "var passage_id='".$_GET['passage_id']."';";
    } else {echo "var passage_id='';";

    }


  ?>


</script>
<script src="js/js.js"></script>

<title>
  <?php echo $title; ?>
</title>

</head>
<body>
  <div id="header">

    <div id="menu-btn" >
      <a id="open"><img src="images/open.png" /></a>
    </div>
    <div id="user-btn">
      <?php
      echo "<img id='user-image' src='".$_SESSION['image_url']."' />";?>
      <div id="drop-down">
      <?php
echo "Welcome, ".$_SESSION['given_name']."!";

    ?>
      <a href="#" class="popup_link" id="settings"><img class='icon' src='images/settings.png' />Settings</a>
      <?php
      if($_SESSION['editor'] == "1") {
        echo "<a href='editors/new_passage.php'><img class='icon' src='images/new.png' />New Passage</a>";
        if(isset($passage_id)) {
          echo "<a href='editors/edit.php?passage_id=".$passage_id."'><img class='icon' src='images/edit.png' />Edit Passage</a>";
        }
      }
       ?>
       <a href="logout.php"><img class='icon' src='images/signout.png' />Sign Out</a>
       <?php
       if ($_GET['page'] != 'instructions') {
       echo "<div id='stats'>
       <strong>Your Scores for this Passage</strong><br />
       <strong>Timed Reading</strong> <br />
       <span class='timed_reading'>Time: $timed_reading_time WPM: $timed_reading_wpm</span><br />
       <strong>Scrolled Reading WMP:</strong> <span class='scrolled_reading'>$scrolled_reading</span><br />
       <strong>Quiz Score:</strong> <span class='comprehension_quiz'>$comprehension_quiz</span><br />
       <a id='email_results' class='btn popup_link' style='color: white'>Email Results</a>
     </div>";
   }
     ?>
    </div>
    </div>

    <?php if(isset($current_passage)) {
      echo $title; }
      else { echo 'SoftRead 3.0';}
      ?>
  </div>

  <div id="main">
    <div id="nav-panel">
      <ul id='reading-list'>
      <?php include_once('list.php'); ?>
      </ul>
    </div>
    <div id="content">
      <div id="navbar">
        <a id="reading-btn" class="nav-btn">Reading</a>
        <a id="scroller-btn" class="nav-btn">Scrolled Reading</a>
        <a id="timer-btn" class="nav-btn">Timed Reading</a>
        <a id="quiz-btn" class="nav-btn">Quiz</a>
        <a id="vocab-btn" class="nav-btn">Vocabulary</a>
      </div>
      <div id="page">
        <!-- instructions page -->
        <div class="page" id="instructions">
          <h1>Welcome to SoftRead</h1>
          <p>Begin by using the menu on the right to select a passage to read. After you select a passage, there will be a menu at the top of the page.</p> <p>The <strong>Reading</strong> tab lets you read the passage. The <strong>Scrolled Reading</strong> tab lets you set a speed at which the text will scroll so that you can improve reading rate. The <strong>Timed Reading</strong> tab lets you time yourself as you read at any pace you'd like. The <strong>Quiz</strong> tab includes comprehension questions. The <strong>Vocabulary</strong> tab give you a list of vocabulary words from the reading.</p></div>
        </div>

        <!-- reading page -->
        <div class="page" id="reading">
          <?php
          if(isset($current_passage)) {
            echo $passage; }
          else { echo 'SoftRead 3.0';}
          echo "This passage comes from ".$source.".";
          ?>

        </div>
        <!-- scroller page -->
        <div class="page" id="scroller">
          <div class='block'>
            <div id='window'>
              <div id='scrollPassage'>
                <?php echo $passage; ?>
              </div>
            </div>
            <div id='instruction'>
              <!-- <p class='instructions'> Select how fast you want to read. When you push ok, the text above will begin scrolling.</p> -->
              <div contenteditable="true" id='userSpeed'>Enter Rate as WPM (1000)</div>
              <a class="btn" id='go' onclick='scrollThePassage("<?php echo $wordcount; ?>")'>Go!</a>
              <a class="btn" id='reset-scroller' href="index.php?passage_id=<?php echo $current_passage; ?>&page=scroller">Reset</a>
            </div>
          </div>
        </div>
        <!-- timer page -->
        <div class="page" id="timer">
          <p class='instructions'>Click on 'Start' to start the timer. When you are finished reading, click 'Stop.'</p>
          <div class="btn-bar">
            <a class='btn timer-btn' id="start-timer" onclick='startTheTimer()'>Start</a>
            <a class='btn timer-btn' id='timer-results' href="index.php?passage_id=<?php echo $current_passage; ?>&page=timer">Reset</a>
            <?php echo "<a class='btn timer-btn' id='stop-timer' onclick='stopTheTimer($wordcount)'>Stop</a>"; ?>
          </div>
          <div id="timer-btn_bar2">
          </div>
          <?php
              echo $passage."<br />";
           ?>
        </div>

        <!-- quiz page -->
        <div class="page" id="quiz">
          <?php
            $query_quiz = $sr_db->prepare("Select * from Questions where passage_id= ? order by question_order asc");
            $query_quiz->bind_param("s", $passage_id);
            $query_quiz->execute();
            $quiz_results = $query_quiz->get_result();

          while($quiz_results_rows = $quiz_results->fetch_assoc()){
              echo "<div class='question-box'><div class='stem'>".$quiz_results_rows['question_text']."</div>";
              $answers = array(
              "<div class='answer correct-answer'><input type='radio' name='".$quiz_results_rows['question_id']."' value='correct'> ".$quiz_results_rows['correct_answer']."</div>",
              "<div class='answer'><input type='radio' name='".$quiz_results_rows['question_id']."' value='incorrect'> ".$quiz_results_rows['distractor_1']."</div>",
              "<div class='answer'><input type='radio' name='".$quiz_results_rows['question_id']."' value='incorrect'> ".$quiz_results_rows['distractor_2']."</div>",
              "<div class='answer'><input type='radio' name='".$quiz_results_rows['question_id']."' value='incorrect'> ".$quiz_results_rows['distractor_3']."</div>"
            );
            if ($quiz_results_rows['correct_answer'] == "True") {
              echo $answers[0];
              echo $answers[1];

            } elseif ($quiz_results_rows['correct_answer'] == "False") {
              echo $answers[1];
              echo $answers[0];
            }
            else {
            shuffle($answers);
              echo $answers[0];
              echo $answers[1];
              echo $answers[2];
              echo $answers[3];

            }
            echo "</div>";
          }

          ?>
<div class="btn-bar">
          <a id="check-answers" class="btn">Check Answers</a></div>
        </div>
        <!-- end quiz page -->

        <!-- vocab page -->
        <div class="page" id="vocab">
          <?php
          if($vocabulary == ""){
            $query_vocab = $sr_db->prepare("Select * from Vocabulary where passage_id= ? order by word asc");
            $query_vocab->bind_param("s", $passage_id);
            $query_vocab->execute();
            $vocab_results = $query_vocab->get_result();

            while($vocab_results_row = $vocab_results->fetch_assoc()){

              echo "<p class='vocab'><strong>".$vocab_results_row['word']."</strong> - ".$vocab_results_row['definition']."<br />";
              if ($vocab_results_row['example']) { echo "<em>".$vocab_results_row['example']."</em></p>";}
            }
          } else {
            echo $vocabulary;
          }
          ?>

        </div>
        <!-- end vocab page -->

      </div>
    </div>
  </div>
  <div id="footer">
    Copyright &copy; <span id="year">year</span>. English Language Center
  </div>
  <div id="invisible-background"></div>
  <div id="email_results_popup" class="popup">
    <a class='close_popup' id='close_email_popup'>x</a>
    <?php echo "<h2>Email Results</h2><br />
    <form id='email_results_form'>
      Please enter the email address you wish to send the results to.
      <input type='hidden' name='user_id' value='$user_id' />
      <input type='hidden' name='passage_id' value='$passage_id' />
      <input type='text' id='email' name='email' style='width:100%; font-size: 1.3em; margin-top: 1em;margin-bottom: 1em;'/>
      </form>
      <a class='btn' id='send_email'>Send Email</>
      ";
      ?>
  <div id="sent" class="response">
  </div>
</div>
<div id="settings_popup" class="popup">
  <a class='close_popup' id='close_email_popup'>x</a>
  <?php echo "<h2>Settings</h2>
  <p>Enter in your BYU NetID for access to more passages.</p>
    <form id='attach_netid_form'>
    <input type='hidden' name='user_id' value='$user_id' />
    <input type='text' id='netid' name='netid' value='$netid' style='width:100%; font-size: 1.3em; margin-top: 1em;margin-bottom: 1em;'/>
    </form>
  <a class='btn' id='attach_netid'>Validate NetID</a>
  ";
  ?>
  <div id="attached" class="response">
  </div>
</div>














</body>
</html>
