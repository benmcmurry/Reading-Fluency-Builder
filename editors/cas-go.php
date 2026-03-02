<?php
require_once dirname($_SERVER['DOCUMENT_ROOT']) . '/config.php';
require_once dirname($_SERVER['DOCUMENT_ROOT']) . '/CAS.php';

phpCAS::setDebug();
phpCAS::setVerbose(true);
phpCAS::client(CAS_VERSION_2_0, $cas_host, $cas_port, $cas_context);
phpCAS::setNoCasServerValidation();

if (isset($_REQUEST['logout'])) {
    phpCAS::logout();
}

$auth = phpCAS::checkAuthentication();
if ($auth) {
    $netid = phpCAS::getUser();
    $attrs = phpCAS::getAttributes();
    $name = $attrs['name'];
    $id = "<button id='user' type='button' aria-expanded='false'>" . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . "</button><a id='logout' href='?logout='>Logout</a>";
} else {
    phpCAS::forceAuthentication();
    $id = '';
}

$_SESSION['netid'] = $netid;
$_SESSION['name'] = $attrs['name'];
$_SESSION['emailAddress'] = $attrs['emailAddress'];
$_SESSION['preferredFirstName'] = $attrs['preferredFirstName'];
$_SESSION['surname'] = $attrs['surname'];

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
