<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Small\CodeGeneration\Creation\Src\Entity\Savers;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\Savers\EntitySaverCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\Factory\FileFactory;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\Factory\FindReplaceFactory;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\File\Writer;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\Config;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Small\ConfigTest;
use PHPUnit\Framework\TestCase;

class EntitySaverCreatorTest extends TestCase
{
    /**
     * @test
     */
    public function itCanCreateANewEntitySaver(): void
    {
        $newObjectFqn = 'EdmondsCommerce\\DoctrineStaticMeta\\Entity\\Savers\\TestEntitySaver';
        $file         = $this->getCreator()->createTargetFileObject($newObjectFqn)->getTargetFile();
        $expected     = '<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Savers;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\AbstractEntitySpecificSaver;

class TestEntitySaver extends AbstractEntitySpecificSaver
{

}';
        $actual       = $file->getContents();
        self::assertSame($expected, $actual);
    }

    private function getCreator(): EntitySaverCreator
    {
        $namespaceHelper = new NamespaceHelper();
        $config          = new Config(ConfigTest::SERVER);

        return new EntitySaverCreator(
            new FileFactory($namespaceHelper, $config),
            $namespaceHelper,
            new Writer(),
            $config,
            new FindReplaceFactory()
        );
    }

    /**
     * @test
     */
    public function itCanCreateANewDeeplyNestedEntitySaver(): void
    {
        $newObjectFqn = 'EdmondsCommerce\\DoctrineStaticMeta\\Entity\\Savers\\Deeply\\Ne\\S\\ted\\TestEntitySaver';
        $file         = $this->getCreator()->createTargetFileObject($newObjectFqn)->getTargetFile();
        $expected     = '<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\Deeply\Ne\S\ted;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\Deeply\Ne\S\ted\AbstractEntitySpecificSaver;

class TestEntitySaver extends AbstractEntitySpecificSaver
{

}';
        $actual       = $file->getContents();
        self::assertSame($expected, $actual);
    }
}