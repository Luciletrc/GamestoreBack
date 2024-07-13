<?php

namespace App\DataFixtures;

use App\Entity\{Product, Store, Images, Category, Order};
use App\Service\Utils;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Exception;
use Symfony\Component\Uid\Uuid;

class ProductFixtures extends Fixture implements DependentFixtureInterface
{
    /** @throws Exception */
    public function load(ObjectManager $manager): void
    {
        // Create a new array to hold the products
        $products = [];

        for ($i_product = 1; $i_product <= 20; $i_product++) {

            $i_store = $i_product % 5 + 1; // Cycle entre les nombre 1 à 5

            /** @var Product $product */
            $title = "Article n°$i_product";

            $product = (new Product())
                ->setUuid(Uuid::v4())
                ->setName($title)
                ->setDescription("Description n°$i_product")
                ->setPegi(random_int(1, 18))
                ->setStock(random_int(1, 5))
                ->setPrice(mt_rand(10, 100))
                ->addStore($this->getReference('Store' . $i_store));

            // Add the product to the array
            $products[] = $product;

            $manager->persist($product);

            $this->setReference($title, $product);
            $this->addReference('Jeux' . $i_product, $product);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [StoreFixtures::class];
    }
}
