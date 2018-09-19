<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Savers;

use Doctrine\ORM\EntityManagerInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\PrimaryKey\UuidFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\BulkEntityUpdater\BulkEntityUpdateHelper;
use EdmondsCommerce\DoctrineStaticMeta\Schema\MysqliConnectionFactory;
use ts\Reflection\ReflectionClass;

class BulkEntityUpdater extends AbstractBulkProcess
{
    public const QUERY_MODE_MULTI = 'multi';
    public const QUERY_MODE_ASYNC = 'async';
    /**
     * @var BulkEntityUpdateHelper
     */
    private $extractor;
    /**
     * @var string
     */
    private $tableName;
    /**
     * @var string
     */
    private $entityFqn;
    /**
     * @var \mysqli
     */
    private $mysqli;
    /**
     * This holds the bulk SQL query
     *
     * @var string
     */
    private $query;
    private $queryMode = self::QUERY_MODE_MULTI;

    public function __construct(EntityManagerInterface $entityManager, MysqliConnectionFactory $mysqliConnectionFactory)
    {
        parent::__construct($entityManager);
        $this->mysqli = $mysqliConnectionFactory->createFromEntityManager($entityManager);
    }

    public function addEntityToSave(EntityInterface $entity)
    {
        if (false === $entity instanceof $this->entityFqn) {
            throw new \RuntimeException('You can only bulk save a single entity type, currently saving ' .
                                        $this->entityFqn .
                                        ' but you are trying to save ' .
                                        \get_class($entity));
        }
        parent::addEntityToSave($entity);
    }


    public function addEntitiesToSave(array $entities)
    {
        foreach ($entities as $entity) {
            if (false === $entity instanceof $this->entityFqn) {
                throw new \RuntimeException('You can only bulk save a single entity type, currently saving ' .
                                            $this->entityFqn .
                                            ' but you are trying to save ' .
                                            \get_class($entity));
            }
        }
        parent::addEntitiesToSave($entities);
    }


    public function setExtractor(BulkEntityUpdateHelper $extractor): void
    {
        $this->extractor = $extractor;
        $this->tableName = $extractor->getTableName();
        $this->entityFqn = $extractor->getEntityFqn();
        $this->ensureNotBinaryId();
    }

    private function ensureNotBinaryId()
    {
        $traits = (new ReflectionClass($this->entityFqn))->getTraits();
        if (array_key_exists(UuidFieldTrait::class, $traits)) {
            throw new \RuntimeException(' you can not use the updater on entities that have binary keys');
        }
    }

    public function startBulkProcess(): AbstractBulkProcess
    {
        if (!$this->extractor instanceof BulkEntityUpdateHelper) {
            throw new \RuntimeException(
                'You must call setExtractor with your extractor logic before starting the process. '
                . 'Note - a small anonymous class would be ideal'
            );
        }
        $this->resetQuery();

        return parent::startBulkProcess();
    }

    private function resetQuery()
    {
        $this->query = '';
    }

    /**
     * @param string $queryMode
     *
     * @return BulkEntityUpdater
     */
    public function setQueryMode(string $queryMode): BulkEntityUpdater
    {
        if (!\in_array($queryMode, [self::QUERY_MODE_MULTI, self::QUERY_MODE_ASYNC], true)) {
            throw new \InvalidArgumentException('Invalid query mode ' . $queryMode);
        }
        $this->queryMode = $queryMode;

        return $this;
    }

    protected function doSave(): void
    {
        foreach ($this->entitiesToSave as $entity) {
            if (!$entity instanceof $this->entityFqn || !$entity instanceof EntityInterface) {
                throw new \LogicException('Invalid entity, should only be instances of ' . $this->entityFqn);
            }
            $this->appendToQuery(
                $this->convertExtractedToSqlRow(
                    $this->extractor->extract($entity)
                )
            );
        }
        $this->runQuery();
        $this->resetQuery();
    }

    private function appendToQuery(string $sql)
    {
        $this->query .= "\n$sql";
    }

    /**
     * Take the extracted array and build an update query
     *
     * @param array $extracted
     *
     * @return string
     */
    private function convertExtractedToSqlRow(array $extracted): string
    {
        if ([] === $extracted) {
            throw new \RuntimeException('Extracted array is empty in ' . __METHOD__);
        }
        $primaryKeyCol = null;
        $primaryKey    = null;
        $sql           = "update `{$this->tableName}` set ";
        $sqls          = [];
        foreach ($extracted as $key => $value) {
            if (null === $primaryKeyCol) {
                $primaryKeyCol = $key;
                $primaryKey    = $value;
                continue;
            }
            $value  = $this->mysqli->escape_string((string)$value);
            $sqls[] = "`$key` = '$value'";
        }
        $sql .= implode(",\n", $sqls);
        $sql .= " where `$primaryKeyCol` = $primaryKey; ";

        return $sql;
    }

    private function runQuery(): void
    {
        if ('' === $this->query) {
            return;
        }
        if (self::QUERY_MODE_ASYNC === $this->queryMode) {

        }

        $this->mysqli->multi_query("
            SET AUTOCOMMIT = 0; 
            SET FOREIGN_KEY_CHECKS = 0; 
            SET UNIQUE_CHECKS = 0;
            {$this->query}
            COMMIT;            
            SET FOREIGN_KEY_CHECKS = 1; 
            SET UNIQUE_CHECKS = 1;
            "
        );
    }

    private function runQueryAsync()
    {
        foreach (explode(';', $this->query) as $query) {
            $this->mysqli->query($query, MYSQLI_ASYNC);
        }
    }
}