<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class Produit extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker =\Faker\Factory::create('fr_FR');

        for($i =0; $i < mt_rand(8, 10); $i++){
            $produit =new Produit;

            // $produit->setTitre($faker->sentence(3))
            //         ->setPhoto($faker->imageUrl)
            //         ->setPrix($faker->randomFloat(2, 10, 100));

             $manager->persist($produit);       
        }


        $manager->flush();
    }
}
