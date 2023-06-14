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

final class AccountFixture extends Fixture implements FixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Faker::create();

        $adminAccount = new Account(email: 'admin@example.com', roles: [AccountRole::ROLE_ADMIN]);
        $adminAccount->setPassword(password: '$2y$13$beiwKOPtxnMLiKkI9unBheZzzi1eTaW2o9uNrxiIL9DBsaOrjXswm');
        $adminAccount->setStatus(status: AccountStatus::VERIFIED);
        $manager->persist($adminAccount);

        $userAccount = new Account(email: 'user@example.com', roles: [AccountRole::ROLE_USER]);
        $userAccount->setPassword(password: '$2y$13$beiwKOPtxnMLiKkI9unBheZzzi1eTaW2o9uNrxiIL9DBsaOrjXswm');
        $userAccount->setStatus(status: AccountStatus::VERIFIED);
        $manager->persist($userAccount);

        for ($index = 1; $index <= 10; $index++) {
            $account = new Account($faker->email(), [AccountRole::ROLE_USER]);
            $account->setPassword(password: '$2y$13$beiwKOPtxnMLiKkI9unBheZzzi1eTaW2o9uNrxiIL9DBsaOrjXswm');
            $account->setStatus(status: AccountStatus::VERIFIED);
            $manager->persist($account);
        }

        $manager->flush();
    }
}
