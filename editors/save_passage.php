<?php
session_start();
if (!isset($_SESSION['editor']) || (int) $_SESSION['editor'] !== 1) {
    http_response_code(403);
    echo 'Unauthorized';
    exit;
}

include_once('../../../connectFiles/connect_fb.php');

$passage_id = isset($_POST['passage_id']) ? (int) $_POST['passage_id'] : 0;
$passage_title = isset($_POST['passage_title']) ? $_POST['passage_title'] : '';
$passage_text = isset($_POST['passage_text']) ? $_POST['passage_text'] : '';
$author = isset($_POST['author']) ? $_POST['author'] : '';
$source = isset($_POST['source']) ? $_POST['source'] : '';
$length = isset($_POST['length']) ? $_POST['length'] : '';
$lexile = isset($_POST['lexile']) ? $_POST['lexile'] : '';
$flesch_reading_ease = is_numeric($_POST['flesch_reading_ease'] ?? null) ? (float) $_POST['flesch_reading_ease'] : 0;
$flesch_kincaid_level = is_numeric($_POST['flesch_kincaid_level'] ?? null) ? (float) $_POST['flesch_kincaid_level'] : 0;
$library_id = isset($_POST['library_id']) ? $_POST['library_id'] : '';
$vocabulary = isset($_POST['vocabulary']) ? $_POST['vocabulary'] : '';
$modified_by = isset($_POST['modified_by']) ? $_POST['modified_by'] : '';

$update_passage = $fb_db->prepare('UPDATE Passages SET passage_text = ?, title = ?, author = ?, source = ?, length = ?, lexile = ?, flesch_reading_ease = ?, flesch_kincaid_level = ?, library_id = ?, vocabulary = ?, modified_by = ? WHERE passage_id = ?');
$update_passage->bind_param('ssssssddsssi', $passage_text, $passage_title, $author, $source, $length, $lexile, $flesch_reading_ease, $flesch_kincaid_level, $library_id, $vocabulary, $modified_by, $passage_id);
$update_passage->execute();

echo 'Passage saved.';
