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
    question_text = $("#question_text-"+question_id).text();console.log(question_text);
    correct_answer = $("#correct_answer-"+question_id).text();console.log(correct_answer);
    distractor_1 = $("#distractor_1-"+question_id).text();console.log(distractor_1);
    distractor_2 = $("#distractor_2-"+question_id).text();console.log(distractor_2);
    distractor_3 = $("#distractor_3-"+question_id).text();console.log(distractor_3);
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
    save_passage();

  });
  $(".editable-passage").on("blur", function(){
    save_passage();
  });

  $(".editable-passage, .editable").on("paste", function(e){
    e.preventDefault();
    if (e.clipboardData)
    {text = e.clipboardData.getData('text/plain');
  console.log("1: "+text);}
else if (window.clipboardData)
    {text = window.clipboardData.getData('Text');
    console.log("2: "+text);}
else if (e.originalEvent.clipboardData)
    {text = e.originalEvent.clipboardData.getData('text');
  console.log("3: "+ text);}

      destination = this.id;
    document.execCommand("insertHTML", false, text);
  });
});

function save_passage() {
  $.ajax({
   type: "POST",
   url: "save_passage.php",
   dataType: "html",
   data: {
     passage_id: passage_id,
     passage_title: $("#title").text(),
     passage_text: $("#passage_text").html(),
     author: $("#author").text(),
     source: $("#source").text(),
     length: $("#length").text(),
     lexile: $("#lexile").text(),
     flesch_reading_ease: $("#flesch_reading_ease").text(),
     flesch_kincaid_level: $("#flesch_kincaid_level").text(),
     library_id: $("#library_id").text(),
     vocabulary: $("#vocabulary").html(),
     modified_by: google_id
   },
   success: function(phpfile)
   {

   $("#save_dialog").html(phpfile).fadeIn().delay(2000).fadeOut(2000);
   }
   });
}
