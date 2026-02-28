<?php

declare(strict_types=1);

require_once __DIR__ . '/config.php';

$orderStore = new JsonStorage(DATA_DIR . '/orders.json');
$id = (int)($_GET['id'] ?? 0);
$order = $orderStore->find($id);

if (!$order) {
    http_response_code(404);
    exit('Commande introuvable.');
}

$title = 'Commande confirmée';
require_once __DIR__ . '/elements/header.php';
?>

<div class="card shadow-sm">
    <div class="card-body p-4">
        <h1 class="h3 text-success">Merci pour votre commande</h1>
        <p class="mb-2">Référence: <strong>#<?= (int)$order['id'] ?></strong></p>
        <p class="mb-2">Montant payé: <strong><?= e(amount_format((float)$order['total_amount'])) ?></strong></p>
        <p class="text-secondary mb-4">Un email de confirmation peut être branché ici (SMTP/API).</p>
        <div class="d-flex gap-2">
            <a class="btn btn-brand" href="/shop.php">Continuer mes achats</a>
            <a class="btn btn-outline-secondary" href="/index.php">Accueil</a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/elements/footer.php'; ?>
