<?php
session_start();
if($_SESSION['logged_in'] == 'yes'){
  // echo "logged in";
} else {
  $current_url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
  echo  "<meta HTTP-EQUIV='REFRESH' content='0; url=start.php?current_url=$current_url'>";

}

include_once('../../connectFiles/connect_sr.php');
if(isset($_GET['passage_id'])) {
  $current_passage = $_GET['passage_id'];
  $passage_query = "Select * from Passages where passage_id='$current_passage'";
  if(!$passage_results = $db->query($passage_query)){
    die('There was an error running the query [' . $db->error . ']');
  }
  while($passage_results_row = $passage_results->fetch_assoc()){
    $title = "SoftRead 3.0 - ".$passage_results_row['title'];
    $passage = $passage_results_row['passage_text'];
    $wordcount=$passage_results_row['length'];
  }
  $passage_results->free(); //free results
$passage_id = $_GET['passage_id'];

} else {
$title = "SoftRead 3.0";

}

?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://fonts.googleapis.com/css?family=Martel+Sans:400,700" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Roboto+Slab" rel="stylesheet">

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
    } else {echo "var passage_id='';";}

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
      <a href="#"><img class='icon' src='images/settings.png' />Settings</a>
       <a href="logout.php"><img class='icon' src='images/signout.png' />Sign Out</a>
    </div>
    </div>

    <?php if(isset($current_passage)) {
      echo $title; }
      else { echo 'SoftRead 3.0';}
      ?>
  </div>

  <div id="main">
    <div id="nav-panel">
      <?php include_once('list.php'); ?>
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
            if(isset($current_passage)) {
              echo "<h3>".$title."</h3>";
              echo $passage."<br />";


            }
            else {
            echo "Instructions";
            }
           ?>
        </div>

        <!-- quiz page -->
        <div class="page" id="quiz">
          <?php
            $query_quiz = "Select * from Questions where passage_id=$passage_id order by question_order asc";
            if(!$quiz_results = $db->query($query_quiz)){
              die('There was an error running the query [' . $db->error . ']');
            }

          while($quiz_results_rows = $quiz_results->fetch_assoc()){
              echo "<div class='question-box'><div class='stem'>".$quiz_results_rows['question_text']."</div>";
              $answers = array(
              "<div class='answer correct-answer'><input type='radio' name='".$quiz_results_rows['question_id']."' value='correct'> ".$quiz_results_rows['correct_answer']."</div>",
              "<div class='answer'><input type='radio' name='".$quiz_results_rows['question_id']."' value='incorrect'> ".$quiz_results_rows['distractor_1']."</div>",
              "<div class='answer'><input type='radio' name='".$quiz_results_rows['question_id']."' value='incorrect'> ".$quiz_results_rows['distractor_2']."</div>",
              "<div class='answer'><input type='radio' name='".$quiz_results_rows['question_id']."' value='incorrect'> ".$quiz_results_rows['distractor_2']."</div>"
            );
            shuffle($answers);
              echo $answers[0];
              echo $answers[1];
              echo $answers[2];
              echo $answers[3];
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

            $query_vocab = "Select * from Vocabulary where passage_id=$passage_id order by word asc";
            if(!$vocab_results = $db->query($query_vocab)){
              die('There was an error running the query [' . $db->error . ']');
            }

            while($vocab_results_row = $vocab_results->fetch_assoc()){

              echo "<p class='vocab'><strong>".$vocab_results_row['word']."</strong> - ".$vocab_results_row['definition']."<br />";
              if ($vocab_results_row['example']) { echo "<em>".$vocab_results_row['example']."</em></p>";}
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














</body>
</html>
