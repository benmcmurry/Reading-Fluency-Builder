<?php
$passage = "Select * from Passages where passage_id='$current_passage'";
if(!$results = $db->query($passage)){
  die('There was an error running the query [' . $db->error . ']');
}
while($results_row = $results->fetch_assoc()){
  echo "<h3>".$results_row['title']."</h3>";
  echo $results_row['passage_text'];

}
$results->free(); //free results
 ?>
