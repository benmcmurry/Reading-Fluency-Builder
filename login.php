<?php
include_once('../../connectFiles/connect_sr.php');
$google_id = $_POST['google_id'];
$full_name = $_POST['full_name'];
$given_name = $_POST['given_name'];
$family_name = $_POST['family_name'];
$image_url = $_POST['image_url'];
$email = $_POST['email'];
$current_url = $_POST['current_url'];


$search_for_id = "Select * from Users where google_id=$google_id";
if(!$result = $db->query($search_for_id)){
  die('There was an error running the query [' . $db->error . ']');
}
if (mysqli_num_rows($result)==0) {

  $add_user = "Insert into Users (google_id, full_name, given_name, family_name, image_url, email)
  values ('$google_id', '$full_name', '$given_name', '$family_name', '$image_url', '$email')";
  if(!$result = $db->query($add_user)){
    die('There was an error running the query [' . $db->error . ']');
  }
  
} else {
  $update_user = "UPDATE Users SET full_name = '$full_name', given_name = '$given_name', family_name = '$family_name', image_url = '$image_url', email = '$email' WHERE google_id='$google_id'";
  if(!$result = $db->query($update_user)){
    die('There was an error running the query [' . $db->error . ']');
  }
}
if(!isset($_SESSION)){session_start();}
$_SESSION['google_id'] = $google_id;
$_SESSION['given_name'] = $given_name;
$_SESSION['family_name'] = $family_name;
$_SESSION['image_url'] = $image_url;
$_SESSION['email'] = $email;
$_SESSION['logged_in'] = "yes";


echo  "<meta HTTP-EQUIV='REFRESH' content='0; url=$current_url'>";

echo $_SESSION['given_name'];
echo $full_name;
echo $given_name;
echo $family_name;
echo $image_url;
echo $email;
?>
