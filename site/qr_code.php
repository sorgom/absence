<?php
declare(strict_types=1);

$libraryPath = __DIR__ . '/php-qrcode/qrcode.php';

if (!is_file($libraryPath)) {
    http_response_code(500);
    header('Content-Type: text/plain; charset=UTF-8');
    echo 'QR code library is not available.';
    exit;
}

if (!extension_loaded('gd') || !function_exists('imagepng')) {
    http_response_code(500);
    header('Content-Type: text/plain; charset=UTF-8');
    echo 'PHP GD extension is required for QR code generation.';
    exit;
}

require_once $libraryPath;

if (!class_exists('QRCode')) {
    http_response_code(500);
    header('Content-Type: text/plain; charset=UTF-8');
    echo 'QR code generator class is not available.';
    exit;
}

$host = (string) ($_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? 'localhost');
$host = preg_replace('/[^A-Za-z0-9.\-:_\[\]]/', '', $host) ?: 'localhost';

$forwardedProto = (string) ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '');
$scheme = $forwardedProto !== ''
    ? strtolower(trim(explode(',', $forwardedProto)[0]))
    : ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http');

if (!in_array($scheme, ['http', 'https'], true)) {
    $scheme = 'http';
}

$appUrl = $scheme . '://' . $host;

header('Content-Type: image/png');
header('Cache-Control: no-store, max-age=0');

$qrCode = new QRCode($appUrl, [
    's' => 'qrm',
    'w' => 360,
    'h' => 360,
    'p' => 16,
]);

$image = $qrCode->render_image();
imagepng($image);
imagedestroy($image);
