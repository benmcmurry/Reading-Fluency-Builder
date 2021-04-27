<?php
include_once('../../connectFiles/connect_sr.php');
if(isset($_GET['phrase'])) {
$phrase = $_GET['phrase'];

$passage_query = $fb_db->prepare("SELECT * FROM Passages WHERE title LIKE '%{$phrase}%'");
// $passage_query->bind_param("s", $phrase);
$passage_query->execute();
$passage_results = $passage_query->get_result();
$list = array();
while($passage_results_row = $passage_results->fetch_assoc()){
  $list[] =array (
    'id' => $passage_results_row['passage_id'],
    'title' => $passage_results_row['title'],
    'link' => "index.php?passage_id=".$passage_results_row['passage_id'],
    'passage_id' => $passage_results_row['passage_id'],


  );
}

echo json_encode($list);


$passage_results->free(); //free results
// $title = "Reading Fluency Builder - ".;
// $passage_name = $passage_results_row['title'];
// $source = $passage_results_row['source'];
// $passage = $passage_results_row['passage_text'];
// $wordcount=$passage_results_row['length'];
// $vocabulary = $passage_results_row['vocabulary'];
}
 ?>
