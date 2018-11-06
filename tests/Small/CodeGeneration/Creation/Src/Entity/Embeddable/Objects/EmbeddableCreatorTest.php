<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Small\CodeGeneration\Creation\Src\Entity\Embeddable\Objects;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\Embeddable\Objects\EmbeddableCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\Factory\FileFactory;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\Factory\FindReplaceFactory;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\File\Writer;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\Config;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Small\ConfigTest;
use PHPUnit\Framework\TestCase;

/**
 * @small
 * @covers \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\Embeddable\Objects\EmbeddableCreator
 */
class EmbeddableCreatorTest extends TestCase
{
    private const EXPECTED_FILE = <<<'PHP'
<?php declare(strict_types=1);

namespace Test\Project\Entity\Embeddable\Objects\Foo;

use Doctrine\ORM\Mapping\ClassMetadata;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Objects\AbstractEmbeddableObject;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use Test\Project\Entity\Embeddable\Interfaces\Foo\HasBarEmbeddableInterface;
use Test\Project\Entity\Embeddable\Interfaces\Objects\Foo\BarEmbeddableInterface;

class BarEmbeddable extends AbstractEmbeddableObject implements BarEmbeddableInterface
{
    /**
     * @var string
     */
    private $propertyOne;
    /**
     * @var string
     */
    private $propertyTwo;

    public function __construct(string $propertyOne, string $propertyTwo)
    {
        $this->validate($propertyOne, $propertyTwo);
        $this->propertyOne = $propertyOne;
        $this->propertyTwo = $propertyTwo;
    }

    private function validate(string $propertyOne, string $propertyTwo): void
    {
        $errors = [];
        if ('' === $propertyOne) {
            $errors[] = 'property one is empty';
        }
        if ('' === $propertyTwo) {
            $errors[] = 'property two is empty';
        }
        if ([] === $errors) {
            return;
        }
        throw new \InvalidArgumentException('Invalid arguments: ' . print_r($errors, true));
    }

    /**
     * @param ClassMetadata $metadata
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function loadMetadata(ClassMetadata $metadata): void
    {
        $builder = self::setEmbeddableAndGetBuilder($metadata);
        MappingHelper::setSimpleFields(
            [
                BarEmbeddableInterface::EMBEDDED_PROP_PROPERTY_ONE => MappingHelper::TYPE_STRING,
                BarEmbeddableInterface::EMBEDDED_PROP_PROPERTY_TWO => MappingHelper::TYPE_STRING,
            ],
            $builder
        );
    }

    /**
     * @param array $properties
     *
     * @return $this
     */
    public static function create(array $properties): BarEmbeddableInterface
    {
        if (array_key_exists(BarEmbeddableInterface::EMBEDDED_PROP_PROPERTY_ONE, $properties)) {
            return new self(
                $properties[BarEmbeddableInterface::EMBEDDED_PROP_PROPERTY_ONE],
                $properties[BarEmbeddableInterface::EMBEDDED_PROP_PROPERTY_TWO]
            );
        }

        return new self(...array_values($properties));
    }

    public function __toString(): string
    {
        return (string)print_r(
            [
                'barEmbeddable' => [
                    BarEmbeddableInterface::EMBEDDED_PROP_PROPERTY_ONE => $this->getPropertyOne(),
                    BarEmbeddableInterface::EMBEDDED_PROP_PROPERTY_TWO => $this->getPropertyTwo(),
                ],
            ],
            true
        );
    }

    /**
     * @return string
     */
    public function getPropertyOne(): string
    {
        return $this->propertyOne;
    }

    /**
     * @return string
     */
    public function getPropertyTwo(): string
    {
        return $this->propertyTwo;
    }

    protected function getPrefix(): string
    {
        return HasBarEmbeddableInterface::PROP_BAR_EMBEDDABLE;
    }
}
PHP;


    /**
     * @test
     */
    public function itCanCreateTheFile(): void
    {
        $file     = $this->getCreator()
                         ->setCatName('Foo')
                         ->setName('Bar')
                         ->createTargetFileObject()
                         ->getTargetFile();
        $expected = self::EXPECTED_FILE;
        $actual   = $file->getContents();
        self::assertSame($expected, $actual);
    }

    private function getCreator(): EmbeddableCreator
    {
        $namespaceHelper = new NamespaceHelper();
        $config          = new Config(ConfigTest::SERVER);

        $creator = new EmbeddableCreator(
            new FileFactory($namespaceHelper, $config),
            $namespaceHelper,
            new Writer(),
            $config,
            new FindReplaceFactory()
        );
        $creator->setProjectRootNamespace('Test\Project');

        return $creator;
    }
}