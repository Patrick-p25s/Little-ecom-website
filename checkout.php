<?php

declare(strict_types=1);

require_once __DIR__ . '/config.php';

$productStore = new JsonStorage(DATA_DIR . '/products.json');
$orderStore = new JsonStorage(DATA_DIR . '/orders.json');
$userStore = new JsonStorage(DATA_DIR . '/users.json');

$allProducts = [];
foreach ($productStore->all() as $product) {
    $allProducts[(int)$product['id']] = $product;
}

$cart = cart_items();
$lineItems = [];
$total = 0.0;

foreach ($cart as $item) {
    $product = $allProducts[(int)$item['id']] ?? null;
    if (!$product) {
        continue;
    }

    $quantity = max(1, (int)$item['quantity']);
    $price = (float)$product['price'];
    $lineTotal = $price * $quantity;
    $total += $lineTotal;

    $lineItems[] = [
        'id' => (int)$product['id'],
        'name' => (string)$product['name'],
        'unit_price' => $price,
        'quantity' => $quantity,
        'line_total' => $lineTotal,
    ];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();

    if ($lineItems === []) {
        set_flash('danger', 'Panier vide.');
        redirect('/shop.php');
    }

    $fullName = trim((string)($_POST['full_name'] ?? ''));
    $email = trim((string)($_POST['email'] ?? ''));
    $phone = trim((string)($_POST['phone'] ?? ''));
    $addressLine = trim((string)($_POST['address_line'] ?? ''));
    $city = trim((string)($_POST['city'] ?? ''));
    $postalCode = trim((string)($_POST['postal_code'] ?? ''));
    $country = trim((string)($_POST['country'] ?? 'France'));

    if ($fullName === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $phone === '' || $addressLine === '' || $city === '' || $postalCode === '' || $country === '') {
        set_flash('danger', 'Veuillez remplir toutes les informations de livraison.');
        redirect('/checkout.php');
    }

    foreach ($lineItems as $item) {
        $product = $productStore->find((int)$item['id']);
        if (!$product || (int)$product['stock'] < (int)$item['quantity']) {
            set_flash('danger', 'Stock insuffisant pour ' . ($item['name'] ?? 'un produit') . '.');
            redirect('/cart.php');
        }
    }

    $user = null;
    foreach ($userStore->all() as $existingUser) {
        if (lower((string)$existingUser['email']) === lower($email)) {
            $user = $existingUser;
            break;
        }
    }

    if (!$user) {
        $user = $userStore->create([
            'name' => $fullName,
            'email' => $email,
            'phone' => $phone,
        ]);
    }

    foreach ($lineItems as $item) {
        $product = $productStore->find((int)$item['id']);
        if (!$product) {
            continue;
        }

        $productStore->update((int)$item['id'], [
            'name' => $product['name'],
            'description' => $product['description'] ?? '',
            'price' => (float)$product['price'],
            'stock' => max(0, (int)$product['stock'] - (int)$item['quantity']),
        ]);
    }

    $order = $orderStore->create([
        'user_id' => (int)$user['id'],
        'customer_name' => $fullName,
        'customer_email' => $email,
        'customer_phone' => $phone,
        'items' => $lineItems,
        'address' => [
            'address_line' => $addressLine,
            'city' => $city,
            'postal_code' => $postalCode,
            'country' => $country,
        ],
        'status' => 'paid',
        'total_amount' => $total,
    ]);

    cart_clear();
    set_flash('success', 'Commande validée. Référence #' . (int)$order['id']);
    redirect('/order-success.php?id=' . (int)$order['id']);
}

if ($lineItems === []) {
    set_flash('warning', 'Votre panier est vide.');
    redirect('/shop.php');
}

$title = 'Checkout';
require_once __DIR__ . '/elements/header.php';
?>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card shadow-sm">
            <div class="card-header">Adresse de livraison</div>
            <div class="card-body">
                <form method="post" class="row g-3">
                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                    <div class="col-md-6">
                        <label class="form-label">Nom complet</label>
                        <input class="form-control" name="full_name" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input class="form-control" type="email" name="email" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Téléphone</label>
                        <input class="form-control" name="phone" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Pays</label>
                        <input class="form-control" name="country" value="France" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Adresse</label>
                        <input class="form-control" name="address_line" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Ville</label>
                        <input class="form-control" name="city" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Code postal</label>
                        <input class="form-control" name="postal_code" required>
                    </div>
                    <div class="col-12 d-grid">
                        <button class="btn btn-brand btn-lg" type="submit">Confirmer la commande</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card shadow-sm">
            <div class="card-header">Récapitulatif</div>
            <ul class="list-group list-group-flush">
                <?php foreach ($lineItems as $item): ?>
                    <li class="list-group-item d-flex justify-content-between">
                        <span><?= e((string)$item['name']) ?> x<?= (int)$item['quantity'] ?></span>
                        <span><?= e(amount_format((float)$item['line_total'])) ?></span>
                    </li>
                <?php endforeach; ?>
                <li class="list-group-item d-flex justify-content-between fw-bold">
                    <span>Total</span>
                    <span><?= e(amount_format($total)) ?></span>
                </li>
            </ul>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/elements/footer.php'; ?>
