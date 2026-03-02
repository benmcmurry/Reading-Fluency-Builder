<?php
session_start();
if (!isset($_SESSION['editor']) || (int) $_SESSION['editor'] !== 1) {
    http_response_code(403);
    echo 'Unauthorized';
    exit;
}

include_once('../../../connectFiles/connect_fb.php');

$question_id = isset($_POST['question_id']) ? (int) $_POST['question_id'] : 0;
$question_text = isset($_POST['question_text']) ? $_POST['question_text'] : '';
$correct_answer = isset($_POST['correct_answer']) ? $_POST['correct_answer'] : '';
$distractor_1 = isset($_POST['distractor_1']) ? $_POST['distractor_1'] : '';
$distractor_2 = isset($_POST['distractor_2']) ? $_POST['distractor_2'] : '';
$distractor_3 = isset($_POST['distractor_3']) ? $_POST['distractor_3'] : '';
$modified_by = isset($_POST['modified_by']) ? $_POST['modified_by'] : '';

$update_question = $fb_db->prepare('UPDATE Questions SET question_text = ?, correct_answer = ?, distractor_1 = ?, distractor_2 = ?, distractor_3 = ?, modified_by = ? WHERE question_id = ?');
$update_question->bind_param('ssssssi', $question_text, $correct_answer, $distractor_1, $distractor_2, $distractor_3, $modified_by, $question_id);
$update_question->execute();

echo 'Question saved.';
