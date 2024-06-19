<?php
namespace App\Entity;

class Basket
{
    public function __construct(
        private ?int $basket_id,
        private User $user,
        private Order $order,
    ) {

    }

    //GET методы
    public function getId(): int
    {
        return $this->basket_id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getOrder(): Order
    {
        return $this->order;
    }

    //SET методы
    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function setOrder(Order $order): self
    {
        $this->order = $order;

        return $this;
    }
}