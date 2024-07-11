<?php

namespace App\DataFixtures;

use App\Entity\{Category, Product};
use App\Service\Utils;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Exception;
use Symfony\Component\Uid\Uuid;

class CategoryFixtures extends Fixture implements DependentFixtureInterface
{
    /** @throws Exception */
    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 5; $i++) {
            /** @var Product $product */
            $product = $this->getReference("Product" . random_int(1, 20));
            $product_id = $this->getReference("Product_id" . random_int(1, 20));
            $title = "Category n°$i";

            $category = (new Category())
                ->setUuid(Uuid::v4())
                ->setName($title)
                ->setProductId($product_id);

            $manager->persist($category);
            $this->addReference("Category" . $i, $category);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [ProductFixtures::class];
    }
}