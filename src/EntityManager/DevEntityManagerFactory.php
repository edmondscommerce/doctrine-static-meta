<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\EntityManager;

use Doctrine\Common\Persistence\Mapping\Driver\StaticPHPDriver;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools;

class DevEntityManagerFactory implements EntityManagerFactoryInterface
{

    /**
     * @param bool $checkSchema
     *
     * @return EntityManager
     * @throws \Exception
     */
    public function getEm(bool $checkSchema = true): EntityManager
    {

        $dbUser    = $_SERVER['dbUser'];
        $dbPass    = $_SERVER['dbPass'];
        $dbHost    = $_SERVER['dbHost'];
        $dbName    = $_SERVER['dbName'];
        $dbDebug   = true;
        $isDevMode = true;

        $paths = [
            $_SERVER['dbEntitiesPath'],
        ];

        $dbParams = [
            'driver'   => 'pdo_mysql',
            'user'     => $dbUser,
            'password' => $dbPass,
            'dbname'   => $dbName,
            'host'     => $dbHost,
            'charset'  => 'utf8mb4',

        ];

        $config = Tools\Setup::createConfiguration(
            $isDevMode
        );
        $driver = new StaticPHPDriver($paths);
        $config->setMetadataDriverImpl($driver);

        $entityManager = EntityManager::create($dbParams, $config);
        $connection    = $entityManager->getConnection();
        if ($dbDebug) {
            $connection->query(
                "
                set global general_log = 1;
                set global log_output = 'table';
                truncate general_log;
                "
            );
        }

        if (true === $isDevMode) {
            $validator = new Tools\SchemaValidator($entityManager);
            $errors    = $validator->validateMapping();
            if (!empty($errors)) {
                $cmf         = $entityManager->getMetadataFactory();
                $classes     = $cmf->getAllMetadata();
                $mappingPath = __DIR__.'/../../var/doctrineMapping.ser';
                file_put_contents($mappingPath, print_r($classes, true));
                throw new \Exception(
                    'Found errors in doctring mapping, mapping has been dumped to '.$mappingPath."\n\n".print_r(
                        $errors,
                        true
                    )
                );
            }
        }
        if (true === $checkSchema) {
            $schemaTool        = new Tools\SchemaTool($entityManager);
            $schemaUpdateSql   = $schemaTool->getUpdateSchemaSql(
                $entityManager->getMetadataFactory()->getAllMetadata(),
                true
            );
            $schemaUpdateCount = count($schemaUpdateSql);
            if ($schemaUpdateCount) {
                throw new \Exception(
                    'Database Schema '.$dbName.' Not In Sync with Doctrine Meta Data '
                    .$schemaUpdateCount.' Queries - Please Update'
                );
            }
        }

        return $entityManager;
    }
}
