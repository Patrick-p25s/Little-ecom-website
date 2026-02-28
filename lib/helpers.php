<?php

declare(strict_types=1);

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $path): never
{
    header('Location: ' . $path);
    exit;
}

function set_flash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function get_flash(): ?array
{
    if (!isset($_SESSION['flash'])) {
        return null;
    }

    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);

    return $flash;
}

function amount_format(float $amount): string
{
    return number_format($amount, 2, ',', ' ') . ' EUR';
}

function lower(string $value): string
{
    if (function_exists('mb_strtolower')) {
        return mb_strtolower($value, 'UTF-8');
    }

    return strtolower($value);
}

function csrf_token(): string
{
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function verify_csrf(?string $token): bool
{
    return isset($_SESSION['csrf_token']) && is_string($token) && hash_equals($_SESSION['csrf_token'], $token);
}

function require_csrf(): void
{
    if (!verify_csrf($_POST['csrf_token'] ?? null)) {
        http_response_code(419);
        exit('CSRF token invalide.');
    }
}

/** @return array{id:int,email:string,name:string}|null */
function current_admin(): ?array
{
    return $_SESSION['admin'] ?? null;
}

function is_admin_logged_in(): bool
{
    return current_admin() !== null;
}

function require_admin(): void
{
    if (!is_admin_logged_in()) {
        set_flash('warning', 'Connectez-vous en administrateur.');
        redirect('/admin/login.php');
    }
}

/** @return array<int, array{id:int,quantity:int}> */
function cart_items(): array
{
    return $_SESSION['cart'] ?? [];
}

function cart_count(): int
{
    $count = 0;
    foreach (cart_items() as $item) {
        $count += (int)($item['quantity'] ?? 0);
    }

    return $count;
}

function cart_add(int $productId, int $quantity): void
{
    $quantity = max(1, $quantity);
    $cart = cart_items();

    foreach ($cart as &$item) {
        if ((int)$item['id'] === $productId) {
            $item['quantity'] += $quantity;
            $_SESSION['cart'] = $cart;
            return;
        }
    }

    $cart[] = ['id' => $productId, 'quantity' => $quantity];
    $_SESSION['cart'] = $cart;
}

/** @param array<int,int> $quantities */
function cart_update_quantities(array $quantities): void
{
    $cart = cart_items();

    foreach ($cart as $index => $item) {
        $id = (int)$item['id'];
        $quantity = max(0, (int)($quantities[$id] ?? $item['quantity']));

        if ($quantity <= 0) {
            unset($cart[$index]);
            continue;
        }

        $cart[$index]['quantity'] = $quantity;
    }

    $_SESSION['cart'] = array_values($cart);
}

function cart_remove(int $productId): void
{
    $_SESSION['cart'] = array_values(array_filter(
        cart_items(),
        static fn(array $item): bool => (int)$item['id'] !== $productId
    ));
}

function cart_clear(): void
{
    $_SESSION['cart'] = [];
}

function status_badge_class(string $status): string
{
    return match ($status) {
        'paid' => 'success',
        'shipped' => 'info',
        'cancelled' => 'danger',
        default => 'secondary',
    };
}
