<?php

declare(strict_types=1);

require_once __DIR__ . '/config.php';

$productStore = new JsonStorage(DATA_DIR . '/products.json');
$products = $productStore->all();

usort($products, static fn(array $a, array $b): int => (int)$b['id'] <=> (int)$a['id']);
$featured = array_slice($products, 0, 4);

$title = 'RedCart Pro - Accueil';
require_once __DIR__ . '/elements/header.php';
?>

<section class="hero p-4 p-md-5 mb-4">
    <div class="row align-items-center g-4">
        <div class="col-lg-7">
            <h1 class="display-6 fw-bold">Boutique e-commerce professionnelle en PHP</h1>
            <p class="lead text-secondary mb-4">Catalogue moderne, panier dynamique, commande avec adresse de livraison, et back-office administrateur sécurisé.</p>
            <div class="d-flex gap-2">
                <a class="btn btn-brand btn-lg" href="/shop.php">Explorer le shop</a>
                <a class="btn btn-outline-secondary btn-lg" href="/cart.php">Voir le panier</a>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card shadow-sm metric-card">
                <div class="card-body">
                    <h2 class="h5 mb-3">Plateforme prête à déployer</h2>
                    <ul class="mb-0">
                        <li>Authentification admin + dashboard privé</li>
                        <li>JSON data layer avec verrouillage fichier</li>
                        <li>Checkout complet avec livraison</li>
                        <li>UI Bootstrap + thème rouge + dark/light</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<section>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h4 mb-0">Produits récents</h2>
        <a class="btn btn-sm btn-outline-secondary" href="/shop.php">Voir tout</a>
    </div>

    <div class="row g-3">
        <?php foreach ($featured as $product): ?>
            <div class="col-md-6 col-xl-3">
                <article class="card product-card h-100 shadow-sm">
                    <div class="card-body d-flex flex-column">
                        <h3 class="h6"><?= e((string)$product['name']) ?></h3>
                        <p class="text-secondary small flex-grow-1"><?= e((string)($product['description'] ?? 'Produit premium pour votre setup.')) ?></p>
                        <div class="fw-semibold mb-2"><?= e(amount_format((float)$product['price'])) ?></div>
                        <a class="btn btn-brand btn-sm" href="/product.php?id=<?= (int)$product['id'] ?>">Voir détails</a>
                    </div>
                </article>
            </div>
        <?php endforeach; ?>
        <?php if ($featured === []): ?>
            <p class="text-secondary">Aucun produit disponible.</p>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/elements/footer.php'; ?>
