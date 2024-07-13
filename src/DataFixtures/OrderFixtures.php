<?php

namespace App\DataFixtures;

use App\Entity\{Order, Product, Store, User, OrderStatus};
use App\Service\Utils;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Exception;
use Symfony\Component\Uid\Uuid;

class OrderFixtures extends Fixture implements DependentFixtureInterface
{
    /** @throws Exception */
    public function load(ObjectManager $manager): void
    {
        $products = new ArrayCollection();

        for ($i_order = 1; $i_order <= 5; $i_order++) {

            /** @var User $user */
            $user = $this->getReference('User' . $i_order); // Récupération de l'utilisateur

            $title = "Commande n°$i_order";

            $order = (new Order())
                ->setUuid(Uuid::v4())
                ->setUserId($user) // Utilisation de l'utilisateur récupéré
                ->setProductId($products)
                ->setTotal(mt_rand(100, 500))
                ->setCreatedAt(new DateTimeImmutable());

            $manager->persist($order);
            $this->addReference('Order' . $i_order, $order);
            
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [UserFixtures::class, ProductFixtures::class, CategoryFixtures::class];
    }
}

