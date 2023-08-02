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
use Symfony\Component\Uid\Uuid;

final class AccountFixture extends Fixture implements FixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Faker::create();
        $password = '$2y$13$beiwKOPtxnMLiKkI9unBheZzzi1eTaW2o9uNrxiIL9DBsaOrjXswm';

        $adminAccount = new Account(
            uuid: Uuid::v7()->toRfc4122(),
            email: 'admin@example.com',
            password: $password,
            status: AccountStatus::VERIFIED,
            roles: [AccountRole::ROLE_ADMIN],
        );
        $manager->persist($adminAccount);

        $userAccount = new Account(
            uuid: Uuid::v7()->toRfc4122(),
            email: 'user@example.com',
            password: $password,
            status: AccountStatus::VERIFIED,
            roles: [AccountRole::ROLE_USER],
        );
        $manager->persist($userAccount);

        for ($index = 1; $index <= 10; $index++) {
            $account = new Account(
                uuid: Uuid::v7()->toRfc4122(),
                email: $faker->email(),
                password: $password,
                status: AccountStatus::VERIFIED,
                roles: [AccountRole::ROLE_USER],
            );
            $manager->persist($account);
        }

        $manager->flush();
    }
}
