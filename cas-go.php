<?php
require_once dirname($_SERVER['DOCUMENT_ROOT']) . '/config.php';

function build_base_url(): string
{
    $is_https = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    $scheme = $is_https ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';

    return $scheme . '://' . $host;
}

function app_base_path_for_root(): string
{
    $script_name = $_SERVER['SCRIPT_NAME'] ?? '/index.php';
    $dir = rtrim(str_replace('\\', '/', dirname($script_name)), '/');

    return $dir === '/' ? '' : $dir;
}

function remove_auth_params(array $params): array
{
    unset($params['code'], $params['state'], $params['logout'], $params['auth'], $params['ticket']);
    return $params;
}

function current_request_path(): string
{
    return strtok($_SERVER['REQUEST_URI'] ?? '/index.php', '?') ?: '/index.php';
}

function current_url_without_auth_params(): string
{
    $path = current_request_path();
    $params = remove_auth_params($_GET);
    $query = http_build_query($params);

    return build_base_url() . $path . ($query ? ('?' . $query) : '');
}

function current_relative_url_with_auth(string $provider): string
{
    $params = remove_auth_params($_GET);
    $params['auth'] = $provider;
    $query = http_build_query($params);

    return current_request_path() . ($query ? ('?' . $query) : '');
}

function clear_local_session(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    session_destroy();
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

function shared_auth_config(): array
{
    static $config = null;

    if ($config !== null) {
        return $config;
    }

    $config = [];
    $path = dirname($_SERVER['DOCUMENT_ROOT']) . '/google_auth_config.php';
    if (is_readable($path)) {
        $loaded = include $path;
        if (is_array($loaded)) {
            $config = $loaded;
        }
    }

    return $config;
}

function shared_auth_config_value(string $config_key, string $env_key, string $default = ''): string
{
    $env = env_value($env_key);
    if ($env !== '') {
        return trim($env);
    }

    $config = shared_auth_config();
    if (isset($config[$config_key]) && $config[$config_key] !== '') {
        return trim((string) $config[$config_key]);
    }

    return $default;
}

function shared_google_app_id(): string
{
    $env = env_value('FLUENCYBUILDER_GOOGLE_APP_ID');
    if ($env !== '') {
        return trim($env);
    }

    $config = shared_auth_config();
    if (isset($config['shared_auth_apps']) && is_array($config['shared_auth_apps']) && isset($config['shared_auth_apps']['fluencybuilder'])) {
        return trim((string) $config['shared_auth_apps']['fluencybuilder']);
    }

    return 'fluencybuilder';
}

function shared_google_root(): string
{
    return shared_auth_config_value('shared_auth_web_root', 'SHARED_AUTH_WEB_ROOT', build_base_url() . '/sharedAuth');
}

function shared_google_expected_issuer(): string
{
    return rtrim(shared_auth_config_value('shared_auth_issuer', 'SHARED_AUTH_ISSUER', shared_google_root()), '/');
}

function shared_google_public_key_path(): string
{
    return shared_auth_config_value(
        'google_shared_public_key_path',
        'GOOGLE_SHARED_PUBLIC_KEY_PATH',
        dirname($_SERVER['DOCUMENT_ROOT']) . '/keys/google_jwt_public.pem'
    );
}

function shared_google_enabled(): bool
{
    return is_readable(shared_google_public_key_path());
}

function build_absolute_url(string $relative_or_absolute): string
{
    if (preg_match('#^https?://#i', $relative_or_absolute)) {
        return $relative_or_absolute;
    }

    return build_base_url() . $relative_or_absolute;
}

function build_url_with_query(string $url, array $params): string
{
    $parts = parse_url($url);
    $query = [];
    if (isset($parts['query'])) {
        parse_str($parts['query'], $query);
    }

    foreach ($params as $key => $value) {
        if ($value === null) {
            unset($query[$key]);
        } else {
            $query[$key] = $value;
        }
    }

    $scheme = isset($parts['scheme']) ? $parts['scheme'] . '://' : '';
    $host = $parts['host'] ?? '';
    $port = isset($parts['port']) ? ':' . $parts['port'] : '';
    $user = $parts['user'] ?? '';
    $pass = isset($parts['pass']) ? ':' . $parts['pass'] : '';
    $pass = ($user !== '' || $pass !== '') ? $pass . '@' : '';
    $path = $parts['path'] ?? '';
    $fragment = isset($parts['fragment']) ? '#' . $parts['fragment'] : '';
    $query_string = $query ? '?' . http_build_query($query) : '';

    return $scheme . $user . $pass . $host . $port . $path . $query_string . $fragment;
}

function base64url_decode_str(string $input)
{
    $remainder = strlen($input) % 4;
    if ($remainder) {
        $input .= str_repeat('=', 4 - $remainder);
    }

    return base64_decode(strtr($input, '-_', '+/'));
}

function verify_shared_google_token(string $token): ?array
{
    $parts = explode('.', $token);
    if (count($parts) !== 3) {
        return null;
    }

    $header_json = base64url_decode_str($parts[0]);
    $payload_json = base64url_decode_str($parts[1]);
    $signature = base64url_decode_str($parts[2]);
    if ($header_json === false || $payload_json === false || $signature === false) {
        return null;
    }

    $header = json_decode($header_json, true);
    $payload = json_decode($payload_json, true);
    if (!is_array($header) || !is_array($payload) || ($header['alg'] ?? '') !== 'RS256') {
        return null;
    }

    $public_key_path = shared_google_public_key_path();
    if (!is_readable($public_key_path)) {
        return null;
    }

    $public_key = openssl_pkey_get_public(file_get_contents($public_key_path));
    if (!$public_key) {
        return null;
    }

    $verified = openssl_verify($parts[0] . '.' . $parts[1], $signature, $public_key, OPENSSL_ALGO_SHA256);
    if ($verified !== 1) {
        return null;
    }

    if (!isset($payload['exp']) || (int) $payload['exp'] < time()) {
        return null;
    }

    if (($payload['aud'] ?? '') !== shared_google_app_id()) {
        return null;
    }

    if (rtrim((string) ($payload['iss'] ?? ''), '/') !== shared_google_expected_issuer()) {
        return null;
    }

    return $payload;
}

function claim_is_true($value): bool
{
    if (is_bool($value)) {
        return $value;
    }

    return strtolower((string) $value) === 'true' || (string) $value === '1';
}

function cas_is_configured(): bool
{
    global $cas_host, $cas_port, $cas_context;

    return isset($cas_host, $cas_port, $cas_context) && $cas_host !== '' && $cas_context !== '';
}

function cas_init_client(): void
{
    global $cas_host, $cas_port, $cas_context;

    require_once dirname($_SERVER['DOCUMENT_ROOT']) . '/CAS.php';
    phpCAS::client(CAS_VERSION_2_0, $cas_host, $cas_port, $cas_context);
    phpCAS::setNoCasServerValidation();
}

function sync_session_from_cas(): void
{
    $netid = (string) phpCAS::getUser();
    $attrs = phpCAS::getAttributes();

    $email = (string) ($attrs['emailAddress'] ?? $attrs['mail'] ?? '');
    $name = (string) ($attrs['name'] ?? $attrs['displayName'] ?? $netid);
    $given = (string) ($attrs['preferredFirstName'] ?? $attrs['given_name'] ?? $attrs['givenName'] ?? $name);
    $surname = (string) ($attrs['surname'] ?? $attrs['family_name'] ?? $attrs['sn'] ?? '');

    $_SESSION['netid'] = $netid;
    $_SESSION['name'] = $name;
    $_SESSION['emailAddress'] = $email;
    $_SESSION['preferredFirstName'] = $given;
    $_SESSION['surname'] = $surname;
    $_SESSION['cas_authenticated'] = 1;
    $_SESSION['google_authenticated'] = 0;
    $_SESSION['auth_provider'] = 'cas';
}

function render_login_choice(bool $cas_enabled, bool $google_enabled): void
{
    http_response_code(401);
    $cas_url = htmlspecialchars(current_relative_url_with_auth('cas'), ENT_QUOTES, 'UTF-8');
    $google_url = htmlspecialchars(current_relative_url_with_auth('google'), ENT_QUOTES, 'UTF-8');

    echo '<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">';
    echo '<title>Sign in - Reading Fluency Builder</title>';
    echo '<style>body{font-family:Arial,sans-serif;background:#f6f8fb;margin:0}.wrap{max-width:540px;margin:72px auto;background:#fff;border:1px solid #d9e0ea;border-radius:10px;padding:28px}h1{margin:0 0 8px;color:#002e5d}p{margin:0 0 16px;color:#334155}.btn{display:block;text-decoration:none;padding:12px 16px;border-radius:8px;margin:10px 0;text-align:center;font-weight:600}.btn-cas{background:#002e5d;color:#fff}.btn-google{background:#fff;color:#1f2937;border:1px solid #d1d5db}.disabled{background:#e5e7eb;color:#6b7280;cursor:not-allowed}small{color:#64748b;display:block;margin-top:8px}</style>';
    echo '</head><body><div class="wrap"><h1>Sign in</h1><p>Choose how you want to continue.</p>';

    if ($cas_enabled) {
        echo '<a class="btn btn-cas" href="' . $cas_url . '">Continue with CAS</a>';
    } else {
        echo '<span class="btn btn-cas disabled">CAS unavailable</span>';
    }

    if ($google_enabled) {
        echo '<a class="btn btn-google" href="' . $google_url . '">Continue with Google</a>';
    } else {
        echo '<span class="btn btn-google disabled">Google unavailable</span>';
    }

    echo '<small>If one option is unavailable, check server auth configuration.</small>';
    echo '</div></body></html>';
    exit;
}

$google_enabled = shared_google_enabled();
$cas_enabled = cas_is_configured();

if (isset($_GET['logout'])) {
    $provider = (string) ($_SESSION['auth_provider'] ?? '');

    if ($provider === 'cas' && $cas_enabled) {
        cas_init_client();
        phpCAS::logout();
        exit;
    }

    clear_local_session();
    header('Location: ' . current_url_without_auth_params());
    exit;
}

$is_authenticated = isset($_SESSION['netid']) && (
    (isset($_SESSION['google_authenticated']) && $_SESSION['google_authenticated'] === 1) ||
    (isset($_SESSION['cas_authenticated']) && $_SESSION['cas_authenticated'] === 1)
);

$auth_request = isset($_GET['auth']) ? (string) $_GET['auth'] : '';
$is_cas_callback = isset($_GET['ticket']);

if (!$is_authenticated) {
    if ($auth_request === 'cas' || $is_cas_callback) {
        if (!$cas_enabled) {
            http_response_code(500);
            echo 'CAS authentication is not configured.';
            exit;
        }

        cas_init_client();
        if (!phpCAS::checkAuthentication()) {
            phpCAS::forceAuthentication();
        }

        sync_session_from_cas();
        header('Location: ' . current_url_without_auth_params());
        exit;
    }

    if ($auth_request === 'google_consume') {
        if (!$google_enabled) {
            http_response_code(500);
            echo 'Google authentication is not configured. Shared auth public key is missing.';
            exit;
        }

        $returned_state = isset($_GET['state']) ? (string) $_GET['state'] : '';
        $stored_state = isset($_SESSION['oauth_state']) ? (string) $_SESSION['oauth_state'] : '';
        if ($returned_state === '' || !hash_equals($stored_state, $returned_state)) {
            http_response_code(400);
            echo 'Invalid Google state.';
            exit;
        }

        unset($_SESSION['oauth_state']);

        $claims = verify_shared_google_token((string) ($_GET['token'] ?? ''));
        if (!is_array($claims)) {
            http_response_code(401);
            echo 'Unable to verify Google sign-in.';
            exit;
        }

        $email = (string) ($claims['email'] ?? '');
        $sub = (string) ($claims['sub'] ?? '');
        $name = (string) ($claims['name'] ?? $email);
        $given_name = (string) ($claims['given_name'] ?? $name);
        $family_name = (string) ($claims['family_name'] ?? '');

        $_SESSION['netid'] = derive_netid($email, $sub);
        $_SESSION['name'] = $name;
        $_SESSION['emailAddress'] = $email;
        $_SESSION['preferredFirstName'] = $given_name;
        $_SESSION['surname'] = $family_name;
        $_SESSION['google_authenticated'] = 1;
        $_SESSION['cas_authenticated'] = 0;
        $_SESSION['auth_provider'] = 'google';

        $redirect_after_auth = isset($_SESSION['post_auth_redirect']) ? (string) $_SESSION['post_auth_redirect'] : '';
        unset($_SESSION['post_auth_redirect']);

        $target = $redirect_after_auth !== '' ? $redirect_after_auth : current_url_without_auth_params();
        header('Location: ' . $target);
        exit;
    }

    if ($auth_request === 'google') {
        if (!$google_enabled) {
            http_response_code(500);
            echo 'Google authentication is not configured. Shared auth public key is missing.';
            exit;
        }

        $state = bin2hex(random_bytes(16));
        $_SESSION['oauth_state'] = $state;
        $_SESSION['post_auth_redirect'] = current_url_without_auth_params();

        $return_to = build_absolute_url(current_relative_url_with_auth('google_consume'));
        $shared_start = rtrim(shared_google_root(), '/') . '/google_start.php';
        $target = build_url_with_query($shared_start, [
            'app' => shared_google_app_id(),
            'return_to' => $return_to,
            'state' => $state,
        ]);

        header('Location: ' . $target);
        exit;
    }

    render_login_choice($cas_enabled, $google_enabled);
}

$netid = (string) ($_SESSION['netid'] ?? '');
$name = (string) ($_SESSION['name'] ?? 'User');

$id = "<button id='user' type='button' aria-expanded='false'>" . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . "</button><a id='logout' href='?logout=1'>Logout</a>";

include_once('../../connectFiles/connect_fb.php');

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
