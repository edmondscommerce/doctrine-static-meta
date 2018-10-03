<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Small\CodeGeneration\Modification;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Modification\CodeGenClassTypeFactory;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use Nette\PhpGenerator\ClassType;
use PHPUnit\Framework\TestCase;
use Roave\BetterReflection\Reflection\ReflectionClass;

/**
 * @covers \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Modification\CodeGenClassTypeFactory
 * @small
 */
class CodeGenClassTypeFactoryTest extends TestCase
{
    /**
     * @var CodeGenClassTypeFactory
     */
    private static $factory;

    public static function setUpBeforeClass()
    {
        self::$factory = new CodeGenClassTypeFactory(new NamespaceHelper());
    }

    /**
     * @test
     */
    public function itCanCreateClassTypeFromFqn(): void
    {
        $classType = self::$factory->createClassTypeFromFqn(static::class);
        self::assertSame(ClassType::TYPE_CLASS, $classType->getType());
        self::assertSame('CodeGenClassTypeFactoryTest', $classType->getName());
    }

    /**
     * @test
     */
    public function itCanCreateClassTypeFromBetterReflection(): void
    {
        $classType = self::$factory->createClassTypeFromBetterReflection(ReflectionClass::createFromName(static::class));
        self::assertSame(ClassType::TYPE_CLASS, $classType->getType());
        self::assertSame('CodeGenClassTypeFactoryTest', $classType->getName());
    }

    /**
     * @test
     */
    public function itCanCreateCLassTypeFromPath(): void
    {
        $classType = self::$factory->createClassTypeFromPath(__FILE__, 'EdmondsCommerce\\DoctrineStaticMeta\\Tests');
        self::assertSame(ClassType::TYPE_CLASS, $classType->getType());
        self::assertSame('CodeGenClassTypeFactoryTest', $classType->getName());
    }

    /**
     * @test
     */
    public function itCanCreateAFileFromAClassTypeAndAReflectionClass()
    {
        $reflection = ReflectionClass::createFromName(static::class);
        $classType  = self::$factory->createClassTypeFromBetterReflection($reflection);
        $file       = self::$factory->createFileFromReflectionAndClassType($reflection, $classType);
        self::assertStringEqualsFile(__FILE__, $file->__toString());
    }

}