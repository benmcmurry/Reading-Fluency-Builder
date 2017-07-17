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
<script src="https://apis.google.com/js/platform.js" async defer></script>
<!-- <script src="https://apis.google.com/js/api.js"></script> -->
<link href="https://fonts.googleapis.com/css?family=Martel+Sans:400,700" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Roboto+Slab" rel="stylesheet">


<script>
$(document).ready(function() {

});


  function onSignIn(googleUser) {
  var profile = googleUser.getBasicProfile();
  console.log('ID: ' + profile.getId()); // Do not send to your backend! Use an ID token instead.
  console.log('Name: ' + profile.getName());
  console.log('Given Name: ' + profile.getGivenName());
  console.log('Family Name: ' + profile.getFamilyName());
  console.log('Image URL: ' + profile.getImageUrl());
  console.log('Email: ' + profile.getEmail()); // This is null if the 'email' scope is not present.

  google_id = profile.getId(); // Do not send to your backend! Use an ID token instead.
  full_name = profile.getName();
  given_name = profile.getGivenName();
  family_name = profile.getFamilyName();
  image_url = profile.getImageUrl();
  email =  profile.getEmail(); // This is null if the 'email' scope is not present.

  $.ajax({
    method: "POST",
    url: "login.php",
    data: {
      google: 'google',
      google_id: google_id,
      full_name: full_name,
      given_name: given_name,
      family_name: family_name,
      image_url: image_url,
      email: email
      }
  }).done(function(phpfile) {
  $("#save_dialog").html(phpfile);
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
    <div class="g-signin2" data-onsuccess="onSignIn" data-theme="dark" data-longtitle="true"></div>
    <h1>SoftRead 3.0a</h1>
    <img class="screenshot" src="images/screens.gif" />
    <div id="save_dialog"></div>  
  </div>
</body>

</html>
