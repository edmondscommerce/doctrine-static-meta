<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Repositories;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\LazyCriteriaCollection;
use Doctrine\ORM\NativeQuery;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;

/**
 * Entity Repository Interface
 *
 * This is a modified interface based upon the standard Doctrine 2 Entity Repository, but without magic and with type
 * safety
 *
 * @package EdmondsCommerce\DoctrineStaticMeta\Entity\Repositories
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
interface EntityRepositoryInterface
{
    public function find(mixed $id, ?int $lockMode = null, ?int $lockVersion = null): ?EntityInterface;

    /**
     * @return EntityInterface[]
     */
    public function findAll(): array;

    /**
     * @param array<string,mixed>        $criteria
     * @param array<string, string>|null $orderBy
     *
     * @return EntityInterface[]
     */
    public function findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null): array;

    /**
     * @param array<string,mixed>        $criteria
     * @param array<string, string>|null $orderBy
     */
    public function findOneBy(array $criteria, ?array $orderBy = null): ?EntityInterface;

    public function get(mixed $id, ?int $lockMode = null, ?int $lockVersion = null): EntityInterface;

    /**
     * @param array<string,mixed>        $criteria
     * @param array<string, string>|null $orderBy
     */
    public function getOneBy(array $criteria, ?array $orderBy = null): EntityInterface;

    public function getClassName(): string;

    public function matching(Criteria $criteria): LazyCriteriaCollection;

    public function createQueryBuilder(string $alias, string $indexBy = null): QueryBuilder;

    public function createResultSetMappingBuilder(string $alias): Query\ResultSetMappingBuilder;

    public function createNamedQuery(string $queryName): Query;

    public function createNativeNamedQuery(string $queryName): NativeQuery;

    public function clear();

    public function count(array $criteria = []);
}
