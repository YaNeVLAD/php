<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\Basket;
use App\Service\Data\UserData;
use App\Service\Data\OrderData;
use App\Service\Data\BasketData;
use App\Repository\UserRepositoryInterface;
use App\Repository\OrderRepositoryInterface;
use App\Repository\BasketRepositoryInterface;

class BasketService {

    private $userRepository;

    private $orderRepository;

    private $basketRepository;

    public function __construct(
    UserRepositoryInterface $userRepository, 
    OrderRepositoryInterface $orderRepository, 
    BasketRepositoryInterface $basketRepository) 
    {
        $this->userRepository = $userRepository;
        $this->orderRepository = $orderRepository;
        $this->basketRepository = $basketRepository;
    }

    public function add(UserData $userData, OrderData $orderData): void
    {
        if (!$userData || !$orderData) {
            throw new \Exception('Failed to find user or order with this ID\'s');
        }

        $userEmail = $userData->getEmail();
        $orderName = $orderData->getName();

        $user = $this->userRepository->findByEmail($userEmail);
        $order = $this->orderRepository->findByName($orderName);

        if (!$user || !$order) {
            throw new \Exception('Failed to find order or user with received data');
        }
        
        $basket = new Basket(
            null,
            $user,
            $order,        
        );

        $this->basketRepository->store($basket);
    }

    public function show(int $userId): ?array
    {
        $user = $this->userRepository->findById($userId);

        if (!$user) {
           throw new \Exception('Failed to find user with this ID');
        }

        $basketItems = $this->basketRepository->findByUserId($userId);

        $orders = [];
        foreach ($basketItems as $basketItem) {
            $orders[] = $basketItem->getOrder();
        }

        return $orders;
    }

    public function remove(UserData $userData, OrderData $orderData): void
    {
        if (!$userData || !$orderData) {
            throw new \Exception('Order data or User data is missing');
        }

        $userEmail = $userData->getEmail();
        $orderName = $orderData->getName();

        $user = $this->userRepository->findByEmail($userEmail);
        $order = $this->orderRepository->findByName($orderName);

        if (!$user || !$order) {
            throw new \Exception('Failed to find order or user with received data');
        }
        
        $basket = $this->basketRepository->findByUserAndOrder($user->getId(), $order->getId());

        $this->basketRepository->delete($basket);
    }
}