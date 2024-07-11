<?php

namespace App\DataFixtures;

use App\Entity\Order;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Uid\Uuid;

class OrderFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i_user = 1; $i_user <= 5; $i_user++) {
            $user = $this->getReference("User" . $i_user);

            for ($i_order = 1; $i_order <= 5; $i_order++) {
                $order = (new Order())
                    ->setUuid(Uuid::v4())
                    ->setUserId($user)
                    ->setOrderId($i_order)
                    ->setTotal(mt_rand(100, 500));

                $manager->persist($order);
                $this->addReference("Order" . $i_order, $order);
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [UserFixtures::class];
    }
}
