<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/config.php';
require_admin();

$store = new JsonStorage(DATA_DIR . '/users.json');
$users = $store->all();
usort($users, static fn(array $a, array $b): int => (int)$b['id'] <=> (int)$a['id']);

$title = 'Admin Clients';
require_once dirname(__DIR__) . '/elements/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Clients enregistrés</h1>
    <a class="btn btn-sm btn-outline-secondary" href="/admin/dashboard.php">Retour dashboard</a>
</div>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table mb-0">
            <thead><tr><th>#</th><th>Nom</th><th>Email</th><th>Téléphone</th><th>Date</th></tr></thead>
            <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= (int)$user['id'] ?></td>
                    <td><?= e((string)$user['name']) ?></td>
                    <td><?= e((string)$user['email']) ?></td>
                    <td><?= e((string)($user['phone'] ?? '')) ?></td>
                    <td><?= e((string)($user['created_at'] ?? '')) ?></td>
                </tr>
            <?php endforeach; ?>
            <?php if ($users === []): ?>
                <tr><td colspan="5" class="text-center text-secondary">Aucun client.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/elements/footer.php'; ?>
