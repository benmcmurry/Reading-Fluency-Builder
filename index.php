<?php
include_once('../../connectFiles/connect_sr.php');
if(isset($_GET['passage_id'])) {
  $current_passage = $_GET['passage_id'];
  $passage_query = "Select * from Passages where passage_id='$current_passage'";
  if(!$passage_results = $db->query($passage_query)){
    die('There was an error running the query [' . $db->error . ']');
  }
  while($passage_results_row = $passage_results->fetch_assoc()){
    $title = $passage_results_row['title'];
    $passage = $passage_results_row['passage_text'];
    $wordcount=$passage_results_row['length'];
  }
  $passage_results->free(); //free results
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

    }
    if(isset($_GET['passage_id'])) {
      echo "passage_id='".$_GET['passage_id']."';";
    }
  ?>


</script>
<script src="js/js.js"></script>

<title>
  SoftRead 3.0 - <?php echo $title; ?>
</title>

</head>
<body>
  <div id="header">
    <div id="menu-btn" >
      <a id="open"><img src="open.png" /></a>
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
              <a class="btn" id='' onclick='scrollThePassage("<?php echo $wordcount; ?>")'>Go!</a>
              <a class="btn" id='reset-scroller' href="index.php?passage_id=<?php echo $current_passage; ?>&page=scroller">Reset</a>
            </div>
          </div>
        </div>
        <!-- timer page -->
        <div class="page" id="timer">
          <p class='instructions'>Click on 'Start' to start the timer. When you are finished reading, click 'Stop.'</p>
          <div id="timer-btn-bar">
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
quiz
        </div>
        <!-- end quiz page -->

        <!-- vocab page -->
        <div class="page" id="vocab">
vocab
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
