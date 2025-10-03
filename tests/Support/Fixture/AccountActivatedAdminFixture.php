<?php

declare(strict_types=1);

namespace Tests\Support\Fixture;

use App\Domain\Account\AccountStatus;
use App\Domain\Account\AccountRole;
use App\Domain\Localization\LocaleCode;
use App\Infrastructure\Doctrine\Mapping\AccountEntity;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory as Faker;
use Override;
use Symfony\Component\Clock\DatePoint;

final class AccountActivatedAdminFixture extends Fixture implements FixtureInterface
{
    #[Override]
    public function load(ObjectManager $manager): void
    {
        $faker = Faker::create();
        $account = new AccountEntity(
            id: $faker->unique()->uuid(),
            createdAt: DatePoint::createFromMutable($faker->dateTime()),
            email: $faker->unique()->bothify(string: 'admin@example.com'),
            password: 'password4#account',
            locale: LocaleCode::EnUs->toString(),
            roles: [AccountRole::Admin->toString()],
            status: AccountStatus::Activated->toString(),
        );
        $manager->persist($account);
        $this->addReference(name: 'account:activated:admin', object: $account);
        $manager->flush();
    }
}
