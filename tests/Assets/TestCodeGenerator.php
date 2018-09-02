<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Assets;

use Composer\Autoload\ClassLoader;
use EdmondsCommerce\DoctrineStaticMeta\Builder\Builder;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Large\FullProjectBuildLargeTest;
use Symfony\Component\Filesystem\Filesystem;

class TestCodeGenerator
{
    public const TEST_PROJECT_ROOT_NAMESPACE = FullProjectBuildLargeTest::TEST_PROJECT_ROOT_NAMESPACE;
    public const TEST_ENTITIES               = FullProjectBuildLargeTest::TEST_ENTITIES;
    public const TEST_RELATIONS              = FullProjectBuildLargeTest::TEST_RELATIONS;
    public const TEST_FIELD_FQN_BASE         = FullProjectBuildLargeTest::TEST_FIELD_NAMESPACE_BASE . '\\Traits';
    public const BUILD_DIR                   = AbstractTest::VAR_PATH . '/testCode';

    /**
     * @var Builder
     */
    protected $builder;
    /**
     * @var Filesystem
     */
    protected $filesystem;

    public function __construct(Builder $builder, Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
        $this->initBuildDir();
        $this->builder = $builder->setProjectRootNamespace(self::TEST_PROJECT_ROOT_NAMESPACE)
                                 ->setPathToProjectRoot(self::BUILD_DIR);
        $this->buildOnce();
    }

    private function initBuildDir(): void
    {

        if (!is_dir(self::BUILD_DIR)) {
            $this->filesystem->mkdir(self::BUILD_DIR);
        }
    }

    private function isBuilt(): bool
    {
        return is_dir(self::BUILD_DIR . '/src');
    }

    public function copyTo(string $destinationPath): void
    {
        $this->filesystem->mirror(self::BUILD_DIR, $destinationPath);
    }

    public function buildOnce(): void
    {
        if ($this->isBuilt()) {
            return;
        }
        $this->extendAutoloader();
        $entityGenerator    = $this->builder->getEntityGenerator();
        $fieldGenerator     = $this->builder->getFieldGenerator();
        $fieldSetter        = $this->builder->getFieldSetter();
        $relationsGenerator = $this->builder->getRelationsGenerator();
        $fields             = [];
        foreach (MappingHelper::COMMON_TYPES as $type) {
            $fields[] = $fieldGenerator->generateField(
                self::TEST_FIELD_FQN_BASE . '\\' . ucwords($type),
                $type
            );
        }
        foreach (self::TEST_ENTITIES as $entityFqn) {
            $entityGenerator->generateEntity($entityFqn);
            foreach ($fields as $fieldFqn) {
                $fieldSetter->setEntityHasField($entityFqn, $fieldFqn);
            }
        }
        foreach (self::TEST_RELATIONS as $relation) {
            $relationsGenerator->setEntityHasRelationToEntity(...$relation);
        }
        $this->resetAutoloader();
    }

    private function extendAutoloader(): void
    {
        $testLoader = new class(self::TEST_PROJECT_ROOT_NAMESPACE) extends ClassLoader
        {
            /**
             * @var string
             */
            protected $namespace;

            public function __construct(string $namespace)
            {
                $this->namespace = $namespace;
            }

            public function loadClass($class)
            {
                if (false === strpos($class, $this->namespace)) {
                    return false;
                }
                $found = parent::loadClass($class);
                if (false === $found || null === $found) {
                    //good point to set a breakpoint
                    return $found;
                }

                return $found;
            }
        };
        $testLoader->addPsr4(self::TEST_PROJECT_ROOT_NAMESPACE . '\\', self::BUILD_DIR . '/src', true);
        $testLoader->addPsr4(self::TEST_PROJECT_ROOT_NAMESPACE . '\\', self::BUILD_DIR . '/tests', true);
        $testLoader->register();
    }

    private function resetAutoloader(): void
    {
        $registered = \spl_autoload_functions();
        $loader     = array_pop($registered);
        \spl_autoload_unregister($loader);
    }

}
