<?php

namespace App\Controller;

use App\Enum\animalType;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[
    Route('/product'),
]
final class ProductController extends AbstractController
{
    #[Route('/all', name: 'app_findAll_products')]
    public function menu(CategoryRepository $categoryRepository): Response
    {
        // Fetch all categories from the database
        $categories = $categoryRepository->findAll();
        $animalType = animalType::cases();

        return $this->render('/product/findAll.html.twig', [
            'categories' => $categories,
            'animalType' => $animalType,
        ]);
    }
}
