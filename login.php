<?php
require_once __DIR__ . '/../sharedAuth/broker.php';
require_once __DIR__ . '/../shared-ui/layout.php';

$appRoot = '/fluencybuilder';
$indexUrl = $appRoot . '/index.php';
$requestedRedirect = isset($_GET['redirect']) ? shared_auth_safe_return_to((string) $_GET['redirect']) : $indexUrl;
if ($requestedRedirect === '') {
    $requestedRedirect = $indexUrl;
}

$logoutBlocked = shared_auth_logout_blocked();
$localUser = isset($_SESSION['netid']) ? trim((string) $_SESSION['netid']) : '';
$sharedUser = shared_auth_current_session_user();
if (!$logoutBlocked && ($localUser !== '' || is_array($sharedUser))) {
    shared_auth_redirect($indexUrl);
}

$loginUrl = shared_auth_login_url($requestedRedirect, 'fluencybuilder');
$sharedLogo = shared_ui_asset_url('assets/img/elc.png');
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reading Fluency Builder</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Atkinson+Hyperlegible:wght@400;700&family=Source+Sans+3:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="../shared-ui/theme.css">
    <style>
        body.landing {
            min-height: 100vh;
            background:
                radial-gradient(circle at top left, rgba(17, 103, 177, 0.14) 0, rgba(17, 103, 177, 0) 34%),
                radial-gradient(circle at top right, rgba(95, 111, 133, 0.14) 0, rgba(95, 111, 133, 0) 28%),
                var(--bg);
        }

        .landing-main {
            max-width: 1120px;
            margin: 0 auto;
            padding: 2rem 1rem 4rem;
        }

        .landing-grid {
            display: grid;
            gap: 1rem;
            align-items: stretch;
        }

        .hero-panel,
        .signin-panel {
            border: 1px solid var(--border);
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0 18px 45px rgba(31, 41, 51, 0.08);
            border-radius: 1rem;
        }

        .hero-panel {
            padding: 1.5rem;
            overflow: hidden;
            position: relative;
        }

        .hero-panel::after {
            content: "";
            position: absolute;
            inset: auto -4rem -4rem auto;
            width: 12rem;
            height: 12rem;
            border-radius: 50%;
            background: linear-gradient(180deg, rgba(17, 103, 177, 0.15), rgba(17, 103, 177, 0));
            pointer-events: none;
        }

        .eyebrow {
            margin: 0 0 0.5rem;
            color: var(--primary);
            font-size: 0.8rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .hero-panel h1 {
            margin: 0 0 0.75rem;
            font-family: 'Atkinson Hyperlegible', sans-serif;
            font-size: clamp(2rem, 5vw, 3.25rem);
            line-height: 1.06;
            color: var(--text);
        }

        .hero-panel p {
            margin: 0 0 1rem;
            max-width: 42rem;
            color: var(--text-muted);
            font-size: 1.05rem;
        }

        .feature-list {
            display: grid;
            gap: 0.6rem;
            margin: 1.25rem 0 0;
            padding: 0;
            list-style: none;
        }

        .feature-list li {
            display: flex;
            gap: 0.7rem;
            align-items: flex-start;
            color: var(--text);
        }

        .feature-list li::before {
            content: "";
            width: 0.8rem;
            height: 0.8rem;
            margin-top: 0.4rem;
            border-radius: 999px;
            background: var(--primary);
            flex: 0 0 auto;
            box-shadow: 0 0 0 5px rgba(17, 103, 177, 0.08);
        }

        .signin-panel {
            padding: 1.5rem;
        }

        .signin-panel h2 {
            margin: 0 0 0.45rem;
            font-family: 'Atkinson Hyperlegible', sans-serif;
            font-size: 1.5rem;
        }

        .signin-panel .btn-row {
            margin-top: 1.25rem;
        }

        .signin-panel .btn {
            width: 100%;
            min-height: 3rem;
            font-size: 1rem;
        }

        .signin-note {
            margin-top: 1rem;
            color: var(--text-muted);
            font-size: 0.95rem;
        }

        .app-chip {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            margin-top: 1rem;
            padding: 0.35rem 0.7rem;
            border-radius: 999px;
            background: var(--surface-muted);
            color: var(--text-muted);
            font-size: 0.85rem;
            font-weight: 700;
        }

        @media (min-width: 880px) {
            .landing-grid {
                grid-template-columns: minmax(0, 1.5fr) minmax(320px, 0.85fr);
            }

            .hero-panel,
            .signin-panel {
                min-height: 28rem;
                display: flex;
                flex-direction: column;
                justify-content: center;
            }
        }
    </style>
</head>
<body class="landing">
<?php
shared_ui_render_header(array(
    'brand_href' => $indexUrl,
    'brand_label' => 'Reading Fluency Builder',
    'brand_image' => $sharedLogo,
    'brand_image_alt' => 'English Language Center',
    'brand_title' => 'Reading Fluency Builder',
    'nav_items' => array(),
    'user' => null,
    'auth_href' => $loginUrl,
    'logout_href' => '',
    'sign_in_label' => 'Login',
    'sign_out_label' => 'Logout',
));
?>

<main class="landing-main">
    <div class="landing-grid">
        <section class="hero-panel">
            <p class="eyebrow">Reading tool</p>
            <h1>Practice passages, measure fluency, and review progress in one place.</h1>
            <p>
                Reading Fluency Builder helps students work through passages with timed reading, scrolled reading,
                quiz checks, and vocabulary support. It keeps the workflow simple for teachers while giving students
                a focused reading experience.
            </p>
            <p>
                Sign in with your BYU account to open the passages assigned to you and continue where you left off.
            </p>
            <ul class="feature-list" aria-label="App features">
                <li>Passage reading with a clean, distraction-light layout</li>
                <li>Timed reading and scrolled reading practice</li>
                <li>Comprehension quiz and vocabulary support</li>
            </ul>
            <span class="app-chip">BYU login required for protected passages</span>
        </section>

        <aside class="signin-panel">
            <p class="eyebrow">Sign in</p>
            <h2>Continue to Reading Fluency Builder</h2>
            <p>
                Use your BYU account to open the app. If you were sent here from a specific passage or editor page,
                you will be returned there after sign-in.
            </p>
            <div class="btn-row">
                <a class="btn" href="<?php echo htmlspecialchars($loginUrl, ENT_QUOTES, 'UTF-8'); ?>">Login</a>
            </div>
            <p class="signin-note">
                If you already signed in on another page, refreshing should take you straight into the app.
            </p>
        </aside>
    </div>
</main>
<?php
shared_ui_render_footer(array(
    'columns' => array(
        array(
            'title' => 'Reading Fluency Builder',
            'items' => array(
                array('label' => 'Login', 'href' => $loginUrl),
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
    'note' => 'Reading Fluency Builder for BYU English Language Center.',
));
?>
</body>
</html>
