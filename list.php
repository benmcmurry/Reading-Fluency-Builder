<?php


?>
<li>
  <h3>New Passages</h3>
 <ul>

    <?php
    $list="";$i=0;
      $query = $db->prepare("Select * from Passages where date_created >= '2017-07-31'");
      $query->execute();
      $results = $query->get_result();

      while ($results_row = $results->fetch_assoc()) {
          echo checkStatus($results_row['share_status'], $results_row['passage_id'], $results_row['title'], $results_row['lexile'], $_SESSION['cas'], $list, $i);
      }

      $results->free(); //free results
    ?>
 </ul></li>
<li>
  <h3>by Lexile > </h3>
  <ul class='sublist'>
 <li><h4>Lexile: 0L-100L > </h4>
 <ul>

    <?php
    $list="";$i=0;
      $query = $db->prepare("Select * from Passages where lexile <'101' order by lexile ASC");
      $query->execute();
      $results = $query->get_result();
      while ($results_row = $results->fetch_assoc()) {
          echo checkStatus($results_row['share_status'], $results_row['passage_id'], $results_row['title'], $results_row['lexile'], $_SESSION['cas'], $list, $i);
      }
      $results->free(); //free results
    ?>
 </ul></li>

<li><h4>Lexile: 100L-200L > </h4>
 <ul>

   <?php
   $list="";$i=0;
     $query = $db->prepare("Select * from Passages where lexile <'201' AND lexile > '99' order by lexile ASC");
     $query->execute();
     $results = $query->get_result();
     while ($results_row = $results->fetch_assoc()) {
         echo checkStatus($results_row['share_status'], $results_row['passage_id'], $results_row['title'], $results_row['lexile'], $_SESSION['cas'], $list, $i);
     }
     $results->free(); //free results
    ?>
  </ul></li>
<li><h4>Lexile: 200L-300L > </h4>
 <ul>

    <?php
      $list=""; $i=0;
      $query = $db->prepare("Select * from Passages where lexile <'301' AND lexile > '199' order by lexile ASC");
      $query->execute();
      $results = $query->get_result();
      while ($results_row = $results->fetch_assoc()) {
          echo checkStatus($results_row['share_status'], $results_row['passage_id'], $results_row['title'], $results_row['lexile'], $_SESSION['cas'], $list, $i);
      }
      $results->free(); //free results
   ?>
 </ul></li>
<li><h4>Lexile: 300L-400L > </h4>
 <ul>

   <?php
   $list="";$i=0;
     $query = $db->prepare("Select * from Passages where lexile <'401' AND lexile > '299' order by lexile ASC");
     $query->execute();
     $results = $query->get_result();
     while ($results_row = $results->fetch_assoc()) {
         echo checkStatus($results_row['share_status'], $results_row['passage_id'], $results_row['title'], $results_row['lexile'], $_SESSION['cas'], $list, $i);
     }
     $results->free(); //free results
  ?>
</ul></li>
<li><h4>Lexile: 400L-500L > </h4>
 <ul>

    <?php
    $list="";$i=0;
      $query = $db->prepare("Select * from Passages where lexile <'401' AND lexile > '299' order by lexile ASC");
      $query->execute();
      $results = $query->get_result();
      while ($results_row = $results->fetch_assoc()) {
          echo checkStatus($results_row['share_status'], $results_row['passage_id'], $results_row['title'], $results_row['lexile'], $_SESSION['cas'], $list, $i);
      }
      $results->free(); //free results
   ?>
 </ul></li>
<li><h4>Lexile: 500L-600L > </h4>
 <ul>

    <?php
    $list="";$i=0;
      $query = $db->prepare("Select * from Passages where lexile <'601' AND lexile > '499' order by lexile ASC");
      $query->execute();
      $results = $query->get_result();
      while ($results_row = $results->fetch_assoc()) {
          echo checkStatus($results_row['share_status'], $results_row['passage_id'], $results_row['title'], $results_row['lexile'], $_SESSION['cas'], $list, $i);
      }
      $results->free(); //free results
   ?>
 </ul></li>
<li><h4>Lexile: 600L-700L > </h4>
 <ul>

    <?php
      $list="";$i=0;
      $query = $db->prepare("Select * from Passages where lexile <'701' AND lexile > '599' order by lexile ASC");
      $query->execute();
      $results = $query->get_result();
      while ($results_row = $results->fetch_assoc()) {
          echo checkStatus($results_row['share_status'], $results_row['passage_id'], $results_row['title'], $results_row['lexile'], $_SESSION['cas'], $list, $i);
      }
      $results->free(); //free results
    ?>
 </ul></li>
