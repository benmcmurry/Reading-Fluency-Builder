$(document).ready(function() {

  window.onbeforeunload = function() {
    window.location.replace('index.php');
  }
  emSize = parseFloat($("body").css("font-size"));
  var largeWindow = emSize * 40;

  moveBtnBar();
  $("#user-btn img").on("click", function(){
    console.log("clicked");
    $("#drop-down").slideToggle();
    $("#invisible-background").toggle();
  });

  $("#invisible-background").on("click", function(){
    console.log("clicked background");
    $("#email_results_popup").hide();
    if($("#drop-down").is(":visible")) {
      $("#drop-down").slideToggle();
      $("#invisible-background").toggle();
  }
  });

  // Gets Copyright Year
  var d = new Date();
  var n = d.getFullYear();
  $("span#year").text(n);
  // End Gets Copyright Year

  //Nav Panel functionality
  $("#reading-list, .sublist").accordion({
    collapsible: true,
    heightStyle: "content",
    active: false,
  });

  //panel opener
  $("#open").on("click", function() {
    $("#nav-panel").toggle();
  });

  //Nav button functionality
  $(".nav-btn").on("click", function() {

    pageID = this.id.slice(0, -4);
    console.log("Menu Click Triggered: "+ pageID);
    page = pageID;
    $(".page").hide();
    $("#" + pageID).show();
    currentPage = window.location.href;
    window.history.replaceState(pageID, "", "index.php?passage_id=" + passage_id + "&page=" + pageID);

    $(".nav-btn").css({
      "background-color": "rgb(239, 239, 239)",
      "color": "black"
    });

    $(this).css({
      "background-color": "rgb(62, 149, 240)",
      "color": "white"
    });
  });

  $(".nav-btn").hover(
    function() {
      $(this).css({
        "background-color": "rgb(62, 149, 240)",
        "color": "white"
      });
    }, function() {
      $(this).css({
        "background-color": "rgb(239, 239, 239)",
        "color": "black"
      });
    }
  );


  $("#check-answers").on("click", function() {
    $(".correct-answer").css({
      "color": "green",
      "font-weight": "800"
    });
    totalPossible = $(".question-box").length;
    totalCorrect = 0;
    $(":input:checked").each(function() {
      if (this.value == 'correct') {
        totalCorrect++;
      }

    });
    score = Math.round(totalCorrect / totalPossible * 100);
    score = totalCorrect + "/" + totalPossible + " correct - " + score +"%";
    $("#check-answers").html(score).off();

    $.ajax({
       type: "POST",
       url: "history.php",
       data: {score: score},
       success: function(phpfile)
       {
       $(".comprehension_quiz").html(phpfile);
       }
    });

  });

  //Window resize actions
  $(window).resize(function() {
    if ($(window).width() > largeWindow) {
      $("#nav-panel").show();
    } else {
      $("#nav-panel").hide();
    }
    if (page == "timer" || page == "quiz") {
      moveBtnBar();
    }
  });

  //manipulate selected page and nav buttons
  if(passage_id=='') {
    $('#navbar').hide();
    $('#page').css("padding-top" , "0px");
  }
pageSet(page);

$("#userSpeed").on("click", function(){$(this).empty();});
$("#userSpeed").keypress(function(e){
  if(e.which === 13) {
    e.preventDefault();
    $("#go").trigger("click");

}
});

$("#email_results").on("click", function(){
  $("#invisible-background").show();
  w = $("#email_results_popup").width();
  h = $("#email_results_popup").height();
  windoww = $(window).width();
  windowh = $(window).height();
  topPos = (windowh-h)/2;
  leftPos = (windoww-w)/2;

  $("#email_results_popup").css({
    "top" : topPos,
    "left" : leftPos
  }).show();
});

}); //end document ready

function pageSet(page) {
  switch (page) {

    case "reading":
      console.log(page+" from pageSet");
      $("#reading-btn").click();
      break;
    case "scroller":
      console.log(page+" from pageSet");
      $("#scroller-btn").click();
      break;
    case "timer":
      console.log(page+" from pageSet");
      $("#timer-btn").click();
      break;
    case "quiz":
      console.log(page+" from pageSet");
      $("#quiz-btn").click();
      break;
    case "vocab":
      console.log(page+" from pageSet");
      $("#vocab-btn").click();
      break;
    case "instructions":
      console.log(page+" from pageSet");
      $("#instructions").show();
      break;
  }
}
// Functions for scrolling passages
function scrollThePassage(wordcount) { // scrolls text
  wpm = $("#userSpeed").html();
  if (wpm < 100 || isNaN(wpm)) {
    $("#userSpeed").val('100');
    wpm = 100;
  }
  speed = wordcount / wpm * 60000;
  var passageHeight = $("#scrollPassage").height() + 16;
  $("#scrollPassage").animate({
    top: "-" + passageHeight,
  }, speed, "linear");

  $.ajax({
     type: "POST",
     url: "history.php",
     data: {userSpeed: wpm},
     success: function(phpfile)
     {
     $(".scrolled_reading").html(phpfile);
     }
 });


} // ends script scrolling

// end functions for scrolling Passages

// Functions for timing passages

function startTheTimer() {
  $("#start-timer").hide();
  $("#stop-timer").show().css("display", "block");
  working = 1;
  startTime = 0;
  date = new Date();
  startTime = date.getTime();
}

function stopTheTimer(wordcount) {
  $("#stop-timer").hide();
  $("#timer-results").show().css("display", "block");;
  stopTime = 0;
  working = 0;
  date = new Date();
  stopTime = date.getTime();
  difference = (stopTime - startTime) / 1000;
  minutes = difference / 60;
  minutesRound = Math.floor(difference / 60);
  seconds = Math.floor((minutes - minutesRound) * 60);
  if (seconds < 10) {
    seconds = "0" + seconds;
  }
  completeTime = minutesRound + ":" + seconds + "";
  timedwpm = Math.round(wordcount / minutes);
  $("#timer-results").html("Time: " + completeTime + "  WPM: " + timedwpm).text;
    $.ajax({
       type: "POST",
       url: "history.php",
       data: {time: completeTime,
              wpm: timedwpm},
       success: function(phpfile)
       {
       $(".timed_reading").html(phpfile);
       }
   });

}

function moveBtnBar() {
  mainW = $("#main").width();
  contentW = $("#content").width();
  windowW = $(window).width();
  sideL = (windowW - mainW) / 2 + (mainW - contentW);
  sideR = (windowW - mainW) / 2 - (mainW - contentW);

  $(".btn-bar").css({
    "padding-left": "10px",
    "padding-right": "10px",
    "width": contentW - 20,
    "left": sideL,
    "right": sideR
  });

}
// end Functions for timing passages
