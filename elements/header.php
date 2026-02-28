<?php
$flash = get_flash();
$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: '';
$admin = current_admin();
?>
<!doctype html>
<html lang="fr" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title ?? APP_NAME) ?></title>
    <link rel="stylesheet" href="/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<?php if (!($hideNav ?? false)): ?>
<nav class="navbar navbar-expand-lg sticky-top custom-nav py-2">
    <div class="container">
        <a class="navbar-brand fw-bold" href="/index.php">Ecom Patrick</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="menu">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link <?= $currentPath === '/index.php' ? 'active' : '' ?>" href="/index.php">Accueil</a></li>
                <li class="nav-item"><a class="nav-link <?= $currentPath === '/shop.php' ? 'active' : '' ?>" href="/shop.php">Shop</a></li>
                <li class="nav-item"><a class="nav-link <?= $currentPath === '/cart.php' ? 'active' : '' ?>" href="/cart.php">Panier <span class="badge rounded-pill text-bg-light"><?= cart_count() ?></span></a></li>
                <?php if ($admin): ?>
                    <li class="nav-item"><a class="nav-link <?= $currentPath === '/admin/dashboard.php' ? 'active' : '' ?>" href="/admin/dashboard.php">Admin</a></li>
                <?php endif; ?>
            </ul>
            <div class="d-flex align-items-center gap-2">
                <?php if ($admin): ?>
                    <span class="text-white small d-none d-md-inline">Admin: <?= e($admin['name']) ?></span>
                    <a class="btn btn-sm btn-light" href="/admin/logout.php">Logout</a>
                <?php else: ?>
                    <a class="btn btn-sm btn-light" href="/admin/login.php">Login Admin</a>
                <?php endif; ?>
                <button class="btn btn-sm btn-outline-light" id="themeToggle" type="button">Mode sombre</button>
            </div>
        </div>
    </div>
</nav>
<?php endif; ?>
<main class="container py-4">
    <?php if ($flash): ?>
        <div class="alert alert-<?= e($flash['type']) ?> alert-dismissible fade show" role="alert">
            <?= e($flash['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
