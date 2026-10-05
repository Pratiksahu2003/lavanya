<?php
/**
 * NOVAHOMZ — Category & Navigation Functions
 */
require_once __DIR__ . '/../config/db.php';

function getAllCategories(bool $activeOnly = true): array {
    $db = getDB();
    $sql = "SELECT * FROM categories";
    if ($activeOnly) $sql .= " WHERE is_active = 1";
    $sql .= " ORDER BY sort_order ASC, name ASC";
    return $db->query($sql)->fetchAll();
}

function getCategoryBySlug(string $slug): ?array {
    $stmt = getDB()->prepare("SELECT * FROM categories WHERE slug = :slug AND is_active = 1");
    $stmt->execute([':slug' => $slug]);
    return $stmt->fetch() ?: null;
}

function getNavCategories(): array {
    $db = getDB();
    $cats = $db->query(
        "SELECT * FROM categories WHERE is_active = 1 AND show_in_nav = 1 ORDER BY sort_order ASC"
    )->fetchAll();

    foreach ($cats as &$cat) {
        $stmt = $db->prepare(
            "SELECT * FROM subcategories WHERE category_id = :id AND is_active = 1 ORDER BY sort_order ASC"
        );
        $stmt->execute([':id' => $cat['id']]);
        $cat['subcategories'] = $stmt->fetchAll();
    }
    return $cats;
}

function getSubcategoriesByCategory(int $categoryId): array {
    $stmt = getDB()->prepare(
        "SELECT * FROM subcategories WHERE category_id = :id AND is_active = 1 ORDER BY sort_order ASC"
    );
    $stmt->execute([':id' => $categoryId]);
    return $stmt->fetchAll();
}

function getSubcategoryBySlug(string $slug): ?array {
    $stmt = getDB()->prepare("SELECT * FROM subcategories WHERE slug = :slug AND is_active = 1");
    $stmt->execute([':slug' => $slug]);
    return $stmt->fetch() ?: null;
}

function getAllCollections(bool $activeOnly = true): array {
    $db = getDB();
    $sql = "SELECT * FROM collections";
    if ($activeOnly) $sql .= " WHERE is_active = 1";
    $sql .= " ORDER BY sort_order ASC";
    return $db->query($sql)->fetchAll();
}

function getIndustries(): array {
    return getDB()->query(
        "SELECT * FROM industries WHERE is_active = 1 ORDER BY sort_order ASC"
    )->fetchAll();
}

function getTestimonials(int $limit = 4): array {
    $stmt = getDB()->prepare(
        "SELECT * FROM testimonials WHERE is_active = 1 ORDER BY sort_order ASC LIMIT :lim"
    );
    $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

function getHomepageBanners(): array {
    return getDB()->query(
        "SELECT * FROM homepage_banners WHERE is_active = 1 ORDER BY sort_order ASC"
    )->fetchAll();
}
