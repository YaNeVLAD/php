<?php
declare(strict_types=1);
require_once __DIR__ . '/vendor/autoload.php';
if ($_POST === []) {
    die('Не передано никаких данных через POST запрос');
}

$controller = new \App\Controller\UserController();
$controller->createUser($_POST, $_FILES['avatar_path']);