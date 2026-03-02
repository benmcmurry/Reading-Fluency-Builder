<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

include_once('cas-go.php');
include_once('../../connectFiles/connect_fb.php');

function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

$page = isset($_GET['page']) ? $_GET['page'] : 'instructions';
$passage_id = isset($_GET['passage_id']) ? (int) $_GET['passage_id'] : null;
$has_passage = $passage_id !== null;

$title = 'Reading Fluency Builder';
$passage_name = '';
$source = '';
$passage_html = '';
$wordcount = 0;
$vocabulary_html = '';

$timed_reading_wpm = 'N/A';
$timed_reading_time = 'N/A';
$scrolled_reading = 'N/A';
$comprehension_quiz = 'N/A';

if ($has_passage) {
    $_SESSION['passage_id'] = $passage_id;

    $passage_query = $fb_db->prepare('SELECT * FROM Passages WHERE passage_id = ?');
    $passage_query->bind_param('i', $passage_id);
    $passage_query->execute();
    $passage_results = $passage_query->get_result();

    if ($row = $passage_results->fetch_assoc()) {
        $passage_name = $row['title'];
        $title = 'Reading Fluency Builder - ' . $passage_name;
        $source = $row['source'];
        $passage_html = $row['passage_text'];
        $wordcount = (int) $row['length'];
        $vocabulary_html = (string) $row['vocabulary'];
    }

    $scores_query = $fb_db->prepare('SELECT * FROM Scores WHERE netid = ? AND passage_id = ?');
    $scores_query->bind_param('si', $_SESSION['netid'], $passage_id);
    $scores_query->execute();
    $scores_results = $scores_query->get_result();

    if (!$scores_results->fetch_assoc()) {
        $insert_score = $fb_db->prepare('INSERT INTO Scores (netid, passage_id, date_modified) VALUES (?, ?, NOW())');
        $insert_score->bind_param('si', $_SESSION['netid'], $passage_id);
        $insert_score->execute();
    }

    $scores_query = $fb_db->prepare('SELECT * FROM Scores WHERE netid = ? AND passage_id = ?');
    $scores_query->bind_param('si', $_SESSION['netid'], $passage_id);
    $scores_query->execute();
    $scores_results = $scores_query->get_result();

    if ($score = $scores_results->fetch_assoc()) {
        $timed_reading_wpm = $score['timed_reading_wpm'] ?: 'N/A';
        $timed_reading_time = $score['timed_reading_time'] ?: 'N/A';
        $scrolled_reading = $score['scrolled_reading'] ?: 'N/A';
        $comprehension_quiz = $score['comprehension_quiz'] ?: 'N/A';
    }
}

$editor = isset($_SESSION['editor']) && (int) $_SESSION['editor'] === 1;
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo e($title); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Atkinson+Hyperlegible:wght@400;700&family=Source+Sans+3:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header class="topbar">
    <div class="topbar__left">
        <button id="menuToggle" type="button" class="icon-btn" aria-controls="nav-panel" aria-expanded="false" aria-label="Toggle reading list">
            <img src="images/open.png" alt="">
        </button>
        <h1 class="app-title"><?php echo e($has_passage ? $title : 'Reading Fluency Builder'); ?></h1>
    </div>
    <div id="user-btn" class="user-menu">
        <?php echo $id; ?>
        <div id="drop-down" class="user-dropdown" hidden>
            <p class="welcome">Welcome, <?php echo e($_SESSION['preferredFirstName']); ?>!</p>
            <?php if ($editor): ?>
                <a href="editors/new_passage.php"><img class="icon" src="images/new.png" alt="">New Passage</a>
                <?php if ($has_passage): ?>
                    <a href="editors/edit.php?passage_id=<?php echo e($passage_id); ?>"><img class="icon" src="images/edit.png" alt="">Edit Passage</a>
                <?php endif; ?>
            <?php endif; ?>
            <?php if ($has_passage): ?>
                <section id="stats" aria-live="polite">
                    <h2>Your Scores</h2>
                    <p><strong>Timed Reading:</strong> <span class="timed_reading">Time: <?php echo e($timed_reading_time); ?> WPM: <?php echo e($timed_reading_wpm); ?></span></p>
                    <p><strong>Scrolled Reading WPM:</strong> <span class="scrolled_reading"><?php echo e($scrolled_reading); ?></span></p>
                    <p><strong>Quiz Score:</strong> <span class="comprehension_quiz"><?php echo e($comprehension_quiz); ?></span></p>
                    <button id="email_results" type="button" class="btn popup_link">Email Results</button>
                </section>
            <?php endif; ?>
        </div>
    </div>
