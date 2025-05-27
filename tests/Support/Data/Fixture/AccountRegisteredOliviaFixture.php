<?php

declare(strict_types=1);

namespace Tests\Support\Data\Fixture;

use App\Domain\Model\AccountRole;
use App\Domain\Model\AccountStatus;
use App\Infrastructure\Doctrine\Mapping\AccountEntity;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory as Faker;
use Override;

final class AccountRegisteredOliviaFixture extends Fixture implements FixtureInterface
{
    #[Override]
    public function load(ObjectManager $manager): void
    {
        $faker = Faker::create();
        $account = new AccountEntity(
            id: $faker->unique()->uuid(),
            createdAt: DateTimeImmutable::createFromMutable($faker->dateTime()),
            email: $faker->unique()->bothify(string: 'olivia@example.com'),
            password: 'password4#account',
            locale: 'en_US',
            roles: [AccountRole::USER],
            status: AccountStatus::REGISTERED,
        );
        $manager->persist($account);
        $this->addReference(name: 'account:registered:olivia', object: $account);
        $manager->flush();
    }
}
