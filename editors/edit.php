<?php
session_start();
if($_SESSION['logged_in'] == 'yes' && $_SESSION['editor'] == "1" && isset($_GET['passage_id'])){
  // echo "logged in";
} else {
  $current_url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
  echo  "<meta HTTP-EQUIV='REFRESH' content='0; url=../start.php?current_url=$current_url'>";

}
$google_id = $_SESSION['google_id'];
$passage_id = $_GET['passage_id'];
include_once('../../../connectFiles/connect_sr.php');

$query = $db->prepare("Select * from Passages where passage_id= ? ");
$query->bind_param("s", $passage_id);
$query->execute();
$passage = $query->get_result();

while($passage_row = $passage->fetch_assoc()){
  $title= $passage_row['title'];
  $passage_text = $passage_row['passage_text'];
  $source = $passage_row['source'];
  $author = $passage_row['author'];
  $length = $passage_row['length'];
  $lexile = $passage_row['lexile'];
  $flesch_reading_ease = $passage_row['flesch_reading_ease'];
  $flesch_kincaid_level = $passage_row['flesch_kincaid_level'];
  $share_status = $passage_row['share_status'];
  $library_id = $passage_row['library_id'];
  $vocabulary = $passage_row['vocabulary'];
}
?>

<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
<link href="https://fonts.googleapis.com/css?family=Martel+Sans:400,700" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600,700" rel="stylesheet">

<link rel="stylesheet" href="../style.css">
<link rel="stylesheet" href="style.css">
<script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>
var nocontent=false;
page=false;
passage_id = <?php echo $passage_id; ?>;
google_id = "<?php echo $google_id; ?>";
</script>
<script src="../js/js.js">
</script>
<script src="js.js">
</script>

</head>
<body>
  <div id="header">
    <div id="user-btn">
      <?php
      echo "<img id='user-image' src='".$_SESSION['image_url']."' />";?>
      <div id="drop-down">
      <?php
echo "Welcome, ".$_SESSION['given_name']."!";

       echo "<a href='../index.php?passage_id=$passage_id'><img class='icon' src='../images/return.png' />Return<a>";
       ?>
       <a href='new_passage.php'><img class='icon' src='../images/new.png' />New Passage</a>
      <a href="#"><img class='icon' src='../images/settings.png' />Settings</a>
       <a href="../logout.php"><img class='icon' src='../images/signout.png' />Sign Out</a>
    </div>
  </div>
  SoftRead Editor: <span id='title' class='editable-passage' contenteditable="true" ><?php echo $title; ?></span>
</div>
<div id="main">
  <div id="save_dialog"></div>

  <div id="edit-menu">
    <h1 style="font-size:1.3em">Edit Menu</h1>
    <a id="save" class='button'>Save</a>

  <a class="navigator" href="#passage_text">Passage Text</a>
  <a class="navigator" href="#author">Author</a>
  <a class="navigator" href="#source">Source</a>
  <a class="navigator" href="#length">Length</a>
  <a class="navigator" href="#lexile">Lexile</a>
  <a class="navigator" href="#flesch_reading_ease">FRE</a>
  <a class="navigator" href="#flesch_kincaid_level">FKL</a>
  <a class="navigator" href="#library_id">Library</a>
  <a class="navigator" href="#passage_text">Vocabulary</a>
  <a class="navigator" href="#quiz">Quiz Questions</a>
  <a class='button' id='new_question'>Add question</a>
  <div id="save_dialog"></div>
  </div>
  <div id="inside-wrapper">
  <div class='editable-box' id='passage_details'>
<h1> Passage Information </h1>
  <div class='editable-chunk-special'>
    <div class='label'>Passage Text</div>
    <div id='passage_text' class='editable-passage' contenteditable='true'><?php echo $passage_text; ?></div>
  </div>
  <div class='editable-chunk'>
  <div class='label'>Author</div>
  <div id='author' class='editable-passage' contenteditable='true'><?php echo $author; ?></div>
</div>
<div class='editable-chunk'>
<div class='label'>Source</div>
<div id='source' class='editable-passage' contenteditable='true'><?php echo $source; ?></div>
</div>
<div class='editable-chunk'>  <div class='label'>Length</div>
  <div id='length' class='editable-passage' contenteditable='true'><?php echo $length;?></div>