</header>

<main id="main" class="layout">
    <aside id="nav-panel" class="panel" aria-label="Passage navigation">
        <label for="search" class="sr-only">Search passages</label>
        <input id="search" type="search" autocomplete="off" placeholder="Search passages">
        <ul id="search-results" class="search-results" role="listbox" hidden></ul>
        <nav aria-label="Reading list">
            <?php include_once('list.php'); ?>
        </nav>
    </aside>

    <section id="content" class="content-area">
        <nav id="navbar" class="tab-nav" aria-label="Reading tools" <?php echo $has_passage ? '' : 'hidden'; ?>>
            <button type="button" id="reading-btn" class="nav-btn" data-page="reading">Reading</button>
            <button type="button" id="scroller-btn" class="nav-btn" data-page="scroller">Scrolled Reading</button>
            <button type="button" id="timer-btn" class="nav-btn" data-page="timer">Timed Reading</button>
            <button type="button" id="quiz-btn" class="nav-btn" data-page="quiz">Quiz</button>
            <button type="button" id="vocab-btn" class="nav-btn" data-page="vocab">Vocabulary</button>
        </nav>

        <div id="page" class="page-wrap">
            <section class="page" id="instructions">
                <h2>Welcome to Reading Fluency Builder</h2>
                <p>Use the reading list to select a passage. After selecting one, use the tabs above to practice.</p>
                <p><strong>Reading</strong> shows the full passage. <strong>Scrolled Reading</strong> auto-scrolls at your selected WPM. <strong>Timed Reading</strong> tracks pace. <strong>Quiz</strong> checks comprehension. <strong>Vocabulary</strong> shows key words.</p>
            </section>

            <section class="page" id="reading">
                <article class="text-content"><?php echo $passage_html ?: '<p>Select a passage to begin.</p>'; ?></article>
                <?php if ($source): ?>
                    <p class="source-note">This passage comes from <?php echo e($source); ?>.</p>
                <?php endif; ?>
            </section>

            <section class="page" id="scroller">
                <div class="block">
                    <div id="window" class="scroll-window" aria-live="off">
                        <div id="scrollPassage" class="scroll-passage"><?php echo $passage_html; ?></div>
                    </div>
                    <label for="userSpeed" class="field-label">Rate (WPM)</label>
                    <input id="userSpeed" type="number" inputmode="numeric" min="100" value="300">
                    <div class="btn-row">
                        <button class="btn" id="go" type="button">Go</button>
                        <a class="btn btn--subtle" id="reset-scroller" href="index.php?passage_id=<?php echo e($passage_id); ?>&page=scroller">Reset</a>
                    </div>
                </div>
            </section>

            <section class="page" id="timer">
                <p class="instructions">Press Start, read the passage, then press Stop.</p>
                <div class="btn-row btn-row--sticky">
                    <button class="btn" id="start-timer" type="button">Start</button>
                    <button class="btn" id="stop-timer" type="button" hidden>Stop</button>
                    <a class="btn btn--subtle" id="timer-results" href="index.php?passage_id=<?php echo e($passage_id); ?>&page=timer" hidden>Reset</a>
                </div>
                <article class="text-content"><?php echo $passage_html; ?></article>
            </section>

            <section class="page" id="quiz">
                <?php
                if ($has_passage) {
                    $query_quiz = $fb_db->prepare('SELECT * FROM Questions WHERE passage_id = ? ORDER BY question_order ASC');
                    $query_quiz->bind_param('i', $passage_id);
                    $query_quiz->execute();
                    $quiz_results = $query_quiz->get_result();
                    while ($q = $quiz_results->fetch_assoc()) {
                        $question_name = 'question_' . (int) $q['question_id'];

                        $answers = [
                            ['value' => 'correct', 'text' => $q['correct_answer'], 'class' => 'correct-answer'],
                            ['value' => 'incorrect', 'text' => $q['distractor_1'], 'class' => ''],
                            ['value' => 'incorrect', 'text' => $q['distractor_2'], 'class' => ''],
                            ['value' => 'incorrect', 'text' => $q['distractor_3'], 'class' => ''],
                        ];

                        if ($q['correct_answer'] === 'True') {
                            $answers = [$answers[0], $answers[1]];
                        } elseif ($q['correct_answer'] === 'False') {
                            $answers = [$answers[1], $answers[0]];
                        } else {
                            shuffle($answers);
                        }

                        $answers = array_values(array_filter($answers, function ($answer) {
                            return trim((string) $answer['text']) !== '';
                        }));

                        if (count($answers) === 0) {
                            continue;
                        }

                        echo "<fieldset class='question-box'><legend class='stem'>" . e($q['question_text']) . "</legend>";

                        foreach ($answers as $answer) {
                            echo "<label class='answer " . e($answer['class']) . "'><input type='radio' name='" . e($question_name) . "' value='" . e($answer['value']) . "'> " . e($answer['text']) . "</label>";
                        }
                        echo '</fieldset>';
                    }
                }
                ?>
                <div class="btn-row">
                    <button id="check-answers" class="btn" type="button">Check Answers</button>
                </div>
            </section>

            <section class="page" id="vocab">
                <?php
                if ($vocabulary_html === '' && $has_passage) {
                    $query_vocab = $fb_db->prepare('SELECT * FROM Vocabulary WHERE passage_id = ? ORDER BY word ASC');
                    $query_vocab->bind_param('i', $passage_id);
                    $query_vocab->execute();
                    $vocab_results = $query_vocab->get_result();

                    while ($vocab = $vocab_results->fetch_assoc()) {
                        echo "<p class='vocab'><strong>" . e($vocab['word']) . "</strong> - " . e($vocab['definition']);
                        if ($vocab['example']) {
                            echo '<br><em>' . e($vocab['example']) . '</em>';
                        }
                        echo '</p>';
                    }
                } else {
                    echo $vocabulary_html;
                }
                ?>
            </section>
        </div>
    </section>
