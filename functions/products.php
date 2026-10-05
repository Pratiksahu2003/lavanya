<?php
/**
 * NOVAHOMZ — Product Functions
 */
require_once __DIR__ . '/../config/db.php';

function getAllProducts(array $filters = [], int $limit = 0, int $offset = 0): array {
    $db = getDB();
    $where = ['p.status = :status'];
    $params = [':status' => 'active'];

    if (!empty($filters['category_slug'])) {
        $where[] = 'c.slug = :cat_slug';
        $params[':cat_slug'] = $filters['category_slug'];
    }
    if (!empty($filters['subcategory_slug'])) {
        $where[] = 'sc.slug = :sub_slug';
        $params[':sub_slug'] = $filters['subcategory_slug'];
    }
    if (!empty($filters['search'])) {
    $where[] = '(p.name LIKE :search_name 
              OR p.product_code LIKE :search_code
              OR p.short_desc LIKE :search_desc)';

    $search = '%' . $filters['search'] . '%';

    $params[':search_name'] = $search;
    $params[':search_code'] = $search;
    $params[':search_desc'] = $search;
}
    if (isset($filters['is_featured'])) {
        $where[] = 'p.is_featured = :featured';
        $params[':featured'] = (int)$filters['is_featured'];
    }

    $sql = "SELECT p.*, c.name AS category_name, c.slug AS category_slug,
                   sc.name AS subcategory_name, sc.slug AS subcategory_slug,
                   pi.image AS primary_image
            FROM products p
            LEFT JOIN categories c ON c.id = p.category_id
            LEFT JOIN subcategories sc ON sc.id = p.subcategory_id
            LEFT JOIN product_images pi ON pi.product_id = p.id AND pi.is_primary = 1
            WHERE " . implode(' AND ', $where) . "
            ORDER BY p.is_featured DESC, p.sort_order ASC, p.created_at DESC";

    if ($limit > 0) {
        $sql .= " LIMIT :limit OFFSET :offset";
    }

    $stmt = $db->prepare($sql);
    foreach ($params as $k => $v) {
        $stmt->bindValue($k, $v);
    }
    if ($limit > 0) {
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    }
    $stmt->execute();
    return $stmt->fetchAll();
}

function getProductBySlug(string $slug): ?array {
    $db = getDB();
    $stmt = $db->prepare(
        "SELECT p.*, c.name AS category_name, c.slug AS category_slug,
                sc.name AS subcategory_name, sc.slug AS subcategory_slug
         FROM products p
         LEFT JOIN categories c ON c.id = p.category_id
         LEFT JOIN subcategories sc ON sc.id = p.subcategory_id
         WHERE p.slug = :slug AND p.status = 'active'"
    );
    $stmt->execute([':slug' => $slug]);
    $product = $stmt->fetch();
    if (!$product) return null;

    // Increment views
    $db->prepare("UPDATE products SET views = views + 1 WHERE id = :id")
       ->execute([':id' => $product['id']]);

    $product['images']   = getProductImages($product['id']);
    $product['features'] = getProductFeatures($product['id']);
    $product['reviews']  = getProductReviews($product['id']);
    return $product;
}

function getProductByCode(string $code): ?array {
    $db = getDB();
    $stmt = $db->prepare(
        "SELECT p.*, c.name AS category_name, c.slug AS category_slug,
                sc.name AS subcategory_name,
                pi.image AS primary_image
         FROM products p
         LEFT JOIN categories c ON c.id = p.category_id
         LEFT JOIN subcategories sc ON sc.id = p.subcategory_id
         LEFT JOIN product_images pi ON pi.product_id = p.id AND pi.is_primary = 1
         WHERE p.product_code = :code AND p.status = 'active'"
    );
    $stmt->execute([':code' => $code]);
    return $stmt->fetch() ?: null;
}

