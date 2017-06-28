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

<link rel="stylesheet" href="https://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.css">
<link rel="stylesheet" href="style.css">

<script src="https://code.jquery.com/jquery-1.11.3.min.js"></script>
<script src="https://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.js"></script>
<script src="js/js.js"></script>
</head>
<body>

<!-- Main Page -->
<div data-role="page" class="ui-responsive-panel" data-theme="a" id="main">



  <div data-role="panel" data-theme="b" data-position="left" data-display="push" id="nav-panel">
    <?php include("list.php"); ?>

  </div> <!-- end panel -->
  <div data-role="header" data-position="fixed" data-id="main-header">
    <?php
      if (isset($title)) { echo "<h1>".$title."</h1>";} else {echo "<h1>SoftRead 3</h1>";}
     ?>
    <a href="#nav-panel" data-icon="bars" data-iconpos="notext">Menu</a>

    <div data-role="navbar">
          <ul>
            <li><a href="#reading">Reading</a></li>
            <li><a href="#scroll">Scrolled Reading</a></li>
            <li><a href="#timer">Timed Reading</a></li>
            <li><a href="#quiz">Quiz</a></li>
            <li><a href="#vocab">Vocabulary</a></li>
          </ul>
        </div>

  </div><!-- end header -->



  <div data-role="main" class="ui-content">
    <h1> Instructions </h1>

  </div><!-- end main -->

  <div class="footer" data-role="footer" data-position="fixed" data-id="main-footer">
    <h1>Copyright &copy; <span id="year">year</span>. English Language Center</h1>
  </div> <!-- end footer -->
</div>
<!-- end main page -->

<!-- reading page -->
<div data-role="page" class="ui-responsive-panel" data-theme="a" id="reading">
  <div data-role="panel" data-theme="b" data-position="left" data-display="push" id="nav-panel2">
    <?php include("list.php"); ?>

  </div> <!-- end panel -->
  <div data-role="header" data-position="fixed" data-id="main-header">
    <?php
      if (isset($title)) { echo "<h1>".$title."</h1>";} else {echo "<h1>SoftRead 3</h1>";}
     ?>
    <a href="#nav-panel2" data-icon="bars" data-iconpos="notext">Menu</a>

    <div data-role="navbar">
          <ul>
            <li><a href="#reading">Reading</a></li>
            <li><a href="#scroll">Scrolled Reading</a></li>
            <li><a href="#timer">Timed Reading</a></li>
            <li><a href="#quiz">Quiz</a></li>
            <li><a href="#vocab">Vocabulary</a></li>
          </ul>
        </div>

  </div><!-- end header -->
  <div data-role="main" class="ui-content">
    <div class='left-block'>
      <?php include("list.php"); ?>
    </div>
    <div class='right-block'>

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
   </div><!-- end main -->

  <div class="footer" data-role="footer" data-position="fixed" data-id="main-footer">
    <h1>Copyright &copy; <span id="year">year</span>. English Language Center</h1>
  </div> <!-- end footer -->
</div>
<!-- end reading page -->

<!-- scroller page -->
<div data-role="page" class="ui-responsive-panel" data-theme="a" id="scroll">
    <div data-role="panel" data-theme="b" data-position="left" data-display="push" id="nav-panel3">
      <?php include("list.php"); ?>

    </div> <!-- end panel -->
    <div data-role="header" data-position="fixed" data-id="main-header">
      <?php
        if (isset($title)) { echo "<h1>".$title."</h1>";} else {echo "<h1>SoftRead 3</h1>";}
       ?>
      <a href="#nav-panel3" data-icon="bars" data-iconpos="notext">Menu</a>

      <div data-role="navbar">
            <ul>
              <li><a href="#reading">Reading</a></li>
              <li><a href="#scroll">Scrolled Reading</a></li>
              <li><a href="#timer">Timed Reading</a></li>
              <li><a href="#quiz">Quiz</a></li>
              <li><a href="#vocab">Vocabulary</a></li>
            </ul>
          </div>

    </div><!-- end header -->
    <div data-role="main" class="ui-content">
      <div id='window'>
        <div id='scrollPassage'>
          <?php echo $passage; ?>
        </div>
      </div>
      <div id='instruction'>" + "
        <p> Select how fast you want to read. You'll be asked if you are ready. When you push ok the text will begin scrolling on the left in the box. When you are finished, you can click on 'Quiz' above and take a quiz.</p>
        <input id='userSpeed' type='text' />
        <input type='button' id='' value='Go!' onclick='scrollThePassage("<?php echo $wordcount; ?>")' />
        <input id='reset' type='button' value='Reset' onclick='resetit()' />
      </div>

    </div><!-- end main -->

    <div class="footer" data-role="footer" data-position="fixed" data-id="main-footer">
      <h1>Copyright &copy; <span id="year">year</span>. English Language Center</h1>
    </div> <!-- end footer -->