</main>

<footer id="footer" class="footer">
    <div>
        <a rel="license" href="http://creativecommons.org/licenses/by-nc-sa/4.0/">
            <img alt="Creative Commons License" src="https://i.creativecommons.org/l/by-nc-sa/4.0/88x31.png">
        </a>
    </div>
    <div id="attribution">
        <span>Developed by Ben McMurry</span>
        <span>English Language Center, BYU</span>
    </div>
</footer>

<div id="invisible-background" class="overlay" hidden></div>
<section id="email_results_popup" class="popup" role="dialog" aria-modal="true" aria-labelledby="email-results-title" hidden>
    <button class="close_popup" type="button" aria-label="Close">x</button>
    <h2 id="email-results-title">Email Results</h2>
    <p>Please enter the email address you wish to send the results to.</p>
    <form id="email_results_form">
        <input type="hidden" name="netid" value="<?php echo e($_SESSION['netid']); ?>">
        <input type="hidden" name="passage_id" value="<?php echo e($passage_id); ?>">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" required>
    </form>
    <button class="btn" id="send_email" type="button">Send Email</button>
    <div id="sent" class="response" role="status" aria-live="polite"></div>
</section>

<script>
window.APP_CONFIG = {
    page: <?php echo json_encode($page); ?>,
    passageId: <?php echo json_encode($passage_id); ?>,
    wordCount: <?php echo json_encode($wordcount); ?>
};
</script>
<script src="js/app.js"></script>
</body>
</html>
