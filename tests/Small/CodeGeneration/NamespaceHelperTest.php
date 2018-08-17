<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Small\CodeGeneration;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\String\BusinessIdentifierCodeFakerData;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\String\CountryCodeFakerData;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String\BusinessIdentifierCodeFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String\CountryCodeFieldTrait;
use PHPUnit\Framework\TestCase;

/**
 * Class NamespaceHelperTest
 *
 * @package EdmondsCommerce\DoctrineStaticMeta\Small\CodeGeneration
 * @coversDefaultClass \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper
 */
class NamespaceHelperTest extends TestCase
{
    /**
     * @var NamespaceHelper
     */
    private static $helper;

    public static function setupBeforeClass()
    {
        self::$helper = new NamespaceHelper();
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
     * @small
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
     * @small
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
     * @small
     * @covers ::getObjectShortName
     */
    public function getObjectShortName(): void
    {

        $expectedToObjects = [
            'NamespaceHelperTest' => $this,
            'NamespaceHelper'     => self::$helper,
        ];
        $actual            = [];
        foreach ($expectedToObjects as $object) {
            $actual[self::$helper->getObjectShortName($object)] = $object;
        }
        self::assertSame($expectedToObjects, $actual);
    }

    /**
     * @test
     * @small
     * @covers ::getObjectFqn
     */
    public function getObjectFqn(): void
    {

        $expectedToObjects = [
            \get_class($this)         => $this,
            \get_class(self::$helper) => self::$helper,
        ];
        $actual            = [];
        foreach ($expectedToObjects as $object) {
            $actual[self::$helper->getObjectFqn($object)] = $object;
        }
        self::assertSame($expectedToObjects, $actual);
    }

    /**
     * @test
     * @small
     * @covers ::getClassShortName
     */
    public function getClassShortName(): void
    {
        $expectedToFqns = [
            'NamespaceHelperTest' => \get_class($this),
            'Cheese'              => '\\Super\\Cheese',
        ];
        $actual         = [];
        foreach ($expectedToFqns as $fqn) {
            $actual[self::$helper->getClassShortName($fqn)] = $fqn;
        }
        self::assertSame($expectedToFqns, $actual);
    }

    /**
     * @test
     * @small
     * @covers ::getFakerProviderFqnFromFieldTraitReflection
     */
    public function getFakerProviderFqnFromFieldTraitReflection(): void
    {
        $expected = [
            BusinessIdentifierCodeFieldTrait::class => BusinessIdentifierCodeFakerData::class,
            CountryCodeFieldTrait::class            => CountryCodeFakerData::class,
        ];
        $actual   = [];
        foreach (array_keys($expected) as $fieldFqn) {
            $actual[$fieldFqn] = self::$helper->getFakerProviderFqnFromFieldTraitReflection(
                new \ts\Reflection\ReflectionClass($fieldFqn)
            );
        }
        self::assertSame($expected, $actual);
    }
}
