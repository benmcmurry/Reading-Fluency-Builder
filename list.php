<h3 class='reading_menu'>New Passages</h3>
  <div>
    <?php
      $query = "Select * from Passages where date_created >= '2017-07-31'";
      if (!$results = $db->query($query)) {
          die('There was an error running the query [' . $db->error . ']');
      }
      while ($results_row = $results->fetch_assoc()) {
        echo "<a class='reading_menu_options' href='index.php?passage_id=".$results_row['passage_id']."'>".$results_row['title']."<br /> <span class='lexile'>Lexile: ".$results_row['lexile']."L </span></a>";
      }
      $results->free(); //free results
    ?>
 </div>

 <h3 class='reading_menu'>Lexile: 0L-100L</h3>
  <div>
    <?php
      $query = "Select * from Passages where lexile <'101' order by lexile ASC";
      if (!$results = $db->query($query)) {
          die('There was an error running the query [' . $db->error . ']');
      }
      while ($results_row = $results->fetch_assoc()) {
          echo "<a class='reading_menu_options' href='index.php?passage_id=".$results_row['passage_id']."'>".$results_row['title']."<br /> <span class='lexile'>Lexile: ".$results_row['lexile']."L </span></a>";
      }
      $results->free(); //free results
    ?>
 </div>

<h3 class='reading_menu'>Lexile: 100L-200L</h3>
  <div>
   <?php
     $query = "Select * from Passages where lexile <'201' AND lexile > '99' order by lexile ASC";
     if (!$results = $db->query($query)) {
         die('There was an error running the query [' . $db->error . ']');
     }
     while ($results_row = $results->fetch_assoc()) {
         echo "<a class='reading_menu_options' href='index.php?passage_id=".$results_row['passage_id']."'>".$results_row['title']."<br /> <span class='lexile'>Lexile: ".$results_row['lexile']."L </span></a>";
     }
     $results->free(); //free results
    ?>
  </div>
<h3 class='reading_menu'>Lexile: 200L-300L</h3>
  <div>
    <?php
      $query = "Select * from Passages where lexile <'301' AND lexile > '199' order by lexile ASC";
      if (!$results = $db->query($query)) {
          die('There was an error running the query [' . $db->error . ']');
      }
      while ($results_row = $results->fetch_assoc()) {
          echo "<a class='reading_menu_options' href='index.php?passage_id=".$results_row['passage_id']."'>".$results_row['title']."<br /> <span class='lexile'>Lexile: ".$results_row['lexile']."L </span></a>";
      }
      $results->free(); //free results
   ?>
 </div>
<h3 class='reading_menu'>Lexile: 300L-400L</h3>
 <div>
   <?php
     $query = "Select * from Passages where lexile <'401' AND lexile > '299' order by lexile ASC";
     if (!$results = $db->query($query)) {
         die('There was an error running the query [' . $db->error . ']');
     }
     while ($results_row = $results->fetch_assoc()) {
         echo "<a class='reading_menu_options' href='index.php?passage_id=".$results_row['passage_id']."'>".$results_row['title']."<br /> <span class='lexile'>Lexile: ".$results_row['lexile']."L </span></a>";
     }
     $results->free(); //free results
  ?>
</div>
<h3 class='reading_menu'>Lexile: 400L-500L</h3>
  <div>
    <?php
      $query = "Select * from Passages where lexile <'401' AND lexile > '299' order by lexile ASC";
      if (!$results = $db->query($query)) {
          die('There was an error running the query [' . $db->error . ']');
      }
      while ($results_row = $results->fetch_assoc()) {
          echo "<a class='reading_menu_options' href='index.php?passage_id=".$results_row['passage_id']."'>".$results_row['title']."<br /> <span class='lexile'>Lexile: ".$results_row['lexile']."L </span></a>";
      }
      $results->free(); //free results
   ?>
 </div>
<h3 class='reading_menu'>Lexile: 500L-600L</h3>
  <div>
    <?php
      $query = "Select * from Passages where lexile <'601' AND lexile > '499' order by lexile ASC";
      if (!$results = $db->query($query)) {
          die('There was an error running the query [' . $db->error . ']');
      }
      while ($results_row = $results->fetch_assoc()) {
        echo "<a class='reading_menu_options' href='index.php?passage_id=".$results_row['passage_id']."'>".$results_row['title']."<br /> <span class='lexile'>Lexile: ".$results_row['lexile']."L </span></a>";
      }
      $results->free(); //free results
   ?>
 </div>
<h3 class='reading_menu'>Lexile: 600L-700L</h3>
  <div>
    <?php
      $query = "Select * from Passages where lexile <'701' AND lexile > '599' order by lexile ASC";
      if (!$results = $db->query($query)) {
          die('There was an error running the query [' . $db->error . ']');
      }
      while ($results_row = $results->fetch_assoc()) {
        echo "<a class='reading_menu_options' href='index.php?passage_id=".$results_row['passage_id']."'>".$results_row['title']."<br /> <span class='lexile'>Lexile: ".$results_row['lexile']."L </span></a>";
      }
      $results->free(); //free results
    ?>
 </div>
<h3 class='reading_menu'>Lexile: 700L-800L</h3>
  <div>
    <?php
      $query = "Select * from Passages where lexile <'801' AND lexile > '699' order by lexile ASC";
      if (!$results = $db->query($query)) {
          die('There was an error running the query [' . $db->error . ']');
      }
      while ($results_row = $results->fetch_assoc()) {
        echo "<a class='reading_menu_options' href='index.php?passage_id=".$results_row['passage_id']."'>".$results_row['title']."<br /> <span class='lexile'>Lexile: ".$results_row['lexile']."L </span></a>";
      }
      $results->free(); //free results
    ?>
 </div>
<h3 class='reading_menu'>Lexile: 800L-900L</h3>
  <div>
    <?php
      $query = "Select * from Passages where lexile <'901' AND lexile > '799' order by lexile ASC";
      if (!$results = $db->query($query)) {
          die('There was an error running the query [' . $db->error . ']');
      }
      while ($results_row = $results->fetch_assoc()) {
        echo "<a class='reading_menu_options' href='index.php?passage_id=".$results_row['passage_id']."'>".$results_row['title']."<br /> <span class='lexile'>Lexile: ".$results_row['lexile']."L </span></a>";
      }
      $results->free(); //free results
    ?>
 </div>
<h3 class='reading_menu'>Lexile: 900L-1000L</h3>
 <div>
   <?php
     $query = "Select * from Passages where lexile <'1001' AND lexile > '899' order by lexile ASC";
     if (!$results = $db->query($query)) {
         die('There was an error running the query [' . $db->error . ']');
     }
     while ($results_row = $results->fetch_assoc()) {
       echo "<a class='reading_menu_options' href='index.php?passage_id=".$results_row['passage_id']."'>".$results_row['title']."<br /> <span class='lexile'>Lexile: ".$results_row['lexile']."L </span></a>";
     }
     $results->free(); //free results
   ?>
</div>
<h3 class='reading_menu'>Lexile: 1000L and Higher</h3>
  <div>
    <?php
      $query = "Select * from Passages where lexile > '999' order by lexile ASC";
      if (!$results = $db->query($query)) {
          die('There was an error running the query [' . $db->error . ']');
      }
      while ($results_row = $results->fetch_assoc()) {
        echo "<a class='reading_menu_options' href='index.php?passage_id=".$results_row['passage_id']."'>".$results_row['title']."<br /> <span class='lexile'>Lexile: ".$results_row['lexile']."L </span></a>";
      }
      $results->free(); //free results
    ?>
 </div>
