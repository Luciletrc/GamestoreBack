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
        for ($i_images = 1; $i_images <= 20; $i_images++) {
            $title = "Image n°$i_images";

            /** @var Product $product */
            $product = $this->getReference('Jeux' . $i_images);

            $images = (new Images())
                ->setUuid(Uuid::v4())
                ->setName($title)
                ->setProductId($product); // Ici, on associe l'image au produit

            $manager->persist($images);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [ProductFixtures::class, StoreFixtures::class];
    }
}