</div>
<!-- end scroller page -->


<!-- timer page -->
<div data-role="page" class="ui-responsive-panel" data-theme="a" id="timer">
  <div data-role="panel" data-theme="b" data-position="left" data-display="push" id="nav-panel4">
    <?php include("list.php"); ?>

  </div> <!-- end panel -->
  <div data-role="header" data-position="fixed" data-id="main-header">
    <?php
      if (isset($title)) { echo "<h1>".$title."</h1>";} else {echo "<h1>SoftRead 3</h1>";}
     ?>
    <a href="#nav-panel4" data-icon="bars" data-iconpos="notext">Menu</a>

    <div data-role="navbar">
          <ul>
            <li><a href="#reading">Reading</a></li>
            <li><a href="#scroll">Scrolled Reading</a></li>
            <li><a href="#timer">Timed Reading</a></li>
            <li><a href="#quiz">Quiz</a></li>
            <li><a href="#vocab">Vocabulary</a></li>
          </ul>
        </div>

  </div><!-- end header -->
  <div data-role="main" class="ui-content">
    <?php
      echo "Timer";
     ?>

  </div><!-- end main -->

  <div class="footer" data-role="footer" data-position="fixed" data-id="main-footer">
    <h1>Copyright &copy; <span id="year">year</span>. English Language Center</h1>
  </div> <!-- end footer -->
</div>
<!-- end timer page -->

<!-- quiz page -->
<div data-role="page" class="ui-responsive-panel" data-theme="a" id="quiz">
  <div data-role="panel" data-theme="b" data-position="left" data-display="push" id="nav-panel5">
    <?php include("list.php"); ?>

  </div> <!-- end panel -->
  <div data-role="header" data-position="fixed" data-id="main-header">
    <?php
      if (isset($title)) { echo "<h1>".$title."</h1>";} else {echo "<h1>SoftRead 3</h1>";}
     ?>
    <a href="#nav-panel5" data-icon="bars" data-iconpos="notext">Menu</a>

    <div data-role="navbar">
          <ul>
            <li><a href="#reading">Reading</a></li>
            <li><a href="#scroll">Scrolled Reading</a></li>
            <li><a href="#timer">Timed Reading</a></li>
            <li><a href="#quiz">Quiz</a></li>
            <li><a href="#vocab">Vocabulary</a></li>
          </ul>
        </div>

  </div><!-- end header -->
  <div data-role="main" class="ui-content">
    <?php
      echo "Quiz";
     ?>

  </div><!-- end main -->

  <div class="footer" data-role="footer" data-position="fixed" data-id="main-footer">
    <h1>Copyright &copy; <span id="year">year</span>. English Language Center</h1>
  </div> <!-- end footer -->
</div>
<!-- end quiz page -->

<!-- vocab page -->
<div data-role="page" class="ui-responsive-panel" data-theme="a" id="vocab">
  <div data-role="panel" data-theme="b" data-position="left" data-display="push" id="nav-panel6">
    <?php include("list.php"); ?>

  </div> <!-- end panel -->
  <div data-role="header" data-position="fixed" data-id="main-header">
    <?php
      if (isset($title)) { echo "<h1>".$title."</h1>";} else {echo "<h1>SoftRead 3</h1>";}
     ?>
    <a href="#nav-panel6" data-icon="bars" data-iconpos="notext">Menu</a>

    <div data-role="navbar">
          <ul>
            <li><a href="#reading">Reading</a></li>
            <li><a href="#scroll">Scrolled Reading</a></li>
            <li><a href="#timer">Timed Reading</a></li>
            <li><a href="#quiz">Quiz</a></li>
            <li><a href="#vocab">Vocabulary</a></li>
          </ul>
        </div>

  </div><!-- end header -->
  <div data-role="main" class="ui-content">
    <?php
    echo "Vocab";
     ?>

  </div><!-- end main -->

  <div class="footer" data-role="footer" data-position="fixed" data-id="main-footer">
    <h1>Copyright &copy; <span id="year">year</span>. English Language Center</h1>
  </div> <!-- end footer -->
</div>
<!-- end vocab page -->









</body>
</html>
