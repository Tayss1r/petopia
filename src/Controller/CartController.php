<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use App\Enum\animalType;
use App\Repository\ProductRepository;
use App\Service\Cart;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

final class CartController extends AbstractController {
    #[Route('/cart', name: 'app_cart', methods: ['GET'])]
    public function index(SessionInterface $session, ManagerRegistry $doctrine, Request $request, PaginatorInterface $paginator, Cart $cart): Response
    {
        $data = $cart->getCart($session);
        //dd($cartWithData);
        $repo = $doctrine->getRepository(Category::class);
        $categories = $repo->findAll();
        $animalType = animalType::cases();

        $itemsCollection = new ArrayCollection($data['cart']);

        $pagination = $paginator->paginate(
            $itemsCollection, // the data to paginate
            $request->query->getInt('page', 1), // current page number, default is 1
            5 // number of items per page
        );

        return $this->render('cart/index.html.twig', [
            'items' => $pagination,
            'total' => $data['total'],
            'categories' => $categories,
            'animalType' => $animalType,
            'pagination' => $pagination
        ]);
    }

    #[Route('/cart/add/{id}', name: 'app_add_cart', methods: ['GET'])]
    public function addToCart(int $id, SessionInterface $session, Request $request): Response
    {
        $cart = $session->get('cart', []);
        if(!empty($cart[$id])) {
            $cart[$id]++;
        } else {
            $cart[$id] = 1;
        }
        // Save the updated cart back to the session
        $session->set('cart', $cart);

        $referer = $request->headers->get('referer');
        if ($referer) {
            return $this->redirect($referer);
        }

        return $this->redirectToRoute('app_all_product');
    }
    #[Route('/cart/delete/{id}', name: 'app_delete_from_cart', methods: ['GET'])]
    public function deleteFromCart(int $id, SessionInterface $session, Request $request): Response
    {
        $cart = $session->get('cart', []);
        if(!empty($cart[$id])) {
            unset($cart[$id]);
        }
        // Save the updated cart back to the session
        $session->set('cart', $cart);

        $referer = $request->headers->get('referer');
        if ($referer) {
            return $this->redirect($referer);
        }

        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/delete', name: 'app_delete_cart', methods: ['GET'])]
    public function deleteCart(SessionInterface $session, Request $request): Response
    {
        $session->set('cart', []);
        $referer = $request->headers->get('referer');
        if ($referer) {
            return $this->redirect($referer);
        }

        return $this->redirectToRoute('app_cart');
    }
}
