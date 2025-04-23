<?php

declare(strict_types=1);

namespace Tests\Support\Data\Fixture;

use App\Domain\Entity\AccountRole;
use App\Domain\Entity\AccountStatus;
use App\Infrastructure\Doctrine\Mapping\Default\AccountEntity;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory as Faker;
use Override;

final class AccountActivatedJamesFixture extends Fixture implements FixtureInterface
{
    #[Override]
    public function load(ObjectManager $manager): void
    {
        $faker = Faker::create();
        $account = new AccountEntity(
            uuid: '019661ed-775d-7b12-9c57-446490b0165e',
            createdAt: new DateTimeImmutable(),
            email: $faker->unique()->bothify(string: 'james@example.com'),
            password: 'password4#account',
            locale: 'en_US',
            roles: [AccountRole::USER],
            status: AccountStatus::ACTIVATED,
        );
        $manager->persist($account);
        $manager->flush();
    }
}
