<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Schema;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\SchemaValidator;
use EdmondsCommerce\DoctrineStaticMeta\EntityManager\EntityManagerFactory;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;

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
     * @var SchemaValidator
     */
    protected $schemaValidator;

    /**
     * SchemaBuilder constructor.
     *
     * @param EntityManager   $entityManager
     * @param SchemaTool      $schemaTool
     * @param SchemaValidator $schemaValidator
     */
    public function __construct(EntityManager $entityManager, SchemaTool $schemaTool, SchemaValidator $schemaValidator)
    {
        $this->entityManager   = $entityManager;
        $this->schemaTool      = $schemaTool;
        $this->schemaValidator = $schemaValidator;
    }

    /**
     * Get the name of the database being used by the EntityManager
     *
     * @return string
     */
    public function getDbName(): string
    {
        $database = $this->entityManager->getConnection()->getDatabase();

        return $database;
    }

    public function resetDb()
    {
        $this->schemaTool->dropDatabase();

        return $this;
    }

    /**
     * Get the Entity Configuration Meta Data
     *
     * @return array
     */
    public function getAllMetaData(): array
    {
        $metadata = $this->entityManager->getMetadataFactory()->getAllMetadata();

        return $metadata;
    }

    /**
     * Create all the tables
     *
     * @return SchemaBuilder
     */
    public function create(): SchemaBuilder
    {
        return $this->update();
    }

    /**
     * Update or Create all the tables
     *
     * @return SchemaBuilder
     */
    public function update(): SchemaBuilder
    {
        if (!'cli' === PHP_SAPI) {
            throw new \RuntimeException('This should only be called from the command line');
        }
        $metadata          = $this->getAllMetaData();
        $schemaUpdateSql   = $this->schemaTool->getUpdateSchemaSql($metadata);
        $schemaUpdateCount = count($schemaUpdateSql);
        if ($schemaUpdateCount) {
            $this->schemaTool->updateSchema($metadata);
        }

        return $this;
    }

    /**
     * Validate the configured metadata
     *
     * @return SchemaBuilder
     * @throws DoctrineStaticMetaException
     */
    public function validate(): SchemaBuilder
    {
        $errors = $this->schemaValidator->validateMapping();
        if (!empty($errors)) {
            $allMetaData = $this->getAllMetaData();
            $path        = __DIR__.'/../../var/doctrineMapping.ser';
            file_put_contents(
                $path,
                print_r(
                    [
                        'errors'   => $errors,
                        'metadata' => $allMetaData,
                    ],
                    true
                )
            );
            throw new DoctrineStaticMetaException(
                'Found errors in Doctrine mapping, mapping has been dumped to '.$path."\n\n".print_r(
                    $errors,
                    true
                )
            );
        }

        return $this;
    }
}
