<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Fixture;

use App\Domain\Account\AccountRole;
use App\Domain\Account\AccountStatus;
use App\Domain\Localization\LocaleCode;
use App\Infrastructure\Doctrine\Mapping\AccountEntity;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory as Faker;
use Override;
use Symfony\Component\Clock\DatePoint;
use Symfony\Component\PasswordHasher\Hasher\NativePasswordHasher;

final class AccountFixture extends Fixture implements FixtureInterface
{
    #[Override]
    public function load(ObjectManager $manager): void
    {
        $faker = Faker::create();
        $passwordHasher = new NativePasswordHasher();
        $passwordHash = $passwordHasher->hash(plainPassword: 'password4#account');

        $accountAdmin = new AccountEntity(
            id: $faker->unique()->uuid(),
            createdAt: DatePoint::createFromMutable($faker->dateTime()),
            email: $faker->unique()->bothify(string: 'admin@example.com'),
            password: $passwordHash,
            locale: LocaleCode::EnUs->toString(),
            roles: [AccountRole::Admin->toString()],
            status: AccountStatus::Activated->toString(),
        );
        $manager->persist($accountAdmin);
        $this->addReference(name: 'account:admin', object: $accountAdmin);

        $accountUser = new AccountEntity(
            id: $faker->unique()->uuid(),
            createdAt: DatePoint::createFromMutable($faker->dateTime()),
            email: $faker->unique()->bothify(string: 'user@example.com'),
            password: $passwordHash,
            locale: LocaleCode::EnUs->toString(),
            roles: [AccountRole::User->toString()],
            status: AccountStatus::Activated->toString(),
        );
        $manager->persist($accountUser);
        $this->addReference(name: 'account:user', object: $accountUser);

        for ($index = 1; $index <= 10; $index++) {
            /** @var string $accountStatus */
            $accountStatus = $faker->randomElement(array: AccountStatus::values());
            $accountRandom = new AccountEntity(
                id: $faker->unique()->uuid(),
                createdAt: DatePoint::createFromMutable($faker->dateTime()),
                email: $faker->unique()->email(),
                password: $passwordHash,
                locale: LocaleCode::EnUs->toString(),
                roles: [AccountRole::User->toString()],
                status: $accountStatus,
            );
            $manager->persist($accountRandom);
        }

        $manager->flush();
    }
}
