<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use App\Enum\animalType;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProductController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function sidebar(ManagerRegistry $doctrine): Response
    {
        $repo = $doctrine->getRepository(Category::class);
        $categories = $repo->findAll();
        $animalType = animalType::cases();

        return $this->render('home.html.twig', [
            'categories' => $categories,
            'animalType' => $animalType,
        ]);
    }

    #[Route('/{animalType}/{categoryName}', name: 'app_category')]
    public function findProduct(
        string $animalType,
        string $categoryName,
        ProductRepository $productRepository,
        CategoryRepository $categoryRepository
    ): Response {
        $products = $productRepository->findByCategory($animalType, $categoryName);

        $categories = $categoryRepository->findAll();
        $animalType = animalType::cases();
        return $this->render('category/category.html.twig', [
            'products' => $products,
            'categories' => $categories,
            'animalType' => $animalType,
        ]);
    }

}
















