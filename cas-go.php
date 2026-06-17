<?php
require_once __DIR__ . '/bootstrap.php';
require_once dirname(__DIR__) . '/sharedAuth/broker.php';

function build_request_scheme(): string
{
    if (!empty($_SERVER['HTTP_X_FORWARDED_PROTO'])) {
        $values = explode(',', $_SERVER['HTTP_X_FORWARDED_PROTO']);
        return strtolower(trim($values[0])) === 'https' ? 'https' : 'http';
    }

    if (!empty($_SERVER['HTTP_X_FORWARDED_PROTOCOL'])) {
        $values = explode(',', $_SERVER['HTTP_X_FORWARDED_PROTOCOL']);
        return strtolower(trim($values[0])) === 'https' ? 'https' : 'http';
    }

    if (!empty($_SERVER['HTTP_X_FORWARDED_SSL']) && strtolower((string) $_SERVER['HTTP_X_FORWARDED_SSL']) === 'on') {
        return 'https';
    }

    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        return 'https';
    }

    return 'https';
}

function build_request_host(): string
{
    if (!empty($_SERVER['HTTP_X_FORWARDED_HOST'])) {
        $hosts = explode(',', $_SERVER['HTTP_X_FORWARDED_HOST']);
        $host = trim($hosts[0]);
        if ($host !== '') {
            return $host;
        }
    }

    if (!empty($_SERVER['HTTP_HOST'])) {
        return $_SERVER['HTTP_HOST'];
    }

    if (!empty($_SERVER['SERVER_NAME'])) {
        return $_SERVER['SERVER_NAME'];
    }

    return 'localhost';
}

function build_base_url(): string
{
    return build_request_scheme() . '://' . build_request_host();
}

function public_origin(): string
{
    $envOrigin = env_value('AR_PUBLIC_ORIGIN');
    if ($envOrigin !== '') {
        return function_exists('shared_auth_normalize_origin')
            ? shared_auth_normalize_origin($envOrigin)
            : rtrim(trim($envOrigin), '/');
    }

    return build_base_url();
}

function app_base_path_for_root(): string
{
    $script_name = $_SERVER['SCRIPT_NAME'] ?? '/index.php';
    $dir = rtrim(str_replace('\\', '/', dirname($script_name)), '/');

    return $dir === '/' ? '' : $dir;
}

function remove_auth_params(array $params): array
{
    unset($params['code'], $params['state'], $params['logout'], $params['auth'], $params['ticket'], $params['mode']);
    return $params;
}

function current_request_path(): string
{
    return isset($_SERVER['SCRIPT_NAME']) && $_SERVER['SCRIPT_NAME'] !== ''
        ? $_SERVER['SCRIPT_NAME']
        : (strtok($_SERVER['REQUEST_URI'] ?? '/index.php', '?') ?: '/index.php');
}

function current_url_without_auth_params(): string
{
    $path = current_request_path();
    $params = remove_auth_params($_GET);
    $query = http_build_query($params);

    return public_origin() . $path . ($query ? ('?' . $query) : '');
}

