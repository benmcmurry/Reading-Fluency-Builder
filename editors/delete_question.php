<?php
require_once dirname(__DIR__) . '/bootstrap.php';
if (!isset($_SESSION['editor']) || (int) $_SESSION['editor'] !== 1) {
    http_response_code(403);
    echo 'Unauthorized';
    exit;
}

include_once((getenv('APP_PRIVATE_ROOT') ? rtrim(trim((string) getenv('APP_PRIVATE_ROOT')), '/') : dirname(__DIR__, 3) . '/private-config') . '/connectFiles/connect_fb.php');
$question_id = isset($_POST['question_id']) ? (int) $_POST['question_id'] : 0;

$delete_question = $fb_db->prepare('DELETE FROM Questions WHERE question_id = ?');
$delete_question->bind_param('i', $question_id);
$delete_question->execute();

echo 'Question deleted.';
