<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Schema;

use EdmondsCommerce\DoctrineStaticMeta\ConfigInterface;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;

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
     * @var ConfigInterface
     */
    private $config;

    /**
     * @var null|\mysqli
     */
    private $link = null;

    public function __construct(ConfigInterface $config)
    {
        $this->config = $config;
    }

    /**
     * @return \mysqli
     * @throws DoctrineStaticMetaException
     */
    private function connect(): \mysqli
    {
        if (null === $this->link) {
            $this->link = mysqli_connect(
                $this->config->get(ConfigInterface::PARAM_DB_HOST),
                $this->config->get(ConfigInterface::PARAM_DB_USER),
                $this->config->get(ConfigInterface::PARAM_DB_PASS)
            );
            if (!$this->link) {
                throw new DoctrineStaticMetaException('Failed getting connection in '.__METHOD__);
            }
        }

        return $this->link;
    }

    protected function throwUnsure(): void
    {
        throw new \InvalidArgumentException('You must pass in `true` to show you are sure');
    }

    /**
     * You have to pass in true to confirm you really want to do it
     *
     * @param bool $sure
     *
     * @return Database
     * @throws \InvalidArgumentException
     * @throws DoctrineStaticMetaException
     */
    public function drop($sure): Database
    {
        if (true !== $sure) {
            $this->throwUnsure();
        }
        $link = $this->connect();
        $sql  = "DROP DATABASE IF EXISTS `{$this->config->get(ConfigInterface::PARAM_DB_NAME)}`";
        mysqli_query($link, $sql);

        return $this;
    }

    /**
     * You have to pass in true to confirm you really want to do it
     *
     * @param bool $sure
     *
     * @return Database
     * @throws \InvalidArgumentException
     * @throws DoctrineStaticMetaException
     */
    public function create($sure): Database
    {
        if (true !== $sure) {
            $this->throwUnsure();
        }
        $link = $this->connect();
        $sql  = 'CREATE DATABASE IF NOT EXISTS `'
                .$this->config->get(ConfigInterface::PARAM_DB_NAME)
                .'` CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci';
        mysqli_query($link, $sql);

        return $this;
    }

    public function close(): void
    {
        if (null !== $this->link) {
            mysqli_close($this->link);
            $this->link = null;
        }
    }

    public function __destruct()
    {
        $this->close();
    }
}
