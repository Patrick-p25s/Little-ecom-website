<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/config.php';

logout_admin();
set_flash('success', 'Vous êtes déconnecté.');
redirect('/index.php');
