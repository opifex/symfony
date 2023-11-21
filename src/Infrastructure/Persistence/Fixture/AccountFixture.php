<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Fixture;

use App\Domain\Entity\Account;
use App\Domain\Entity\AccountRole;
use App\Domain\Entity\AccountStatus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory as Faker;
use Symfony\Component\PasswordHasher\Hasher\NativePasswordHasher;

final class AccountFixture extends Fixture implements FixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Faker::create();
        $passwordHasher = new NativePasswordHasher();
        $password = $passwordHasher->hash(plainPassword: 'password4#account');

        $adminAccount = new Account(
            uuid: $faker->unique()->uuid(),
            email: $faker->unique()->bothify(string: 'admin@example.com'),
            password: $password,
            status: AccountStatus::VERIFIED,
            roles: [AccountRole::ROLE_ADMIN],
        );
        $manager->persist($adminAccount);

        $userAccount = new Account(
            uuid: $faker->unique()->uuid(),
            email: $faker->unique()->bothify(string: 'user@example.com'),
            password: $password,
            status: AccountStatus::VERIFIED,
        );
        $manager->persist($userAccount);

        for ($index = 1; $index <= 10; $index++) {
            $account = new Account(
                uuid: $faker->unique()->uuid(),
                email: $faker->unique()->email(),
                password: $password,
                status: $faker->bothify($faker->randomElement(array: AccountStatus::STATUSES)),
            );
            $manager->persist($account);
        }

        $manager->flush();
    }
}
