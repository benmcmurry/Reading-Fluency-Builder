<?php
include_once('../../connectFiles/connect_fb.php');
header('Content-Type: application/json; charset=utf-8');

$phrase = isset($_GET['phrase']) ? trim($_GET['phrase']) : '';
if ($phrase === '') {
    echo json_encode([]);
    exit;
}

$like = '%' . $phrase . '%';
$passage_query = $fb_db->prepare('SELECT passage_id, title FROM Passages WHERE title LIKE ? ORDER BY title ASC LIMIT 15');
$passage_query->bind_param('s', $like);
$passage_query->execute();
$passage_results = $passage_query->get_result();

$list = [];
while ($row = $passage_results->fetch_assoc()) {
    $list[] = [
        'id' => (int) $row['passage_id'],
        'title' => $row['title'],
        'link' => 'index.php?passage_id=' . $row['passage_id'] . '&page=reading',
    ];
}

echo json_encode($list);