function safe_return_to_current_url(): string
{
    $current = current_url_without_auth_params();
    $parts = parse_url($current);
    if (!$parts || !isset($parts['host']) || !isset($parts['scheme'])) {
        return current_request_path();
    }

    return $current;
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
        $cookiePaths = array_unique(array(
            $params['path'] ?? '/',
            '/',
            '/fluencybuilder',
            '/fluencybuilder/editors',
            '/sharedAuth',
        ));
        foreach ($cookiePaths as $path) {
            setcookie(session_name(), '', time() - 42000, $path, $params['domain'], $params['secure'], $params['httponly']);
        }
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

function fluencybuilder_shared_auth_config(): array
{
    static $config = null;

    if ($config !== null) {
        return $config;
    }

    $config = [];
    $path = shared_auth_first_readable_path(array(
        getenv('SHARED_AUTH_CONFIG_PATH') ?: '',
        dirname(__DIR__, 3) . '/shared_auth_config.php',
        dirname(__DIR__, 3) . '/google_auth_config.php',
    ));
    if ($path !== '') {
        $loaded = include $path;
        if (is_array($loaded)) {
            $config = $loaded;
        }
    }

    return $config;
}

function fluencybuilder_shared_auth_config_value(string $config_key, string $env_key, string $default = ''): string
{
    $env = env_value($env_key);
    if ($env !== '') {
        return trim($env);
    }

    $config = fluencybuilder_shared_auth_config();
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

    return 'fluencybuilder';
}

function shared_google_root(): string
{
    return fluencybuilder_shared_auth_config_value('shared_auth_web_root', 'SHARED_AUTH_WEB_ROOT', public_origin() . '/sharedAuth');
}

function shared_google_expected_issuer(): string
{
    return rtrim(fluencybuilder_shared_auth_config_value('shared_auth_issuer', 'SHARED_AUTH_ISSUER', shared_google_root()), '/');
}

function shared_google_public_key_path(): string
{
    return fluencybuilder_shared_auth_config_value(
        'google_shared_public_key_path',
        'GOOGLE_SHARED_PUBLIC_KEY_PATH',
        shared_auth_first_readable_path(array(
            dirname(__DIR__, 3) . '/keys/google_jwt_public.pem',
        ))
    );
}

function shared_google_enabled(): bool
{
    return shared_auth_app_google_enabled('fluencybuilder') && is_readable(shared_google_public_key_path());
}

function build_absolute_url(string $relative_or_absolute): string
{
    if (preg_match('#^https?://#i', $relative_or_absolute)) {
        return $relative_or_absolute;
    }

    return public_origin() . $relative_or_absolute;
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

function sync_session_from_shared_auth(): void
{
    $identity = shared_auth_current_session_user();
    if (!$identity || !is_array($identity)) {
        return;
    }

    $netid = isset($identity['netid']) ? (string) $identity['netid'] : '';
    if ($netid === '') {
        return;
    }

    $attrs = isset($identity['attributes']) && is_array($identity['attributes']) ? $identity['attributes'] : array();
    $given = (string) ($attrs['givenName'] ?? $attrs['preferredFirstName'] ?? '');
    $surname = (string) ($attrs['surname'] ?? '');

    $_SESSION['netid'] = $netid;
    $_SESSION['name'] = (string) ($identity['name'] ?? $netid);
    $_SESSION['emailAddress'] = (string) ($identity['emailAddress'] ?? '');
    $_SESSION['preferredFirstName'] = $given !== '' ? $given : (string) ($identity['name'] ?? $netid);
    $_SESSION['surname'] = $surname;
    $_SESSION['google_authenticated'] = isset($identity['provider']) && (string) $identity['provider'] === 'google' ? 1 : 0;
    $_SESSION['auth_provider'] = isset($identity['provider']) ? (string) $identity['provider'] : '';
}

$google_enabled = shared_google_enabled();
$logoutBlocked = shared_auth_logout_blocked();

if ($logoutBlocked) {
    clear_local_session();
}

if (isset($_GET['logout'])) {
    $provider = (string) ($_SESSION['auth_provider'] ?? '');
    $sharedIdentity = shared_auth_current_session_user();
    $sharedProvider = is_array($sharedIdentity) && isset($sharedIdentity['provider']) ? (string) $sharedIdentity['provider'] : '';

    if ($provider === 'google' && !$sharedIdentity) {
        clear_local_session();
        header('Location: ' . current_url_without_auth_params());
        exit;
    }

    if ($provider === 'google' || $provider === 'okta' || $provider === 'byu' || $sharedProvider === 'okta' || $sharedProvider === 'byu' || $sharedIdentity) {
        clear_local_session();
        shared_auth_redirect(shared_auth_logout_url(build_url_with_query(public_origin() . app_base_path_for_root() . '/login.php', array(
            'redirect' => current_url_without_auth_params(),
        )), 'fluencybuilder'));
    }

    clear_local_session();
    header('Location: ' . current_url_without_auth_params());
    exit;
}

if (!$logoutBlocked && !isset($_SESSION['netid']) && shared_auth_current_session_user()) {
    sync_session_from_shared_auth();
}

$is_authenticated = isset($_SESSION['netid']) && trim((string) ($_SESSION['auth_provider'] ?? '')) !== '';
$auth_request = isset($_GET['auth']) ? (string) $_GET['auth'] : '';
$mode_request = isset($_GET['mode']) ? (string) $_GET['mode'] : '';
$is_google_consume = $auth_request === 'google_consume' || (isset($_GET['token']) && isset($_GET['state']));

if (!$is_authenticated) {
    if (($auth_request === 'google' || $mode_request === 'login') && $google_enabled) {
        $state = bin2hex(random_bytes(16));
        $_SESSION['fb_google_login'] = array(
            'state' => $state,
            'redirect' => isset($_GET['redirect']) ? shared_auth_safe_return_to((string) $_GET['redirect']) : current_url_without_auth_params(),
            'created' => time(),
        );

        $return_to = build_absolute_url(current_relative_url_with_auth('google_consume'));
        $shared_start = rtrim(shared_google_root(), '/') . '/google_start.php';
        $target = build_url_with_query($shared_start, array(
            'app' => shared_google_app_id(),
            'return_to' => $return_to,
            'state' => $state,
        ));

        header('Location: ' . $target);
        exit;
    }

    if ($is_google_consume) {
        if (!$google_enabled) {
            http_response_code(500);
            echo 'Google authentication is not configured. Shared auth public key is missing.';
            exit;
        }

        $oauth_state = isset($_SESSION['fb_google_login']) && is_array($_SESSION['fb_google_login']) ? $_SESSION['fb_google_login'] : array();
        $returned_state = isset($_GET['state']) ? (string) $_GET['state'] : '';
        $stored_state = isset($oauth_state['state']) ? (string) $oauth_state['state'] : '';
        if ($returned_state === '' || !hash_equals($stored_state, $returned_state)) {
            http_response_code(400);
            echo 'Invalid Google state.';
            exit;
        }

        unset($_SESSION['fb_google_login']);

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
        $_SESSION['auth_provider'] = 'google';

        $redirect_after_auth = isset($oauth_state['redirect']) && $oauth_state['redirect'] !== ''
            ? (string) $oauth_state['redirect']
            : current_url_without_auth_params();
        header('Location: ' . $redirect_after_auth);
        exit;
    }

    shared_auth_redirect(build_url_with_query(public_origin() . app_base_path_for_root() . '/login.php', array(
        'redirect' => current_url_without_auth_params(),
    )));
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
