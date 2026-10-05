<?php
/**
 * NOVAHOMZ — Inquiry & Contact Functions
 */
require_once __DIR__ . '/../config/db.php';

function saveInquiry(array $data): bool {
    $db = getDB();
    $stmt = $db->prepare(
        "INSERT INTO inquiries (type, product_id, product_name, product_code, name, email, phone, message, source)
         VALUES (:type, :product_id, :product_name, :product_code, :name, :email, :phone, :message, :source)"
    );
    return $stmt->execute([
        ':type'         => $data['type'] ?? 'general',
        ':product_id'   => $data['product_id'] ?? null,
        ':product_name' => $data['product_name'] ?? null,
        ':product_code' => $data['product_code'] ?? null,
        ':name'         => sanitize($data['name'] ?? ''),
        ':email'        => sanitize($data['email'] ?? ''),
        ':phone'        => sanitize($data['phone'] ?? ''),
        ':message'      => sanitize($data['message'] ?? ''),
        ':source'       => $data['source'] ?? 'website',
    ]);
}

function saveContactMessage(array $data): bool {
    $db = getDB();
    $stmt = $db->prepare(
        "INSERT INTO contact_messages (name, email, phone, subject, message)
         VALUES (:name, :email, :phone, :subject, :message)"
    );
    return $stmt->execute([
        ':name'    => sanitize($data['name'] ?? ''),
        ':email'   => sanitize($data['email'] ?? ''),
        ':phone'   => sanitize($data['phone'] ?? ''),
        ':subject' => sanitize($data['subject'] ?? ''),
        ':message' => sanitize($data['message'] ?? ''),
    ]);
}

function getInquiries(int $limit = 50, int $offset = 0, string $type = ''): array {
    $db = getDB();
    $where = '';
    $params = [];
    if ($type) {
        $where = 'WHERE type = :type';
        $params[':type'] = $type;
    }
    $stmt = $db->prepare(
        "SELECT * FROM inquiries $where ORDER BY created_at DESC LIMIT :lim OFFSET :off"
    );
    foreach ($params as $k => $v) $stmt->bindValue($k, $v);
    $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':off', $offset, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

if (!function_exists('countUnreadInquiries')) {
    function countUnreadInquiries(): int {
        return (int)getDB()->query("SELECT COUNT(*) FROM inquiries WHERE is_read = 0")->fetchColumn();
    }
}

if (!function_exists('countUnreadMessages')) {
    function countUnreadMessages(): int {
        return (int)getDB()->query("SELECT COUNT(*) FROM contact_messages WHERE is_read = 0")->fetchColumn();
    }
}

function sanitize(string $value): string {
    return htmlspecialchars(strip_tags(trim($value)), ENT_QUOTES, 'UTF-8');
}
