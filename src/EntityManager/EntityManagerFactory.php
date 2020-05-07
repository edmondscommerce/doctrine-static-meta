<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\EntityManager;

use Doctrine\Common\Cache\Cache;
use Doctrine\Common\Persistence\Mapping\Driver\StaticPHPDriver;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Tools;
use EdmondsCommerce\DoctrineStaticMeta\ConfigInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Factory\EntityFactoryInterface;
use EdmondsCommerce\DoctrineStaticMeta\EntityManager\Decorator\EntityFactoryManagerDecorator;
use EdmondsCommerce\DoctrineStaticMeta\EntityManager\Mapping\ClassMetadataFactoryWithEntityFactories;
use EdmondsCommerce\DoctrineStaticMeta\EntityManager\RetryConnection\PingingAndReconnectingConnection;
use EdmondsCommerce\DoctrineStaticMeta\EntityManager\RetryConnection\ShouldConnectionByRetried;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use Exception;
use Ramsey\Uuid\Doctrine\UuidBinaryOrderedTimeType;
use Ramsey\Uuid\Doctrine\UuidBinaryType;
use Ramsey\Uuid\Doctrine\UuidType;

/**
 * Class EntityManagerFactory
 *
 * @package EdmondsCommerce\DoctrineStaticMeta\EntityManager
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class EntityManagerFactory implements EntityManagerFactoryInterface
{

    /**
     * @var Cache
     */
    protected Cache $cache;
    /**
     * @var EntityFactoryInterface
     */
    protected EntityFactoryInterface $entityFactory;

    public function __construct(Cache $cache, EntityFactoryInterface $entityFactory)
    {
        $this->cache         = $cache;
        $this->entityFactory = $entityFactory;
        $this->registerUuidDbalType();
    }

    /**
     * @throws DBALException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    private function registerUuidDbalType(): void
    {
        if (!Type::hasType(MappingHelper::TYPE_UUID)) {
            Type::addType(MappingHelper::TYPE_UUID, UuidBinaryOrderedTimeType::class);
        }

        if (!Type::hasType(MappingHelper::TYPE_NON_BINARY_UUID)) {
            Type::addType(MappingHelper::TYPE_NON_BINARY_UUID, UuidType::class);
        }

        if (!Type::hasType(MappingHelper::TYPE_NON_ORDERED_BINARY_UUID)) {
            Type::addType(MappingHelper::TYPE_NON_ORDERED_BINARY_UUID, UuidBinaryType::class);
        }
    }

    /**
     * This is used to create a new instance of the entity manager. Each of the steps involved need to take place,
     * however you may wish to make modifications to individual ones. There for the method is final, but the child
     * steps are public and can be overwritten if you extend the class
     *
     * @param ConfigInterface $config
     *
     * @return EntityManagerInterface
     * @throws DoctrineStaticMetaException
     */
    final public function getEntityManager(ConfigInterface $config): EntityManagerInterface
    {
        try {
            $dbParams       = $this->getDbConnectionInfo($config);
            $doctrineConfig = $this->getDoctrineConfig($config);
            $this->addDsmParamsToConfig($doctrineConfig, $config);
            $entityManager = $this->createEntityManager($dbParams, $doctrineConfig);
            $this->addEntityFactories($entityManager);
            $this->setDebuggingInfo($config, $entityManager);

            return $entityManager;
        } catch (Exception $e) {
            $message = 'Exception in ' . __METHOD__ . ': ' . $e->getMessage();

            throw new DoctrineStaticMetaException($message, $e->getCode(), $e);
        }
    }


    /**
     * This is used to get the connection information for doctrine. By default this pulls the information out of the
     * configuration interface, however if you connection information is in a different format you can override this
     * method and set it
     *
     * @param ConfigInterface $config
     *
     * @return array
     */
    public function getDbConnectionInfo(ConfigInterface $config): array
    {
        $dbUser             = $config->get(ConfigInterface::PARAM_DB_USER);
        $dbPass             = $config->get(ConfigInterface::PARAM_DB_PASS);
        $dbHost             = $config->get(ConfigInterface::PARAM_DB_HOST);
        $dbName             = $config->get(ConfigInterface::PARAM_DB_NAME);
        $useRetryConnection = $config->get(ConfigInterface::PARAM_USE_RETRY_CONNECTION);

        $return = [
            'driver'   => 'pdo_mysql',
            'user'     => $dbUser,
            'password' => $dbPass,
            'dbname'   => $dbName,
            'host'     => $dbHost,
            'charset'  => 'utf8mb4',

        ];
        if (true === $useRetryConnection) {
            $return['wrapperClass'] = PingingAndReconnectingConnection::class;
        }

        return $return;
    }

    /**
     * This is used to get the doctrine configuration object. By default this creates a new instance, however if you
     * already have an object configured you can override this method and inject it
     *
     * @param ConfigInterface $config
     *
     * @return Configuration
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function getDoctrineConfig(ConfigInterface $config): Configuration
    {
        $isDevMode = (bool)$config->get(ConfigInterface::PARAM_DEVMODE);
        $proxyDir  = $config->get(ConfigInterface::PARAM_DOCTRINE_PROXY_DIR);
        $cache     = $isDevMode ? null : $this->cache;

        return Tools\Setup::createConfiguration($isDevMode, $proxyDir, $cache);
    }

    /**
     * This is used to add the DSM specific configuration to doctrine Configuration object. You shouldn't need to
     * override this, but if you do you can
     *
     * @param Configuration   $doctrineConfig
     * @param ConfigInterface $config
     */
    public function addDsmParamsToConfig(Configuration $doctrineConfig, ConfigInterface $config): void
    {
        $paths          = $this->getPathInformation($config);
        $namingStrategy = $config->get(ConfigInterface::PARAM_DOCTRINE_NAMING_STRATEGY);
        $driver         = new StaticPHPDriver($paths);
        $doctrineConfig->setMetadataDriverImpl($driver);
        $doctrineConfig->setNamingStrategy($namingStrategy);
    }

    /**
     * By default we only return a single path to the entities, however if you have your entities in multiple places you
     * can override this method and include them all
     *
     * @param ConfigInterface $config
     *
     * @return array
     */
    public function getPathInformation(ConfigInterface $config): array
    {
        $dbEntitiesPath = $config->get(ConfigInterface::PARAM_ENTITIES_PATH);

        return [
            $dbEntitiesPath,
        ];
    }

    /**
     * This is used to create the Entity manager. You can override this if there are any calls you wish to make on it
     * after it has been created
     *
     * @param array         $dbParams
     * @param Configuration $doctrineConfig
     *
     * @return EntityManagerInterface
     * @throws ORMException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function createEntityManager(array $dbParams, Configuration $doctrineConfig): EntityManagerInterface
    {
        $entityManager = EntityManager::create($dbParams, $doctrineConfig);

        return new EntityFactoryManagerDecorator($entityManager);
    }

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function addEntityFactories(EntityManagerInterface $entityManager): void
    {
        if (!$entityManager instanceof EntityFactoryManagerDecorator) {
            return;
        }
        $this->entityFactory->setEntityManager($entityManager);
        $entityManager->addGenericFactory($this->entityFactory);
    }

    /**
     * This is used to set any debugging information, by default it enables MySql logging and clears the log table.
     * Override this method if there is anything else that you need to do
     *
     * @param ConfigInterface        $config
     * @param EntityManagerInterface $entityManager
     *
     * @throws DBALException
     */
    public function setDebuggingInfo(ConfigInterface $config, EntityManagerInterface $entityManager): void
    {
        $isDbDebug = (bool)$config->get(ConfigInterface::PARAM_DB_DEBUG);
        if (false === $isDbDebug) {
            return;
        }
        $connection = $entityManager->getConnection();
        $connection->query(
            "
                set global general_log = 1;
                set global log_output = 'table';
                truncate general_log;
                "
        );
    }
}
