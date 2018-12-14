<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\EntityManager\Connection;

use Doctrine\Common\EventManager;
use Doctrine\DBAL\Cache\QueryCacheProfile;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver;

class RetryConnection extends Connection
{
    private $shouldConnectionByRetried;

    /** @var \ReflectionProperty */
    private $selfReflectionNestingLevelProperty;

    public function __construct(
        array $params,
        Driver $driver,
        ?Configuration $config = null,
        ?EventManager $eventManager = null
    ) {
        $this->shouldConnectionByRetried = ShouldConnectionByRetried::createWithConfigParams($params);
        parent::__construct($params, $driver, $config, $eventManager);
    }

    public function executeUpdate($query, array $params = [], array $types = [])
    {
        $args = [$query, $params, $types];
        return $this->connectionWrapper('executeUpdate', $args, false);
    }

    public function query(...$args)
    {
        return $this->connectionWrapper('query', $args, false);
    }

    public function executeQuery($query, array $params = [], $types = [], QueryCacheProfile $qcp = null)
    {
        $args = [$query, $params, $types, $qcp];
        return $this->connectionWrapper('executeQuery', $args, false);
    }

    public function beginTransaction()
    {
        if (0 !== $this->getTransactionNestingLevel()) {
            parent::beginTransaction();
        }
        $this->connectionWrapper('beginTransaction', [], true);
    }

    public function connect()
    {
        return $this->connectionWrapper('connect', [], false);
    }

    /**
     * @param $sql
     *
     * @return Statement
     */
    public function prepare($sql)
    {
        return $this->prepareWrapped($sql);
    }

    /**
     * returns a reconnect-wrapper for Statements.
     *
     * @param $sql
     *
     * @return Statement
     */
    protected function prepareWrapped($sql): Statement
    {
        return new Statement($sql, $this, $this->shouldConnectionByRetried);
    }

    /**
     * do not use, only used by Statement-class
     * needs to be public for access from the Statement-class.
     *
     * @internal
     */
    public function prepareUnwrapped($sql): Driver\Statement
    {
        // returns the actual statement
        return parent::prepare($sql);
    }

    private function connectionWrapper(string $function, array $args, bool $ignoreTransaction)
    {
        $retryConnectionFlag  = true;
        $checkRetryConnection = $this->shouldConnectionByRetried;
        $numberOfAttempts     = 0;
        $result               = null;
        while ($retryConnectionFlag === true) {
            try {
                $retryConnectionFlag = false;
                $numberOfAttempts++;
                $result = parent::$function(...$args);
            } catch (\Exception $exception) {
                $nestingLevel        = $this->getTransactionNestingLevel();
                $retryConnectionFlag = $checkRetryConnection
                    ->checkAndSleep(
                        $exception,
                        $numberOfAttempts,
                        $nestingLevel,
                        $ignoreTransaction
                    );
                if ($retryConnectionFlag === false) {
                    throw $exception;
                }
                $this->close();
                $numberOfAttempts++;
                if ($ignoreTransaction === true && 0 < $this->getTransactionNestingLevel()) {
                    $this->resetTransactionNestingLevel();
                }
            }
        }

        return $result;
    }

    /**
     * This is required because beginTransaction increment _transactionNestingLevel
     * before the real query is executed, and results incremented also on gone away error.
     * This should be safe for a new established connection.
     */
    private function resetTransactionNestingLevel(): void
    {
        if (! $this->selfReflectionNestingLevelProperty instanceof \ReflectionProperty) {
            $reflection = new \ReflectionClass(Connection::class);
            $this->selfReflectionNestingLevelProperty = $reflection->getProperty('_transactionNestingLevel');
            $this->selfReflectionNestingLevelProperty->setAccessible(true);
        }

        $this->selfReflectionNestingLevelProperty->setValue($this, 0);
    }
}
