<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Savers;

use Doctrine\ORM\EntityManagerInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\BulkEntityUpdater\BulkEntityUpdateHelper;
use EdmondsCommerce\DoctrineStaticMeta\Schema\MysqliConnectionFactory;
use EdmondsCommerce\DoctrineStaticMeta\Schema\UuidFunctionPolyfill;
use mysqli;
use Ramsey\Uuid\Doctrine\UuidBinaryOrderedTimeType;
use Ramsey\Uuid\UuidInterface;
use RuntimeException;

use function get_class;

class BulkEntityUpdater extends AbstractBulkProcess
{
    /**
     * @var BulkEntityUpdateHelper
     */
    private BulkEntityUpdateHelper $extractor;
    /**
     * @var string
     */
    private string $tableName;
    /**
     * @var string
     */
    private string $entityFqn;
    /**
     * @var mysqli
     */
    private mysqli $mysqli;
    /**
     * This holds the bulk SQL query
     *
     * @var string
     */
    private string $query;

    /**
     * @var float
     */
    private float $requireAffectedRatio = 1.0;

    /**
     * @var int
     */
    private int $totalAffectedRows = 0;
    /**
     * @var UuidFunctionPolyfill
     */
    private UuidFunctionPolyfill $uuidFunctionPolyfill;

    /**
     * Is the UUID binary
     *
     * @var bool
     */
    private bool $isBinaryUuid = true;

    public function __construct(
        EntityManagerInterface $entityManager,
        UuidFunctionPolyfill $uuidFunctionPolyfill,
        MysqliConnectionFactory $mysqliConnectionFactory
    ) {
        parent::__construct($entityManager);
        $this->uuidFunctionPolyfill = $uuidFunctionPolyfill;
        $this->mysqli               = $mysqliConnectionFactory->createFromEntityManager($entityManager);
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
            throw new RuntimeException('You can only bulk save a single entity type, currently saving ' .
                                        $this->entityFqn .
                                        ' but you are trying to save ' .
                                        get_class($entity));
        }
        parent::addEntityToSave($entity);
    }

    public function setExtractor(BulkEntityUpdateHelper $extractor): void
    {
        $this->extractor    = $extractor;
        $this->tableName    = $extractor->getTableName();
        $this->entityFqn    = $extractor->getEntityFqn();
        $this->isBinaryUuid = $this->isBinaryUuid();
        $this->runPolyfillIfRequired();
    }

    private function isBinaryUuid(): bool
    {
        $meta      = $this->entityManager->getClassMetadata($this->entityFqn);
        $idMapping = $meta->getFieldMapping($meta->getSingleIdentifierFieldName());

        return $idMapping['type'] === UuidBinaryOrderedTimeType::NAME;
    }

    private function runPolyfillIfRequired(): void
    {
        if (false === $this->isBinaryUuid) {
            return;
        }
        $this->uuidFunctionPolyfill->run();
    }

    public function startBulkProcess(): AbstractBulkProcess
    {
        if (!$this->extractor instanceof BulkEntityUpdateHelper) {
            throw new RuntimeException(
                'You must call setExtractor with your extractor logic before starting the process. '
                . 'Note - a small anonymous class would be ideal'
            );
        }
        $this->resetQuery();

        return parent::startBulkProcess();
    }

    private function resetQuery(): void
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
                throw new RuntimeException(
                    'You can only bulk save a single entity type, currently saving ' . $this->entityFqn .
                    ' but you are trying to save ' . get_class($entity)
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

    private function appendToQuery(string $sql): void
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
            throw new RuntimeException('Extracted array is empty in ' . __METHOD__);
        }
        $primaryKeyCol = null;
        $primaryKey    = null;
        $sql           = "update `{$this->tableName}` set ";
        $sqls          = [];
        foreach ($extracted as $key => $value) {
            if (null === $primaryKeyCol) {
                $primaryKeyCol = $key;
                $primaryKey    = $this->convertUuidToSqlString($value);
                continue;
            }
            $value  = $this->mysqli->escape_string((string)$value);
            $sqls[] = "`$key` = '$value'";
        }
        $sql .= implode(",\n", $sqls);
        $sql .= " where `$primaryKeyCol` = $primaryKey; ";

        return $sql;
    }

    private function convertUuidToSqlString(UuidInterface $uuid): string
    {
        $uuidString = (string)$uuid;
        if (false === $this->isBinaryUuid) {
            return "'$uuidString'";
        }

        return UuidFunctionPolyfill::UUID_TO_BIN . "('$uuidString', true)";
    }

    private function runQuery(): void
    {
        if ('' === $this->query) {
            return;
        }
        $this->query = "
           START TRANSACTION;
           SET FOREIGN_KEY_CHECKS = 0; 
           SET UNIQUE_CHECKS = 0;
           {$this->query}             
           SET FOREIGN_KEY_CHECKS = 1; 
           SET UNIQUE_CHECKS = 1;
           COMMIT;";
        $result      = $this->mysqli->multi_query($this->query);
        if (true !== $result) {
            throw new RuntimeException(
                'Multi Query returned false which means the first statement failed: ' .
                $this->mysqli->error
            );
        }
        $affectedRows = 0;
        $queryCount   = 0;
        do {
            $queryCount++;
            if (0 !== $this->mysqli->errno) {
                throw new RuntimeException(
                    'Query #' . $queryCount .
                    ' got MySQL Error #' . $this->mysqli->errno .
                    ': ' . $this->mysqli->error
                    . "\nQuery: " . $this->getQueryLine($queryCount) . "'\n"
                );
            }
            $affectedRows += max($this->mysqli->affected_rows, 0);
            if (false === $this->mysqli->more_results()) {
                break;
            }
            $this->mysqli->next_result();
        } while (true);
        if ($affectedRows < count($this->entitiesToSave) * $this->requireAffectedRatio) {
            throw new RuntimeException(
                'Affected rows count of ' . $affectedRows .
                ' does match the expected count of entitiesToSave ' . count($this->entitiesToSave)
            );
        }
        $this->totalAffectedRows += $affectedRows;
        $this->mysqli->commit();
    }

    private function getQueryLine(int $line): string
    {
        $lines = explode(';', $this->query);

        return $lines[$line + 1];
    }
}
