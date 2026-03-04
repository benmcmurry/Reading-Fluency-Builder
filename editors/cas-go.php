<?php
require_once dirname($_SERVER['DOCUMENT_ROOT']) . '/config.php';

function build_base_url(): string
{
    $is_https = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    $scheme = $is_https ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';

    return $scheme . '://' . $host;
}

function app_base_path_for_editor(): string
{
    $script_name = $_SERVER['SCRIPT_NAME'] ?? '/editors/edit.php';
    $normalized = str_replace('\\', '/', $script_name);
    if (strpos($normalized, '/editors/') !== false) {
        return rtrim(substr($normalized, 0, (int) strpos($normalized, '/editors/')), '/');
    }

    $dir = rtrim(dirname($normalized), '/');
    return $dir === '/' ? '' : $dir;
}

function remove_auth_params(array $params): array
{
    unset($params['code'], $params['state'], $params['logout']);
    return $params;
}

function current_url_without_auth_params(): string
{
    $path = strtok($_SERVER['REQUEST_URI'] ?? '/index.php', '?') ?: '/index.php';
    $params = remove_auth_params($_GET);
    $query = http_build_query($params);

    return build_base_url() . $path . ($query ? ('?' . $query) : '');
}

function google_post(string $url, array $fields): ?array
{
    $payload = http_build_query($fields);

    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'],
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_TIMEOUT => 15,
        ]);
        $response = curl_exec($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response !== false && $status >= 200 && $status < 300) {
            $decoded = json_decode($response, true);
            return is_array($decoded) ? $decoded : null;
        }

        return null;
    }

    $context = stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
            'content' => $payload,
            'timeout' => 15,
        ],
    ]);

    $response = @file_get_contents($url, false, $context);
    if ($response === false) {
        return null;
    }

    $decoded = json_decode($response, true);
    return is_array($decoded) ? $decoded : null;
}

function google_get_json(string $url): ?array
{
    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 15,
        ]);
        $response = curl_exec($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response !== false && $status >= 200 && $status < 300) {
            $decoded = json_decode($response, true);
            return is_array($decoded) ? $decoded : null;
        }

        return null;
    }

    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'timeout' => 15,
        ],
    ]);

    $response = @file_get_contents($url, false, $context);
    if ($response === false) {
        return null;
    }

    $decoded = json_decode($response, true);
    return is_array($decoded) ? $decoded : null;
}

function derive_netid(string $email, string $sub): string
{
    $parts = explode('@', $email);
    if (count($parts) === 2 && strtolower($parts[1]) === 'byu.edu') {
        return $parts[0];
    }

    return $sub;
}

function env_value(string $key): string
{
    $value = getenv($key);
    if ($value !== false && $value !== '') {
        return (string) $value;
    }

    if (isset($_SERVER[$key]) && $_SERVER[$key] !== '') {
        return (string) $_SERVER[$key];
    }

    if (isset($_ENV[$key]) && $_ENV[$key] !== '') {
        return (string) $_ENV[$key];
    }

    return '';
}

function claim_is_true($value): bool
{
    if (is_bool($value)) {
        return $value;
    }

    return strtolower((string) $value) === 'true' || (string) $value === '1';
}

if (isset($_GET['logout'])) {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    session_destroy();

    header('Location: ' . current_url_without_auth_params());
    exit;
}

$client_id = env_value('GOOGLE_CLIENT_ID');
$client_secret = env_value('GOOGLE_CLIENT_SECRET');
$hosted_domain = env_value('GOOGLE_HOSTED_DOMAIN');

$app_base = app_base_path_for_editor();
$redirect_uri = env_value('GOOGLE_REDIRECT_URI');
if ($redirect_uri === '') {
    $redirect_uri = build_base_url() . $app_base . '/index.php';
}

if ($client_id === '' || $client_secret === '') {
    http_response_code(500);
    echo 'Google authentication is not configured. Set GOOGLE_CLIENT_ID and GOOGLE_CLIENT_SECRET.';
    exit;
}

$is_authenticated = isset($_SESSION['google_authenticated']) && $_SESSION['google_authenticated'] === 1;

