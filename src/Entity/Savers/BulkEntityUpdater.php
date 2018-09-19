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

    /**
     * @var float
     */
    private $requireAffectedRatio = 1.0;

    /**
     * @var int
     */
    private $totalAffectedRows = 0;

    public function __construct(EntityManagerInterface $entityManager, MysqliConnectionFactory $mysqliConnectionFactory)
    {
        parent::__construct($entityManager);
        $this->mysqli = $mysqliConnectionFactory->createFromEntityManager($entityManager);
    }

    /**
     * @param float $requireAffectedRatio
     *
     * @return BulkEntityUpdater
     */
    public function setRequireAffectedRatio(float $requireAffectedRatio): BulkEntityUpdater
    {
        $this->requireAffectedRatio = $requireAffectedRatio;

        return $this;
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
            throw new \RuntimeException(' you can not use this updater on entities that have binary keys');
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
     * @return int
     */
    public function getTotalAffectedRows(): int
    {
        return $this->totalAffectedRows;
    }

    protected function doSave(): void
    {
        foreach ($this->entitiesToSave as $entity) {
            if (!$entity instanceof $this->entityFqn || !$entity instanceof EntityInterface) {
                throw new \RuntimeException(
                    'You can only bulk save a single entity type, currently saving ' . $this->entityFqn .
                    ' but you are trying to save ' . \get_class($entity)
                );
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
        $sql .= " where `$primaryKeyCol` = '$primaryKey'; ";

        return $sql;
    }

    private function runQuery(): void
    {
        if ('' === $this->query) {
            return;
        }
        $this->query = "
            SET FOREIGN_KEY_CHECKS = 0; 
            SET UNIQUE_CHECKS = 0;
            SET AUTOCOMMIT = 0; {$this->query} COMMIT;            
            SET FOREIGN_KEY_CHECKS = 1; 
            SET UNIQUE_CHECKS = 1; ";
        $this->mysqli->multi_query($this->query);
        $affectedRows = 0;
        do {
            $affectedRows += max($this->mysqli->affected_rows, 0);
        } while ($this->mysqli->more_results() && $this->mysqli->next_result());
        if ($affectedRows < count($this->entitiesToSave) * $this->requireAffectedRatio) {
            throw new \RuntimeException(
                'Affected rows count of ' . $affectedRows .
                ' does match the expected count of entitiesToSave ' . count($this->entitiesToSave)
            );
        }
        $this->totalAffectedRows += $affectedRows;
    }
}