function getProductImages(int $productId): array {
    $stmt = getDB()->prepare(
        "SELECT * FROM product_images WHERE product_id = :id ORDER BY sort_order ASC"
    );
    $stmt->execute([':id' => $productId]);
    return $stmt->fetchAll();
}

function getProductFeatures(int $productId): array {
    $stmt = getDB()->prepare(
        "SELECT feature FROM product_features WHERE product_id = :id ORDER BY sort_order ASC"
    );
    $stmt->execute([':id' => $productId]);
    return array_column($stmt->fetchAll(), 'feature');
}

function getProductReviews(int $productId): array {
    $stmt = getDB()->prepare(
        "SELECT * FROM reviews WHERE product_id = :id AND status = 'approved' ORDER BY sort_order ASC, created_at DESC"
    );
    $stmt->execute([':id' => $productId]);
    return $stmt->fetchAll();
}

function getApprovedReviews(int $limit = 4): array {
    $stmt = getDB()->prepare(
        "SELECT * FROM reviews WHERE status = 'approved' ORDER BY sort_order ASC, created_at DESC LIMIT :lim"
    );
    $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

function getRelatedProducts(int $productId, int $categoryId, int $limit = 4): array {
    $stmt = getDB()->prepare(
        "SELECT p.*, pi.image AS primary_image, c.name AS category_name, c.slug AS category_slug
         FROM products p
         LEFT JOIN product_images pi ON pi.product_id = p.id AND pi.is_primary = 1
         LEFT JOIN categories c ON c.id = p.category_id
         WHERE p.category_id = :cat_id AND p.id != :prod_id AND p.status = 'active'
         ORDER BY RAND() LIMIT :lim"
    );
    $stmt->bindValue(':cat_id', $categoryId, PDO::PARAM_INT);
    $stmt->bindValue(':prod_id', $productId, PDO::PARAM_INT);
    $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

function getFeaturedProducts(int $limit = 8): array {
    return getAllProducts(['is_featured' => 1], $limit);
}

function countProducts(array $filters = []): int {
    $db = getDB();
    $where = ['p.status = :status'];
    $params = [':status' => 'active'];

    if (!empty($filters['category_slug'])) {
        $where[] = 'c.slug = :cat_slug';
        $params[':cat_slug'] = $filters['category_slug'];
    }
    if (!empty($filters['subcategory_slug'])) {
        $where[] = 'sc.slug = :sub_slug';
        $params[':sub_slug'] = $filters['subcategory_slug'];
    }
   if (!empty($filters['search'])) {
    $where[] = '(p.name LIKE :search_name 
              OR p.product_code LIKE :search_code
              OR p.short_desc LIKE :search_desc)';

    $search = '%' . $filters['search'] . '%';

    $params[':search_name'] = $search;
    $params[':search_code'] = $search;
    $params[':search_desc'] = $search;
}

    $sql = "SELECT COUNT(*) FROM products p
            LEFT JOIN categories c ON c.id = p.category_id
            LEFT JOIN subcategories sc ON sc.id = p.subcategory_id
            WHERE " . implode(' AND ', $where);

    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    return (int)$stmt->fetchColumn();
}

function createSlug(string $text): string {
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    $text = preg_replace('/[\s-]+/', '-', $text);
    return trim($text, '-');
}

function getUniqueSlug(string $base, int $excludeId = 0, string $table = 'products'): string {
    $db = getDB();
    $slug = createSlug($base);
    $original = $slug;
    $counter = 1;
    while (true) {
        $q = $excludeId
            ? $db->prepare("SELECT id FROM `$table` WHERE slug = :slug AND id != :id")
            : $db->prepare("SELECT id FROM `$table` WHERE slug = :slug");
        $params = [':slug' => $slug];
        if ($excludeId) $params[':id'] = $excludeId;
        $q->execute($params);
        if (!$q->fetch()) break;
        $slug = $original . '-' . $counter++;
    }
    return $slug;
}
