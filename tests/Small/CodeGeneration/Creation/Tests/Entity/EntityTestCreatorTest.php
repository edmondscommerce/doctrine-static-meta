<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Small\CodeGeneration\Creation\Tests\Entity;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Tests\Entities\EntityTestCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\Factory\FileFactory;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\Factory\FindReplaceFactory;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\File\Writer;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\Config;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Small\ConfigTest;
use PHPUnit\Framework\TestCase;

class EntityTestCreatorTest extends TestCase
{


    /**
     * @test
     */
    public function itCanCreateANewEntityTest()
    {
        $newObjectFqn = 'EdmondsCommerce\\DoctrineStaticMeta\\Entities\\TestEntityTest';
        $file         = $this->getCreator()->createTargetFileObject($newObjectFqn)->getTargetFile();
        $expected     = '<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entities;

use FQNFor\AbstractEntityTest;

class TestEntityTest extends AbstractEntityTest
{

}
';
        $actual       = $file->getContents();
        self::assertSame($expected, $actual);
    }

    private function getCreator(): EntityTestCreator
    {
        $namespaceHelper = new NamespaceHelper();
        $config          = new Config(ConfigTest::SERVER);

        return new EntityTestCreator(
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
    public function itCanCreateADeeplyNamespacedNewEntityFixture()
    {
        $newObjectFqn = 'EdmondsCommerce\\DoctrineStaticMeta\\Entities\\Deeply\\Nested\\Entities\\TestEntityTest';
        $file         = $this->getCreator()->createTargetFileObject($newObjectFqn)->getTargetFile();
        $expected     = '<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entities\Deeply\Nested\Entities;

use FQNFor\AbstractEntityTest;

class TestEntityTest extends AbstractEntityTest
{

}
';
        $actual       = $file->getContents();
        self::assertSame($expected, $actual);
    }


}