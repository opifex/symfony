<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Repository;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;

abstract class AbstractRepository
{
    public function __construct(protected EntityManagerInterface $entityManager)
    {
    }

    /**
     * @throws Exception
     */
    protected function insertOne(object $entity): void
    {
        $classMetadata = $this->entityManager->getClassMetadata($entity::class);
        $convertToDatabaseValue = $this->entityManager->getConnection()->convertToDatabaseValue(...);
        $tableFields = [];

        foreach ($classMetadata->getFieldNames() as $fieldName) {
            $tableFields[$classMetadata->getColumnName($fieldName)] = $convertToDatabaseValue(
                value: $classMetadata->getFieldValue($entity, $fieldName),
                type: strval($classMetadata->getTypeOfField($fieldName)),
            );
        }

        $this->entityManager->getConnection()->insert($classMetadata->getTableName(), $tableFields);
    }
}
