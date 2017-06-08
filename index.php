<?php
include_once('../../connectFiles/connect_sr.php');

if(isset($_GET['passage_id'])) {$current_passage = $_GET['passage_id'];}
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    	<meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SoftRead</title>
    <!-- <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css"> -->

    <link href="style.css" rel="stylesheet" type="text/css" />

    <script type="text/javascript" src="js/jquery.js"></script>
    <script type="text/javascript" src="js/jquery-ui.js"></script>
      <script type="text/javascript" src="js/js.js"></script>
  </head>
  <body>
    <header>SoftRead 3</header>
      <main>
        <nav>Nav</nav>
    <article id="passage_list">
      <div id="passages">
      <h3 class='reading_menu'>Lexile: 10L-400L</h3>
        <div>
        <?php
        $query = "Select * from Passages where lexile <'401' order by lexile ASC";
        if(!$results = $db->query($query)){
      		die('There was an error running the query [' . $db->error . ']');
      	}
      	while($results_row = $results->fetch_assoc()){
      		echo "<a  class='reading_menu_options' href='index.php?passage_id=".$results_row['passage_id']."'>".$results_row['title']."<br /> <span class='lexile'>Lexile: ".$results_row['lexile']."L </span></a>";

      	}
      	$results->free(); //free results
         ?>
       </div>
      <h3 class='reading_menu'>Lexile: 400L-600L</h3>
        <div>
        <?php
        $query = "Select * from Passages where lexile <'601' AND lexile > '399' order by lexile ASC";
        if(!$results = $db->query($query)){
      		die('There was an error running the query [' . $db->error . ']');
      	}
      	while($results_row = $results->fetch_assoc()){
      		echo "<a  class='reading_menu_options' href='index.php?passage_id=".$results_row['passage_id']."'>".$results_row['title']." - ".$results_row['lexile']."L </a>";

      	}
      	$results->free(); //free results
         ?>
       </div>
      <h3 class='reading_menu'>Lexile: 600L-700L</h3>
        <div>
        <?php
        $query = "Select * from Passages where lexile <'701' AND lexile > '599' order by lexile ASC";
        if(!$results = $db->query($query)){
          die('There was an error running the query [' . $db->error . ']');
        }
        while($results_row = $results->fetch_assoc()){
          echo "<a  class='reading_menu_options' href='index.php?passage_id=".$results_row['passage_id']."'>".$results_row['title']." - ".$results_row['lexile']."L </a>";

        }
        $results->free(); //free results
         ?>
       </div>
      <h3 class='reading_menu'>Lexile: 700L-850L</h3>
        <div>
        <?php
        $query = "Select * from Passages where lexile <'851' AND lexile > '699' order by lexile ASC";
        if(!$results = $db->query($query)){
          die('There was an error running the query [' . $db->error . ']');
        }
        while($results_row = $results->fetch_assoc()){
          echo "<a  class='reading_menu_options' href='index.php?passage_id=".$results_row['passage_id']."'>".$results_row['title']." - ".$results_row['lexile']."L </a>";

        }
        $results->free(); //free results
         ?>
       </div>
      <h3 class='reading_menu'>Lexile: 850L-1000L</h3>
        <div>
        <?php
        $query = "Select * from Passages where lexile <'1001' AND lexile > '849' order by lexile ASC";
        if(!$results = $db->query($query)){
          die('There was an error running the query [' . $db->error . ']');
        }
        while($results_row = $results->fetch_assoc()){
          echo "<a  class='reading_menu_options' href='index.php?passage_id=".$results_row['passage_id']."'>".$results_row['title']." - ".$results_row['lexile']."L </a>";

        }
        $results->free(); //free results
         ?>
       </div>
      <h3 class='reading_menu'>Lexile: 1000L-1200L</h3>
        <div>
        <?php
        $query = "Select * from Passages where lexile > '999' order by lexile ASC";
        if(!$results = $db->query($query)){
          die('There was an error running the query [' . $db->error . ']');
        }
        while($results_row = $results->fetch_assoc()){
          echo "<a  class='reading_menu_options' href='index.php?passage_id=".$results_row['passage_id']."'>".$results_row['title']." - ".$results_row['lexile']."L </a>";

        }
        $results->free(); //free results
         ?>
       </div>
</div>
    </article>

  </main>
  </body>
</html>
