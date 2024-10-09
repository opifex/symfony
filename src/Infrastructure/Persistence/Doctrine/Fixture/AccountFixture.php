<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Fixture;

use App\Domain\Entity\AccountRole;
use App\Domain\Entity\AccountStatus;
use App\Infrastructure\Persistence\Doctrine\Repository\Account\AccountEntity;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory as Faker;
use Override;
use Symfony\Component\PasswordHasher\Hasher\NativePasswordHasher;

final class AccountFixture extends Fixture implements FixtureInterface
{
    #[Override]
    public function load(ObjectManager $manager): void
    {
        $faker = Faker::create();
        $passwordHasher = new NativePasswordHasher();
        $password = $passwordHasher->hash(plainPassword: 'password4#account');

        $adminAccount = new AccountEntity(
            uuid: $faker->unique()->uuid(),
            createdAt: new DateTimeImmutable(),
            email: $faker->unique()->bothify(string: 'admin@example.com'),
            password: $password,
            locale: 'en_US',
            roles: [AccountRole::ROLE_ADMIN],
            status: AccountStatus::ACTIVATED,
        );
        $manager->persist($adminAccount);

        $userAccount = new AccountEntity(
            uuid: $faker->unique()->uuid(),
            createdAt: new DateTimeImmutable(),
            email: $faker->unique()->bothify(string: 'user@example.com'),
            password: $password,
            locale: 'en_US',
            roles: [AccountRole::ROLE_USER],
            status: AccountStatus::ACTIVATED,
        );
        $manager->persist($userAccount);

        for ($index = 1; $index <= 10; $index++) {
            $account = new AccountEntity(
                uuid: $faker->unique()->uuid(),
                createdAt: new DateTimeImmutable(),
                email: $faker->unique()->email(),
                password: $password,
                locale: 'en_US',
                roles: [AccountRole::ROLE_USER],
                status: $faker->bothify($faker->randomElement(array: AccountStatus::STATUSES)),
            );
            $manager->persist($account);
        }

        $manager->flush();
    }
}
