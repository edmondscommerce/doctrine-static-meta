<?php

declare(strict_types=1);

// phpcs:disable
namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Small\CodeGeneration\Creation\Src\Entity\Embeddable\Interfaces\Objects;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\Embeddable\Interfaces\Objects\EmbeddableInterfaceCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\Factory\FileFactory;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\Factory\FindReplaceFactory;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\File\Writer;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\Config;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Small\ConfigTest;
use PHPUnit\Framework\TestCase;
// phpcs:enable
/**
 * @small
 * @covers \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\Embeddable\Interfaces\Objects\EmbeddableInterfaceCreator
 */
class EmbeddableInterfaceCreatorTest extends TestCase
{
    private const EXPECTED_FILE = "<?php declare(strict_types=1);

namespace Test\Project\Entity\Embeddable\Interfaces\Objects\Foo;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Interfaces\Objects\AbstractEmbeddableObjectInterface;

interface BarEmbeddableInterface extends AbstractEmbeddableObjectInterface
{
    public const EMBEDDED_PROP_PROPERTY_ONE = 'propertyOne';
    public const EMBEDDED_PROP_PROPERTY_TWO = 'propertyTwo';

    public const DEFAULT_PROPERTY_ONE = 'NOT SET';
    public const DEFAULT_PROPERTY_TWO = 'NOT SET';

    public const DEFAULTS = [
        self::EMBEDDED_PROP_PROPERTY_ONE => self::DEFAULT_PROPERTY_ONE,
        self::EMBEDDED_PROP_PROPERTY_TWO => self::DEFAULT_PROPERTY_TWO,
    ];

    /**
     * @return string
     */
    public function getPropertyOne(): string;

    /**
     * @return string
     */
    public function getPropertyTwo(): string;
}";

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

    private function getCreator(): EmbeddableInterfaceCreator
    {
        $namespaceHelper = new NamespaceHelper();
        $config          = new Config(ConfigTest::SERVER);

        $creator = new EmbeddableInterfaceCreator(
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
