<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/config.php';
require_admin();

$store = new JsonStorage(DATA_DIR . '/orders.json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();

    $action = $_POST['action'] ?? '';

    if ($action === 'update_status') {
        $id = (int)($_POST['id'] ?? 0);
        $status = (string)($_POST['status'] ?? 'created');
        $allowed = ['created', 'paid', 'shipped', 'cancelled'];

        if (!in_array($status, $allowed, true)) {
            set_flash('danger', 'Status invalide.');
            redirect('/admin/orders.php');
        }

        $order = $store->find($id);
        if ($order) {
            $order['status'] = $status;
            $store->update($id, $order);
            set_flash('success', 'Status commande mis à jour.');
        }

        redirect('/admin/orders.php');
    }
}

$orders = $store->all();
usort($orders, static fn(array $a, array $b): int => (int)$b['id'] <=> (int)$a['id']);

$title = 'Admin Commandes';
require_once dirname(__DIR__) . '/elements/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Gestion des commandes</h1>
    <a class="btn btn-sm btn-outline-secondary" href="/admin/dashboard.php">Retour dashboard</a>
</div>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead>
            <tr>
                <th>#</th>
                <th>Client</th>
                <th>Adresse</th>
                <th>Montant</th>
                <th>Status</th>
                <th>Articles</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($orders as $order): ?>
                <tr>
                    <td><?= (int)$order['id'] ?></td>
                    <td>
                        <div class="fw-semibold"><?= e((string)($order['customer_name'] ?? $order['user_name'] ?? 'N/A')) ?></div>
                        <div class="small text-secondary"><?= e((string)($order['customer_email'] ?? '')) ?></div>
                    </td>
                    <td>
                        <?php $a = $order['address'] ?? []; ?>
                        <div class="small"><?= e((string)($a['address_line'] ?? '')) ?></div>
                        <div class="small text-secondary"><?= e((string)($a['postal_code'] ?? '')) ?> <?= e((string)($a['city'] ?? '')) ?>, <?= e((string)($a['country'] ?? '')) ?></div>
                    </td>
                    <td><?= e(amount_format((float)($order['total_amount'] ?? 0))) ?></td>
                    <td>
                        <form method="post" class="d-flex gap-2">
                            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                            <input type="hidden" name="action" value="update_status">
                            <input type="hidden" name="id" value="<?= (int)$order['id'] ?>">
                            <select class="form-select form-select-sm" name="status">
                                <?php foreach (['created', 'paid', 'shipped', 'cancelled'] as $status): ?>
                                    <option value="<?= e($status) ?>" <?= ($order['status'] ?? '') === $status ? 'selected' : '' ?>><?= e($status) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button class="btn btn-sm btn-brand" type="submit">OK</button>
                        </form>
                    </td>
                    <td>
                        <?php $items = $order['items'] ?? []; ?>
                        <?php if (is_array($items) && $items !== []): ?>
                            <ul class="small mb-0 ps-3">
                                <?php foreach ($items as $item): ?>
                                    <li><?= e((string)($item['name'] ?? '')) ?> x<?= (int)($item['quantity'] ?? 0) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <span class="small text-secondary">N/A</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if ($orders === []): ?>
                <tr><td colspan="6" class="text-center text-secondary">Aucune commande.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/elements/footer.php'; ?>
