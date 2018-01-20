<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Schema;


use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Entity;

class TestSchemaBuilder
{

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * TestSchemaBuilder constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getTestSchemaName()
    {
        $config = $this->entityManager->getConnection()->getConfiguration();
    }

    public function dropAndCreateTestDatabase()
    {

    }
}
