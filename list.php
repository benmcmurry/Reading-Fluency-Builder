

<div data-role="collapsible" data-inset="false" data-icon="false" class="list-heading">
  <h4>Lexile: 10L-400L</h4>
  <ul data-role='listview'>
    <?php
    $query = "Select * from Passages where lexile <'401' order by lexile ASC";
    if (!$results = $db->query($query)) {
      die('There was an error running the query [' . $db->error . ']');
    }
    while ($results_row = $results->fetch_assoc()) {
      echo "<li data-icon='false'><a class='reading_menu_options' href='index.php?passage_id=".$results_row['passage_id']."'>".$results_row['title']."<br /> <span class='lexile'>Lexile: ".$results_row['lexile']."L </span></a></li>";
    }
    $results->free(); //free results
    ?>
  </ul>
</div>

<div data-role="collapsible" data-inset="false" class="list-heading">
  <h4>Lexile: 400L-600L</h4>
  <ul data-role='listview'>
    <?php
    $query = "Select * from Passages where lexile <'601' AND lexile > '399' order by lexile ASC";
    if (!$results = $db->query($query)) {
      die('There was an error running the query [' . $db->error . ']');
    }
    while ($results_row = $results->fetch_assoc()) {
      echo "<li data-icon='false'><a class='reading_menu_options' href='index.php?passage_id=".$results_row['passage_id']."'>".$results_row['title']."<br /> <span class='lexile'>Lexile: ".$results_row['lexile']."L </span></a></li>";
    }
    $results->free(); //free results
    ?>
  </ul>
</div>

<div data-role="collapsible" data-inset="false" class="list-heading">
  <h4>Lexile: 600L-700L</h4>
  <ul data-role='listview'>
    <?php
    $query = "Select * from Passages where lexile <'701' AND lexile > '599' order by lexile ASC";
    if (!$results = $db->query($query)) {
      die('There was an error running the query [' . $db->error . ']');
    }
    while ($results_row = $results->fetch_assoc()) {
      echo "<li data-icon='false'><a class='reading_menu_options' href='index.php?passage_id=".$results_row['passage_id']."'>".$results_row['title']."<br /> <span class='lexile'>Lexile: ".$results_row['lexile']."L </span></a></li>";
    }
    $results->free(); //free results
    ?>
  </ul>
</div>

<div data-role="collapsible" data-inset="false" class="list-heading">
  <h4>Lexile: 700L-850L</h4>
  <ul data-role='listview'>
    <?php
    $query = "Select * from Passages where lexile <'851' AND lexile > '699' order by lexile ASC";
    if (!$results = $db->query($query)) {
      die('There was an error running the query [' . $db->error . ']');
    }
    while ($results_row = $results->fetch_assoc()) {
      echo "<li data-icon='false'><a class='reading_menu_options' href='index.php?passage_id=".$results_row['passage_id']."'>".$results_row['title']."<br /> <span class='lexile'>Lexile: ".$results_row['lexile']."L </span></a></li>";
    }
    $results->free(); //free results
    ?>
  </ul>
</div>

<div data-role="collapsible" data-inset="false" class="list-heading">
  <h4>Lexile: 850L-1000L</h4>
  <ul data-role='listview'>
    <?php
    $query = "Select * from Passages where lexile <'1001' AND lexile > '849' order by lexile ASC";
    if (!$results = $db->query($query)) {
      die('There was an error running the query [' . $db->error . ']');
    }
    while ($results_row = $results->fetch_assoc()) {
      echo "<li data-icon='false'><a class='reading_menu_options' href='index.php?passage_id=".$results_row['passage_id']."'>".$results_row['title']."<br /> <span class='lexile'>Lexile: ".$results_row['lexile']."L </span></a></li>";
    }
    $results->free(); //free results
    ?>
  </ul>
</div>

<div data-role="collapsible" data-inset="false" class="list-heading">
  <h4>Lexile: 1000L-1200L</h4>
  <ul data-role='listview'>
    <?php
    $query = "Select * from Passages where lexile > '999' order by lexile ASC";
    if (!$results = $db->query($query)) {
      die('There was an error running the query [' . $db->error . ']');
    }
    while ($results_row = $results->fetch_assoc()) {
      echo "<li data-icon='false'><a class='reading_menu_options' href='index.php?passage_id=".$results_row['passage_id']."'>".$results_row['title']."<br /> <span class='lexile'>Lexile: ".$results_row['lexile']."L </span></a></li>";
    }
    $results->free(); //free results
    ?>
  </ul>
</div>
