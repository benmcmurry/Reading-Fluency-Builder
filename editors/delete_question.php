<?php
session_start();
if (!isset($_SESSION['editor']) || (int) $_SESSION['editor'] !== 1) {
    http_response_code(403);
    echo 'Unauthorized';
    exit;
}

include_once('../../../connectFiles/connect_fb.php');
$question_id = isset($_POST['question_id']) ? (int) $_POST['question_id'] : 0;

$delete_question = $fb_db->prepare('DELETE FROM Questions WHERE question_id = ?');
$delete_question->bind_param('i', $question_id);
$delete_question->execute();

echo 'Question deleted.';
