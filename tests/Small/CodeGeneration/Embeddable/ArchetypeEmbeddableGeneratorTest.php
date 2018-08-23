<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Small\CodeGeneration\Embeddable;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\CodeHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\Embeddable\ArchetypeEmbeddableGenerator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\FileCreationTransaction;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\FindAndReplaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\PathHelper;
use EdmondsCommerce\DoctrineStaticMeta\Config;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Objects\Financial\MoneyEmbeddable;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Objects\Identity\FullNameEmbeddable;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Small\ConfigTest;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

//phpcs:disable
/**
 * Class ArchetypeEmbeddableGeneratorTest
 *
 * @package EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Embeddable
 * @coversDefaultClass \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\Embeddable\ArchetypeEmbeddableGenerator
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
//phpcs:enable
class ArchetypeEmbeddableGeneratorTest extends TestCase
{
    /**
     * @var ArchetypeEmbeddableGenerator
     */
    private static $instance;

    /**
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\ConfigException
     */
    public static function setUpBeforeClass(): void
    {
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
    public function itShouldExceptIfEmbeddableFqnDoesNotExist(): void
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
    public function itShouldExceptIfTheNewClassNameIsNamespaced(): void
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
    public function itShouldExceptIfTheNewClassNameDoesNotEndInEmbeddable(): void
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
    public function itShouldExceptIfArchtypeIsNotAnEmbeddableObject(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        self::$instance->createFromArchetype(
            self::class,
            'Price'
        );
    }

    /**
     * @test
     * @small
     * @covers ::checkForIssues()
     */
    public function itShouldExceptIfTheNewClassIsAPrefixOfTheArchetype(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        self::$instance->createFromArchetype(
            FullNameEmbeddable::class,
            'PrefixedFullNameEmbeddable'
        );
    }
}
