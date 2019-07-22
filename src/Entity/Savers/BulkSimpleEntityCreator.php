<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Savers;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Factories\UuidFactory;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\BulkEntityUpdater\BulkSimpleEntityCreatorHelper;
use EdmondsCommerce\DoctrineStaticMeta\Schema\MysqliConnectionFactory;
use EdmondsCommerce\DoctrineStaticMeta\Schema\UuidFunctionPolyfill;
use Ramsey\Uuid\Doctrine\UuidBinaryOrderedTimeType;
use Ramsey\Uuid\UuidInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class BulkSimpleEntityCreator extends AbstractBulkProcess
{
    public const INSERT_MODE_INSERT  = 'INSERT ';
    public const INSERT_MODE_IGNORE  = 'INSERT IGNORE ';
    public const INSERT_MODE_DEFAULT = self::INSERT_MODE_INSERT;
    public const INSERT_MODES        = [
        self::INSERT_MODE_INSERT,
        self::INSERT_MODE_IGNORE,
    ];

    /**
     * @var BulkSimpleEntityCreatorHelper
     */
    private $helper;
    /**
     * @var string
     */
    private $tableName;
    /**
     * @var string
     */
    private $entityFqn;
    /**
     * Is the UUID binary
     *
     * @var bool
     */
    private $isBinaryUuid = true;
    /**
     * @var ClassMetadataInfo
     */
    private $meta;
    /**
     * @var string
     */
    private $primaryKeyCol;
    /**
     * @var \mysqli
     */
    private $mysqli;
    /**
     * @var UuidFunctionPolyfill
     */
    private $uuidFunctionPolyfill;
    /**
     * @var UuidFactory
     */
    private $uuidFactory;
    /**
     * @var string
     */
    private $query;
    /**
     * For creation this should always be 100%, so 1
     *
     * @var int
     */
    private $requireAffectedRatio = 1;
    /**
     * @var int
     */
    private $totalAffectedRows = 0;

    private $insertMode = self::INSERT_MODE_DEFAULT;

    public function __construct(
        EntityManagerInterface $entityManager,
        MysqliConnectionFactory $mysqliConnectionFactory,
        UuidFunctionPolyfill $uuidFunctionPolyfill,
        UuidFactory $uuidFactory
    ) {
        parent::__construct($entityManager);
        $this->mysqli               = $mysqliConnectionFactory->createFromEntityManager($entityManager);
        $this->uuidFunctionPolyfill = $uuidFunctionPolyfill;
        $this->uuidFactory          = $uuidFactory;
    }

    public function endBulkProcess(): void
    {
        parent::endBulkProcess();
        // Reset the insert mode to default to prevent state bleeding across batch runs
        $this->setInsertMode(self::INSERT_MODE_DEFAULT);
    }

    /**
     * @param string $insertMode
     *
     * @return BulkSimpleEntityCreator
     */
    public function setInsertMode(string $insertMode): BulkSimpleEntityCreator
    {
        if (false === \in_array($insertMode, self::INSERT_MODES, true)) {
            throw new \InvalidArgumentException('Invalid insert mode');
        }
        $this->insertMode = $insertMode;
        if ($this->insertMode === self::INSERT_MODE_IGNORE) {
            $this->requireAffectedRatio = 0;
        }

        return $this;
    }

    public function addEntityToSave(EntityInterface $entity): void
    {
        throw new \RuntimeException('You should not try to save Entities with this saver');
    }

    public function addEntitiesToSave(array $entities): void
    {
        foreach ($entities as $entityData) {
            if (\is_array($entityData)) {
                $this->addEntityCreationData($entityData);
                continue;
            }
            throw new \InvalidArgumentException('You should only pass in simple arrays of scalar entity data');
        }
    }

    public function addEntityCreationData(array $entityData): void
    {
        $this->entitiesToSave[] = $entityData;
        $this->bulkSaveIfChunkBigEnough();
    }

    public function setHelper(BulkSimpleEntityCreatorHelper $helper): void
    {
        $this->helper        = $helper;
        $this->tableName     = $helper->getTableName();
        $this->entityFqn     = $helper->getEntityFqn();
        $this->meta          = $this->entityManager->getClassMetadata($this->entityFqn);
        $this->primaryKeyCol = $this->meta->getSingleIdentifierFieldName();
        $this->isBinaryUuid  = $this->isBinaryUuid();
        $this->runPolyfillIfRequired();
    }

    private function isBinaryUuid(): bool
    {
        $idMapping = $this->meta->getFieldMapping($this->meta->getSingleIdentifierFieldName());

        return $idMapping['type'] === UuidBinaryOrderedTimeType::NAME;
    }

    private function runPolyfillIfRequired(): void
    {
        if (false === $this->isBinaryUuid) {
            return;
        }
        $this->uuidFunctionPolyfill->run();
    }

    /**
     * As these are not actually entities, lets empty them out before
     * parent::freeResources tries to detach from the entity manager
     */
    protected function freeResources()
    {
        $this->entitiesToSave = [];
        parent::freeResources();
    }

    protected function doSave(): void
    {
        foreach ($this->entitiesToSave as $entityData) {
            $this->appendToQuery($this->buildSql($entityData));
        }
        $this->runQuery();
        $this->reset();
    }

    private function appendToQuery(string $sql)
    {
        $this->query .= "\n$sql";
    }

    private function buildSql(array $entityData): string
    {
        $sql  = $this->insertMode . " into {$this->tableName} set ";
        $sqls = [
            $this->primaryKeyCol . ' = ' . $this->generateId(),
        ];
        foreach ($entityData as $key => $value) {
            if ($key === $this->primaryKeyCol) {
                throw new \InvalidArgumentException(
                    'You should not pass in IDs, they will be auto generated'
                );
            }
            if ($value instanceof UuidInterface) {
                $sqls[] = "`$key` = " . $this->getUuidSql($value);
                continue;
            }
            $value  = $this->mysqli->escape_string((string)$value);
            $sqls[] = "`$key` = '$value'";
        }
        $sql .= implode(', ', $sqls) . ';';

        return $sql;
    }

    private function generateId()
    {
        if ($this->isBinaryUuid) {
            return $this->getUuidSql($this->uuidFactory->getOrderedTimeUuid());
        }

        return $this->getUuidSql($this->uuidFactory->getUuid());
    }

    private function getUuidSql(UuidInterface $uuid)
    {
        if ($this->isBinaryUuid) {
            $uuidString = (string)$uuid;
            return "UUID_TO_BIN('$uuidString', true)";
        }

        throw new \RuntimeException('This is not currently suppported - should be easy enough though');
    }

    private function runQuery(): void
    {
        if ('' === $this->query) {
            return;
        }
        $this->mysqli->ping();
        $this->query = "
           START TRANSACTION;
           SET FOREIGN_KEY_CHECKS = 0; 
           {$this->query}             
           SET FOREIGN_KEY_CHECKS = 1; 
           COMMIT;";
        $result      = $this->mysqli->multi_query($this->query);
        if (true !== $result) {
            throw new \RuntimeException(
                'Multi Query returned false which means the first statement failed: ' .
                $this->mysqli->error
            );
        }
        $affectedRows = 0;
        $queryCount   = 0;
        do {
            $queryCount++;
            $errorNo = (int)$this->mysqli->errno;
            if (0 !== $errorNo) {
                $errorMessage = 'Query #' . $queryCount .
                                ' got MySQL Error #' . $errorNo .
                                ': ' . $this->mysqli->error
                                . "\nQuery: " . $this->getQueryLine($queryCount) . "'\n";
                throw new \RuntimeException($errorMessage);
            }
            $affectedRows += max($this->mysqli->affected_rows, 0);
            if (false === $this->mysqli->more_results()) {
                break;
            }
            $this->mysqli->next_result();
        } while (true);
        if ($affectedRows < count($this->entitiesToSave) * $this->requireAffectedRatio) {
            throw new \RuntimeException(
                'Affected rows count of ' . $affectedRows .
                ' does match the expected count of entitiesToSave ' . count($this->entitiesToSave)
            );
        }
        $this->totalAffectedRows += $affectedRows;
        $this->mysqli->commit();
    }

    private function getQueryLine(int $line): string
    {
        $lines = explode(";\n", $this->query);

        return $lines[$line + 1];
    }

    private function reset(): void
    {
        $this->entitiesToSave = [];
        $this->query          = '';
    }
}
