$(document).ready(function() {

  // Gets Copyright Year
  var d = new Date();
	var n = d.getFullYear();
	$("span#year").text(n);
  // End Gets Copyright Year

  uiContentHeight = $("#nav-panel").height();

  $("#scroll .header, #timer .header, #quiz .header, #vocab .header").html($("#reading .header").html());

});