</div>
<div class='editable-chunk'>
  <div class='label'>Lexile (example: 700)</div>
  <div id='lexile' class='editable-passage' contenteditable='true'><?php echo $lexile;?></div>
</div>
<div class='editable-chunk'>
  <div class='label'>Flesch Reading Ease (example: 58.4)</div>
  <div id='flesch_reading_ease' class='editable-passage' contenteditable='true'><?php echo $flesch_reading_ease;?></div>
</div>
<div class='editable-chunk'>
  <div class='label'>Flesch Kincaid Level (example: 9.4)</div>
  <div id='flesch_kincaid_level' class='editable-passage' contenteditable='true'><?php echo $flesch_kincaid_level;?></div>
</div>

<div class='editable-chunk'>
  <div class='label'>Library</div>
  <div id='library_id' class='editable-passage' contenteditable='true'><?php echo $library_id;?></div>
</div>
<div class='editable-chunk'>
  <div class='label'>Temporary Vocabulary Holder</div>
  <div id='vocabulary' class='editable-passage' contenteditable='true'>
    <?php
      if ($vocabulary == ""){
        $query_vocab = $db->prepare("Select * from Vocabulary where passage_id= ? order by word asc");
        $query_vocab->bind_param("s", $passage_id);
        $query_vocab->execute();
        $vocab_results = $query_vocab->get_result();

        while($vocab_results_row = $vocab_results->fetch_assoc()){

          echo "<p class='vocab'><strong>".$vocab_results_row['word']."</strong> - ".$vocab_results_row['definition']."<br />";
          if ($vocab_results_row['example']) { echo "<em>".$vocab_results_row['example']."</em></p>";}
        }
      } else{
        echo $vocabulary;
      }

    ?>
  </div>
</div>
</div>
<div class="editable-box">

  <h1 id="quiz"> Quiz Items</h1>
  <ul id="questions">

  <?php
    $query_quiz = $db->prepare("Select * from Questions where passage_id= ? order by question_order asc");
    $query_quiz->bind_param("s", $passage_id);
    $query_quiz->execute();
    $quiz_result = $query_quiz->get_result();


  while($quiz_results_rows = $quiz_result->fetch_assoc()){
      if(empty($quiz_results_rows['question_order'])) {$quiz_results_rows['question_order'] = 0;}
      echo "<li class='question-box' id='{$quiz_results_rows['question_id']}_{$quiz_results_rows['question_order']}'>
      <div class='delete' id='delete_{$quiz_results_rows['question_id']}'><img src='images/delete.png' /></div>
      <div class='handle'>
        <img src='images/cursor-move-icon.png' />
      </div>
              <div class='label'>Stem</div>
              <div id='question_text-{$quiz_results_rows['question_id']}' class='quiz_item editable' contenteditable='true'>".$quiz_results_rows['question_text']."</div>";
      $answers = array(
      "<div class='label indent'>Correct Answer</div><div id='correct_answer-{$quiz_results_rows['question_id']}' contenteditable='true' class='quiz_item editable indent'>".$quiz_results_rows['correct_answer']."</div>",
      "<div class='label indent'>Distractor 1</div><div id='distractor_1-{$quiz_results_rows['question_id']}' contenteditable='true' class='quiz_item editable indent'>".$quiz_results_rows['distractor_1']."</div>",
      "<div class='label indent'>Distractor 2</div><div id='distractor_2-{$quiz_results_rows['question_id']}' contenteditable='true' class='quiz_item editable indent'>".$quiz_results_rows['distractor_2']."</div>",
      "<div class='label indent'>Distractor 3</div><div id='distractor_3-{$quiz_results_rows['question_id']}' contenteditable='true' class='quiz_item editable indent'>".$quiz_results_rows['distractor_3']."</div>"
    );

      echo $answers[0];
      echo $answers[1];
      echo $answers[2];
      echo $answers[3];
      echo "</li>";
  }

  ?>
</ul>
</div>

</div>
</div>
<div id="invisible-background"></div>
</body>
</html>
