<?php
session_start();
if (!isset($_SESSION['editor']) || (int) $_SESSION['editor'] !== 1) {
    http_response_code(403);
    echo 'Unauthorized';
    exit;
}

include_once('../../../connectFiles/connect_fb.php');
$passage_id = isset($_POST['passage_id']) ? (int) $_POST['passage_id'] : 0;

$add_question = $fb_db->prepare('INSERT INTO Questions (passage_id) VALUES (?)');
$add_question->bind_param('i', $passage_id);
$add_question->execute();

echo 'Question added.';
