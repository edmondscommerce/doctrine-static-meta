<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Embeddable;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\CodeHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\Embeddable\ArchetypeEmbeddableGenerator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\FileCreationTransaction;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\FindAndReplaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\PathHelper;
use EdmondsCommerce\DoctrineStaticMeta\Config;
use EdmondsCommerce\DoctrineStaticMeta\ConfigTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Objects\Financial\MoneyEmbeddable;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class ArchetypeEmbeddableGeneratorTest
 *
 * @package EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Embeddable
 * @coversDefaultClass \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\Embeddable\ArchetypeEmbeddableGenerator
 */
class ArchetypeEmbeddableGeneratorTest extends TestCase
{
    /**
     * @var ArchetypeEmbeddableGenerator
     */
    private static $instance;

    /**
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\ConfigException
     */
    public static function setUpBeforeClass()
    {
/* The :void return type declaration that should be here would cause a BC issue */
        $filesystem      = new Filesystem();
        $namespaceHelper = new NamespaceHelper();
        self::$instance  = new ArchetypeEmbeddableGenerator(
            $filesystem,
            new FileCreationTransaction(),
            $namespaceHelper,
            new Config(ConfigTest::SERVER),
            new CodeHelper($namespaceHelper),
            new PathHelper(
                $filesystem,
                new FileCreationTransaction()
            ),
            new FindAndReplaceHelper($namespaceHelper)
        );
    }

    /**
     * @test
     * @small
     * @covers ::validateArguments()
     */
    public function itShouldExceptIfEmbeddableFqnDoesNotExist()
    {
        $this->expectException(\InvalidArgumentException::class);
        self::$instance->createFromArchetype(
            'Not\\Exists\\Fqn',
            'NewClass'
        );
    }

    /**
     * @test
     * @small
     * @covers ::validateArguments()
     */
    public function itShouldExceptIfTheNewClassNameIsNamespaced()
    {
        $this->expectException(\InvalidArgumentException::class);
        self::$instance->createFromArchetype(
            MoneyEmbeddable::class,
            'My\\Test\\Project\\Entity\\Embeddable\\Object\\PriceEmbeddable'
        );
    }

    /**
     * @test
     * @small
     * @covers ::validateArguments()
     */
    public function itShouldExceptIfTheNewClassNameDoesNotEndInEmbeddable()
    {
        $this->expectException(\InvalidArgumentException::class);
        self::$instance->createFromArchetype(
            MoneyEmbeddable::class,
            'Price'
        );
    }

    /**
     * @test
     * @small
     * @covers ::validateArguments()
     */
    public function itShouldExceptIfArchtypeIsNotAnEmbeddableObject()
    {
        $this->expectException(\InvalidArgumentException::class);
        self::$instance->createFromArchetype(
            self::class,
            'Price'
        );
    }
}
