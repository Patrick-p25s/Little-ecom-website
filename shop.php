<?php

declare(strict_types=1);

use JsonStorage;

require_once __DIR__ . '/config.php';

$store = new JsonStorage(DATA_DIR . '/products.json');
$products = $store->all();

$query = trim((string)($_GET['q'] ?? ''));
if ($query !== '') {
    $products = array_values(array_filter(
        $products,
        static fn(array $product): bool => str_contains(lower((string)$product['name']), lower($query))
    ));
}

usort($products, static fn(array $a, array $b): int => strcmp((string)$a['name'], (string)$b['name']));

$title = 'Shop';
require_once __DIR__ . '/elements/header.php';
?>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <h1 class="h3 mb-0">Shop produits</h1>
    <form class="d-flex gap-2" method="get">
        <input class="form-control" name="q" placeholder="Rechercher un produit" value="<?= e($query) ?>">
        <button class="btn btn-brand" type="submit">Search</button>
    </form>
</div>

<div class="row g-3">
    <?php foreach ($products as $product): ?>
        <div class="col-sm-6 col-xl-4">
            <article class="card product-card h-100 shadow-sm">
                <div class="card-body d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h2 class="h5 mb-0"><?= e((string)$product['name']) ?></h2>
                        <span class="badge text-bg-light">Stock: <?= (int)$product['stock'] ?></span>
                    </div>
                    <p class="text-secondary small flex-grow-1"><?= e((string)($product['description'] ?? 'Produit de qualite professionnelle.')) ?></p>
                    <div class="d-flex justify-content-between align-items-center mt-2">
                        <div class="fw-semibold"><?= e(amount_format((float)$product['price'])) ?></div>
                        <a class="btn btn-brand btn-sm" href="/product.php?id=<?= (int)$product['id'] ?>">Details</a>
                    </div>
                </div>
            </article>
        </div>
    <?php endforeach; ?>
    <?php if ($products === []): ?>
        <div class="col-12"><div class="alert alert-info">Aucun produit trouvé.</div></div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/elements/footer.php'; ?>
