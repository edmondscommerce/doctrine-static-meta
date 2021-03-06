<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Small\CodeGeneration;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Interfaces\Objects\Financial\MoneyEmbeddableInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Interfaces\Objects\Geo\AddressEmbeddableInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Interfaces\Objects\Identity\FullNameEmbeddableInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Objects\Financial\MoneyEmbeddable;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Objects\Geo\AddressEmbeddable;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Objects\Identity\FullNameEmbeddable;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use PHPUnit\Framework\TestCase;
use ts\Reflection\ReflectionClass;

/**
 * @covers \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper
 * @small
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
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
     */
    public function itCanGetAllTheArchetypeFieldFqns(): void
    {
        $actual = self::$helper->getAllArchetypeFieldFqns();
        foreach ($actual as $fqn) {
            $reflection = new ReflectionClass($fqn);
            self::assertTrue($reflection->isTrait());
        }
        self::assertGreaterThan(10, count($actual));
    }

    /**
     * @test
     */
    public function itCanGetTheEmbeddableObjectFqnFromTheInterfaceFqn(): void
    {
        $expectedToInterface = [
            MoneyEmbeddable::class    => MoneyEmbeddableInterface::class,
            AddressEmbeddable::class  => AddressEmbeddableInterface::class,
            FullNameEmbeddable::class => FullNameEmbeddableInterface::class,
        ];
        foreach ($expectedToInterface as $expected => $interface) {
            $this->assertSame(
                $expected,
                self::$helper->getEmbeddableObjectFqnFromEmbeddableObjectInterfaceFqn($interface)
            );
        }
    }

    /**
     * @test
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
     */
    public function itCanGetTheEntityDtoFactoryFqnFromEntityFqn(): void
    {
        $expected = AbstractTest::TEST_PROJECT_ROOT_NAMESPACE . '\\Entity\\Factories\\Blah\\FooDtoFactory';
        $actual   = self::$helper->getDtoFactoryFqnFromEntityFqn(
            AbstractTest::TEST_PROJECT_ROOT_NAMESPACE . '\\Entities\\Blah\\Foo'
        );
        self::assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function itCanGetTheEntityFqnFromEntityDtoFactoryFqn(): void
    {
        $factory  = AbstractTest::TEST_PROJECT_ROOT_NAMESPACE . '\\Entity\\Factories\\Blah\\FooDtoFactory';
        $expected = AbstractTest::TEST_PROJECT_ROOT_NAMESPACE . '\\Entities\\Blah\\Foo';
        $actual   = self::$helper->getEntityFqnFromEntityDtoFactoryFqn($factory);
        self::assertSame($expected, $actual);
    }

    /**
     * @test
     * @large
     */
    public function getFixtureFqnFromEntityFqn(): void
    {
        $expected = AbstractTest::TEST_PROJECT_ROOT_NAMESPACE . '\\Assets\\Entity\\Fixtures\\Blah\\FooFixture';
        $actual   = self::$helper->getFixtureFqnFromEntityFqn(
            AbstractTest::TEST_PROJECT_ROOT_NAMESPACE . '\\Entities\\Blah\\Foo'
        );
        self::assertSame($expected, $actual);
    }

    /**
     * @test
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
     *      */
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
     *      */
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

    /**
     * @test
     */
    public function itCanGetADtoFqnFromAnEntityFqn(): void
    {
        $expected = '\\Test\\Project\\Entity\\DataTransferObjects\\Foo\\BarDto';
        $actual   = self::$helper->getEntityDtoFqnFromEntityFqn(
            '\\Test\\Project\\Entities\\Foo\\Bar'
        );
        self::assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function itCanGetAnUpsertFqnFromAnEntityFqn(): void
    {
        $expected = '\\Test\\Project\\Entity\\Savers\\Foo\\BarUpserter';
        $actual   = self::$helper->getEntityUpserterFqnFromEntityFqn(
            '\\Test\\Project\\Entities\\Foo\\Bar'
        );
        self::assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function itCanGetAnEntityFqnFromAnUpsertFqn(): void
    {
        $expected = '\\Test\\Project\\Entities\\Foo\\Bar';
        $actual   = self::$helper->getEntityFqnFromEntityUpserterFqn(
            '\\Test\\Project\\Entity\\Savers\\Foo\\BarUpserter'
        );
        self::assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function itCanGetAnUnitOfWorkHelperFqnFromAnEntityFqn(): void
    {
        $expected = '\\Test\\Project\\Entity\\Savers\\Foo\\BarUnitOfWorkHelper';
        $actual   = self::$helper->getEntityUnitOfWorkHelperFqnFromEntityFqn(
            '\\Test\\Project\\Entities\\Foo\\Bar'
        );
        self::assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function itCanGetAnEntityFqnFromAnUnitOfWorkHelperFqn(): void
    {
        $expected = '\\Test\\Project\\Entities\\Foo\\Bar';
        $actual   = self::$helper->getEntityFqnFromEntityUnitOfWorkHelperFqn(
            '\\Test\\Project\\Entity\\Savers\\Foo\\BarUnitOfWorkHelper'
        );
        self::assertSame($expected, $actual);
    }
}
