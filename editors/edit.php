<?php
require_once dirname(__DIR__) . '/bootstrap.php';
require_once dirname(__DIR__) . '/../shared-ui/layout.php';
$passage_id = isset($_GET['passage_id']) ? (int) $_GET['passage_id'] : 0;

if (!isset($_SESSION['editor']) || (int) $_SESSION['editor'] !== 1 || $passage_id <= 0) {
    header('Location: ../index.php?passage_id=' . $passage_id);
    exit;
}

include_once('cas-go.php');
include_once((getenv('APP_PRIVATE_ROOT') ? rtrim(trim((string) getenv('APP_PRIVATE_ROOT')), '/') : dirname(__DIR__, 3) . '/private-config') . '/connectFiles/connect_fb.php');

function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

$query = $fb_db->prepare('SELECT * FROM Passages WHERE passage_id = ?');
$query->bind_param('i', $passage_id);
$query->execute();
$passage = $query->get_result()->fetch_assoc();

if (!$passage) {
    header('Location: ../index.php');
    exit;
}

$query_quiz = $fb_db->prepare('SELECT * FROM Questions WHERE passage_id = ? ORDER BY question_order ASC, question_id ASC');
$query_quiz->bind_param('i', $passage_id);
$query_quiz->execute();
$quiz_result = $query_quiz->get_result();
$sharedLogo = shared_ui_asset_url('assets/img/elc.png');
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SoftRead Editor - <?php echo e($passage['title']); ?></title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="../../shared-ui/theme.css">
    <script defer src="<?= e(shared_ui_asset_url('ui.js')) ?>"></script>
</head>
<body>
<?php
shared_ui_render_header(array(
    'brand_href' => '../index.php?passage_id=' . $passage_id,
    'brand_label' => 'SoftRead Editor',
    'brand_image' => $sharedLogo,
    'brand_image_alt' => 'English Language Center',
    'brand_title' => 'SoftRead Editor',
    'nav_items' => array(
        array(
            'label' => 'Return',
            'href' => '../index.php?passage_id=' . $passage_id,
        ),
    ),
    'user' => array('name' => $_SESSION['name'] ?? $_SESSION['preferredFirstName'] ?? $_SESSION['netid'] ?? 'User'),
    'display_name' => $_SESSION['preferredFirstName'] ?? $_SESSION['name'] ?? $_SESSION['netid'] ?? 'User',
    'auth_href' => '../login.php',
    'logout_href' => '?logout=1',
    'sign_in_label' => 'Login',
    'sign_out_label' => 'Logout',
    'menu_items' => array(
        array('type' => 'badge', 'label' => 'Editor'),
        array('label' => 'New Passage', 'href' => 'new_passage.php'),
        array('label' => 'Reader View', 'href' => '../index.php?passage_id=' . $passage_id),
    ),
));
?>

<main class="editor-layout">
    <aside class="editor-menu">
        <button id="save" class="btn" type="button">Save</button>
        <button id="new_question" class="btn" type="button">Add Question</button>
        <p class="hint">Tip: Ctrl/Cmd + S saves.</p>
        <div id="save_dialog" role="status" aria-live="polite"></div>
    </aside>

    <section class="editor-content">
        <article class="editable-box">
            <h2>Passage Information</h2>

            <label class="label" for="title">Title</label>
            <div id="title" class="editable-passage" contenteditable="true"><?php echo e($passage['title']); ?></div>

            <label class="label" for="passage_text">Passage Text</label>
            <div id="passage_text" class="editable-passage" contenteditable="true"><?php echo $passage['passage_text']; ?></div>

            <label class="label" for="author">Author</label>
            <div id="author" class="editable-passage" contenteditable="true"><?php echo e($passage['author']); ?></div>

            <label class="label" for="source">Source</label>
            <div id="source" class="editable-passage" contenteditable="true"><?php echo e($passage['source']); ?></div>

            <label class="label" for="length">Length</label>
            <div id="length" class="editable-passage" contenteditable="true"><?php echo e($passage['length']); ?></div>

            <label class="label" for="lexile">Lexile</label>
            <div id="lexile" class="editable-passage" contenteditable="true"><?php echo e($passage['lexile']); ?></div>

            <label class="label" for="flesch_reading_ease">Flesch Reading Ease</label>
            <div id="flesch_reading_ease" class="editable-passage" contenteditable="true"><?php echo e($passage['flesch_reading_ease']); ?></div>

            <label class="label" for="flesch_kincaid_level">Flesch Kincaid Level</label>
            <div id="flesch_kincaid_level" class="editable-passage" contenteditable="true"><?php echo e($passage['flesch_kincaid_level']); ?></div>

            <label class="label" for="library_id">Library</label>
            <div id="library_id" class="editable-passage" contenteditable="true"><?php echo e($passage['library_id']); ?></div>

            <label class="label" for="vocabulary">Vocabulary (HTML allowed)</label>
            <div id="vocabulary" class="editable-passage" contenteditable="true"><?php echo $passage['vocabulary']; ?></div>
        </article>

        <article class="editable-box">
            <h2>Quiz Items</h2>
            <ol id="questions" class="questions">
                <?php while ($q = $quiz_result->fetch_assoc()): ?>
                    <li class="question-box" data-question-id="<?php echo e($q['question_id']); ?>">
                        <div class="question-actions">
                            <button type="button" class="button-lite move-up" aria-label="Move question up">Up</button>
                            <button type="button" class="button-lite move-down" aria-label="Move question down">Down</button>
                            <button type="button" class="button-lite delete" aria-label="Delete question">Delete</button>
                        </div>

                        <label class="label">Stem</label>
                        <div class="quiz_item editable" data-field="question_text" contenteditable="true"><?php echo e($q['question_text']); ?></div>

                        <label class="label">Correct Answer</label>
                        <div class="quiz_item editable" data-field="correct_answer" contenteditable="true"><?php echo e($q['correct_answer']); ?></div>

                        <label class="label">Distractor 1</label>
                        <div class="quiz_item editable" data-field="distractor_1" contenteditable="true"><?php echo e($q['distractor_1']); ?></div>

                        <label class="label">Distractor 2</label>
                        <div class="quiz_item editable" data-field="distractor_2" contenteditable="true"><?php echo e($q['distractor_2']); ?></div>

                        <label class="label">Distractor 3</label>
                        <div class="quiz_item editable" data-field="distractor_3" contenteditable="true"><?php echo e($q['distractor_3']); ?></div>
                    </li>
                <?php endwhile; ?>
            </ol>
        </article>
    </section>
</main>

<script>
window.EDITOR_CONFIG = {
    passageId: <?php echo json_encode($passage_id); ?>,
    netid: <?php echo json_encode($_SESSION['netid']); ?>
};
</script>
<script src="js/editor.js"></script>
<?php
shared_ui_render_footer(array(
    'columns' => array(
        array(
            'title' => 'SoftRead Editor',
            'items' => array(
                array('label' => 'Reader View', 'href' => '../index.php?passage_id=' . $passage_id),
                array('label' => 'New Passage', 'href' => 'new_passage.php'),
            ),
        ),
        array(
            'title' => 'Support',
            'items' => array(
                array('label' => 'English Language Center', 'href' => 'https://elc.byu.edu'),
                array('label' => 'BYU', 'href' => 'https://www.byu.edu'),
            ),
        ),
    ),
    'note' => 'Reading Fluency Builder editor for BYU English Language Center.',
));
?>
</body>
</html>
