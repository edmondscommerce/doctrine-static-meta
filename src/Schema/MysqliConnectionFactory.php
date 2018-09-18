<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Schema;

use Doctrine\ORM\EntityManagerInterface;

class MysqliConnectionFactory
{
    public function createFromEntityManager(EntityManagerInterface $entityManager)
    {
        $params = $entityManager->getConnection()->getParams();

        return new \mysqli($params['host'], $params['user'], $params['password'], $params['dbname']);
    }
}