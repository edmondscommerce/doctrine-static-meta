<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Builder;

use EdmondsCommerce\DoctrineStaticMeta\Config;
use EdmondsCommerce\DoctrineStaticMeta\Container;
use EdmondsCommerce\DoctrineStaticMeta\SimpleEnv;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class BuilderContainer
 *
 * @package EdmondsCommerce\DoctrineStaticMeta\Builder
 * @SuppressWarnings(PHPMD.Superglobals)
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class BuilderContainer
{

    public function setEnvFilePath(string $envPath): self
    {
        SimpleEnv::setEnv($envPath);

        return $this;
    }

    public function setDbName(string $dbName): self
    {
        $_SERVER[Config::PARAM_DB_NAME] = $dbName;

        return $this;
    }

    public function setDbUser(string $dbUser): self
    {
        $_SERVER[Config::PARAM_DB_USER] = $dbUser;

        return $this;
    }

    public function setDbPass(string $dbPass): self
    {
        $_SERVER[Config::PARAM_DB_PASS] = $dbPass;

        return $this;
    }

    public function setDbHost(string $dbHost): self
    {
        $_SERVER[Config::PARAM_DB_HOST] = $dbHost;

        return $this;
    }

    public function setDbConfig(array $config): self
    {
        foreach ($config as $key => $value) {
            $_SERVER[$key] = $value;
        }

        return $this;
    }

    /**
     * @return ContainerBuilder
     * @SuppressWarnings(PHPMD)
     */
    public function getContainer(): ContainerBuilder
    {
        $containerBuilder = new ContainerBuilder();
        $containerBuilder->autowire(Builder::class)->setPublic(true);
        (new Container())->addConfiguration($containerBuilder, $_SERVER);
        $containerBuilder->compile();

        return $containerBuilder;
    }

    /**
     * @return Builder
     * @throws \Exception
     */
    public function getBuilder(): Builder
    {
        /**
         * @var Builder
         */
        $builder = $this->getContainer()->get(Builder::class);

        return $builder;
    }
}
