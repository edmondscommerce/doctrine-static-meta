<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Schema;

use EdmondsCommerce\DoctrineStaticMeta\ConfigInterface;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use InvalidArgumentException;
use mysqli;

/**
 * Class Database
 *
 * Drop and Create the Actual Database using raw mysqli
 *
 * @package EdmondsCommerce\DoctrineStaticMeta\Schema
 */
class Database
{

    /**
     * @see https://dev.mysql.com/doc/refman/5.7/en/identifiers.html
     */
    public const MAX_IDENTIFIER_LENGTH = 64;

    /**
     * @see https://dev.mysql.com/doc/refman/5.7/en/precision-math-decimal-characteristics.html
     */
    public const MAX_DECIMAL_PRECISION = 65;
    public const MAX_DECIMAL_SCALE     = 30;

    /**
     * @see https://github.com/symfony/symfony-docs/issues/639
     * basically, if we are using utf8mb4 then the max col length on strings is no longer 255
     */
    public const MAX_VARCHAR_LENGTH = 190;

    public const MAX_INT_VALUE = 2147483647;

    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @var null|mysqli
     */
    private $link;

    public function __construct(ConfigInterface $config)
    {
        $this->config = $config;
    }

    /**
     * You have to pass in true to confirm you really want to do it
     *
     * @param bool $sure
     *
     * @return Database
     * @throws InvalidArgumentException
     * @throws DoctrineStaticMetaException
     */
    public function drop(bool $sure): Database
    {
        if (true !== $sure) {
            $this->throwUnsure();
        }
        $link      = $this->connect();
        $existsSql = "SHOW DATABASES WHERE `database` = '{$this->config->get(ConfigInterface::PARAM_DB_NAME)}'";
        $exists    = mysqli_fetch_assoc(mysqli_query($link, $existsSql));
        if (null === $exists) {
            return $this;
        }
        $dropSql = "DROP DATABASE `{$this->config->get(ConfigInterface::PARAM_DB_NAME)}`";
        if (true !== mysqli_query($link, $dropSql)) {
            $this->throwExceptionWithError(
                $link,
                'Failed to drop the database ' . $this->config->get(ConfigInterface::PARAM_DB_NAME)
            );
        }

        return $this;
    }

    private function throwExceptionWithError($link, string $message): void
    {
        $mysqlError = mysqli_errno($link) . ': ' . mysqli_error($link);
        throw new DoctrineStaticMetaException(
            $message . "\n"
            . ' Mysql Error - ' . $mysqlError
        );
    }

    protected function throwUnsure(): void
    {
        throw new InvalidArgumentException('You must pass in `true` to show you are sure');
    }

    /**
     * @return mysqli
     * @throws DoctrineStaticMetaException
     */
    private function connect(): mysqli
    {
        if (null === $this->link) {
            $this->link = mysqli_connect(
                $this->config->get(ConfigInterface::PARAM_DB_HOST),
                $this->config->get(ConfigInterface::PARAM_DB_USER),
                $this->config->get(ConfigInterface::PARAM_DB_PASS)
            );
            if (!$this->link) {
                throw new DoctrineStaticMetaException('Failed getting connection in ' . __METHOD__);
            }
        }

        return $this->link;
    }

    /**
     * You have to pass in true to confirm you really want to do it
     *
     * @param bool $sure
     *
     * @return Database
     * @throws InvalidArgumentException
     * @throws DoctrineStaticMetaException
     */
    public function create(bool $sure): Database
    {
        if (true !== $sure) {
            $this->throwUnsure();
        }
        $link = $this->connect();
        $sql  = 'CREATE DATABASE IF NOT EXISTS `'
                . $this->config->get(ConfigInterface::PARAM_DB_NAME)
                . '` CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci';
        if (true !== mysqli_query($link, $sql)) {
            $this->throwExceptionWithError(
                $link,
                'Failed to create the database ' . $this->config->get(ConfigInterface::PARAM_DB_NAME)
            );
        }

        return $this;
    }

    public function __destruct()
    {
        $this->close();
    }

    public function close(): void
    {
        if (null !== $this->link) {
            mysqli_close($this->link);
            $this->link = null;
        }
    }
}
