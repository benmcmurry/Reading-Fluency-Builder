<?php
session_start();
include_once('../../../connectFiles/connect_fb.php');

$title = 'New Passage by ' . $_SESSION['preferredFirstName'];
$creator = $_SESSION['netid'];

$new_passage = $fb_db->prepare('INSERT INTO Passages (title, creator, length, lexile, date_created) VALUES (?, ?, "1", "1", NOW())');
$new_passage->bind_param('ss', $title, $creator);
$new_passage->execute();

$last_id = $fb_db->insert_id;
header('Location: edit.php?passage_id=' . $last_id);
exit;
