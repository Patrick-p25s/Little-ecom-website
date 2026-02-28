<?php

declare(strict_types=1);

require_once __DIR__ . '/config.php';

$productStore = new JsonStorage(DATA_DIR . '/products.json');
$products = [];
foreach ($productStore->all() as $product) {
    $products[(int)$product['id']] = $product;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();

    $removeId = (int)($_POST['remove_id'] ?? 0);
    if ($removeId > 0) {
        cart_remove($removeId);
        set_flash('warning', 'Produit retiré du panier.');
        redirect('/cart.php');
    }

    $raw = $_POST['qty'] ?? [];
    $quantities = [];

    if (is_array($raw)) {
        foreach ($raw as $id => $qty) {
            $quantities[(int)$id] = (int)$qty;
        }
    }

    cart_update_quantities($quantities);
    set_flash('success', 'Panier mis à jour.');
    redirect('/cart.php');
}

$cart = cart_items();
$total = 0.0;

$title = 'Panier';
require_once __DIR__ . '/elements/header.php';
?>

<h1 class="h3 mb-3">Votre panier</h1>

<?php if ($cart === []): ?>
    <div class="alert alert-info">Votre panier est vide. <a href="/shop.php">Explorer le shop</a>.</div>
<?php else: ?>
    <form method="post">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <div class="card shadow-sm mb-3">
            <div class="table-responsive">
                <table class="table mb-0 align-middle">
                    <thead>
                    <tr>
                        <th>Produit</th>
                        <th>Prix</th>
                        <th width="130">Quantité</th>
                        <th>Total</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($cart as $item): ?>
                        <?php $product = $products[(int)$item['id']] ?? null; ?>
                        <?php if (!$product) { continue; } ?>
                        <?php
                            $quantity = max(1, (int)$item['quantity']);
                            $line = (float)$product['price'] * $quantity;
                            $total += $line;
                        ?>
                        <tr>
                            <td><a href="/product.php?id=<?= (int)$product['id'] ?>"><?= e((string)$product['name']) ?></a></td>
                            <td><?= e(amount_format((float)$product['price'])) ?></td>
                            <td>
                                <input class="form-control" type="number" min="1" max="<?= (int)$product['stock'] ?>" name="qty[<?= (int)$product['id'] ?>]" value="<?= $quantity ?>">
                            </td>
                            <td><?= e(amount_format($line)) ?></td>
                            <td>
                                <button class="btn btn-sm btn-outline-danger" name="remove_id" value="<?= (int)$product['id'] ?>" type="submit">Supprimer</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div class="h5 mb-0">Total: <?= e(amount_format($total)) ?></div>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-secondary" type="submit">Mettre à jour</button>
                <a class="btn btn-brand" href="/checkout.php">Passer la commande</a>
            </div>
        </div>
    </form>
<?php endif; ?>

<?php require_once __DIR__ . '/elements/footer.php'; ?>
