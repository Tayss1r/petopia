<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\City;
use App\Entity\Order;
use App\Entity\OrderProducts;
use App\Enum\animalType;
use App\Form\OrderType;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Service\Cart;
use App\Service\stripePayment;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Stripe\Exception\ApiErrorException;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;

final class OrderController extends AbstractController
{
    public function __construct(private readonly MailerInterface $mailer)
    {
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ApiErrorException
     */
    #[Route('/order', name: 'app_order')]
    public function index(Request $request, ManagerRegistry $doctrine,
        SessionInterface $session,
        ProductRepository $productRepository,
        EntityManagerInterface $entityManager,
        Cart $cart
    ): Response
    {
        $data = $cart->getCart($session);
        $order = new Order();
        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            if($order->isPayOnDelivery()) {
                //dd($order);
                if(!empty($data['total'])) {
                    $order->setTotalPrice($data['total']);
                    $order->setCreatedAt(new \DateTimeImmutable());
                    $entityManager->persist($order);
                    $entityManager->flush();
                    foreach ($data['cart'] as $value) {
                        $orderProduct = new OrderProducts();
                        $orderProduct->setOrder($order);
                        $orderProduct->setProduct($value['product']);
                        $orderProduct->setQuantity($value['quantity']);
                        $order->addOrderProduct($orderProduct);
                        $entityManager->persist($orderProduct);
                        $entityManager->flush();
                    }
                }
                $session->set('cart', []);

                $html = $this->renderView('mail/orderConfirm.html.twig', [
                    'order' => $order,
                ]);

                $email = (new TemplatedEmail())
                    ->from('no-reply@petopia.com')
                    ->to($order->getEmail())
                    ->subject('Order Confirmation - Petopia')
                    ->html($html)
                ;

                $this->mailer->send($email);
            }
            $payment = new stripePayment();
            $shippingCost = $order->getCity()->getShippingCost();
            $payment->startPayment($data, $shippingCost);
            $stripeRedirectUrl = $payment->getStripeRedirectUrl();

            return $this->redirect($stripeRedirectUrl);
        }
        $repo = $doctrine->getRepository(Category::class);
        $categories = $repo->findAll();
        $animalType = animalType::cases();
        return $this->render('order/index.html.twig', [
            'form' => $form->createView(),
            'categories' => $categories,
            'animalType' => $animalType,
            'total' => $data['total'],
        ]);
    }

    #[Route('/order/all', name: 'app_order_show')]
    public function show(OrderRepository $orderRepository, ManagerRegistry $doctrine): Response
    {
        $order = $orderRepository->findAll();
        $repo = $doctrine->getRepository(Category::class);
        $categories = $repo->findAll();
        $animalType = animalType::cases();
        return $this->render('order/show.html.twig', [
            'orders' => $order,
            'categories' => $categories,
            'animalType' => $animalType
        ]);
    }

    #[Route('/order/{id}/is-completed/update', name: 'app_delete_is_completed')]
    public function isCompletedUpdate($id, OrderRepository $orderRepository, EntityManagerInterface $entityManager): Response
    {
        $order = $orderRepository->find($id);
        $order->setIsCompleted(true);
        $entityManager->flush();
        return $this->redirectToRoute('app_order_show');
    }

    #[Route('/order/{id}/delete', name: 'app_delete_order', methods: ['GET'])]
    public function deleteOrder(Order $order, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($order);
        $entityManager->flush();
        return $this->redirectToRoute('app_order_show');
    }

    #[Route('/city/{id}/shippingCost', name: 'app_shipping_cost')]
    public function cityShippingCost(City $city): Response {
        $cityShippingPrice = $city->getShippingCost();
        return new Response(json_encode(['status' => 200, 'message' => 'on', 'content'=>$cityShippingPrice]));
    }
}
