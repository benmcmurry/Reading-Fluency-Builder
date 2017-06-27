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

<div data-role="page" class="ui-responsive-panel" data-theme="a" id="reading">



  <div class="panel" data-role="panel" data-theme="b" data-position="left" data-display="push" id="nav-panel">
    <?php include("list.php"); ?>

  </div> <!-- end panel -->
  <div class="header" data-role="header" data-position="fixed" data-id="main-header">
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
    <?php
      if(isset($current_passage)) {
        echo "<h3>".$title."</h3>";
        echo $passage;
      }
      else {
        echo "Instructions";
      }
     ?>

  </div><!-- end main -->

  <div class="footer" data-role="footer" id="footer" data-id="main-footer">
    <h1>Copyright &copy; <span id="year">year</span>. English Language Center</h1>
  </div> <!-- end footer -->
</div><!-- end page -->

<div id="scroll" data-role="page"  data-theme="a">
<div class="panel" data-role="panel" data-theme="b" data-position="left" data-display="push" id="nav-panel"></div>
  <div class="header" data-role="header" data-id="main-header" data-position="fixed"></div>
  <div data-role="main" class="ui-content">
    <h1> Scroll </h1>
  </div>
  <div class="footer" data-role="footer" data-id="main-footer"></div>
</div>

<div id="timer" data-role="page"  data-theme="a">
<div class="panel" data-role="panel" data-theme="b" data-position="left" data-display="push" id="nav-panel"></div>
  <div class="header" data-role="header" data-id="main-header" data-position="fixed"></div>
  <div data-role="main" class="ui-content">
    <h1> Timer </h1>
  </div>
  <div class="footer" data-role="footer" data-id="main-footer"></div>
</div>

<div id="quiz" data-role="page"  data-theme="a">
<div class="panel" data-role="panel" data-theme="b" data-position="left" data-display="push" id="nav-panel"></div>
  <div class="header" data-role="header" data-id="main-header" data-position="fixed"></div>
  <div data-role="main" class="ui-content">
    <h1> quiz </h1>
  </div>
  <div class="footer" data-role="footer" data-id="main-footer"></div>
</div>

<div id="vocab" data-role="page"  data-theme="a">
<div class="panel" data-role="panel" data-theme="b" data-position="left" data-display="push" id="nav-panel"></div>
  <div class="header" data-role="header" data-id="main-header" data-position="fixed"></div>
  <div data-role="main" class="ui-content">
    <h1> Vocab </h1>
  </div>
  <div class="footer" data-role="footer" data-id="main-footer"></div>
</div>

</body>
</html>
