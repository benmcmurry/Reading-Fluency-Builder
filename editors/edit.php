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
  $vocabulary = $passage_row['vocabulary'];
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

  $("#new_question").on("click", function(){
    console.log("new question");
    $.ajax({
      type: "POST",
      url: "add_question.php",
      datatype: "html",
      data :{
    passage_id: passage_id
  },
  success: function(phpfile) {$("#save_dialog").html(phpfile).fadeIn().delay(2000).fadeOut(2000);
  location.reload();
}
});
  });
  $(".quiz_item").on("blur", function(){
    question_id = $(this).parent().attr("id"); console.log(question_id);
    console.log("question_text-"+question_id);
    question_text = $("#question_text-"+question_id).html();console.log(question_text);
    correct_answer = $("#correct_answer-"+question_id).html();console.log(correct_answer);
    distractor_1 = $("#distractor_1-"+question_id).html();console.log(distractor_1);
    distractor_2 = $("#distractor_2-"+question_id).html();console.log(distractor_2);
    distractor_3 = $("#distractor_3-"+question_id).html();console.log(distractor_3);
    $.ajax({
      type: "POST",
      url: "save_question.php",
      datatype: "html",
      data :{
        question_id: question_id,
        question_text: question_text,
        correct_answer: correct_answer,
        distractor_1: distractor_1,
        distractor_2: distractor_2,
        distractor_3: distractor_3,
        modified_by: google_id
      },
      success: function(phpfile)
      {
        $("#save_dialog").html(phpfile).fadeIn().delay(2000).fadeOut(2000);
      }
    });


  });

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
       library_id: $("#library_id").html(),
       vocabulary: $("#vocabulary").html(),
       modified_by: google_id
     },
     success: function(phpfile)
     {
     $("#save_dialog").html(phpfile).fadeIn().delay(2000).fadeOut(2000);
     }
     });

  });
});

function save_passage() {
  alert("Saving");
}
</script>
<style>
body {
  background-color: rgb(62, 149, 240);
}
h1 {
  font-family: "Martel Sans";

}
#main {
  padding: 1em;
}

.editable-chunk {

}
.label {

  font-size: 1.5em;
  font-family: "Martel Sans";
  margin-top: .3em;
  /*font-variant: small-caps;*/

}

.indent {
  margin-left:2em;
}

.editable {

  font-family: "Martel Sans";
  background-color: white;
  border: dotted grey 2px;
  padding: .5em;
}

.editable-box {
padding: 1em;
  margin-bottom: 1.5em;
  background-color: rgb(245,245,245);
  box-shadow: 2px 2px 15px rgba(0,0,0,0.5);

}

.question-box {
  padding: 1em;
  border: 3px solid rgb(62, 149, 240);
/*background-color: rgb(167, 206, 246);*/
}
#save_dialog {
  position: fixed;
  bottom: 0px;
  right: 0px;
  min-width:17em;
  background-color: yellow;
  padding: 1em;
  display: none;
  text-align: center;
  line-height: 1em;
  font-size:1em;
  font-family: "Martel Sans";
}
.button {

  background-color: white;
  color: rgb(62, 149, 240);
  box-shadow: 2px 2px 15px rgba(0,0,0,0.5);
  cursor: pointer;

  font-family: "Martel Sans";
  padding: .7em;
  border-radius: 8px;
}
.button:hover {
  color:white;
  background-color: rgb(62, 149, 240);

}
#save {
  position: fixed;
  bottom: 10px;
  right: 10px;
  line-height: 1em;
  font-size:1.5em;
}
#save:hover {
  color:white;
  background-color: rgb(240, 30, 62);
}
@media ( min-width: 640px) {

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

       echo "<a href='../index.php?passage_id=$passage_id'><img class='icon' src='../images/return.png' />Return<a>";
       ?>
       <a href='new_passage.php'><img class='icon' src='../images/new.png' />New Passage</a>
      <a href="#"><img class='icon' src='../images/settings.png' />Settings</a>
       <a href="../logout.php"><img class='icon' src='../images/signout.png' />Sign Out</a>
    </div>
  </div>
  SoftRead Editor: <spane id='title' contenteditable="true"><?php echo $title; ?></span>
</div>
<div id="main">
  <div id="save_dialog"></div>
  <a id="save" class='button'>Save</a>


  <div class='editable-box' id='passage_details'>
<h1> Passage Information </h1>
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
  <div class='label'>Library</div>
  <div id='library_id' class='editable' contenteditable='true'><?php echo $library_id;?></div>
</div>
<div class='editable-chunk'>
  <div class='label'>Temporary Vocabulary Holder</div>
  <div id='vocabulary' class='editable' contenteditable='true'>
    <?php
      if ($vocabulary == ""){
        $query_vocab = "Select * from Vocabulary where passage_id=$passage_id order by word asc";
        if(!$vocab_results = $db->query($query_vocab)){
          die('There was an error running the query [' . $db->error . ']');
        }

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
  <h1> Quiz Items</h1>
  <a class='button' id='new_question'>Add another question</a>
  <?php
    $query_quiz = "Select * from Questions where passage_id=$passage_id order by question_order asc";
    if(!$quiz_results = $db->query($query_quiz)){
      die('There was an error running the query [' . $db->error . ']');
    }

  while($quiz_results_rows = $quiz_results->fetch_assoc()){
      echo "<div class='question-box' id='{$quiz_results_rows['question_id']}'><div class='label'>Stem</div><div id='question_text-{$quiz_results_rows['question_id']}' class='quiz_item editable' contenteditable='true'>".$quiz_results_rows['question_text']."</div>";
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
      echo "</div>";
  }

  ?>

</div>


</div>
</body>
</html>
