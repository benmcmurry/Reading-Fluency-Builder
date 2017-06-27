<?php
include_once('../../connectFiles/connect_sr.php');
$query = "Select * from Passages";
if(!$results = $db->query($query)){
  die('There was an error running the query [' . $db->error . ']');
}
while($results_row = $results->fetch_assoc()){
  echo "<h3>".$results_row['title']."</h3>";
  $text =  str_replace("\\n","",$results_row['passage_text']);
  $text =  str_replace("\\","",$text);
  $text = $db->real_escape_string($text);
  $id = $results_row['passage_id'];
  $query2 = "Update Passages set passage_text='$text' where passage_id='".$id."'";
echo $query2;
if(!$results2 = $db->query($query2)){
  die('There was an error running the query [' . $db->error . ']');
}
}
$results->free(); //free results


?>
