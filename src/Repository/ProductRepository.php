<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

//    /**
//     * @return Product[] Returns an array of Product objects
//     */
    public function findByCategory($animalType, $categoryName): array
    {
        return $this->createQueryBuilder('p')
            ->join('p.category', 'c')  // Assuming there's a 'category' relation in Product entity
            ->andWhere('p.type = :animalType')
            ->andWhere('c.name = :categoryName')  // Assuming 'name' is the category field in the Category entity
            ->setParameter('animalType', $animalType)
            ->setParameter('categoryName', $categoryName)
            ->getQuery()
            ->getResult();
    }

//    public function findOneBySomeField($value): ?Product
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