if (!$is_authenticated) {
    if (isset($_GET['code'])) {
        $returned_state = isset($_GET['state']) ? (string) $_GET['state'] : '';
        $stored_state = isset($_SESSION['oauth_state']) ? (string) $_SESSION['oauth_state'] : '';

        if ($returned_state === '' || !hash_equals($stored_state, $returned_state)) {
            http_response_code(400);
            echo 'Invalid OAuth state.';
            exit;
        }

        unset($_SESSION['oauth_state']);

        $token = google_post('https://oauth2.googleapis.com/token', [
            'code' => $_GET['code'],
            'client_id' => $client_id,
            'client_secret' => $client_secret,
            'redirect_uri' => $redirect_uri,
            'grant_type' => 'authorization_code',
        ]);

        $id_token = is_array($token) && isset($token['id_token']) ? (string) $token['id_token'] : '';
        if ($id_token === '') {
            http_response_code(401);
            echo 'Unable to complete Google sign-in.';
            exit;
        }

        $claims = google_get_json('https://oauth2.googleapis.com/tokeninfo?id_token=' . rawurlencode($id_token));
        if (!is_array($claims)) {
            http_response_code(401);
            echo 'Unable to validate Google sign-in token.';
            exit;
        }

        $aud = isset($claims['aud']) ? (string) $claims['aud'] : '';
        $email = isset($claims['email']) ? (string) $claims['email'] : '';
        $email_verified = isset($claims['email_verified']) ? $claims['email_verified'] : false;
        $sub = isset($claims['sub']) ? (string) $claims['sub'] : '';

        if ($aud !== $client_id || $email === '' || $sub === '' || !claim_is_true($email_verified)) {
            http_response_code(401);
            echo 'Google sign-in validation failed.';
            exit;
        }

        if ($hosted_domain !== '') {
            $hd = isset($claims['hd']) ? (string) $claims['hd'] : '';
            if (strtolower($hd) !== strtolower($hosted_domain)) {
                http_response_code(403);
                echo 'This account is not allowed.';
                exit;
            }
        }

        $userinfo = google_get_json('https://openidconnect.googleapis.com/v1/userinfo?access_token=' . rawurlencode((string) ($token['access_token'] ?? '')));

        $name = is_array($userinfo) && isset($userinfo['name']) ? (string) $userinfo['name'] : $email;
        $given_name = is_array($userinfo) && isset($userinfo['given_name']) ? (string) $userinfo['given_name'] : $name;
        $family_name = is_array($userinfo) && isset($userinfo['family_name']) ? (string) $userinfo['family_name'] : '';

        $_SESSION['netid'] = derive_netid($email, $sub);
        $_SESSION['name'] = $name;
        $_SESSION['emailAddress'] = $email;
        $_SESSION['preferredFirstName'] = $given_name;
        $_SESSION['surname'] = $family_name;
        $_SESSION['google_authenticated'] = 1;

        $redirect_after_auth = isset($_SESSION['post_auth_redirect']) ? (string) $_SESSION['post_auth_redirect'] : '';
        unset($_SESSION['post_auth_redirect']);

        $target = $redirect_after_auth !== '' ? $redirect_after_auth : current_url_without_auth_params();
        header('Location: ' . $target);
        exit;
    }

    $state = bin2hex(random_bytes(16));
    $_SESSION['oauth_state'] = $state;
    $_SESSION['post_auth_redirect'] = current_url_without_auth_params();

    $auth_query = http_build_query([
        'client_id' => $client_id,
        'redirect_uri' => $redirect_uri,
        'response_type' => 'code',
        'scope' => 'openid email profile',
        'state' => $state,
        'prompt' => 'select_account',
    ]);

    header('Location: https://accounts.google.com/o/oauth2/v2/auth?' . $auth_query);
    exit;
}

$netid = (string) ($_SESSION['netid'] ?? '');
$name = (string) ($_SESSION['name'] ?? 'User');

$id = "<button id='user' type='button' aria-expanded='false'>" . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . "</button><a id='logout' href='?logout=1'>Logout</a>";

include_once('../../../connectFiles/connect_fb.php');

$search_for_id = $fb_db->prepare('SELECT * FROM Users WHERE netid = ?');
$search_for_id->bind_param('s', $netid);
$search_for_id->execute();
$result = $search_for_id->get_result();

if (mysqli_num_rows($result) === 0) {
    $add_user = $fb_db->prepare('INSERT INTO Users (netid, full_name, given_name, family_name, email) VALUES (?, ?, ?, ?, ?)');
    $add_user->bind_param(
        'sssss',
        $netid,
        $_SESSION['name'],
        $_SESSION['preferredFirstName'],
        $_SESSION['surname'],
        $_SESSION['emailAddress']
    );
    $add_user->execute();
} else {
    $update_user = $fb_db->prepare('UPDATE Users SET full_name = ?, email = ?, given_name = ?, family_name = ? WHERE netid = ?');
    $update_user->bind_param(
        'sssss',
        $_SESSION['name'],
        $_SESSION['emailAddress'],
        $_SESSION['preferredFirstName'],
        $_SESSION['surname'],
        $netid
    );
    $update_user->execute();
}

$get_user_query = $fb_db->prepare('SELECT editor FROM Users WHERE netid = ?');
$get_user_query->bind_param('s', $netid);
$get_user_query->execute();
$user_result = $get_user_query->get_result();
$user = $user_result->fetch_assoc();

$_SESSION['editor'] = ($user && (int) $user['editor'] === 1) ? 1 : 0;
