$(document).ready(function() {

  // Gets Copyright Year
  var d = new Date();
	var n = d.getFullYear();
	$("span#year").text(n);
  // End Gets Copyright Year

  uiContentHeight = $("#nav-panel").height();


});

// Functions for scrolling passages
function scrollThePassage(wordcount) { // scrolls text
	wpm = $("#userSpeed").val(); console.log(wpm);
	speed = wordcount / wpm * 60000; console.log(speed);
	var passageHeight = $("#scrollPassage").height()+16; console.log(passageHeight);
	$("#scrollPassage").animate({
		top: "-"+passageHeight,
	  }, speed, "linear");
} // ends script scrolling

function resetit() { //reset scrolling
	$("#column1").hide();
	$("#column1").html(scrollPage).html();
	$("#scrollPassage").html(passage).html();
	$("#column1").fadeIn("slow");
}
// end to scrolling Passages
