<?php

declare(strict_types=1);

namespace Tests\Support\Data\Fixture;

use App\Domain\Entity\AccountRole;
use App\Domain\Entity\AccountStatus;
use App\Infrastructure\Persistence\Doctrine\Mapping\Default\AccountEntity;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory as Faker;
use Override;
use Symfony\Component\PasswordHasher\Hasher\NativePasswordHasher;

final class AccountAdminFixture extends Fixture implements FixtureInterface
{
    #[Override]
    public function load(ObjectManager $manager): void
    {
        $faker = Faker::create();
        $passwordHasher = new NativePasswordHasher();
        $password = $passwordHasher->hash(plainPassword: 'password4#account');

        $account = new AccountEntity(
            uuid: '00000000-0000-6000-8000-000000000000',
            createdAt: new DateTimeImmutable(),
            email: $faker->unique()->bothify(string: 'admin@example.com'),
            password: $password,
            locale: 'en_US',
            roles: [AccountRole::Admin->value],
            status: AccountStatus::Activated->value,
        );
        $manager->persist($account);
        $manager->flush();
    }
}
