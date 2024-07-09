<?php

namespace App\tests\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testTheAutomaticApiTokenSettingWhenAnUserIsCreated(): void
    {
        $user = new User();
        $this->assertNotNull($user->getApiToken()); // On s'assure que la fonction getApiToken ne retourne pas null
    }
    
    public function testThatUserHaveAtLeastOneRoleUser(): void
    {
        $user = new User();
        $this->assertContains('ROLE_USER', $user->getRoles());
    }

    public function testAnException(): void
    {
        $this->expectException(\TypeError::class);

        $user = new User();
        $user->setFirstName([10]);
    }
    
    public function provideFirstName(): \Generator
    {
        yield ['Thomas'];
        yield ['Eric'];
        yield ['Marie'];
    }

    /** @dataProvider provideFirstName */
    public function testFirstNameSetter(string $name): void
    {
        $user = new User();
        $user->setFirstName($name);

        $this->assertSame($name, $user->getFirstName());
    }
}