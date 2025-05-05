<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\City;
use App\Entity\Order;
use App\Enum\animalType;
use App\Form\OrderType;
use App\Repository\ProductRepository;
use App\Service\Cart;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

final class OrderController extends AbstractController
{
    #[Route('/order', name: 'app_order')]
    public function index(Request $request, ManagerRegistry $doctrine,
        SessionInterface $session,
        ProductRepository $productRepository,
        EntityManagerInterface $entityManager,
        Cart $cart
    ): Response
    {
        $cart = $session->get('cart', []);
        $cartWithData = [];
        foreach ($cart as $id => $quantity) {
            $cartWithData [] = [
                'product' => $productRepository->find($id),
                'quantity' => $quantity
            ];
        }
        $total = array_sum(array_map(function ($item) {
            return $item['product']->getPrice() * $item['quantity'];
        }, $cartWithData));
        $order = new Order();
        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);
        $form->remove('createdAt');
        if($form->isSubmitted() && $form->isValid()) {
            if($order->isPayOnDelivery()) {

            }
            $order->setCreatedAt(new \DateTimeImmutable());
        }
        $repo = $doctrine->getRepository(Category::class);
        $categories = $repo->findAll();
        $animalType = animalType::cases();
        return $this->render('order/index.html.twig', [
            'form' => $form->createView(),
            'categories' => $categories,
            'animalType' => $animalType,
            'total' => $total,
        ]);
    }

    #[Route('/city/{id}/shippingCost', name: 'app_shipping_cost')]
    public function cityShippingCost(City $city): Response {
        $cityShippingPrice = $city->getShippingCost();
        return new Response(json_encode(['status' => 200, 'message' => 'on', 'content'=>$cityShippingPrice]));
    }
}
