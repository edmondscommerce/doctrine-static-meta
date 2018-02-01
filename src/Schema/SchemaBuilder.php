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

    protected $schemaTool;

    /**
     * TestSchemaBuilder constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    protected function getSchemaTool(): SchemaTool
    {
        if (!$this->schemaTool) {
            $this->schemaTool = new SchemaTool($this->entityManager);
        }
        return $this->schemaTool;
    }

    public function getDbName(): string
    {
        $database = $this->entityManager->getConnection()->getDatabase();
        return $database;
    }

    public function resetDb()
    {
        $this->getSchemaTool()->dropDatabase();
    }

    public function createTables()
    {
        $metadatas = $this->entityManager->getMetadataFactory()->getAllMetadata();
        $this->getSchemaTool()->createSchema($metadatas);
    }
}
