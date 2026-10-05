<?php
/**
 * NOVAHOMZ — Settings Helper
 */
require_once __DIR__ . '/../config/db.php';

function getSetting(string $key, string $default = ''): string {
    static $cache = [];
    if (!empty($cache)) {
        return $cache[$key] ?? $default;
    }
    $rows = getDB()->query("SELECT setting_key, setting_val FROM settings")->fetchAll();
    foreach ($rows as $row) {
        $cache[$row['setting_key']] = $row['setting_val'];
    }
    return $cache[$key] ?? $default;
}

function setSetting(string $key, string $value, ?string $label = null): void {
    $db = getDB();
    $stmt = $db->prepare(
        "INSERT INTO settings (setting_key, setting_val, label)
         VALUES (:key, :val, :label)
         ON DUPLICATE KEY UPDATE setting_val = VALUES(setting_val), label = COALESCE(VALUES(label), label)"
    );
    $stmt->execute([
        ':key' => $key,
        ':val' => $value,
        ':label' => $label ?? ucwords(str_replace('_', ' ', $key)),
    ]);
}

function getJsonSetting(string $key, array $default = []): array {
    $raw = getSetting($key, '');
    if ($raw === '') return $default;
    $decoded = json_decode($raw, true);
    return is_array($decoded) ? $decoded : $default;
}

function getSettingImage(string $key, string $fallback = '/assets/images/lc-logo.png'): string {
    require_once __DIR__ . '/uploads.php';
    $value = getSetting($key, '');
    return $value !== '' ? getImageUrl($value) : BASE_URL . $fallback;
}

function getWhatsAppLink(string $message = ''): string {
    $number = preg_replace('/[^0-9]/', '', getSetting('whatsapp', '918796591267'));
    $text   = $message ? '?text=' . rawurlencode($message) : '';
    return "https://wa.me/{$number}{$text}";
}

function getWhatsAppProductLink(string $productName, string $productCode): string {
    $msg = "Hi! I'm interested in the {$productName} (Code: {$productCode}). Can you share more details?";
    return getWhatsAppLink($msg);
}

// ── CMS additions ─────────────────────────────────────────────

if (!function_exists('getSettingImage')) {
    /**
     * Returns a full URL for an uploaded setting image, or falls back to $default.
     */
    function getSettingImage(string $key, string $default = '/assets/images/lc-logo.png'): string {
        require_once __DIR__ . '/uploads.php';
        $val = getSetting($key, '');
        if ($val) return getImageUrl($val);
        return defined('BASE_URL') ? BASE_URL . $default : $default;
    }
}

if (!function_exists('countUnreadInquiries')) {
    function countUnreadInquiries(): int {
        try {
            $db = getDB();
            return (int)$db->query("SELECT COUNT(*) FROM inquiries WHERE is_read=0")->fetchColumn();
        } catch (Exception $e) { return 0; }
    }
}

if (!function_exists('countUnreadMessages')) {
    function countUnreadMessages(): int {
        try {
            $db = getDB();
            return (int)$db->query("SELECT COUNT(*) FROM contact_messages WHERE is_read=0")->fetchColumn();
        } catch (Exception $e) { return 0; }
    }
}
