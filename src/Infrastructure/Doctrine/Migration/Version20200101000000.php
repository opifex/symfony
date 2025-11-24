<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Migration;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Override;

final class Version20200101000000 extends AbstractMigration
{
    #[Override]
    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            CREATE TABLE account (id UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, email VARCHAR(320) NOT NULL, password VARCHAR(60) NOT NULL, locale VARCHAR(5) NOT NULL, roles JSON NOT NULL, status VARCHAR(24) NOT NULL, PRIMARY KEY (id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_7D3656A4E7927C74 ON account (email)
        SQL);
    }

    #[Override]
    public function down(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            DROP TABLE account
        SQL);
    }
}
