<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\EntityManager;

use Doctrine\Common\Persistence\Mapping\Driver\StaticPHPDriver;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools;
use EdmondsCommerce\DoctrineStaticMeta\Config;
use EdmondsCommerce\DoctrineStaticMeta\ConfigInterface;
use EdmondsCommerce\DoctrineStaticMeta\Exception\ConfigException;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use EdmondsCommerce\DoctrineStaticMeta\Schema\SchemaBuilder;
use EdmondsCommerce\DoctrineStaticMeta\SimpleEnv;

class EntityManagerFactory implements EntityManagerFactoryInterface
{
    /**
     * @param ConfigInterface $config
     *
     * @return EntityManager
     * @throws ConfigException
     * @throws DoctrineStaticMetaException
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\ORM\ORMException
     */
    public static function getEntityManager(
        ConfigInterface $config
    ): EntityManager {

        $dbUser         = $config->get(ConfigInterface::PARAM_DB_USER);
        $dbPass         = $config->get(ConfigInterface::PARAM_DB_PASS);
        $dbHost         = $config->get(ConfigInterface::PARAM_DB_HOST);
        $dbName         = $config->get(ConfigInterface::PARAM_DB_NAME);
        $dbEntitiesPath = $config->get(ConfigInterface::PARAM_ENTITIES_PATH);
        $isDbDebug      = $config->get(ConfigInterface::PARAM_DB_DEBUG, true);
        $isDevMode      = $config->get(ConfigInterface::PARAM_DB_DEVMODE, true);

        if (!is_dir($dbEntitiesPath)) {
            throw new ConfigException(" ERROR  Entities path does not exist. You need to either fix the config or create the entities path directory, currently configured as: [".$dbEntitiesPath."] ");
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

        return $entityManager;
    }
}
