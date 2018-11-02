<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Small\CodeGeneration\Creation\Src\Entity\Embeddable\FakerData;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\Embeddable\FakerData\EmbeddableFakerDataCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\Factory\FileFactory;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\Factory\FindReplaceFactory;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\File\Writer;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\Config;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Small\ConfigTest;
use PHPUnit\Framework\TestCase;

/**
 * @small
 * @covers \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\Embeddable\FakerData\EmbeddableFakerDataCreator
 */
class EmbeddableFakerDataCreatorTest extends TestCase
{
    private const EXPECTED_FILE = '<?php declare(strict_types=1);

namespace Test\Project\Entity\Embeddable\FakerData\Foo;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\AbstractFakerDataProvider;
use Test\Project\Entity\Embeddable\Objects\Foo\BarEmbeddable;

class BarEmbeddableFakerData extends AbstractFakerDataProvider
{

    /**
     * This magic method means that the object is callable like a closure,
     * and when that happens this invoke method is called.
     *
     * This method should return your fake data. You can use the generator to pull fake data from if that is useful
     *
     * @return mixed
     */
    public function __invoke()
    {
        $embeddable = new BarEmbeddable(
            $this->generator->text,
            $this->generator->text
        );

        return $embeddable;
    }
}';

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

    private function getCreator(): EmbeddableFakerDataCreator
    {
        $namespaceHelper = new NamespaceHelper();
        $config          = new Config(ConfigTest::SERVER);

        $creator = new EmbeddableFakerDataCreator(
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