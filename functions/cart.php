<?php
/**
 * NOVAHOMZ — Cart Functions (Session-based)
 */

function cartStart(): void {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
}

function cartAdd(array $product): void {
    cartStart();
    $code = $product['product_code'];
    if (isset($_SESSION['cart'][$code])) {
        $_SESSION['cart'][$code]['quantity']++;
    } else {
        $_SESSION['cart'][$code] = [
            'product_id'   => $product['id'],
            'product_code' => $code,
            'product_name' => $product['name'],
            'image'        => $product['primary_image'] ?? '',
            'quantity'     => 1,
        ];
    }
}

function cartRemove(string $code): void {
    cartStart();
    unset($_SESSION['cart'][$code]);
}

function cartGet(): array {
    cartStart();
    return $_SESSION['cart'];
}

function cartCount(): int {
    cartStart();
    return array_sum(array_column($_SESSION['cart'], 'quantity'));
}

function cartClear(): void {
    cartStart();
    $_SESSION['cart'] = [];
}
