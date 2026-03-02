<?php
function render_passage_link($row)
{
    $status = htmlspecialchars($row['share_status'], ENT_QUOTES, 'UTF-8');
    $id = (int) $row['passage_id'];
    $title = htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8');
    $lexile = htmlspecialchars((string) $row['lexile'], ENT_QUOTES, 'UTF-8');
    echo "<li class='$status'><a class='reading_menu_options' href='index.php?passage_id=$id&page=reading'>$title <span class='lexile'>(Lexile: {$lexile}L)</span></a></li>";
}
?>
<ul id="reading-list" class="reading-list">
    <li>
        <details>
            <summary>New Passages</summary>
            <ul>
                <?php
                $query = $fb_db->prepare("SELECT * FROM Passages WHERE date_created >= '2017-07-31' ORDER BY date_created DESC");
                $query->execute();
                $results = $query->get_result();
                while ($row = $results->fetch_assoc()) {
                    render_passage_link($row);
                }
                ?>
            </ul>
        </details>
    </li>

    <li>
        <details>
            <summary>By Lexile</summary>
            <ul>
                <?php
                $bands = [
                    ['label' => '0L-100L', 'where' => 'lexile < 101'],
                    ['label' => '100L-200L', 'where' => 'lexile BETWEEN 100 AND 200'],
                    ['label' => '200L-300L', 'where' => 'lexile BETWEEN 200 AND 300'],
                    ['label' => '300L-400L', 'where' => 'lexile BETWEEN 300 AND 400'],
                    ['label' => '400L-500L', 'where' => 'lexile BETWEEN 400 AND 500'],
                    ['label' => '500L-600L', 'where' => 'lexile BETWEEN 500 AND 600'],
                    ['label' => '600L-700L', 'where' => 'lexile BETWEEN 600 AND 700'],
                    ['label' => '700L-800L', 'where' => 'lexile BETWEEN 700 AND 800'],
                    ['label' => '800L-900L', 'where' => 'lexile BETWEEN 800 AND 900'],
                    ['label' => '900L-1000L', 'where' => 'lexile BETWEEN 900 AND 1000'],
                    ['label' => '1000L and Higher', 'where' => 'lexile > 999'],
                ];

                foreach ($bands as $band) {
                    echo "<li><details><summary>{$band['label']}</summary><ul>";
                    $query = $fb_db->prepare("SELECT * FROM Passages WHERE {$band['where']} ORDER BY lexile ASC");
                    $query->execute();
                    $results = $query->get_result();
                    while ($row = $results->fetch_assoc()) {
                        render_passage_link($row);
                    }
                    echo '</ul></details></li>';
                }
                ?>
            </ul>
        </details>
    </li>

    <li>
        <details>
            <summary>Library</summary>
            <ul>
                <?php
                $libraries = $fb_db->prepare('SELECT DISTINCT library_id FROM Passages WHERE library_id IS NOT NULL AND library_id <> "" ORDER BY library_id ASC');
                $libraries->execute();
                $libraries_results = $libraries->get_result();

                while ($library_row = $libraries_results->fetch_assoc()) {
                    $library_id = $library_row['library_id'];
                    $safe_library = htmlspecialchars($library_id, ENT_QUOTES, 'UTF-8');
                    echo "<li><details><summary>{$safe_library}</summary><ul>";

                    $query = $fb_db->prepare('SELECT * FROM Passages WHERE library_id = ? ORDER BY title ASC');
                    $query->bind_param('s', $library_id);
                    $query->execute();
                    $results = $query->get_result();

                    while ($row = $results->fetch_assoc()) {
                        render_passage_link($row);
                    }

                    echo '</ul></details></li>';
                }
                ?>
            </ul>
        </details>
    </li>
</ul>
