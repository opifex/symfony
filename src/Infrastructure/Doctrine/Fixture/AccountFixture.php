<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Fixture;

use App\Domain\Model\AccountRole;
use App\Domain\Model\AccountStatus;
use App\Domain\Model\LocaleCode;
use App\Infrastructure\Doctrine\Mapping\AccountEntity;
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
        $passwordHash = $passwordHasher->hash(plainPassword: 'password4#account');

        $accountAdmin = new AccountEntity(
            id: $faker->unique()->uuid(),
            createdAt: DateTimeImmutable::createFromMutable($faker->dateTime()),
            email: $faker->unique()->bothify(string: 'admin@example.com'),
            password: $passwordHash,
            locale: LocaleCode::EnUs->toString(),
            roles: [AccountRole::ADMIN],
            status: AccountStatus::Activated->toString(),
        );
        $manager->persist($accountAdmin);
        $this->addReference(name: 'account:admin', object: $accountAdmin);

        $accountUser = new AccountEntity(
            id: $faker->unique()->uuid(),
            createdAt: DateTimeImmutable::createFromMutable($faker->dateTime()),
            email: $faker->unique()->bothify(string: 'user@example.com'),
            password: $passwordHash,
            locale: LocaleCode::EnUs->toString(),
            roles: [AccountRole::USER],
            status: AccountStatus::Activated->toString(),
        );
        $manager->persist($accountUser);
        $this->addReference(name: 'account:user', object: $accountUser);

        for ($index = 1; $index <= 10; $index++) {
            /** @var AccountStatus $accountStatus */
            $accountStatus = $faker->randomElement(array: AccountStatus::values());
            $accountRandom = new AccountEntity(
                id: $faker->unique()->uuid(),
                createdAt: DateTimeImmutable::createFromMutable($faker->dateTime()),
                email: $faker->unique()->email(),
                password: $passwordHash,
                locale: LocaleCode::EnUs->toString(),
                roles: [AccountRole::USER],
                status: $accountStatus->toString(),
            );
            $manager->persist($accountRandom);
        }

        $manager->flush();
    }
}
