<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Schema;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Tools\SchemaTool;
use EdmondsCommerce\DoctrineStaticMeta\EntityManager\DevEntityManagerFactory;

class SchemaBuilder
{

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var EntityManager
     */
    protected $testDbEntityManager;

    /**
     * @var SchemaTool
     */
    protected $schemaTool;

    /**
     * SchemaBuilder constructor.
     *
     * @param EntityManager $entityManager
     * @param SchemaTool    $schemaTool
     */
    public function __construct(EntityManager $entityManager, SchemaTool $schemaTool)
    {
        $this->entityManager = $entityManager;
        $this->schemaTool    = $schemaTool;
    }

    public function getDbName(): string
    {
        $database = $this->entityManager->getConnection()->getDatabase();

        return $database;
    }

    public function resetDb()
    {
        $this->schemaTool->dropDatabase();
    }

    public function createTables()
    {
        $metadatas = $this->entityManager->getMetadataFactory()->getAllMetadata();
        $this->schemaTool->createSchema($metadatas);
    }
}
