<?php

declare(strict_types=1);

function admin_store(): JsonStorage
{
    return new JsonStorage(DATA_DIR . '/admins.json');
}

/** @return array{id:int,email:string,name:string}|null */
function authenticate_admin(string $email, string $password): ?array
{
    $email = lower(trim($email));

    foreach (admin_store()->all() as $admin) {
        if (lower((string)($admin['email'] ?? '')) !== $email) {
            continue;
        }

        if (!password_verify($password, (string)($admin['password_hash'] ?? ''))) {
            return null;
        }

        return [
            'id' => (int)$admin['id'],
            'email' => (string)$admin['email'],
            'name' => (string)$admin['name'],
        ];
    }

    return null;
}

function login_admin(array $admin): void
{
    session_regenerate_id(true);
    $_SESSION['admin'] = [
        'id' => (int)$admin['id'],
        'email' => (string)$admin['email'],
        'name' => (string)$admin['name'],
    ];
}

function logout_admin(): void
{
    unset($_SESSION['admin']);
    session_regenerate_id(true);
}
