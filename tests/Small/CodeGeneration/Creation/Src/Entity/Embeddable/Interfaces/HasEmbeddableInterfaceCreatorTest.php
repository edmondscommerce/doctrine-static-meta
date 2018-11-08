<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Small\CodeGeneration\Creation\Src\Entity\Embeddable\Interfaces;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\Embeddable\Interfaces\HasEmbeddableInterfaceCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\Factory\FileFactory;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\Factory\FindReplaceFactory;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\File\Writer;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\Config;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Small\ConfigTest;
use PHPUnit\Framework\TestCase;

/**
 * @small
 * @covers \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\Embeddable\Interfaces\HasEmbeddableInterfaceCreator
 */
class HasEmbeddableInterfaceCreatorTest extends TestCase
{
    private const EXPECTED_FILE = "<?php declare(strict_types=1);

namespace Test\Project\Entity\Embeddable\Interfaces\Foo;

use Test\Project\Entity\Embeddable\Interfaces\Objects\Foo\BarEmbeddableInterface;

interface HasBarEmbeddableInterface
{
    public const PROP_BAR_EMBEDDABLE = 'barEmbeddable';
    public const COLUMN_PREFIX_BAR   = 'bar_';

    public function getBarEmbeddable(): BarEmbeddableInterface;
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

    private function getCreator(): HasEmbeddableInterfaceCreator
    {
        $namespaceHelper = new NamespaceHelper();
        $config          = new Config(ConfigTest::SERVER);

        $creator = new HasEmbeddableInterfaceCreator(
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
