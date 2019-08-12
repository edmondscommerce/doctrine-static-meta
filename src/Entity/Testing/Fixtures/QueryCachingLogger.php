<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\Fixtures;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\Statement;
use Doctrine\DBAL\Logging\SQLLogger;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Factories\UuidFactory;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use Exception;
use Ramsey\Uuid\UuidInterface;
use RuntimeException;

/**
 * This class is caches queries so that they can be run as quickly as possible on subsequent builds
 *
 * @see \EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\Fixtures\FixturesHelper::createDb
 */
class QueryCachingLogger implements SQLLogger
{
    private $queries = [];

    public function startQuery($sql, array $params = null, array $types = null)
    {
        $this->queries[] = [
            'sql'    => $sql,
            'params' => $params,
            'types'  => $types,
        ];
    }

    public function __sleep(): array
    {
        foreach ($this->queries as &$query) {
            $this->serialiseUuids($query['params']);
        }

        return ['queries'];
    }

    /**
     * Ramsey/UUIDs needs to be converted to string in order to be able to deserialised properly
     *
     * @param array|null $params
     */
    private function serialiseUuids(array &$params = null): void
    {
        if (null === $params) {
            return;
        }
        foreach ($params as &$param) {
            if ($param instanceof UuidInterface) {
                $param = $param->toString();
            }
        }
    }

    public function __wakeup()
    {
        $factory = new UuidFactory(new \Ramsey\Uuid\UuidFactory());
        foreach ($this->queries as &$query) {
            $this->unserialiseUuids($query, $factory);
        }
    }

    private function unserialiseUuids(array &$query, UuidFactory $factory): void
    {
        if (null === $query['params']) {
            return;
        }
        foreach ($query['params'] as $key => &$param) {
            try {
                if (null === $param) {
                    continue;
                }
                switch ($query['types'][$key]) {
                    case MappingHelper::TYPE_UUID:
                        $param = $factory->getOrderedTimeFactory()->fromString($param);
                        continue 2;
                    case MappingHelper::TYPE_NON_ORDERED_BINARY_UUID:
                    case MappingHelper::TYPE_NON_BINARY_UUID:
                        $param = $factory->getUuidFactory()->fromString($param);
                        continue 2;
                    default:
                        continue 2;
                }
            } catch (Exception $e) {
                throw new RuntimeException(
                    'Failed deserialising UUID param key ' . $key . ', ' . $param
                    . "\n" . print_r($query, true),
                    $e->getCode(),
                    $e
                );
            }
        }
    }

    public function stopQuery()
    {
        return;
    }

    public function run(Connection $connection): void
    {

        foreach ($this->queries as $id => $query) {
            if ('"START TRANSACTION"' === $query['sql']) {
                $connection->beginTransaction();
                continue;
            }
            if ('"COMMIT"' === $query['sql']) {
                $connection->commit();
                continue;
            }
            if ($connection->getDatabasePlatform()->getDummySelectSQL() === $query['sql']) {
                //this is a ping query
                unset($this->queries[$id]);
                continue;
            }
            if ([] === $query['params']) {
                $connection->prepare($query)->execute();
                continue;
            }
            $stmt = $connection->prepare($query['sql']);
            $this->runQuery($query, $stmt, $connection);
        }
    }

    private function runQuery(array $query, Statement $stmt, Connection $connection): void
    {
        try {
            if ($query['params'] !== null) {
                $colNum = 1;
                foreach ($query['params'] as $key => $value) {
                    $stmt->bindValue($colNum++, $value, $query['types'][$key]);
                }
            }
            $stmt->execute();
        } catch (Exception $e) {
            if ($connection->isTransactionActive()) {
                $connection->rollBack();
            }
            throw new DoctrineStaticMetaException(
                'Failed running logged query:' . print_r($query, true),
                $e->getCode(),
                $e
            );
        }
    }
}
