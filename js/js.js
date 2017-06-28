$(document).ready(function() {

  // Gets Copyright Year
  var d = new Date();
	var n = d.getFullYear();
	$("span#year").text(n);
  // End Gets Copyright Year
console.log ("working");
  $("#nav-panel").accordion({
    collapsible: true,
    heightStyle: "content",
    active: false,
  });



});

// Functions for scrolling passages
function scrollThePassage(wordcount) { // scrolls text
	wpm = $("#userSpeed").val(); console.log(wpm);
  if (wpm < 100) {$("#userSpeed").val('100'); wpm = 100;}
	speed = wordcount / wpm * 60000; console.log(speed);
	var passageHeight = $("#scrollPassage").height()+16; console.log(passageHeight);
	$("#scrollPassage").animate({
		top: "-"+passageHeight,
	  }, speed, "linear");
} // ends script scrolling

function resetit() { //reset scrolling
	location.reload();
}
// end to scrolling Passages
