<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Schema;

use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\SchemaValidator;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;

class Schema
{

    /**
     * @var EntityManager
     */
    protected $entityManager;

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
     * Remove all tables
     *
     * @return Schema
     */
    public function reset(): Schema
    {
        $this->schemaTool->dropDatabase();

        return $this;
    }

    /**
     * Create all tables
     *
     * @return Schema
     * @throws \RuntimeException
     * @throws DoctrineStaticMetaException
     */
    public function create(): Schema
    {
        return $this->update();
    }

    /**
     * Update or Create all tables
     *
     * @return Schema
     * @throws \RuntimeException
     * @throws DoctrineStaticMetaException
     */
    public function update(): Schema
    {
        if ('cli' !== PHP_SAPI) {
            throw new \RuntimeException('This should only be called from the command line');
        }
        $metadata        = $this->getAllMetaData();
        $schemaUpdateSql = $this->schemaTool->getUpdateSchemaSql($metadata);
        if (0 !== count($schemaUpdateSql)) {
            $connection = $this->entityManager->getConnection();
            foreach ($schemaUpdateSql as $sql) {
                try {
                    $connection->executeQuery($sql);
                } catch (DBALException $e) {
                    throw new DoctrineStaticMetaException(
                        "exception running update sql:\n$sql\n",
                        $e->getCode(),
                        $e
                    );
                }
            }
        }
        $this->generateProxies();

        return $this;
    }

    /**
     * Get the Entity Configuration Meta Data
     *
     * @return array
     */
    protected function getAllMetaData(): array
    {
        return $this->entityManager->getMetadataFactory()->getAllMetadata();
    }

    /**
     * @return Schema
     */
    public function generateProxies(): Schema
    {
        $metadata = $this->getAllMetaData();
        $this->entityManager->getProxyFactory()->generateProxyClasses($metadata);

        return $this;
    }

    /**
     * Validate the configured mapping metadata
     *
     * @return Schema
     * @throws DoctrineStaticMetaException
     */
    public function validate(): Schema
    {
        $errors = $this->schemaValidator->validateMapping();
        if (!empty($errors)) {
            $allMetaData = $this->getAllMetaData();
            $mappingPath = __DIR__ . '/../../var/doctrineMapping.ser';
            file_put_contents($mappingPath, print_r($allMetaData, true));
            throw new DoctrineStaticMetaException(
                'Found errors in Doctrine mapping, mapping has been dumped to ' . $mappingPath . "\n\n" . print_r(
                    $errors,
                    true
                )
            );
        }

        return $this;
    }
}
