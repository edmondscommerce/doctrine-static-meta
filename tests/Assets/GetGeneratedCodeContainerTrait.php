<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Assets;

use EdmondsCommerce\DoctrineStaticMeta\Config;
use EdmondsCommerce\DoctrineStaticMeta\ConfigInterface;
use EdmondsCommerce\DoctrineStaticMeta\Container;
use EdmondsCommerce\DoctrineStaticMeta\SimpleEnv;
use Exception;
use RuntimeException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\FileLoader;

/**
 * During testing you may need to access the generated classes that are created by the test. If they have several
 * dependencies, manually creating these can become tedious and ties you test to the current implementation of not just
 * your class, but each of the dependencies as well.
 *
 * Rather than having to do this, or create factories which present similar problems, it is easier to create a
 * container
 * for the generated code and then access the class that way.
 *
 * This trait does the following:
 *
 *  - Creates a new Symfony DI File loader
 *  - Autowires and sets as public every class in the generated directory
 *  - Adds in the default DSM DI configuration so we have access to those classes as well
 *  - Provides a method to fetch a class from the new container.
 *
 * This functionality is being included within a trait because it is expensive to generate this, so it should only be
 * brought in as and when needed
 */
trait GetGeneratedCodeContainerTrait
{
    /**
     * @var array|ContainerBuilder[]
     */
    private array $generatedContainerClass = [];

    /**
     * Use this to get a generated class from a custom container build just for your test
     *
     * @param string $className
     *
     * @return object
     * @throws Exception
     */
    public function getGeneratedClass(string $className): object
    {
        /* If we don't have these two properties nothing is going to work */
        if (!isset($this->copiedWorkDir, $this->copiedRootNamespace)) {
            throw new RuntimeException('Required properties are not set');
        }

        return $this->getContainerForNamespace($this->copiedRootNamespace)->get(trim($className, '\\'));
    }

    /**
     * As the code is generated for each and every test, it is unlikely that this will need to be reused. However, if
     * you do need access to two different classes in the same test, it would be wasteful to build the container twice.
     *
     * Therefore we will cache the container based on the generated namespace, meaning that getting a second class in
     * the same test should be quick
     *
     * @param string $namespace
     *
     * @return ContainerBuilder
     * @throws Exception
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    private function getContainerForNamespace(string $namespace): ContainerBuilder
    {
        if (isset($this->generatedContainerClass[$namespace])) {
            return $this->generatedContainerClass[$namespace];
        }

        $containerBuilder = new ContainerBuilder();
        $pathToFiles      = $this->copiedWorkDir . '/src/';
        $fileLocator      = new FileLocator($pathToFiles);

        $loader = new class ($containerBuilder, $fileLocator, $namespace, $pathToFiles) extends FileLoader
        {
            /**
             * @var string
             */
            private string $namespace;
            /**
             * @var string
             */
            private string|array $pathToFiles;

            public function __construct(
                ContainerBuilder $container,
                FileLocatorInterface $locator,
                string $namespace,
                string $pathToFiles
            ) {
                parent::__construct($container, $locator);
                $this->namespace   = $namespace . '\\';
                $this->pathToFiles = str_replace('//', '/', $pathToFiles . '/*');
            }

            /**
             * Loads a resource.
             *
             * @param mixed       $resource The resource
             * @param string|null $type     The resource type or null if unknown
             *
             * @throws Exception If something went wrong
             *
             */
            public function load($resource, $type = null): void
            {
                $definition = new Definition();

                $definition->setAutowired(true)->setAutoconfigured(true)->setPublic(true);
                $this->registerClasses($definition, $this->namespace, $this->pathToFiles);
                $dsmContainer = new Container();
                $dsmContainer->addConfiguration($this->container, $this->buildServerConfig());
                $this->container->compile();
            }

            /**
             * @return array
             */
            private function buildServerConfig(): array
            {
                SimpleEnv::setEnv(Config::getProjectRootDirectory() . '/.env');
                $testConfig                                 = $_SERVER;
                $testConfig[ConfigInterface::PARAM_DB_NAME] .= '_test';
                $testConfig[ConfigInterface::PARAM_DEVMODE] = true;

                return $testConfig;
            }

            /**
             * Returns whether this class supports the given resource.
             *
             * @param mixed       $resource A resource
             * @param string|null $type     The resource type or null if unknown
             *
             * @return bool True if this class supports the given resource, false otherwise
             */
            public function supports($resource, $type = null): bool
            {
                return true;
            }

            public function getContainer(): ContainerBuilder
            {
                return $this->container;
            }
        };

        $loader->load('required by the interface');

        $this->generatedContainerClass[$namespace] = $loader->getContainer();

        return $this->generatedContainerClass[$namespace];
    }
}
