<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Basket;

interface BasketRepositoryInterface
{
    public function store(Basket $basket): ?int;

    public function delete(Basket $basket): void;

    public function findAll(): ?array;

    public function findById(int $id): ?Basket;

    public function findByUserId(int $id): ?array;

    public function findByOrderId(int $id): ?Basket;

    public function findByUserAndOrder(int $userId, int $orderId): ?Basket;
}