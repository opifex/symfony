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

        $password = '$2y$13$beiwKOPtxnMLiKkI9unBheZzzi1eTaW2o9uNrxiIL9DBsaOrjXswm';
        $accounts = [
            ['email' => 'admin@example.com', 'roles' => [AccountRole::ROLE_ADMIN], 'status' => AccountStatus::VERIFIED],
            ['email' => 'user@example.com', 'roles' => [AccountRole::ROLE_USER], 'status' => AccountStatus::VERIFIED],
        ];

        foreach ($accounts as $data) {
            $account = new Account($data['email'], $data['roles']);
            $account->setPassword($password);
            $account->setStatus($data['status']);
            $manager->persist($account);
        }

        for ($index = 1; $index <= 10; $index++) {
            $account = new Account($faker->email(), [AccountRole::ROLE_USER]);
            $account->setPassword($password);
            $account->setStatus(status: AccountStatus::VERIFIED);
            $manager->persist($account);
        }

        $manager->flush();
    }
}
