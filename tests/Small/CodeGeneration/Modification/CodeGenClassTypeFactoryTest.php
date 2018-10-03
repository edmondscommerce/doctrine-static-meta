<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Small\CodeGeneration\Modification;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Modification\CodeGenClassTypeFactory;
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
        self::$factory = new CodeGenClassTypeFactory();
    }

    /**
     * @test
     */
    public function itCanCreateClassTypeFromFqn()
    {
        $classType = self::$factory->createFromFqn(static::class);
        self::assertSame(ClassType::TYPE_CLASS, $classType->getType());
        self::assertSame('CodeGenClassTypeFactoryTest', $classType->getName());
    }

    /**
     * @test
     */
    public function itCanCreateClassTypeFromBetterReflection()
    {
        $classType = self::$factory->createFromBetterReflection(ReflectionClass::createFromName(static::class));
        self::assertSame(ClassType::TYPE_CLASS, $classType->getType());
        self::assertSame('CodeGenClassTypeFactoryTest', $classType->getName());
    }

    /**
     * @test
     */
    public function itCanCreateCLassTypeFromPath()
    {
        $classType = self::$factory->createFromPath(__FILE__, 'EdmondsCommerce\\DoctrineStaticMeta\\Tests');
        self::assertSame(ClassType::TYPE_CLASS, $classType->getType());
        self::assertSame('CodeGenClassTypeFactoryTest', $classType->getName());
    }


}