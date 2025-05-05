<?php

namespace App\Service;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Cart extends AbstractController
{
    public function __construct(private readonly ProductRepository $ProductRepository){}
    public function getCart($session): array {
        $cart = $session->get('cart', []);
        $cartWithData = [];
        foreach ($cart as $id => $quantity) {
            $cartWithData [] = [
                'product' => $this->ProductRepository->find($id),
                'quantity' => $quantity
            ];
        }
        $total = array_sum(array_map(function ($item) {
            return $item['product']->getPrice() * $item['quantity'];
        }, $cartWithData));
        return [
            'cart' => $cartWithData,
            'total' => $total
        ];
    }
}