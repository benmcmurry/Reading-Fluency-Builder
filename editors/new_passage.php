<?php
require_once dirname(__DIR__) . '/bootstrap.php';
include_once((getenv('APP_PRIVATE_ROOT') ? rtrim(trim((string) getenv('APP_PRIVATE_ROOT')), '/') : dirname(__DIR__, 3) . '/private-config') . '/connectFiles/connect_fb.php');

$title = 'New Passage by ' . $_SESSION['preferredFirstName'];
$creator = $_SESSION['netid'];

$new_passage = $fb_db->prepare('INSERT INTO Passages (title, creator, length, lexile, date_created) VALUES (?, ?, "1", "1", NOW())');
$new_passage->bind_param('ss', $title, $creator);
$new_passage->execute();

$last_id = $fb_db->insert_id;
header('Location: edit.php?passage_id=' . $last_id);
exit;
