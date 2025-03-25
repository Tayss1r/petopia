<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[
    Route('/product'),
]
final class ProductController extends AbstractController
{
    #[Route('/all', name: 'app_findAll_products')]
    public function index(): Response
    {

        return $this->render('product/index.html.twig');
    }
}
