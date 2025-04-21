<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use App\Enum\animalType;
use App\Form\ProductType;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

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

    #[Route('/edit{id?0}', name: 'app_edit_product')]
    public function editProduct(
        Product $product = null,
        ManagerRegistry $doctrine,
        Request $request,
        SluggerInterface $slugger
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $new = false;

        if (!$product) {
            $product = new Product();
            $new = true;
        }
        $repo = $doctrine->getRepository(Category::class);
        $categories = $repo->findAll();
        $animalType = animalType::cases();
        $form = $this->createForm(ProductType::class, $product);
        $form->remove('createdAt');
        $form->remove('updatedAt');

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $photoFile = $form->get('photo')->getData();

            if ($photoFile) {
                $originalFilename = pathinfo($photoFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$photoFile->guessExtension();

                try {
                    // Déplace le fichier vers le répertoire configuré
                    $directory = $this->getParameter('imageDirectory');
                    $photoFile->move($directory, $newFilename);

                    // Enregistre le nom de fichier dans l'entité
                    $product->setImage($newFilename);
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Error while uploading : ' . $e->getMessage());
                    return $this->redirectToRoute('app_edit_product', ['id' => $product->getId()]);
                }
            }

            if ($new) {
                $message = " has been added successfully";
                $product->setCreatedAt(new \DateTimeImmutable());
                $product->setUpdatedAt(new \DateTimeImmutable());
            } else {
                $message = " has been edited successfully";
            }

            $manager = $doctrine->getManager();
            $manager->persist($product);
            $manager->flush();

            $this->addFlash('success', $product->getName() . $message);
            return $this->redirectToRoute('app_home');
        }

        return $this->render('product/addProduct.html.twig', [
            'form' => $form->createView(),
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
















