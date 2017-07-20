<?php

session_start();
session_unset();

session_destroy();

?>
<!DOCTYPE html>
<html>

<head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="google-signin-client_id" content="87036161150-rodj9tne2c7g865ps9h0pgoq6346gut5.apps.googleusercontent.com">
<script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://apis.google.com/js/platform.js?onload=onLoad" async defer></script>

<link href="https://fonts.googleapis.com/css?family=Martel+Sans:400,700" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Roboto+Slab" rel="stylesheet">


<script>
$(document).ready(function() {
setTimeout(function () {
  window.location.href = "start.php"; //will redirect to your blog page (an ex: blog.html)
    }, 2000);
});

function onLoad() {
  gapi.load('auth2', function() {
    gapi.auth2.init().then(function(){
      var auth2 = gapi.auth2.getAuthInstance();
      auth2.signOut().then(function () {
        console.log('User signed out.');
      });
    });
  });


}

</script>
<style>
  body, html {
    background-color: rgb(62, 149, 240);
    color: rgb(62, 149, 240);
    padding: 0px;
    margin: 0px;
    font-family: "Martel Sans";
    height: 100%;
  }
  h1 {
    font-size: 2em;
    margin:0em;
    padding: 0em;
    display: block;
    text-align: center;
    border-bottom: 1px black solid;
  }

  #login-container {
    /*position: relative;*/
    /*top: -9em;*/
    max-width:40em;
    height: 100%;
    margin-left: auto;
    margin-right: auto;
    /*width: 16em;*/
    background-color: rgb(245,245,245);
    text-align: center;
    padding: 1em;

    /*border-radius: .5em;
    box-shadow: 3px 3px 15px rgba(0,0,0,.5);*/

  }
  .g-signin2 {
    display: inline-block;
    text-align: left;

  }

  img.screenshot {
    width: 19em;

  }

  @media ( min-width: 30em) {
    h1 {
      font-size: 3em;

    }
    .g-signin2 {
      display: inline-block;
      float: right;
    }
    img.screenshot {
      width: 40em;
    }
  }
</style>
</head>

<body>
  <div id="login-container">
    <h1>SoftRead 3.0a</h1>
    <p> Thank you for using SoftRead. </p>

  </div>
</body>

<script>

</script>
