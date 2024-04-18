<?php
declare(strict_types=1);
require_once __DIR__ . '/vendor/autoload.php';

$controller = new \App\Controller\UserController();
$controller->viewAllUsers();