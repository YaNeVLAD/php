<?php
declare(strict_types=1);
require_once __DIR__ . '/vendor/autoload.php';
if ($_GET === []) {
    die('Не передано никаких данных через GET запрос');
}
$controller = new \App\Controller\UserController();
$controller->UpdateUser($_GET, $_FILES['avatar_path']);