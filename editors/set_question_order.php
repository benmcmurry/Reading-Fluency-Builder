<?php
session_start();
if (!isset($_SESSION['editor']) || (int) $_SESSION['editor'] !== 1) {
    http_response_code(403);
    echo 'Unauthorized';
    exit;
}

include_once('../../../connectFiles/connect_fb.php');
$orders = isset($_POST['orders']) && is_array($_POST['orders']) ? $_POST['orders'] : [];

$position = 1;
foreach ($orders as $question_id) {
    $q_id = (int) $question_id;
    $update_order = $fb_db->prepare('UPDATE Questions SET question_order = ? WHERE question_id = ?');
    $update_order->bind_param('ii', $position, $q_id);
    $update_order->execute();
    $position++;
}

echo 'Question order saved.';
