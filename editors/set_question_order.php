<?php
include_once('../../../connectFiles/connect_sr.php');

$i=1;
foreach ($_POST as $key => &$value) {

    $update_order_query = "Update Questions set question_order=$i where question_id=$key";
      if(!$result = $db->query($update_order_query)){
      die('There was an error running the query [' . $db->error . ']');
    } else {


    }
    $i++;

}
echo "Question order saved.";


// $add_question = "Insert into Questions (passage_id) values ($passage_id)";
//
// if(!$result = $db->query($add_question)){
//   die('There was an error running the query [' . $db->error . ']');
// } else {
//   echo "Data saved.";
// }

 ?>
