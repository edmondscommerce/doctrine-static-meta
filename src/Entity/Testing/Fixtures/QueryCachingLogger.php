<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\Fixtures;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Logging\SQLLogger;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;

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
            if ('"SELECT 1"' == $query) {
                //this is a ping query
                continue;
            }
            if ([[[], []]] === $paramsArray) {
                $connection->prepare($query)->execute();
                continue;
            }
            $stmt = $connection->prepare($query);
            foreach ($paramsArray as $paramsTypes) {
                try {
                    list($params, $types) = $paramsTypes;
                    $colNum = 1;
                    foreach ($params as $key => $value) {
                        $stmt->bindValue($colNum++, $value, $types[$key]);
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
    }
}
