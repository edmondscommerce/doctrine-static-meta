<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Schema;

use Doctrine\ORM\EntityManagerInterface;
use mysqli;

class MysqliConnectionFactory
{
    /**
     * @param EntityManagerInterface $entityManager
     *
     * @return mysqli
     */
    public function createFromEntityManager(EntityManagerInterface $entityManager): mysqli
    {
        $params = $entityManager->getConnection()->getParams();

        $conn = new mysqli($params['host'], $params['user'], $params['password'], $params['dbname']);
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

        return $conn;
    }
}
