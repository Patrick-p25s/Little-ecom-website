<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/config.php';
require_admin();

$store = new JsonStorage(DATA_DIR . '/products.json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();

    $action = $_POST['action'] ?? '';

    if ($action === 'create' || $action === 'update') {
        $name = trim((string)($_POST['name'] ?? ''));
        $description = trim((string)($_POST['description'] ?? ''));
        $price = (float)($_POST['price'] ?? 0);
        $stock = (int)($_POST['stock'] ?? 0);

        if ($name === '' || $price <= 0 || $stock < 0) {
            set_flash('danger', 'Données produit invalides.');
            redirect('/admin/products.php');
        }

        $payload = [
            'name' => $name,
            'description' => $description,
            'price' => $price,
            'stock' => $stock,
        ];

        if ($action === 'create') {
            $store->create($payload);
            set_flash('success', 'Produit ajouté.');
        } else {
            $id = (int)($_POST['id'] ?? 0);
            $store->update($id, $payload);
            set_flash('success', 'Produit mis à jour.');
        }

        redirect('/admin/products.php');
    }

    if ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            $store->delete($id);
            set_flash('warning', 'Produit supprimé.');
        }
        redirect('/admin/products.php');
    }
}

$products = $store->all();
usort($products, static fn(array $a, array $b): int => (int)$a['id'] <=> (int)$b['id']);
$edit = isset($_GET['edit']) ? $store->find((int)$_GET['edit']) : null;

$title = 'Admin Produits';
require_once dirname(__DIR__) . '/elements/header.php';
?>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card shadow-sm">
            <div class="card-header"><?= $edit ? 'Modifier produit' : 'Ajouter produit' ?></div>
            <div class="card-body">
                <form method="post" class="vstack gap-3">
                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                    <input type="hidden" name="action" value="<?= $edit ? 'update' : 'create' ?>">
                    <?php if ($edit): ?><input type="hidden" name="id" value="<?= (int)$edit['id'] ?>"><?php endif; ?>
                    <div>
                        <label class="form-label">Nom</label>
                        <input class="form-control" name="name" value="<?= e((string)($edit['name'] ?? '')) ?>" required>
                    </div>
                    <div>
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="3"><?= e((string)($edit['description'] ?? '')) ?></textarea>
                    </div>
                    <div>
                        <label class="form-label">Prix</label>
                        <input class="form-control" type="number" min="0.01" step="0.01" name="price" value="<?= e((string)($edit['price'] ?? '')) ?>" required>
                    </div>
                    <div>
                        <label class="form-label">Stock</label>
                        <input class="form-control" type="number" min="0" name="stock" value="<?= e((string)($edit['stock'] ?? 0)) ?>" required>
                    </div>
                    <button class="btn btn-brand" type="submit"><?= $edit ? 'Enregistrer' : 'Ajouter' ?></button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header">Catalogue</div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead><tr><th>#</th><th>Produit</th><th>Prix</th><th>Stock</th><th>Actions</th></tr></thead>
                    <tbody>
                    <?php foreach ($products as $product): ?>
                        <tr>
                            <td><?= (int)$product['id'] ?></td>
                            <td>
                                <div class="fw-semibold"><?= e((string)$product['name']) ?></div>
                                <div class="small text-secondary"><?= e((string)($product['description'] ?? '')) ?></div>
                            </td>
                            <td><?= e(amount_format((float)$product['price'])) ?></td>
                            <td><?= (int)$product['stock'] ?></td>
                            <td class="d-flex gap-2">
                                <a class="btn btn-sm btn-outline-secondary" href="/admin/products.php?edit=<?= (int)$product['id'] ?>">Edit</a>
                                <form method="post" onsubmit="return confirm('Supprimer ce produit ?');">
                                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= (int)$product['id'] ?>">
                                    <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if ($products === []): ?>
                        <tr><td colspan="5" class="text-center text-secondary">Aucun produit.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/elements/footer.php'; ?>
