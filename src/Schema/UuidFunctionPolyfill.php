<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Schema;

use Doctrine\ORM\EntityManagerInterface;

/**
 * @see http://mysql.rjweb.org/doc.php/uuid
 * @see https://mysqlserverteam.com/mysql-8-0-uuid-support/
 *
 * This class handles ensuring that we have functions for converting to and from binary UUID formats in MySQL
 *
 * In MySQL8, these functions come built in, for below this version though we need to make our own
 */
class UuidFunctionPolyfill
{
    public const UUID_TO_BIN = 'UUID_TO_BIN';

    public const BIN_TO_UUID = 'BIN_TO_UUID';

    /**
     * @var \Doctrine\DBAL\Connection
     */
    private $conn;
    /**
     * @var string
     */
    private $dbName;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->conn   = $entityManager->getConnection();
        $this->dbName = $entityManager->getConnection()->getParams()['dbname'];
    }

    public function run(): void
    {
        $this->checkProcedureExists(self::UUID_TO_BIN) ?: $this->createProcedureUuidToBin();
        $this->checkProcedureExists(self::BIN_TO_UUID) ?: $this->createProcedureBinToUuid();
    }

    public function checkProcedureExists(string $name): bool
    {
        $stmt = $this->conn->prepare("
        SELECT 1 
        FROM mysql.proc 
        WHERE type='FUNCTION' 
        AND db=? 
        AND specific_name=?
        ");
        $stmt->execute([$this->dbName, $name]);
        $result = $stmt->fetchColumn();

        return $result === '1';
    }

    public function createProcedureUuidToBin(): void
    {
        $this->conn->query('
            CREATE FUNCTION ' . self::UUID_TO_BIN . '(_uuid BINARY(36))
                RETURNS BINARY(16)
                LANGUAGE SQL  DETERMINISTIC  CONTAINS SQL  SQL SECURITY INVOKER
            RETURN
                UNHEX(CONCAT(
                    SUBSTR(_uuid, 15, 4),
                    SUBSTR(_uuid, 10, 4),
                    SUBSTR(_uuid,  1, 8),
                    SUBSTR(_uuid, 20, 4),
                    SUBSTR(_uuid, 25) ));
        ');
    }

    public function createProcedureBinToUuid(): void
    {
        $this->conn->query('
        CREATE FUNCTION ' . self::BIN_TO_UUID . "(_bin BINARY(16))
            RETURNS BINARY(36)
            LANGUAGE SQL  DETERMINISTIC  CONTAINS SQL  SQL SECURITY INVOKER
        RETURN
            LCASE(CONCAT_WS('-',
                HEX(SUBSTR(_bin,  5, 4)),
                HEX(SUBSTR(_bin,  3, 2)),
                HEX(SUBSTR(_bin,  1, 2)),
                HEX(SUBSTR(_bin,  9, 2)),
                HEX(SUBSTR(_bin, 11))
                     ));
        ");
    }
}