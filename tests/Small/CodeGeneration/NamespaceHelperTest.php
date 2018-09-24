<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Small\CodeGeneration;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use PHPUnit\Framework\TestCase;


/**
 * @coversDefaultClass \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper
 * @small
 */
class NamespaceHelperTest extends TestCase
{
    /**
     * @var NamespaceHelper
     */
    private static $helper;

    public static function setUpBeforeClass()
    {
        self::$helper = new NamespaceHelper();
    }

    /**
     * @test
     * @small
     * @covers ::getEntityFqnFromEntityInterfaceFqn
     */
    public function itCanGetTheEntityFqnFromTheEntityInterfaceFqn(): void
    {
        $expected = AbstractTest::TEST_PROJECT_ROOT_NAMESPACE . '\\Entities\\Foo\\BlahEntity';
        $actual   = self::$helper->getEntityFqnFromEntityInterfaceFqn(
            AbstractTest::TEST_PROJECT_ROOT_NAMESPACE . '\\Entity\\Interfaces\\Foo\\BlahEntityInterface'
        );
        self::assertSame($expected, $actual);
    }

    /**
     * @test
     * @small
     * @covers ::getFactoryFqnFromEntityFqn
     */
    public function itCanGetTheEntityFactoryFqnFromEntityFqn(): void
    {
        $expected = AbstractTest::TEST_PROJECT_ROOT_NAMESPACE . '\\Entity\\Factories\\Blah\\FooFactory';
        $actual   = self::$helper->getFactoryFqnFromEntityFqn(
            AbstractTest::TEST_PROJECT_ROOT_NAMESPACE . '\\Entities\\Blah\\Foo'
        );
        self::assertSame($expected, $actual);
    }

    /**
     * @test
     * @small
     * @covers ::getFactoryFqnFromEntityFqn
     */
    public function itCanGetTheEntityFqnFromEntityFactoryFqn(): void
    {
        $factory  = AbstractTest::TEST_PROJECT_ROOT_NAMESPACE . '\\Entity\\Factories\\Blah\\FooFactory';
        $expected = AbstractTest::TEST_PROJECT_ROOT_NAMESPACE . '\\Entities\\Blah\\Foo';
        $actual   = self::$helper->getEntityFqnFromEntityFactoryFqn($factory);
        self::assertSame($expected, $actual);
    }

    /**
     * @test
     * @large
     */
    public function getFixtureFqnFromEntityFqn()
    {
        $expected = AbstractTest::TEST_PROJECT_ROOT_NAMESPACE . '\\Assets\\Entity\\Fixtures\\Blah\\FooFixture';
        $actual   = self::$helper->getFixtureFqnFromEntityFqn(
            AbstractTest::TEST_PROJECT_ROOT_NAMESPACE . '\\Entities\\Blah\\Foo'
        );
        self::assertSame($expected, $actual);
    }

    /**
     * @test
     * @small
     * @covers ::cropSuffix
     */
    public function cropSuffix(): void
    {
        $fqn      = 'FooBar';
        $suffix   = 'Bar';
        $expected = 'Foo';
        $actual   = self::$helper->cropSuffix($fqn, $suffix);
        self::assertSame($expected, $actual);
    }

    /**
     * @test
     * @large
     * @covers ::swapSuffix
     */
    public function swapSuffix(): void
    {
        $fqn           = 'FooBar';
        $currentSuffix = 'Bar';
        $newSuffix     = 'Baz';
        $expected      = 'FooBaz';
        $actual        = self::$helper->swapSuffix($fqn, $currentSuffix, $newSuffix);
        self::assertSame($expected, $actual);
    }

    /**
     * @test
     * @large
     * @covers ::cropSuffix
     */
    public function cropSuffixWhereSuffixNotInThere(): void
    {
        $fqn      = 'FooBar';
        $suffix   = 'Cheese';
        $expected = 'FooBar';
        $actual   = self::$helper->cropSuffix($fqn, $suffix);
        self::assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function tidy(): void
    {
        $namespaceToExpected = [
            'Test\\\\Multiple\\\\\\\Separators' => 'Test\\Multiple\\Separators',
            'No\\Changes\\Required'             => 'No\\Changes\\Required',
        ];
        foreach ($namespaceToExpected as $namespace => $expected) {
            self::assertSame($expected, self::$helper->tidy($namespace));
        }
    }

    /**
     * @test
     */
    public function root(): void
    {
        $namespaceToExpected = [
            '\\Test\\\\Multiple\\\\\\\Separators' => 'Test\\Multiple\\Separators',
            'No\\Changes\\Required'               => 'No\\Changes\\Required',
        ];
        foreach ($namespaceToExpected as $namespace => $expected) {
            self::assertSame($expected, self::$helper->root($namespace));
        }
    }
}