<li><h4>Lexile: 700L-800L > </h4>
 <ul>

    <?php
    $list="";$i=0;
      $query = $db->prepare("Select * from Passages where lexile <'801' AND lexile > '699' order by lexile ASC");
      $query->execute();
      $results = $query->get_result();
      while ($results_row = $results->fetch_assoc()) {
          echo checkStatus($results_row['share_status'], $results_row['passage_id'], $results_row['title'], $results_row['lexile'], $_SESSION['cas'], $list, $i);
      }
      $results->free(); //free results
    ?>
 </ul></li>
<li><h4>Lexile: 800L-900L > </h4>
 <ul>

    <?php
    $list="";$i=0;
      $query = $db->prepare("Select * from Passages where lexile <'901' AND lexile > '799' order by lexile ASC");
      $query->execute();
      $results = $query->get_result();
      while ($results_row = $results->fetch_assoc()) {
          echo checkStatus($results_row['share_status'], $results_row['passage_id'], $results_row['title'], $results_row['lexile'], $_SESSION['cas'], $list, $i);
      }
      $results->free(); //free results
    ?>
 </ul></li>
<li><h4>Lexile: 900L-1000L > </h4>
 <ul>

   <?php
   $list="";$i=0;
     $query = $db->prepare("Select * from Passages where lexile <'1001' AND lexile > '899' order by lexile ASC");
     $query->execute();
     $results = $query->get_result();
     while ($results_row = $results->fetch_assoc()) {
         echo checkStatus($results_row['share_status'], $results_row['passage_id'], $results_row['title'], $results_row['lexile'], $_SESSION['cas'], $list, $i);
     }
     $results->free(); //free results
   ?>
</ul></li>
<li><h4>Lexile: 1000L and Higher > </h4>
 <ul>

    <?php
    $list="";$i=0;
      $query = $db->prepare("Select * from Passages where lexile > '999' order by lexile ASC");
      $query->execute();
      $results = $query->get_result();
      while ($results_row = $results->fetch_assoc()) {
          echo checkStatus($results_row['share_status'], $results_row['passage_id'], $results_row['title'], $results_row['lexile'], $_SESSION['cas'], $list, $i);
      }
      $results->free(); //free results
    ?>
 </ul></li>
</li>
</ul>
<li>
  <h3>Library</h3>
  <ul class='sublist'>
<?php
 $libraries = $db->prepare("Select Distinct library_id from Passages");
 $libraries->execute();
 $libraries_results = $libraries->get_result();
 while ($libraries_results_rows = $libraries_results->fetch_assoc()) {
     if ($libraries_results_rows['library_id']) {
         $list= "<li><h4>".$libraries_results_rows['library_id']." > </h4>
 <ul>";
         $i=0;

         $query = $db->prepare("Select * from Passages where library_id = '{$libraries_results_rows['library_id']}'order by title");
         $query->execute();
         $results = $query->get_result();
         while ($results_row = $results->fetch_assoc()) {
             if ($results_row['share_status'] == "public") {
                 $list = $list."<li class=".$results_row['share_status']."><a class='reading_menu_options' href='index.php?passage_id=".$results_row['passage_id']."&page=reading'>".$results_row['title']."<br /> <span class='lexile'>Lexile: ".$results_row['lexile']."L </span></a></li>";
                 $i++;
             } else {
                 if ($results_row['share_status'] == "private" && $_SESSION['cas'] !="") {
                     $list = $list."<li class=".$results_row['share_status']."><a class='reading_menu_options' href='index.php?passage_id=".$results_row['passage_id']."&page=reading'>".$results_row['title']."<br /> <span class='lexile'>Lexile: ".$results_row['lexile']."L </span></a></li>";
                     $i++;
                 }
             }
         }
         $results->free(); //free results
         if ($i>0) {
             echo $list."</ul></li>";
         }
     }
 }
?></ul></li></ul>
<?php


function checkStatus($status, $passage_id, $title, $lexile, $cas, $list, $i)
{
    if ($status == "public") {
        $list = $list."<li class='$status'><a class='reading_menu_options' href='index.php?passage_id=$passage_id&page=reading'>$title<br /> <span class='lexile'>Lexile: ".$lexile."L </span></a></li>";
        $i++;
    } else {
        if ($status == "private" && $cas !="") {
            $list = $list."<li class='$status'><a class='reading_menu_options' href='index.php?passage_id=$passage_id&page=reading'>$title<br /> <span class='lexile'>Lexile: ".$lexile."L </span></a></li>";
            $i++;
        }
    }
    return $list;
}

?>
