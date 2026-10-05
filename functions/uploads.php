<?php
/**
 * NOVAHOMZ — Upload Utility Functions
 */
require_once __DIR__ . '/../config/db.php';

define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/webp']);
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB

function uploadImage(array $file, string $folder): array {
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'error' => 'File upload error: ' . $file['error']];
    }
    if ($file['size'] > MAX_FILE_SIZE) {
        return ['success' => false, 'error' => 'File exceeds 5MB limit.'];
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime  = $finfo->file($file['tmp_name']);
    if (!in_array($mime, ALLOWED_IMAGE_TYPES, true)) {
        return ['success' => false, 'error' => 'Only JPG, PNG and WEBP files are allowed.'];
    }

    $ext      = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid('lc_', true) . '.' . strtolower($ext);
    $destDir  = UPLOAD_PATH . $folder . '/';

    if (!is_dir($destDir)) {
        mkdir($destDir, 0755, true);
    }

    $dest = $destDir . $filename;
    if (!move_uploaded_file($file['tmp_name'], $dest)) {
        return ['success' => false, 'error' => 'Failed to save the file.'];
    }

    return ['success' => true, 'filename' => $folder . '/' . $filename];
}

function deleteUpload(string $path): void {
    if ($path === '' || str_starts_with($path, 'http')) return;
    $full = UPLOAD_PATH . ltrim($path, '/');
    if (file_exists($full)) unlink($full);
}

function getImageUrl(?string $path): string {
    $path = trim((string)$path);
    if ($path === '') return BASE_URL . '/assets/images/lc-logo.png';

    // Preserve external and data URLs exactly as stored.
    if (preg_match('#^(https?:)?//#i', $path) || str_starts_with($path, 'data:')) {
        return $path;
    }

    // Preserve already-normalized project URLs.
    if (str_starts_with($path, BASE_URL . '/uploads/') || str_starts_with($path, BASE_URL . '/assets/')) {
        return $path;
    }

    $relative = ltrim($path, '/');
    if (str_starts_with($relative, 'uploads/')) {
        $relative = substr($relative, strlen('uploads/'));
    }

    $full = UPLOAD_PATH . $relative;
    if (!file_exists($full)) {
        return BASE_URL . '/assets/images/lc-logo.png';
    }

    return UPLOAD_URL . $relative;
}
