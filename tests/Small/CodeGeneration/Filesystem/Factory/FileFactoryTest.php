<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Small\CodeGeneration\Filesystem\Factory;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\Factory\FileFactory;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\Config;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Small\ConfigTest;
use PHPUnit\Framework\TestCase;
use ts\Reflection\ReflectionClass;

/**
 * @covers \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\Factory\FileFactory
 */
class FileFactoryTest extends TestCase
{

    /**
     * @test
     * @small
     * @throws \ReflectionException
     * @throws DoctrineStaticMetaException
     */
    public function itCanCreateFromFqnThatExists()
    {
        $object     = $this->getFactory()->createFromFqn(MappingHelper::class);
        $reflection = new ReflectionClass(MappingHelper::class);
        self::assertSame($reflection->getFileName(), $object->getPath());
    }

    public function getFactory()
    {
        return new FileFactory(new NamespaceHelper(), new Config(ConfigTest::SERVER));
    }

    /**
     * @test
     * @small
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function itCanCreateFromFqnThatDoesNotExist()
    {
        $object = $this->getFactory()->createFromFqn('EdmondsCommerce\\DoctrineStaticMeta\\Test');
        $path   = Config::getProjectRootDirectory() . '/src/Test.php';
        self::assertSame($path, $object->getPath());
    }

    /**
     * @test
     * @small
     */
    public function itDiesIfTheProjectRootNamespaceIsNotPresent()
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
    public function itCanCreateFromAnExistingFile()
    {
        $object = $this->getFactory()->createFromExistingPath(__FILE__);
        self::assertSame(__FILE__, $object->getPath());
    }

    /**
     * @test
     * @small
     */
    public function itWillDieIfCreatingFromExistingPathThatDoesnt()
    {
        $this->expectException(DoctrineStaticMetaException::class);
        $this->expectExceptionMessage('File does not exist at ');
        $this->getFactory()->createFromExistingPath('/not/existing/path.txt');
    }

    /**
     * @test
     * @small
     */
    public function itCanSetProjectRootDirectory()
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
    public function itCanSetProjectRootNamespace()
    {
        $namespace  = 'Test\\Project\\Namespace';
        $factory    = $this->getFactory()->setProjectRootNamespace($namespace);
        $reflection = new ReflectionClass(FileFactory::class);
        $property   = $reflection->getProperty('projectRootNamespace');
        $property->setAccessible(true);
        self::assertSame($namespace, $property->getValue($factory));
    }
}
