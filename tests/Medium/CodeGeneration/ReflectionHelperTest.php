<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Medium\CodeGeneration;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\ReflectionHelper;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\TestCodeGenerator;
use ReflectionException;
use ts\Reflection\ReflectionClass;

/**
 * @covers \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\ReflectionHelper
 * @medium
 */
class ReflectionHelperTest extends AbstractTest
{
    public const WORK_DIR = self::VAR_PATH . '/' . self::TEST_TYPE_MEDIUM . '/ReflectionHelperTest';
    protected static $buildOnce = true;
    protected static $built     = false;
    /**
     * @var ReflectionHelper
     */
    private $helper;

    public function setup()
    {
        parent::setUp();
        $this->generateTestCode();
        $this->helper = new ReflectionHelper(new NamespaceHelper());
    }

    /**$getter     = $trait->getMethod($getterName);
     *
     * @throws ReflectionException
     * @test
     */
    public function itCanGetTheTraitThatHasAMethod(): void
    {
        $class      = new ReflectionClass(self::TEST_ENTITIES_ROOT_NAMESPACE . TestCodeGenerator::TEST_ENTITY_PERSON);
        $methodName = 'setString';
        $expected   =
            new ReflectionClass(self::TEST_PROJECT_ROOT_NAMESPACE . '\\Entity\\Fields\\Traits\\StringFieldTrait');
        $actual     = $this->helper->getTraitImplementingMethod($class, $methodName);
        self::assertEquals($expected, $actual);
    }

    /**
     * @throws ReflectionException
     * @test
     */
    public function itCanGetTheMethodBodyFromAReflectionObject(): void
    {
        $methodName = 'setup';
        $reflection = new ReflectionClass(__CLASS__);
        $actual     = $this->helper->getMethodBody($methodName, $reflection);
        $expected   = '    public function setup()
    {
        parent::setUp();
        $this->generateTestCode();
        $this->helper = new ReflectionHelper(new NamespaceHelper());
    }
';
        self::assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function itCanGetTheTraitContainingAProperty(): void
    {
        $entityReflection =
            new ReflectionClass(self::TEST_ENTITIES_ROOT_NAMESPACE . TestCodeGenerator::TEST_ENTITY_ORDER);

        $expected =
            'My\Test\Project\Entity\Relations\Person\Traits\HasRequiredPerson\HasRequiredPersonUnidirectionalManyToOne';
        $actual   = $this->helper->getTraitProvidingProperty($entityReflection, 'person')->getName();
        self::assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function itCanGetTheUseStatements(): void
    {
        $reflection = new ReflectionClass(__CLASS__);
        $actual     = $this->helper->getUseStatements($reflection);
        $expected   = [
            'use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;',
            'use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\ReflectionHelper;',
            'use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;',
            'use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\TestCodeGenerator;',
            'use ReflectionException;',
            'use ts\Reflection\ReflectionClass;',
        ];
        self::assertSame($expected, $actual);
    }
}
