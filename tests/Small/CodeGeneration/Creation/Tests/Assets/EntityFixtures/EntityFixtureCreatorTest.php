<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Small\CodeGeneration\Creation\Tests\Assets\EntityFixtures;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Tests\Assets\EntityFixtures\EntityFixtureCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\Factory\FileFactory;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\Factory\FindReplaceFactory;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\File\Writer;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\Config;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Small\ConfigTest;
use PHPUnit\Framework\TestCase;

/**
 * @covers \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Tests\Assets\EntityFixtures\EntityFixtureCreator
 * @small
 */
class EntityFixtureCreatorTest extends TestCase
{
    /**
     * @test
     */
    public function itCanCreateANewEntityFixture()
    {
        $newObjectFqn = 'EdmondsCommerce\\DoctrineStaticMeta\\Entities\\TestEntity';
        $file         = $this->getCreator()->createTargetFileObject($newObjectFqn)->getTargetFile();
        $expected     = '<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Assets\EntityFixtures;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\Fixtures\AbstractEntityFixtureLoader;

class TestEntity extends AbstractEntityFixtureLoader
{

}
';
        $actual       = $file->getContents();
        self::assertSame($expected, $actual);
    }

    private function getCreator(): EntityFixtureCreator
    {
        $namespaceHelper = new NamespaceHelper();
        $config          = new Config(ConfigTest::SERVER);

        return new EntityFixtureCreator(
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
        $newObjectFqn = 'EdmondsCommerce\\DoctrineStaticMeta\\Entities\\Deeply\\Nested\\Entities\\TestEntity';
        $file         = $this->getCreator()->createTargetFileObject($newObjectFqn)->getTargetFile();
        $expected     = '<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Assets\EntityFixtures;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\Deeply\Nested\Entities\Fixtures\AbstractEntityFixtureLoader;

class TestEntity extends AbstractEntityFixtureLoader
{

}
';
        $actual       = $file->getContents();
        self::assertSame($expected, $actual);
    }
}