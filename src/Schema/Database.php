<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Schema;

use EdmondsCommerce\DoctrineStaticMeta\ConfigInterface;

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
     * @throws \Exception
     */
    private function connect(): \mysqli
    {
        if (null === $this->link) {
            $this->link = mysqli_connect(
                $this->config->get(ConfigInterface::paramDbHost),
                $this->config->get(ConfigInterface::paramDbUser),
                $this->config->get(ConfigInterface::paramDbPass)
            );
            if (!$this->link) {
                throw new \Exception('Failed getting connection in ' . __METHOD__);
            }
        }
        return $this->link;
    }

    public function drop($sure = true): Database
    {
        if (!$sure) {
            return;
        }
        $link = $this->connect();
        mysqli_query($link, "DROP DATABASE IF EXISTS `{$this->config->get(ConfigInterface::paramDbName)}`");
        return $this;
    }

    public function create($sure = true): Database
    {
        if (!$sure) {
            return;
        }
        $link = $this->connect();
        mysqli_query(
            $link,
            "CREATE DATABASE IF NOT EXISTS `" . $this->config->get(ConfigInterface::paramDbName)
            . "` CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci"
        );
        return $this;
    }

    public function close()
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
