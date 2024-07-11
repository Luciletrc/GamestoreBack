<?php

namespace App\DataFixtures;

use App\Entity\{Images, Product};
use App\Service\Utils;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Exception;
use Symfony\Component\Uid\Uuid;

class ImagesFixtures extends Fixture implements DependentFixtureInterface
{
    /** @throws Exception */
    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 20; $i++) {
            /** @var Product $product */
            $product = $this->getReference("Product" . $i);
            $product_id = $this->getReference("Product_id" . $i);
            $title = "Image n°$i";

            $images = (new Images())
                ->setUuid(Uuid::v4())
                ->setName($title)
                ->setProductId($product_id);

            $manager->persist($images);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [ProductFixtures::class];
    }
}
