<?php

namespace App\DataFixtures;

use App\Entity\User;
use DateTimeImmutable;
use Exception;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Uid\Uuid;

class UserFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher)
    {
    }

    /** @throws Exception */
    public function load(ObjectManager $manager): void
    {
        for ($i_user = 1; $i_user <= 20; $i_user++) {
            $user = (new User())
                ->setUuid(Uuid::v4())
                ->setFirstName("FirstName $i_user")
                ->setLastName("LastName $i_user")
                ->setEmail("email.$i_user@studi.fr")
                ->setUsername("email.$i_user@studi.fr")
                ->setCreatedAt(new DateTimeImmutable());
        
            $user->setPassword($this->passwordHasher->hashPassword($user, "password$i_user"));
        
            $manager->persist($user);
            
            $this->addReference('User' . $i_user, $user);
        }
        
        $manager->flush();
    }
}
