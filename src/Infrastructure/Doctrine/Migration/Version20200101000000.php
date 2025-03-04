<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Migration;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;
use Override;
use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
final class Version20200101000000 extends AbstractMigration
{
    #[Override]
    public function up(Schema $schema): void
    {
        $table = $schema->createTable(name: 'account');
        $table->addColumn(name: 'uuid', typeName: Types::GUID);
        $table->addColumn(name: 'created_at', typeName: Types::DATETIME_IMMUTABLE);
        $table->addColumn(name: 'email', typeName: Types::STRING, options: ['length' => 320]);
        $table->addColumn(name: 'password', typeName: Types::STRING, options: ['length' => 60]);
        $table->addColumn(name: 'locale', typeName: Types::STRING, options: ['length' => 5]);
        $table->addColumn(name: 'roles', typeName: Types::JSON);
        $table->addColumn(name: 'status', typeName: Types::STRING, options: ['length' => 24]);
        $table->addUniqueIndex(['email']);
        $table->setPrimaryKey(columnNames: ['uuid']);
    }

    #[Override]
    public function down(Schema $schema): void
    {
        $schema->dropTable(name: 'account');
    }
}
