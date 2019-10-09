<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Small\CodeGeneration\Filesystem\Factory;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\Factory\FileFactory;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\Config;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Small\ConfigTest;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use ts\Reflection\ReflectionClass;

/**
 * @covers \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\Factory\FileFactory
 */
class FileFactoryTest extends TestCase
{

    /**
     * @test
     * @small
     * @throws ReflectionException
     * @throws DoctrineStaticMetaException
     */
    public function itCanCreateFromFqnThatExists(): void
    {
        $object     = $this->getFactory()->createFromFqn(MappingHelper::class);
        $reflection = new ReflectionClass(MappingHelper::class);
        self::assertSame($reflection->getFileName(), $object->getPath());
    }

    public function getFactory(): FileFactory
    {
        return new FileFactory(new NamespaceHelper(), new Config(ConfigTest::SERVER));
    }

    /**
     * @test
     * @small
     * @throws DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function itCanCreateFromFqnThatDoesNotExist(): void
    {
        $object = $this->getFactory()->createFromFqn('EdmondsCommerce\\DoctrineStaticMeta\\Test');
        $path   = Config::getProjectRootDirectory() . '/src/Test.php';
        self::assertSame($path, $object->getPath());
    }

    /**
     * @test
     * @small
     */
    public function itDiesIfTheProjectRootNamespaceIsNotPresent(): void
    {
        $this->expectException(DoctrineStaticMetaException::class);
        $this->expectExceptionMessage(
            'Exception in EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper::parseFullyQualifiedName: '
            . 'The $fqn [Foo\Cheese\Test] does not contain the project root namespace '
            . '[EdmondsCommerce\DoctrineStaticMeta] - are you sure it is the correct FQN?'
        );
        $this->getFactory()->createFromFqn('Foo\\Cheese\\Test');
    }

    /**
     * @test
     * @small
     */
    public function itCanCreateFromAnExistingFile(): void
    {
        $object = $this->getFactory()->createFromExistingPath(__FILE__);
        self::assertSame(__FILE__, $object->getPath());
    }


    /**
     * @test
     * @small
     */
    public function itWillDieIfCreatingFromExistingPathThatDoesnt(): void
    {
        $this->expectException(DoctrineStaticMetaException::class);
        $this->expectExceptionMessage('File does not exist at ');
        $this->getFactory()->createFromExistingPath('/not/existing/path.txt');
    }

    /**
     * @test
     * @small
     */
    public function itCanCreateFromAnNoneExistingFile(): void
    {
        $path   = '/tmp/test/blah/foo';
        $object = $this->getFactory()->createFromNonExistantPath($path);
        self::assertSame($path, $object->getPath());
    }

    /**
     * @test
     * @small
     */
    public function itWillDieIfCreatingFromNonExistingPathThatDoes(): void
    {
        $this->expectException(DoctrineStaticMetaException::class);
        $this->expectExceptionMessage('File exists at ');
        $this->getFactory()->createFromNonExistantPath(__FILE__);
    }

    /**
     * @test
     * @small
     */
    public function itCanSetProjectRootDirectory(): void
    {
        $path       = '/path/to/root/directory';
        $factory    = $this->getFactory()->setProjectRootDirectory($path);
        $reflection = new ReflectionClass(FileFactory::class);
        $property   = $reflection->getProperty('projectRootDirectory');
        $property->setAccessible(true);
        self::assertSame($path, $property->getValue($factory));
    }

    /**
     * @test
     * @small
     */
    public function itCanSetProjectRootNamespace(): void
    {
        $namespace  = 'Test\\Project\\Namespace';
        $factory    = $this->getFactory()->setProjectRootNamespace($namespace);
        $reflection = new ReflectionClass(FileFactory::class);
        $property   = $reflection->getProperty('projectRootNamespace');
        $property->setAccessible(true);
        self::assertSame($namespace, $property->getValue($factory));
    }
}
