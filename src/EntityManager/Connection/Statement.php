<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\EntityManager\Connection;

use Doctrine\DBAL\Driver\Statement as DriverStatement;
use Doctrine\DBAL\ParameterType;

class Statement implements \IteratorAggregate, DriverStatement
{
    /**
     * @var RetryConnection
     */
    private $connection;
    /**
     * @var array
     */
    private $params = [];
    /**
     * @var ShouldConnectionByRetried
     */
    private $shouldConnectionByRetried;
    /**
     * @var string
     */
    private $sql;
    /**
     * @var array
     */
    private $values = [];
    /**
     * @var \Doctrine\DBAL\Statement
     */
    private $wrappedStatement;

    /**
     * @param                 $sql
     * @param RetryConnection $conn
     */
    public function __construct($sql, RetryConnection $conn, ShouldConnectionByRetried $shouldConnectionByRetried)
    {
        $this->sql                       = $sql;
        $this->connection                = $conn;
        $this->shouldConnectionByRetried = $shouldConnectionByRetried;
        $this->createStatement();
    }

    /**
     * @inheritdoc
     */
    public function bindParam($column, &$variable, $type = ParameterType::STRING, $length = null)
    {
        $this->params[] = [$column, $variable, $type, $length];

        return $this->wrappedStatement->bindParam($column, $variable, $type, $length);
    }

    /**
     * @inheritdoc
     */
    public function bindValue($param, $value, $type = ParameterType::STRING)
    {
        $this->values[] = [$param, $value, $type];

        return $this->wrappedStatement->bindValue($param, $value, $type);
    }

    /**
     * @inheritdoc
     */
    public function closeCursor()
    {
        return $this->wrappedStatement->closeCursor();
    }

    /**
     * @inheritdoc
     */
    public function columnCount()
    {
        return $this->wrappedStatement->columnCount();
    }

    /**
     * @inheritdoc
     */
    public function errorCode()
    {
        return $this->wrappedStatement->errorCode();
    }

    /**
     * @inheritdoc
     */
    public function errorInfo()
    {
        return $this->wrappedStatement->errorInfo();
    }

    /**
     * @inheritdoc
     */
    public function execute($params = null)
    {
        $stmt    = null;
        $attempt = 0;
        $retry   = true;
        while ($retry) {
            $retry = false;
            try {
                $stmt = $this->wrappedStatement->execute($params);
            } catch (\Exception $e) {
                $nesting = $this->connection->getTransactionNestingLevel();
                $retry   = $this->shouldConnectionByRetried->checkAndSleep($e, $nesting, $attempt, false);
                if ($retry === false) {
                    throw $e;
                }
                $this->connection->close();
                $this->createStatement();
                $attempt++;
            }
        }

        return $stmt;
    }

    /**
     * @inheritdoc
     */
    public function fetch($fetchMode = null, $cursorOrientation = \PDO::FETCH_ORI_NEXT, $cursorOffset = 0)
    {
        return $this->wrappedStatement->fetch($fetchMode, $cursorOrientation, $cursorOffset);
    }

    /**
     * @inheritdoc
     */
    public function fetchAll($fetchMode = null, $fetchArgument = null, $ctorArgs = null)
    {
        return $this->wrappedStatement->fetchAll($fetchMode, $fetchArgument, $ctorArgs);
    }

    /**
     * @inheritdoc
     */
    public function fetchColumn($columnIndex = 0)
    {
        return $this->wrappedStatement->fetchColumn($columnIndex);
    }

    /**
     * @inheritdoc
     */
    public function getIterator()
    {
        return $this->wrappedStatement->getIterator();
    }

    /**
     * @inheritdoc
     */
    public function rowCount()
    {
        return $this->wrappedStatement->rowCount();
    }

    /**
     * @inheritdoc
     */
    public function setFetchMode($fetchMode, $arg2 = null, $arg3 = null)
    {
        return $this->wrappedStatement->setFetchMode($fetchMode, $arg2, $arg3);
    }

    /**
     * Create Statement.
     */
    private function createStatement()
    {
        $this->wrappedStatement = $this->connection->prepareUnwrapped($this->sql);
        foreach ($this->params as $params) {
            $this->bindParam(...$params);
        }
        $this->params = [];
        foreach ($this->values as $values) {
            $this->bindValue(...$values);
        }
        $this->values = [];
    }
}