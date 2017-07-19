<?php
session_start();
if($_SESSION['logged_in'] == 'yes' && $_SESSION['google_id'] == "110466253529943196246" && isset($_GET['passage_id'])){
  // echo "logged in";
} else {
  $current_url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
  echo  "<meta HTTP-EQUIV='REFRESH' content='0; url=../start.php?current_url=$current_url'>";

}
$google_id = $_SESSION['google_id'];
$passage_id = $_GET['passage_id'];
include_once('../../../connectFiles/connect_sr.php');

$query = "Select * from Passages where passage_id=$passage_id";
if(!$passage = $db->query($query)){
  die('There was an error running the query [' . $db->error . ']');
}
while($passage_row = $passage->fetch_assoc()){
  $title= $passage_row['title'];
  $passage_text = $passage_row['passage_text'];
  $source = $passage_row['source'];
  $length = $passage_row['length'];
  $lexile = $passage_row['lexile'];
  $flesch_reading_ease = $passage_row['flesch_reading_ease'];
  $flesch_kincaid_level = $passage_row['flesch_kincaid_level'];
  $elc_copyright = $passage_row['elc_copyright'];
  $library_id = $passage_row['library_id'];
}
?>

<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://fonts.googleapis.com/css?family=Martel+Sans:400,700" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Roboto+Slab" rel="stylesheet">

<link rel="stylesheet" href="../style.css">

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
<script>
$(document).ready(function() {


  $("#save").on("click", function(){
    $.ajax({
     type: "POST",
     url: "save_passage.php",
     dataType: "html",
     data: {
       passage_id: passage_id,
       passage_text: $("#passage_text").html(),
       source: $("#source").html(),
       length: $("#length").html(),
       lexile: $("#lexile").html(),
       flesch_reading_ease: $("#flesch_reading_ease").html(),
       flesch_kincaid_level: $("#flesch_kincaid_level").html(),
       elc_copyright: $("#elc_copyright").html(),
       library_id: $("#library_id").html(),
       modified_by: google_id
     },
     success: function(phpfile)
     {
     $("#save_dialog").html(phpfile);
     }
     });

  });
});

function save_passage() {
  alert("Saving");
}
</script>
<style>

#main {
  padding: 1em;
}

.editable-chunk {
  width: 20em;

  margin: .3em;
}
.label {
  font-size: 1.5em;
  font-variant: small-caps;

}

.editable {
  border: dashed grey 2px;
  padding: 1em;
}

.editable-box {
  clear: left;
}
@media ( min-width: 640px) {
  .editable-chunk {
    width: 33%;
    float: left;
    margin: 0em .3em;
  }
  .editable-chunk-special {
    width: 63%;
    float: left;
    margin: 0em .3em;
  }
}
</style>
</head>
<body>
  <div id="header">
    <div id="user-btn">
      <?php
      echo "<img id='user-image' src='".$_SESSION['image_url']."' />";?>
      <div id="drop-down">
      <?php
echo "Welcome, ".$_SESSION['given_name']."!";
       ?>
      <a href="#"><img class='icon' src='images/settings.png' />Settings</a>
       <a href="start.php?message=signout"><img class='icon' src='images/signout.png' />Sign Out</a>
    </div>
  </div>
  SoftRead Editor: <spane id='title' contenteditable="true"><?php echo $title; ?></span>
</div>
<div id="main">
  <div class="editable-box">
  <div id="save">Save</div>
  <div id="save_dialog"></div>
  </div>
  <div class='editable-box' id='passage_details'>
  <div class='editable-chunk-special'>
    <div class='label'>Passage Text</div>
    <div id='passage_text' class='editable' contenteditable='true'><?php echo $passage_text; ?></div>
  </div>
  <div class='editable-chunk'>
  <div class='label'>Source/Author</div>
  <div id='source' class='editable' contenteditable='true'><?php echo $source; ?></div>
</div>
<div class='editable-chunk'>  <div class='label'>Length</div>
  <div id='length' class='editable' contenteditable='true'><?php echo $length;?></div>
</div>
<div class='editable-chunk'>
  <div class='label'>Lexile</div>
  <div id='lexile' class='editable' contenteditable='true'><?php echo $lexile;?></div>
</div>
<div class='editable-chunk'>
  <div class='label'>Flesch Reading Ease</div>
  <div id='flesch_reading_ease' class='editable' contenteditable='true'><?php echo $flesch_reading_ease;?></div>
</div>
<div class='editable-chunk'>
  <div class='label'>Flesch Kincaid Level</div>
  <div id='flesch_kincaid_level' class='editable' contenteditable='true'><?php echo $flesch_kincaid_level;?></div>
</div>
<div class='editable-chunk'>
  <div class='label'>ELC Copyright Permission</div>
  <div id='elc_copyright' class='editable' contenteditable='true'><?php echo $elc_copyright;?></div>
</div>
<div class='editable-chunk'>
  <div class='label'>Library</div>
  <div id='library_id' class='editable' contenteditable='true'><?php echo $library_id;?></div>
</div>
</div>
<div class="editable-box">
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

      echo $answers[0];
      echo $answers[1];
      echo $answers[2];
      echo $answers[3];
      echo "</div>";
  }

  ?>

</div>


</div>
</body>
</html>
