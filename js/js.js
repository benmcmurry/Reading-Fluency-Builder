$(document).ready(function() {
  window.onbeforeunload = function() {
    alert("wait");
    window.location.replace('index.php');
  }
  emSize = parseFloat($("body").css("font-size"));
  largeWindow = emSize * 40;

  moveTimerBtnBar();

  // Gets Copyright Year
  var d = new Date();
	var n = d.getFullYear();
	$("span#year").text(n);
  // End Gets Copyright Year

//Nav Panel functionality
  $("#nav-panel").accordion({
    collapsible: true,
    heightStyle: "content",
    active: false,
  });

//panel opener
  $("#open").on("click", function(){
    $("#nav-panel").toggle();
  });

//Nav button functionality
  $(".nav-btn").on("click",function(){
    pageID = this.id.slice(0, -4); console.log(pageID);
    page=pageID;
    $(".page").slideUp("slow");
    $("#"+pageID).slideDown();
    currentPage = window.location.href;
    window.history.replaceState(pageID, "", "index.php?passage_id="+passage_id+"&page="+pageID);

    $(".nav-btn").css({
      "background-color" : "rgb(239, 239, 239)",
      "color" : "black"
    });

    $(this).css({
      "background-color" : "rgb(62, 149, 240)",
      "color" : "white"
    });
  });

//Window resize actions
  $(window).resize(function(){
    if ($(window).width()> largeWindow ) {$("#nav-panel").show();}
    else {$("#nav-panel").hide();}
    if (page == "timer") {
      moveTimerBtnBar();
    }
  });

//manipulate selected page and nav buttons

  switch (page) {
    case "reading":
      $("#reading-btn").click();
      console.log(page);
      break;
    case "scroller":
      $("#scroller-btn").click();
      console.log(page);
      break;
    case "timer":
      $("#timer-btn").click();
      console.log(page);
      break;
    case "quiz":
      $("#quiz-btn").click();
      console.log(page);
      break;
    case "vocab":
      $("#vocab-btn").click();
      console.log(page);
      break;
  }



}); //end document ready

// Functions for scrolling passages
function scrollThePassage(wordcount) { // scrolls text
	wpm = $("#userSpeed").html(); console.log(wpm);
  if (wpm < 100 || isNaN(wpm)) {$("#userSpeed").val('100'); wpm = 100;}
	speed = wordcount / wpm * 60000; console.log(speed);
	var passageHeight = $("#scrollPassage").height()+16; console.log(passageHeight);
	$("#scrollPassage").animate({
		top: "-"+passageHeight,
	  }, speed, "linear");
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
function stopTheTimer(wordcount){
  $("#stop-timer").hide();
  $("#timer-results").show().css("display", "block");;
	stopTime = 0;
	working = 0;
	date = new Date();
	stopTime = date.getTime();
	difference = (stopTime - startTime)/1000;
	minutes = difference/60;
	minutesRound = Math.floor(difference/60);
	seconds = Math.floor((minutes - minutesRound)*60);
  if (seconds < 10) {seconds = "0"+seconds;}
	completeTime = minutesRound + ":" + seconds + "";
	timedwpm = Math.round(wordcount/minutes);
	$("#timer-results").html("Time: " + completeTime + "  WPM: "+ timedwpm).text;

	}
function moveTimerBtnBar () {
  mainW = $("#main").width();
  contentW = $("#content").width();
  windowW = $(window).width();
  sideL = (windowW - mainW)/2 + (mainW - contentW);
  sideR = (windowW - mainW)/2 - (mainW - contentW);

$("#timer-btn-bar").css({
  "padding-left" : "10px",
  "padding-right" : "10px",
  "width" : contentW - 20,
  "left" : sideL,
  "right" : sideR
});
  // console.log("Position left: "+pos.left);
  // console.log("Position left: "+pos.right);
}
// end Functions for timing passages
