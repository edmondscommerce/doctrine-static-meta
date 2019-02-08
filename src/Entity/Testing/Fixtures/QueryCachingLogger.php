<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\Fixtures;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\Statement;
use Doctrine\DBAL\Logging\SQLLogger;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Factories\UuidFactory;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use Ramsey\Uuid\UuidInterface;

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
        $this->queries[$sql][] = [$params, $types];
    }

    public function __sleep(): array
    {
        foreach ($this->queries as $sql => &$paramsArray) {
            foreach ($paramsArray as &$paramsTypes) {
                $this->serialiseUuids($paramsTypes[0]);
            }
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
        foreach ($this->queries as $sql => &$paramsArray) {
            foreach ($paramsArray as &$paramsTypes) {
                $this->unserialiseUuids($paramsTypes, $factory);
            }
        }
    }

    private function unserialiseUuids(array &$paramsTypes, UuidFactory $factory): void
    {
        if (null === $paramsTypes[0]) {
            return;
        }
        foreach ($paramsTypes[0] as $key => &$param) {
            try {
                if (null === $param) {
                    continue;
                }
                switch ($paramsTypes[1][$key]) {
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
            } catch (\Exception $e) {
                throw new \RuntimeException(
                    'Failed deserialising UUID param key ' . $key . ', ' . $param
                    . "\n" . print_r($paramsTypes, true),
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
        foreach ($this->queries as $query => $paramsArray) {
            if ('"START TRANSACTION"' === $query) {
                $connection->beginTransaction();
                continue;
            }
            if ('"COMMIT"' === $query) {
                $connection->commit();
                continue;
            }
            if ($connection->getDatabasePlatform()->getDummySelectSQL() === $query) {
                //this is a ping query
                unset($this->queries[$query]);
                continue;
            }
            if ([[[], []]] === $paramsArray) {
                $connection->prepare($query)->execute();
                continue;
            }
            $stmt = $connection->prepare($query);
            foreach ($paramsArray as $paramsTypes) {
                $this->runQuery($paramsTypes, $stmt, $connection, $query);
            }
        }
    }

    private function runQuery(array $paramsTypes, Statement $stmt, Connection $connection, string $query): void
    {
        try {
            list($params, $types) = $paramsTypes;
            if ($params !== null) {
                $colNum = 1;
                foreach ($params as $key => $value) {
                    $stmt->bindValue($colNum++, $value, $types[$key]);
                }
            }
            $stmt->execute();
        } catch (\Exception $e) {
            if ($connection->isTransactionActive()) {
                $connection->rollBack();
            }
            throw new DoctrineStaticMetaException(
                'Failed running logged query ' . $query . 'with params and types: '
                . print_r($paramsTypes, true),
                $e->getCode(),
                $e
            );
        }
    }
}
