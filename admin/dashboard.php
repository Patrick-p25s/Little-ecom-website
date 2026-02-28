<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/config.php';
require_admin();

$productStore = new JsonStorage(DATA_DIR . '/products.json');
$userStore = new JsonStorage(DATA_DIR . '/users.json');
$orderStore = new JsonStorage(DATA_DIR . '/orders.json');

$products = $productStore->all();
$users = $userStore->all();
$orders = $orderStore->all();

$revenue = array_reduce($orders, static fn(float $sum, array $order): float => $sum + (float)($order['total_amount'] ?? 0), 0.0);

$title = 'Admin Dashboard';
require_once dirname(__DIR__) . '/elements/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Dashboard administrateur</h1>
    <div class="d-flex gap-2">
        <a class="btn btn-sm btn-outline-secondary" href="/admin/products.php">Gérer produits</a>
        <a class="btn btn-sm btn-outline-secondary" href="/admin/orders.php">Gérer commandes</a>
        <a class="btn btn-sm btn-outline-secondary" href="/admin/users.php">Voir clients</a>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4"><div class="card metric-card shadow-sm"><div class="card-body"><div class="text-secondary">Produits</div><div class="display-6"><?= count($products) ?></div></div></div></div>
    <div class="col-md-4"><div class="card metric-card shadow-sm"><div class="card-body"><div class="text-secondary">Clients</div><div class="display-6"><?= count($users) ?></div></div></div></div>
    <div class="col-md-4"><div class="card metric-card shadow-sm"><div class="card-body"><div class="text-secondary">Revenus</div><div class="display-6"><?= e(amount_format($revenue)) ?></div></div></div></div>
</div>

<div class="card shadow-sm">
    <div class="card-header">Dernières commandes</div>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead><tr><th>#</th><th>Client</th><th>Status</th><th>Total</th><th>Date</th></tr></thead>
            <tbody>
            <?php foreach (array_slice(array_reverse($orders), 0, 8) as $order): ?>
                <tr>
                    <td><?= (int)$order['id'] ?></td>
                    <td><?= e((string)($order['customer_name'] ?? $order['user_name'] ?? 'N/A')) ?></td>
                    <td><span class="badge text-bg-<?= e(status_badge_class((string)($order['status'] ?? 'created'))) ?>"><?= e((string)($order['status'] ?? 'created')) ?></span></td>
                    <td><?= e(amount_format((float)($order['total_amount'] ?? 0))) ?></td>
                    <td><?= e((string)($order['created_at'] ?? '')) ?></td>
                </tr>
            <?php endforeach; ?>
            <?php if ($orders === []): ?>
                <tr><td colspan="5" class="text-center text-secondary">Aucune commande.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/elements/footer.php'; ?>
