<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\EntityManager\RetryConnection;

use Doctrine\Common\EventManager;
use Doctrine\DBAL\Cache\QueryCacheProfile;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver;

/**
 * This is a connection wrapper that enables some retry functionality should the connection to the DB be lost for any
 * reason. Especially useful on long running processes
 */
class RetryConnection extends Connection
{
    private $shouldConnectionByRetried;

    /** @var \ReflectionProperty */
    private $selfReflectionNestingLevelProperty;

    /**
     * RetryConnection constructor.
     *
     * @param array              $params
     * @param Driver             $driver
     * @param Configuration|null $config
     * @param EventManager|null  $eventManager
     *
     * @throws \Doctrine\DBAL\DBALException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
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

        return $this->connectionWrapper(__FUNCTION__, $args, false);
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
        if (!$this->selfReflectionNestingLevelProperty instanceof \ReflectionProperty) {
            $reflection                               = new \ReflectionClass(Connection::class);
            $this->selfReflectionNestingLevelProperty = $reflection->getProperty('_transactionNestingLevel');
            $this->selfReflectionNestingLevelProperty->setAccessible(true);
        }

        $this->selfReflectionNestingLevelProperty->setValue($this, 0);
    }

    public function query(...$args)
    {
        return $this->connectionWrapper('query', $args, false);
    }

    public function executeQuery($query, array $params = [], $types = [], QueryCacheProfile $qcp = null)
    {
        $args = [$query, $params, $types, $qcp];

        return $this->connectionWrapper(__FUNCTION__, $args, false);
    }

    public function beginTransaction()
    {
        if (0 !== $this->getTransactionNestingLevel()) {
            parent::beginTransaction();
        }
        $this->connectionWrapper(__FUNCTION__, [], true);
    }

    public function connect()
    {
        return $this->connectionWrapper(__FUNCTION__, [], false);
    }

    /**
     * @param string $sql
     *
     * @return Statement
     */
    public function prepare($sql): Statement
    {
        return $this->prepareWrapped($sql);
    }

    /**
     * returns a reconnect-wrapper for Statements.
     *
     * @param string $sql
     *
     * @return Statement
     */
    protected function prepareWrapped(string $sql): Statement
    {
        return new Statement($sql, $this, $this->shouldConnectionByRetried);
    }

    /**
     * do not use, only used by Statement-class
     * needs to be public for access from the Statement-class.
     *
     * @internal
     *
     * @param string $sql
     *
     * @return Driver\Statement
     * @throws \Doctrine\DBAL\DBALException
     */
    public function prepareUnwrapped(string $sql): Driver\Statement
    {
        // returns the actual statement
        return parent::prepare($sql);
    }
}
