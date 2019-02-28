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
 *
 * NOTE - the native mysql 8 functions take a second parameter of true to create ordered UUIDs. The polyfill will
 * always create ordered UUIDs
 *
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
        if (\ts\stringStartsWith($this->getVersion(), '8')) {
            return;
        }
        $this->checkProcedureExists(self::UUID_TO_BIN) ?: $this->createProcedureUuidToBin();
        $this->checkProcedureExists(self::BIN_TO_UUID) ?: $this->createProcedureBinToUuid();
    }

    public function getVersion(): string
    {
        $stmt = $this->conn->prepare("select version()");
        $stmt->execute();

        return (string)$stmt->fetchColumn();
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
CREATE FUNCTION ' . self::UUID_TO_BIN . '(_uuid BINARY(36), _ordered BOOL)
	RETURNS BINARY(16)
	LANGUAGE SQL  DETERMINISTIC  CONTAINS SQL  SQL SECURITY INVOKER
	BEGIN            
		IF _ordered
		THEN
			RETURN UNHEX(CONCAT(
		        SUBSTR(_uuid, 15, 4),
		        SUBSTR(_uuid, 10, 4),
		        SUBSTR(_uuid,  1, 8),
		        SUBSTR(_uuid, 20, 4),
		        SUBSTR(_uuid, 25)));
		ELSE
			RETURN UNHEX(CONCAT(
		    	LEFT(_uuid, 8), 
		    	MID(_uuid, 10, 4), 
		    	MID(_uuid, 15, 4), 
		    	MID(_uuid, 20, 4), 
		    	RIGHT(_uuid, 12)));         
		END IF;
	END;
        ');
    }

    public function createProcedureBinToUuid(): void
    {
        $this->conn->query("
CREATE FUNCTION ' . self::BIN_TO_UUID . '(_bin BINARY(16), _ordered BOOL)
	RETURNS BINARY(36)
        LANGUAGE SQL  DETERMINISTIC  CONTAINS SQL  SQL SECURITY INVOKER
    BEGIN
		DECLARE HEX CHAR(32);
		SET HEX = HEX(_bin);
		IF _ordered
		THEN
		    RETURN
		        LCASE(CONCAT_WS('-',
	                HEX(SUBSTR(_bin,  5, 4)),
	                HEX(SUBSTR(_bin,  3, 2)),
	                HEX(SUBSTR(_bin,  1, 2)),
	                HEX(SUBSTR(_bin,  9, 2)),
	                HEX(SUBSTR(_bin, 11))
		        ));
	   	ELSE       	
			RETURN 
				LOWER(CONCAT_WS('-',
					LEFT(HEX, 8), 
					MID(HEX, 9,4),
					MID(HEX, 13,4),
					MID(HEX, 17,4),
					RIGHT(HEX, 12)
				));
		   
	   	END IF;
	END;
        ");
    }
}
