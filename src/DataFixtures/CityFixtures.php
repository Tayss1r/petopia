<?php

namespace App\DataFixtures;

use App\Entity\City;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class CityFixtures extends Fixture implements FixtureGroupInterface
{
    public function load(ObjectManager $manager): void
    {
        $cities = [
            ['name' => 'Tunis', 'shipping_cost' => 5.00],
            ['name' => 'Sfax', 'shipping_cost' => 4.00],
            ['name' => 'Sousse', 'shipping_cost' => 4.50],
            ['name' => 'Kasserine', 'shipping_cost' => 6.00],
            ['name' => 'Bizerte', 'shipping_cost' => 5.50],
            ['name' => 'Kairouan', 'shipping_cost' => 6.00],
            ['name' => 'Gafsa', 'shipping_cost' => 7.00],
            ['name' => 'Nabeul', 'shipping_cost' => 4.50],
            ['name' => 'Monastir', 'shipping_cost' => 5.00],
            ['name' => 'Medenine', 'shipping_cost' => 7.50],
        ];

        foreach ($cities as $data) {
            $city = new City();
            $city->setName($data['name']);
            $city->setShippingCost($data['shipping_cost']);
            $manager->persist($city);
        }

        $manager->flush();
    }
    public static function getGroups(): array
    {
        return ['gcity'];
    }
}
