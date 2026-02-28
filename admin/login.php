<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/config.php';

if (is_admin_logged_in()) {
    redirect('/admin/dashboard.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();

    $email = trim((string)($_POST['email'] ?? ''));
    $password = (string)($_POST['password'] ?? '');

    $admin = authenticate_admin($email, $password);

    if (!$admin) {
        set_flash('danger', 'Identifiants invalides.');
        redirect('/admin/login.php');
    }

    login_admin($admin);
    set_flash('success', 'Connexion réussie.');
    redirect('/admin/dashboard.php');
}

$title = 'Admin Login';
$hideNav = true;
require_once dirname(__DIR__) . '/elements/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-4">
        <div class="card shadow-sm">
            <div class="card-header text-center fw-semibold">Connexion administrateur</div>
            <div class="card-body">
                <form method="post" class="vstack gap-3">
                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                    <div>
                        <label class="form-label">Email</label>
                        <input class="form-control" type="email" name="email" required>
                    </div>
                    <div>
                        <label class="form-label">Mot de passe</label>
                        <input class="form-control" type="password" name="password" required>
                    </div>
                    <button class="btn btn-brand" type="submit">Se connecter</button>
                    <a class="btn btn-outline-secondary" href="/index.php">Retour site</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/elements/footer.php'; ?>
