<?php



$cookie_lifetime = 0;
$cookie_path = '/';
$cookie_domain = '';
$cookie_secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || ($_SERVER['SERVER_PORT'] ?? '') == 443;
$cookie_httponly = true;
$cookie_samesite = 'Lax';

session_set_cookie_params([
    'lifetime' => $cookie_lifetime,
    'path' => $cookie_path,
    'domain' => $cookie_domain,
    'secure' => $cookie_secure,
    'httponly' => $cookie_httponly,
    'samesite' => $cookie_samesite
]);


header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'; img-src 'self' data:; connect-src 'self'; frame-ancestors 'none';");
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
header("Referrer-Policy: no-referrer-when-downgrade");
header("X-XSS-Protection: 1; mode=block");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


function e($s)
{
    return htmlspecialchars((string)$s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}


function generate_csrf_token()
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}


function verify_csrf_token($token)
{
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], (string)$token);
}


function require_login($role = null)
{
    if (empty($_SESSION['user_id'])) {
        header("Location: login.php");
        exit;
    }
    if ($role && (!isset($_SESSION['role']) || $_SESSION['role'] !== $role)) {
        header("Location: index.php");
        exit;
    }
}
