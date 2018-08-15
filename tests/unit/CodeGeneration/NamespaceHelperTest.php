<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\String\BusinessIdentifierCodeFakerData;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\String\CountryCodeFakerData;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String\BusinessIdentifierCodeFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String\CountryCodeFieldTrait;
use PHPUnit\Framework\TestCase;

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

    public function testCropSuffix(): void
    {
        $fqn      = 'FooBar';
        $suffix   = 'Bar';
        $expected = 'Foo';
        $actual   = self::$helper->cropSuffix($fqn, $suffix);
        self::assertSame($expected, $actual);
    }

    public function testSwapSuffix(): void
    {
        $fqn           = 'FooBar';
        $currentSuffix = 'Bar';
        $newSuffix     = 'Baz';
        $expected      = 'FooBaz';
        $actual        = self::$helper->swapSuffix($fqn, $currentSuffix, $newSuffix);
        self::assertSame($expected, $actual);
    }

    public function testCropSuffixWhereSuffixNotInThere(): void
    {
        $fqn      = 'FooBar';
        $suffix   = 'Cheese';
        $expected = 'FooBar';
        $actual   = self::$helper->cropSuffix($fqn, $suffix);
        self::assertSame($expected, $actual);
    }

    public function testGetObjectShortName(): void
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

    public function testGetObjectFqn(): void
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

    public function testGetClassShortName(): void
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
     * @throws \ReflectionException
     */
    public function testGetFakerProviderFqnFromFieldFqn(): void
    {
        $expected = [
            BusinessIdentifierCodeFieldTrait::class => BusinessIdentifierCodeFakerData::class,
            CountryCodeFieldTrait::class            => CountryCodeFakerData::class,
        ];
        $actual   = [];
        foreach ($expected as $fieldFqn => $fakerFqn) {
            $actual[$fieldFqn] = self::$helper->getFakerProviderFqnFromFieldTraitReflection(
                new \ts\Reflection\ReflectionClass($fieldFqn)
            );
        }
        self::assertSame($expected, $actual);
    }
}
