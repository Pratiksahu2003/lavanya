<?php
/**
 * NOVAHOMZ — Admin Auth Helper
 */
require_once __DIR__ . '/../config/db.php';

function adminSession(): void {
    if (session_status() === PHP_SESSION_NONE) session_start();
}

function isAdminLoggedIn(): bool {
    adminSession();
    return !empty($_SESSION['admin_id']) && !empty($_SESSION['admin_email']);
}

function requireAdminLogin(): void {
    if (!isAdminLoggedIn()) {
        header('Location: ' . BASE_URL . '/admin/login.php');
        exit;
    }
}

function adminLogin(string $email, string $password): bool {
    adminSession();
    $stmt = getDB()->prepare(
        "SELECT * FROM admins WHERE email = :email AND is_active = 1"
    );
    $stmt->execute([':email' => $email]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_id']    = $admin['id'];
        $_SESSION['admin_name']  = $admin['name'];
        $_SESSION['admin_email'] = $admin['email'];
        $_SESSION['admin_role']  = $admin['role'];
        // Update last login
        getDB()->prepare("UPDATE admins SET last_login = NOW() WHERE id = :id")
               ->execute([':id' => $admin['id']]);
        return true;
    }
    return false;
}

function adminLogout(): void {
    adminSession();
    session_destroy();
    header('Location: ' . BASE_URL . '/admin/login.php');
    exit;
}

function currentAdmin(): array {
    adminSession();
    return [
        'id'    => $_SESSION['admin_id'] ?? 0,
        'name'  => $_SESSION['admin_name'] ?? '',
        'email' => $_SESSION['admin_email'] ?? '',
        'role'  => $_SESSION['admin_role'] ?? '',
    ];
}
