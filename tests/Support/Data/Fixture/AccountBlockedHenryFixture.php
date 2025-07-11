<?php

declare(strict_types=1);

namespace Tests\Support\Data\Fixture;

use App\Domain\Model\AccountStatus;
use App\Domain\Model\LocaleCode;
use App\Domain\Model\Role;
use App\Infrastructure\Doctrine\Mapping\AccountEntity;
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
            id: $faker->unique()->uuid(),
            createdAt: DateTimeImmutable::createFromMutable($faker->dateTime()),
            email: $faker->unique()->bothify(string: 'henry@example.com'),
            password: 'password4#account',
            locale: LocaleCode::EnUs->toString(),
            roles: [Role::User->toString()],
            status: AccountStatus::Blocked->toString(),
        );
        $manager->persist($account);
        $this->addReference(name: 'account:blocked:henry', object: $account);
        $manager->flush();
    }
}
