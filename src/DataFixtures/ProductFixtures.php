<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Product;
use App\Enum\animalType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProductFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $categories = ['Food', 'Treats', 'Health & Wellness', 'Toys'];

        foreach ($categories as $categoryName) {
            $category = new Category();
            $category->setName($categoryName);
            $manager->persist($category);
        }
        $manager->flush();


        $products = [
            // Dog Food
            ['name' => 'Purina Pro Plan', 'price' => 50, 'description' => 'Purina Pro Plan Savor Adult Shredded Blend Chicken & Rice Formula Dry Dog Food, 6-lb bag', 'stock' => 10, 'category' => $categories[0], 'image' => 'images/dry_dog_food.jpg', 'type' => AnimalType::Dog],
            ['name' => 'Hill\'s Science Diet', 'price' => 55, 'description' => 'Hill\'s Science Diet Adult Sensitive Stomach & Skin Chicken Recipe Dry Dog Food, 4-lb bag', 'stock' => 8, 'category' => $categories[0], 'image' => 'images/dry_dog_food.jpg', 'type' => AnimalType::Dog],
            ['name' => 'Pedigree', 'price' => 12, 'description' => 'Pedigree Chopped Ground Dinner with Beef, Bacon & Cheese Canned Dog Food, 13.2-oz can, case of 12', 'stock' => 20, 'category' => $categories[0], 'image' => 'images/canned_dog_food.jpg', 'type' => AnimalType::Dog],

            // Dog Treats
            ['name' => 'Greenies', 'price' => 9, 'description' => 'Greenies Original Teenie Dental Dog Treats, 20 count', 'stock' => 25, 'category' => $categories[1], 'image' => 'images/dog_treats.jpg', 'type' => AnimalType::Dog],
            ['name' => 'Milk-Bone', 'price' => 5, 'description' => 'Milk-Bone Original Dog Biscuits, 24-oz box', 'stock' => 30, 'category' => $categories[1], 'image' => 'images/dog_biscuits.jpg', 'type' => AnimalType::Dog],

            // Dog Health & Wellness
            ['name' => 'Nutramax Cosequin', 'price' => 30, 'description' => 'Nutramax Cosequin Hip & Joint with Glucosamine & Chondroitin Joint Supplement for Dogs, 80 count', 'stock' => 15, 'category' => $categories[2], 'image' => 'images/joint_supplement.jpg', 'type' => AnimalType::Dog],
            ['name' => 'Pet Wellbeing', 'price' => 45, 'description' => 'Pet Wellbeing Sneeze Ease Bacon Flavored Liquid Allergy Supplement for Dogs, 2-oz bottle', 'stock' => 12, 'category' => $categories[2], 'image' => 'images/allergy_supplement.jpg', 'type' => AnimalType::Dog],

            // Dog Toys
            ['name' => 'KONG Classic', 'price' => 12, 'description' => 'KONG Classic Dog Toy, Medium', 'stock' => 50, 'category' => $categories[3], 'image' => 'images/dog_toy.jpg', 'type' => AnimalType::Dog],

            // Cat Food
            ['name' => 'Purina Pro Plan', 'price' => 50, 'description' => 'Purina Pro Plan Urinary Tract Health Adult Chicken Entree in Gravy Canned Cat Food, 5.5-oz can, case of 24', 'stock' => 10, 'category' => $categories[0], 'image' => 'images/cat_food.jpg', 'type' => AnimalType::Cat],
            ['name' => 'Applaws', 'price' => 7, 'description' => 'Applaws Fish Variety Pack in Broth Limited Ingredient Canned Wet Cat Food, 2.47-oz can, case of 12', 'stock' => 5, 'category' => $categories[0], 'image' => 'images/cat_litter.jpg', 'type' => AnimalType::Cat],
            ['name' => 'Friskies', 'price' => 5, 'description' => 'Friskies Poultry Adult Chicken & Turkey in Gravy Variety Pack Canned Cat Food, 5.5-oz, case of 32', 'stock' => 2, 'category' => $categories[0], 'image' => 'images/dog_treats.jpg', 'type' => AnimalType::Cat],

            // Cat Treats
            ['name' => 'Hartz', 'price' => 8, 'description' => 'Hartz Delectables Squeeze Up Tuna, Chicken, & Salmon Flavored Variety Pack Lickable Cat Treats, 0.5-oz tube, 54 count', 'stock' => 3, 'category' => $categories[1], 'image' => 'images/pet_shampoo.jpg', 'type' => AnimalType::Cat],
            ['name' => 'SmartyKat', 'price' => 8, 'description' => 'SmartyKat Silvervine Cat Attractant Catnip, 0.75-oz pouch', 'stock' => 3, 'category' => $categories[1], 'image' => 'images/pet_shampoo.jpg', 'type' => AnimalType::Cat],
            ['name' => 'Groovies', 'price' => 8, 'description' => 'Groovies Salmon Flavor Dental Cat Chews, 1.76-oz bag', 'stock' => 5, 'category' => $categories[1], 'image' => 'images/pet_shampoo.jpg', 'type' => AnimalType::Cat],

            // Cat Health & Wellness
            ['name' => 'Nutramax Cosequin', 'price' => 30, 'description' => 'Nutramax Cosequin Hip & Joint with Glucosamine & Chondroitin Capsules Joint Supplement for Cats, 80 count', 'stock' => 30, 'category' => $categories[2], 'image' => 'images/joint_supplement.jpg', 'type' => AnimalType::Cat],
            ['name' => 'Pet Wellbeing', 'price' => 45, 'description' => 'Pet Wellbeing Sneeze Ease Bacon Flavored Liquid Allergy Supplement for Dogs & Cats, 2-oz bottle', 'stock' => 10, 'category' => $categories[2], 'image' => 'images/joint_supplement.jpg', 'type' => AnimalType::Cat],
            ['name' => 'Vetnique Labs', 'price' => 25, 'description' => 'Vetnique Labs Dermabliss Medicated Anti-Bacterial & Anti-Fungal Chlorhexidine Skin Infection Cat & Dog Wipes, 50 count', 'stock' => 1, 'category' => $categories[2], 'image' => 'images/joint_supplement.jpg', 'type' => AnimalType::Cat],

            // Cat Toys
            ['name' => 'Petstages', 'price' => 5, 'description' => 'Petstages Crazy Bouncer Cat Toy, Assorted Colors', 'stock' => 20, 'category' => $categories[3], 'image' => 'images/cat_toy.jpg', 'type' => AnimalType::Cat],
            // Bird Food
            ['name' => 'Kaytee', 'price' => 10, 'description' => 'Kaytee Forti-Diet Pro Health Canary & Finch Food, 2-lb bag', 'stock' => 15, 'category' => $categories[0], 'image' => 'images/bird_food.jpg', 'type' => animalType::Bird],
            ['name' => 'Lafeber', 'price' => 12, 'description' => 'Lafeber Premium Daily Diet for Parrots, 10-oz bag', 'stock' => 20, 'category' => $categories[0], 'image' => 'images/bird_food_2.jpg', 'type' => animalType::Bird],
            ['name' => 'Higgins', 'price' => 9, 'description' => 'Higgins Vita Seed Gourmet Canary Food, 3-lb bag', 'stock' => 8, 'category' => $categories[0], 'image' => 'images/bird_food_3.jpg', 'type' => animalType::Bird],
            // Bird Treats
            ['name' => 'Vitakraft', 'price' => 5, 'description' => 'Vitakraft Parrot Treats, 4-oz pack', 'stock' => 10, 'category' => $categories[1], 'image' => 'images/bird_treats.jpg', 'type' => animalType::Bird],
            ['name' => 'Wild Harvest', 'price' => 7, 'description' => 'Wild Harvest Parrot and Bird Nutri-Berries, 12-oz pack', 'stock' => 15, 'category' => $categories[1], 'image' => 'images/bird_treats_2.jpg', 'type' => animalType::Bird],

            // Fish Food
            ['name' => 'Tetra', 'price' => 6, 'description' => 'TetraMin Tropical Flakes Fish Food, 3.53-oz', 'stock' => 30, 'category' => $categories[0], 'image' => 'images/fish_food.jpg', 'type' => animalType::Fish],
            ['name' => 'Hikari', 'price' => 8, 'description' => 'Hikari Bio-Pure Freeze Dried Bloodworms, 0.28-oz pack', 'stock' => 25, 'category' => $categories[0], 'image' => 'images/fish_food_2.jpg', 'type' => animalType::Fish],
            ['name' => 'Omega One', 'price' => 12, 'description' => 'Omega One Super Color Flakes Fish Food, 1.75-oz', 'stock' => 10, 'category' => $categories[0], 'image' => 'images/fish_food_3.jpg', 'type' => animalType::Fish],

            // Fish Treats
            ['name' => 'Zoo Med', 'price' => 5, 'description' => 'Zoo Med Shrimp and Crab Treats for Fish, 2-oz pack', 'stock' => 20, 'category' => $categories[1], 'image' => 'images/fish_treats.jpg', 'type' => animalType::Fish],
            ['name' => 'Repashy', 'price' => 9, 'description' => 'Repashy Gel Food for Fish, 1-oz pack', 'stock' => 18, 'category' => $categories[1], 'image' => 'images/fish_treats_2.jpg', 'type' => animalType::Fish]
        ];
    }
}
