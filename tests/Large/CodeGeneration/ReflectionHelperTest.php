<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\CodeGeneration;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\AbstractGenerator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\ReflectionHelper;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\String\BusinessIdentifierCodeFakerData;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\String\CountryCodeFakerData;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String\BusinessIdentifierCodeFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String\CountryCodeFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\TestCodeGenerator;

/**
 * @covers \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\ReflectionHelper
 * @large
 */
class ReflectionHelperTest extends AbstractTest
{
    public const WORK_DIR = AbstractTest::VAR_PATH . '/' . self::TEST_TYPE_LARGE . '/NamespaceHelperTest';

    private const TEST_ENTITY = self::TEST_ENTITIES_ROOT_NAMESPACE . TestCodeGenerator::TEST_ENTITY_PERSON;

    protected static $buildOnce = true;

    public function setup()
    {
        parent::setUp();
        if (false === self::$built) {
            $this->getTestCodeGenerator()
                 ->copyTo(self::WORK_DIR);
            self::$built = true;
        }
    }

    /**
     * @test
     * @large
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
            $actual[$fieldFqn] = $this->getHelper()->getFakerProviderFqnFromFieldTraitReflection(
                new \ts\Reflection\ReflectionClass($fieldFqn)
            );
        }
        self::assertSame($expected, $actual);
    }

    /**
     * @throws \ReflectionException
     */
    public function testGetEntityNamespaceRootFromEntityReflection(): void
    {

        $entityReflection = new  \ts\Reflection\ReflectionClass(self::TEST_ENTITY);
        $expected         = self::TEST_PROJECT_ROOT_NAMESPACE . '\\' . AbstractGenerator::ENTITIES_FOLDER_NAME;
        $actual           = $this->getHelper()->getEntityNamespaceRootFromEntityReflection($entityReflection);
        self::assertSame($expected, $actual);
    }

    private function getHelper(): ReflectionHelper
    {
        return $this->container->get(ReflectionHelper::class);
    }
}
