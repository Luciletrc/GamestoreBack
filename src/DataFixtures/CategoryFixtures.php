<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Uid\Uuid;

class CategoryFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        for ($i_category = 1; $i_category <= 5; $i_category++) {

            $title = "Catégorie n°$i_category";

            $category = (new Category())
                ->setUuid(Uuid::v4())
                ->setName($title);

            // Link the category to multiple products
            for ($i_product = 1; $i_product <= 20; $i_product++) {
                if ($i_product % $i_category == 0) { // Just an example condition to link category to product
                    $product = $this->getReference('Jeux' . $i_product);
                    $category->addProduct($product);
                }
            }

            $manager->persist($category);

            $this->addReference('Catégorie' . $i_category, $category);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [ProductFixtures::class];
    }
}
