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

function signOut() {

}


  signOut();
</script>
<body>
  <span class='g-signin2'></span>
</body>

<script>

</script>
