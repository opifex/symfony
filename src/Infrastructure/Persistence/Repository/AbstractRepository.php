<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Repository;

use App\Domain\Entity\SearchSorting;
use App\Domain\Entity\SortingOrder;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\OrderBy;
use LogicException;

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
                type: $classMetadata->getTypeOfField($fieldName) ?? '',
            );
        }

        $this->entityManager->getConnection()->insert($classMetadata->getTableName(), $tableFields);
    }

    /**
     * @param SearchSorting $searchSorting
     * @param array&array<string, string> $fieldsMapping
     * @return OrderBy
     */
    protected function buildOrderBy(SearchSorting $searchSorting, array $fieldsMapping): OrderBy
    {
        $sortingField = $fieldsMapping[$searchSorting->field] ?? throw new LogicException(
            message: sprintf('Sorting field "%s" is not supported.', $searchSorting->field),
        );

        return match ($searchSorting->order) {
            SortingOrder::ASC => $this->entityManager->getExpressionBuilder()->asc($sortingField),
            SortingOrder::DESC => $this->entityManager->getExpressionBuilder()->desc($sortingField),
            default => throw new LogicException(
                message: sprintf('Sorting order "%s" is not supported.', $searchSorting->order),
            ),
        };
    }
}
