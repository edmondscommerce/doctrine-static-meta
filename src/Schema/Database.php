<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Schema;

use EdmondsCommerce\DoctrineStaticMeta\ConfigInterface;

/**
 * Class Database
 *
 * Drop and Create the Actual Database
 *
 * @package EdmondsCommerce\DoctrineStaticMeta\Schema
 */
class Database
{
    private $config;

    private $link = null;

    public function __construct(ConfigInterface $config)
    {
        $this->config = $config;
    }

    /**
     * @return \mysqli
     * @throws \Exception
     */
    private function connect()
    {
        if (null == $this->link) {
            $this->link = mysqli_connect($_SERVER['dbHost'], $_SERVER['dbUser'], $_SERVER['dbPass']);
            if (!$this->link) {
                throw new \Exception('Failed getting connection in ' . __METHOD__);
            }
        }
        return $this->link;
    }

    public function drop($sure = true)
    {
        if (!$sure) {
            return;
        }
        $link = $this->connect();
        mysqli_query($link, "DROP DATABASE IF EXISTS `{$this->config->get(ConfigInterface::paramDbName)}`");
    }

    public function create($sure = true)
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
    }

    public function close()
    {
        if ($this->link) {
            mysqli_close($this->link);
            $this->link = null;
        }
    }

    public function __destruct()
    {
        $this->close();
    }
}
