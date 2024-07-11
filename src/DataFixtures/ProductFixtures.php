<?php

namespace App\DataFixtures;

use App\Entity\{Product, Store, Images, Category, Order};
use App\Service\Utils;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Exception;
use Symfony\Component\Uid\Uuid;

class ProductFixtures extends Fixture implements DependentFixtureInterface
{
    /** @throws Exception */
    public function load(ObjectManager $manager): void
    {
        // Create a new ArrayCollection to hold the products
        $products = new ArrayCollection();

        for ($i_product = 1; $i_product <= 20; $i_product++) {
            
            $i_store = $i_product % 5 + 1; // Cycle entre les nombre 1 à 5

            /** @var Store $store */
            $store = $this->getReference("Store" . $i_store);
            $title = "Article n°$i_product";

            $product = (new Product())
                ->setUuid(Uuid::v4())
                ->setName($title)
                ->setDescription("Description n°$i_product")
                ->setStore($store)
                ->setPegi(random_int(1, 18))
                ->setStockId(random_int(1, 5))
                ->setPrice(mt_rand(10, 100))
                ->setProductsId($i_product);

            // Add the product to the ArrayCollection
            $products->add($product);

            $manager->persist($product);
            $this->addReference("Product" . $i_product, $product);
        }

        // Get a reference to an Order entity
        /** @var Order $order */
        $order = $this->getReference("Order1");

        // Set the product_id collection in the Order entity
        $order->setProductId($products);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [StoreFixtures::class, OrderFixtures::class];
    }
}

