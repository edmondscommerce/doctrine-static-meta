<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\EntityManager;

use Doctrine\Common\Persistence\Mapping\Driver\StaticPHPDriver;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools;
use EdmondsCommerce\DoctrineStaticMeta\Config;
use EdmondsCommerce\DoctrineStaticMeta\ConfigInterface;
use EdmondsCommerce\DoctrineStaticMeta\Exception\ConfigException;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use EdmondsCommerce\DoctrineStaticMeta\SimpleEnv;

class DevEntityManagerFactory implements EntityManagerFactoryInterface
{

    public static function setupAndGetEm(array $server): EntityManager
    {
        self::setupEnvIfNotSet($server);

        return self::loadConfigAndGetEm($server);
    }

    /**
     * Check for the `dbUser` environment variable.
     * If it is not found then we need to set up our env variables
     * Note - this bit can be customised to your requirements
     *
     * @param array $server
     *
     * @throws ConfigException
     * @throws \ReflectionException
     */
    protected static function setupEnvIfNotSet(array &$server)
    {
        if (!isset($server[ConfigInterface::PARAM_DB_USER])) {
            SimpleEnv::setEnv(Config::getProjectRootDirectory().'/.env', $server);
        }
    }

    protected static function loadConfigAndGetEm(array $server): EntityManager
    {
        $config        = new Config($server);
        $entityManager = self::getEm($config);

        return $entityManager;
    }

    /**
     * @param ConfigInterface $config
     *
     * @return EntityManager
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\ORM\ORMException
     * @throws DoctrineStaticMetaException
     */
    public static function getEm(ConfigInterface $config): EntityManager
    {

        $dbUser         = $config->get(ConfigInterface::PARAM_DB_USER);
        $dbPass         = $config->get(ConfigInterface::PARAM_DB_PASS);
        $dbHost         = $config->get(ConfigInterface::PARAM_DB_HOST);
        $dbName         = $config->get(ConfigInterface::PARAM_DB_NAME);
        $dbEntitiesPath = $config->get(ConfigInterface::PARAM_ENTITIES_PATH);
        $isDbDebug      = $config->get(ConfigInterface::PARAM_DB_DEBUG, true);
        $isDevMode      = $config->get(ConfigInterface::PARAM_DB_DEVMODE, true);

        if (!is_dir($dbEntitiesPath)) {
            throw new ConfigException(" ERROR  Entities path does not exist-  you need to either fix the config or create the entites path directory, currently configured as: [".$dbEntitiesPath."] ");
        }

        $paths = [
            $dbEntitiesPath,
        ];

        $dbParams = [
            'driver'   => 'pdo_mysql',
            'user'     => $dbUser,
            'password' => $dbPass,
            'dbname'   => $dbName,
            'host'     => $dbHost,
            'charset'  => 'utf8mb4',

        ];

        $doctrineConfig = Tools\Setup::createConfiguration(
            $isDevMode
        );
        $driver         = new StaticPHPDriver($paths);
        $doctrineConfig->setMetadataDriverImpl($driver);

        $entityManager = EntityManager::create($dbParams, $doctrineConfig);
        $connection    = $entityManager->getConnection();
        if ($isDbDebug) {
            $connection->query(
                "
                set global general_log = 1;
                set global log_output = 'table';
                truncate general_log;
                "
            );
        }

        if (true === $isDevMode) {
            $validator   = new Tools\SchemaValidator($entityManager);
            $errors      = $validator->validateMapping();
            $allMetaData = $entityManager->getMetadataFactory()->getAllMetadata();
            if (!empty($errors)) {
                $mappingPath = __DIR__.'/../../var/doctrineMapping.ser';
                file_put_contents($mappingPath, print_r($allMetaData, true));
                throw new DoctrineStaticMetaException(
                    'Found errors in doctring mapping, mapping has been dumped to '.$mappingPath."\n\n".print_r(
                        $errors,
                        true
                    )
                );
            }
            $schemaTool        = new Tools\SchemaTool($entityManager);
            $schemaUpdateSql   = $schemaTool->getUpdateSchemaSql($allMetaData);
            $schemaUpdateCount = count($schemaUpdateSql);
            if ($schemaUpdateCount && 'cli' === PHP_SAPI) {
                $schemaTool->updateSchema($allMetaData);
            }
        }

        return $entityManager;
    }
}
