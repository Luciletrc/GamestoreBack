<?php

namespace App\DataFixtures;

use App\Entity\Store;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Exception;
use Symfony\Component\Uid\Uuid;

class StoreFixtures extends Fixture
{
    /** @throws Exception */
    public function load(ObjectManager $manager): void
    {
        for ($i_store = 1; $i_store <= 5; $i_store++) {
            $store = (new Store())
                ->setUuid(Uuid::v4())
                ->setName("Store n°$i_store")
                ->setDescription("Description n°$i_store")
                ->setOpeningTime(new DateTimeImmutable())
                ->setClosingTime(new DateTimeImmutable())
                ->setCreatedAt(new DateTimeImmutable());

            $manager->persist($store);
            $this->addReference("Store" . $i_store, $store);
        }

        $manager->flush();
    }
}