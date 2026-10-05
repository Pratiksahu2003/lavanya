<?php
/**
 * NOVAHOMZ - Quote modal inquiry handler
 */
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/functions/inquiries.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . '/index.php');
    exit;
}

$name         = trim($_POST['name'] ?? '');
$phone        = trim($_POST['phone'] ?? '');
$businessType = trim($_POST['business_type'] ?? '');
$message      = trim($_POST['message'] ?? '');
$source       = trim($_POST['source'] ?? 'quote_modal');

$referer = strtok($_SERVER['HTTP_REFERER'] ?? (BASE_URL . '/index.php'), '#');
$referer = preg_replace('/([?&])inquiry=[^&]*&?/', '$1', $referer);
$referer = rtrim($referer, '?&');
$separator = strpos($referer, '?') === false ? '?' : '&';

if (!$name || !$phone || !$businessType) {
    header('Location: ' . $referer . $separator . 'inquiry=error#quoteInquiryModal');
    exit;
}

$fullMessage = "Business Type: {$businessType}";
if ($message !== '') {
    $fullMessage .= "\n\nMessage: {$message}";
}

$ok = saveInquiry([
    'type'    => 'bulk',
    'name'    => $name,
    'phone'   => $phone,
    'message' => $fullMessage,
    'source'  => $source,
]);

header('Location: ' . $referer . $separator . 'inquiry=' . ($ok ? 'sent' : 'error') . '#quoteInquiryModal');
exit;
