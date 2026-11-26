<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $categories = [];

        foreach (['Accessoires', 'Mode', 'Audio', 'Téléphonie'] as $name) {
            $category = new Category();
            $category->setName($name);
            $category->setDescription('Découvrez notre sélection de ' . $name);

            $manager->persist($category);
            $categories[$name] = $category;
        }

        $products = [
            ['Montre élégante', 'Une montre moderne et élégante.', '49.90', 15, 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=600', 'Accessoires'],
            ['Chaussures sport', 'Des chaussures confortables pour le sport.', '79.90', 20, 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=600', 'Mode'],
            ['Casque audio', 'Profitez d’un son de qualité.', '59.90', 12, 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=600', 'Audio'],
            ['Sac moderne', 'Un sac pratique pour tous les jours.', '39.90', 10, 'https://images.unsplash.com/photo-1548036328-c9fa89d128fa?w=600', 'Mode'],
            ['Smartphone', 'Un smartphone moderne et performant.', '299.90', 8, 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=600', 'Téléphonie'],
        ];

        foreach ($products as [$name, $description, $price, $stock, $image, $categoryName]) {
            $product = new Product();
            $product->setName($name);
            $product->setDescription($description);
            $product->setPrice($price);
            $product->setStock($stock);
            $product->setImage($image);
            $product->setCategory($categories[$categoryName]);

            $manager->persist($product);
        }

        $manager->flush();
    }
}
