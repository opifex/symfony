<?php

declare(strict_types=1);

namespace Tests\Support\Data\Fixture;

use App\Domain\Model\AccountRole;
use App\Domain\Model\AccountStatus;
use App\Infrastructure\Doctrine\Mapping\Default\AccountEntity;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory as Faker;
use Override;

final class AccountBlockedHenryFixture extends Fixture implements FixtureInterface
{
    #[Override]
    public function load(ObjectManager $manager): void
    {
        $faker = Faker::create();
        $account = new AccountEntity(
            uuid: '019661ee-9ecf-79eb-9ba1-f211f1975995',
            createdAt: new DateTimeImmutable(),
            email: $faker->unique()->bothify(string: 'henry@example.com'),
            password: 'password4#account',
            locale: 'en_US',
            roles: [AccountRole::USER],
            status: AccountStatus::BLOCKED,
        );
        $manager->persist($account);
        $manager->flush();
    }
}
