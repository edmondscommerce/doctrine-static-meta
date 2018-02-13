<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\EntityManager;

use Doctrine\Common\Persistence\Mapping\Driver\StaticPHPDriver;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools;
use EdmondsCommerce\DoctrineStaticMeta\ConfigInterface;
use EdmondsCommerce\DoctrineStaticMeta\Exception\ConfigException;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;

class EntityManagerFactory implements EntityManagerFactoryInterface
{
    /**
     * @param ConfigInterface $config
     *
     * @return EntityManager
     * @throws DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function getEntityManager(
        ConfigInterface $config
    ): EntityManager {
        try {
            $dbUser         = $config->get(ConfigInterface::PARAM_DB_USER);
            $dbPass         = $config->get(ConfigInterface::PARAM_DB_PASS);
            $dbHost         = $config->get(ConfigInterface::PARAM_DB_HOST);
            $dbName         = $config->get(ConfigInterface::PARAM_DB_NAME);
            $dbEntitiesPath = $config->get(ConfigInterface::PARAM_ENTITIES_PATH);
            $isDbDebug      = $config->get(ConfigInterface::PARAM_DB_DEBUG);
            $isDevMode      = $config->get(ConfigInterface::PARAM_DOCTRINE_DEVMODE);
            $proxyDir       = $config->get(ConfigInterface::PARAM_DOCTRINE_PROXY_DIR);

            if (!is_dir($dbEntitiesPath)) {
                throw new ConfigException(
                    ' ERROR  Entities path does not exist. '
                    .'You need to either fix the config or create the entities path directory, '
                    .'currently configured as: ['.$dbEntitiesPath.'] '
                );
            }

            if (!is_dir($proxyDir)) {
                throw new ConfigException(
                    'ERROR  ProxyDir does not exist. '
                    .'You need to either fix the config or create the directory, '
                    .'currently configured as: ['.$proxyDir.'] '
                );
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
                $isDevMode,
                $proxyDir
            );
            $driver         = new StaticPHPDriver($paths);
            $doctrineConfig->setMetadataDriverImpl($driver);

            $entityManager = EntityManager::create($dbParams, $doctrineConfig);
            $connection    = $entityManager->getConnection();
            if (true === $isDbDebug) {
                $connection->query(
                    "
                set global general_log = 1;
                set global log_output = 'table';
                truncate general_log;
                "
                );
            }

            return $entityManager;
        } catch (\Exception $e) {
            throw new DoctrineStaticMetaException('Exception in '.__METHOD__.': '.$e->getMessage(), $e->getCode(), $e);
        }
    }
}
