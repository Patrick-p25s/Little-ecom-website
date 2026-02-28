<?php

declare(strict_types=1);

require_once __DIR__ . '/config.php';

$store = new JsonStorage(DATA_DIR . '/products.json');
$id = (int)($_GET['id'] ?? 0);
$product = $store->find($id);

if (!$product) {
    http_response_code(404);
    exit('Produit introuvable.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();

    $quantity = max(1, (int)($_POST['quantity'] ?? 1));

    if ((int)$product['stock'] < $quantity) {
        set_flash('danger', 'Quantité demandée supérieure au stock disponible.');
    } else {
        cart_add((int)$product['id'], $quantity);
        set_flash('success', 'Produit ajouté au panier.');
    }

    redirect('/product.php?id=' . (int)$product['id']);
}

$title = (string)$product['name'];
require_once __DIR__ . '/elements/header.php';
?>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <h1 class="h2 mb-3"><?= e((string)$product['name']) ?></h1>
                <p class="text-secondary"><?= e((string)($product['description'] ?? 'Description du produit premium.')) ?></p>
                <div class="d-flex gap-3 align-items-center mb-3">
                    <span class="h4 mb-0 fw-bold"><?= e(amount_format((float)$product['price'])) ?></span>
                    <span class="badge text-bg-light">Stock: <?= (int)$product['stock'] ?></span>
                </div>
                <div class="alert alert-light border mb-0">Livraison 24-72h. Retour gratuit sous 14 jours.</div>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card shadow-sm">
            <div class="card-header">Ajouter au panier</div>
            <div class="card-body">
                <form method="post" class="vstack gap-3">
                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                    <label class="form-label mb-0">Quantité</label>
                    <input class="form-control" type="number" min="1" max="<?= (int)$product['stock'] ?>" name="quantity" value="1" required>
                    <button class="btn btn-brand" type="submit">Ajouter</button>
                    <a class="btn btn-outline-secondary" href="/shop.php">Retour shop</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/elements/footer.php'; ?>
