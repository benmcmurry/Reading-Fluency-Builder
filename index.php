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

<script src="js/js.js"></script>
</head>
<body>
  <div id="header">
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
        <a class="nav-btn">Reading</a>
        <a class="nav-btn">Scrolled Reading</a>
        <a class="nav-btn">Timed Reading</a>
        <a class="nav-btn">Quiz</a>
        <a class="nav-btn">Vocabulary</a>
      </div>
      <div id="passage">
      <?php if(isset($current_passage)) {
        echo $passage; }
        else { echo 'SoftRead 3.0';}
        ?>
      </div>
    </div>
  </div>
  <div id="footer">
    Copyright &copy; <span id="year">year</span>. English Language Center
  </div>


<!-- reading page -->
<div class="page" id="reading">
    <?php

      if(isset($current_passage)) {
        echo "<h3>".$title."</h3>";
        echo $passage;
      }
      else {
        echo "Instructions";
      }
     ?>
</div>


<!-- scroller page -->
<div class="page" id="scroll">
  <div class='block'>
    <div id='window'>
      <div id='scrollPassage'>
        <?php echo $passage; ?>
      </div>
    </div>
    <div id='instruction'>
      <p class='instructions'> Select how fast you want to read. When you push ok, the text above will begin scrolling. When you are finished, you can click on 'Quiz' above and take a quiz.</p>
      <input id='userSpeed' type='text' />
      <input type='button' id='' value='Go!' onclick='scrollThePassage("<?php echo $wordcount; ?>")' />
      <input id='reset' type='button' value='Reset' onclick='resetit()' />
    </div>
  </div>
</div>



<!-- timer page -->
<div class="page" id="timer">
    <div class='block'>
      <p>Click on 'Start' to start the timer. When you are finished reading, click 'Stop.'</p>
      <p>Go at your own pace. When you click stop, your words per minute will be displayed on the screen.<br /><br />
        <input type='button' id='start' value='Start' onclick='startTheTimer()' />
        <input id='stop' type='button' value='Stop' onclick='stopTheTimer()'/>
        <input type='button' id='resettimer' value='Reset' onclick='resetTheTimer()' /><br /><br />
        <div id='FieldWpm'></div>
    <?php
    if(isset($current_passage)) {
      echo "<h3>".$title."</h3>";
      echo $passage;
    }
    else {
      echo "Instructions"; }
     ?>
   </div>
</div>

<!-- quiz page -->
<div class="page" id="quiz">

</div>
<!-- end quiz page -->

<!-- vocab page -->
<div class="page" id="vocab">

</div>
<!-- end vocab page -->









</body>
</html>
