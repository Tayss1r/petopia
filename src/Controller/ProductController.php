<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use App\Enum\animalType;
use App\Form\ProductType;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

final class ProductController extends AbstractController
{
    #[Route('/AllProduct', name: 'app_all_product')]
    public function findAll(ManagerRegistry $doctrine, PaginatorInterface $paginator, Request $request): Response
    {
        $repo = $doctrine->getRepository(Category::class);
        $categories = $repo->findAll();
        $animalType = animalType::cases();
        $repository = $doctrine->getRepository(Product::class);

        $query = $repository->createQueryBuilder('p')
            ->orderBy('p.id', 'ASC')
            ->getQuery();

        $products = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            4
        );

        return $this->render('allProduct.html.twig', [
            'categories' => $categories,
            'animalType' => $animalType,
            'products' => $products
        ]);
    }

    #[Route('/details/{id?0}', name: 'app_detail_product')]
    public function details(Product $product = null, ManagerRegistry $doctrine): Response
    {
        $repo = $doctrine->getRepository(Category::class);
        $categories = $repo->findAll();
        $animalType = animalType::cases();
        return $this->render('product/details.html.twig', [
            'product' => $product,
            'categories' => $categories,
            'animalType' => $animalType
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
                    $directory = $this->getParameter('imageDirectory');
                    $photoFile->move($directory, $newFilename);

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
            'product' => $product
        ]);

    }

    #[Route('/delete/{id}', 'app_delete_product'),
        isGranted('ROLE_ADMIN')
    ]
    public function delete(Product $product = null, ManagerRegistry $doctrine): RedirectResponse{
        if($product) {
            $manager = $doctrine->getManager();
            $manager->remove($product);
            $manager->flush();
        }
        return $this->redirectToRoute('app_home');
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

    #[Route('/search', name: 'app_search_product', methods: ['GET'])]
    public function SearchProduct(ManagerRegistry $doctrine, Request $request, ProductRepository $productRepository): Response
    {
        $query = $request->query->get('q', '');
        $products = [];

        if (!empty($query)) {
            $products = $productRepository->search($query, $query);
        }
        $repo = $doctrine->getRepository(Category::class);
        $categories = $repo->findAll();
        $animalType = animalType::cases();

        return $this->render('product/searchProduct.html.twig', [
            'products' => $products,
            'query' => $query,
            'categories' => $categories,
            'animalType' => $animalType,
        ]);
    }

    #[Route('/home', name: 'app_home')]
    public function home(ManagerRegistry $doctrine): Response
    {
        $repo = $doctrine->getRepository(Category::class);
        $categories = $repo->findAll();
        $animalType = animalType::cases();

        return $this->render('home.html.twig', [
            'categories' => $categories,
            'animalType' => $animalType,
        ]);
    }

}
















