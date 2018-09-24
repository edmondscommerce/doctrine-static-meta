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

/**
 * @covers \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Tests\Entities\EntityTestCreator
 * @small
 */
class EntityTestCreatorTest extends TestCase
{
    private const TEST = '<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entities;

use EdmondsCommerce\DoctrineStaticMeta\Entities\AbstractEntityTest;

class TestEntityTest extends AbstractEntityTest
{

}
';

    private const TEST_NESTED = '<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entities\Deeply\Nested\Entities;

use EdmondsCommerce\DoctrineStaticMeta\Entities\AbstractEntityTest;

class TestEntityTest extends AbstractEntityTest
{

}
';

    /**
     * @test
     */
    public function itCanCreateANewEntityTest()
    {
        $newObjectFqn = 'EdmondsCommerce\\DoctrineStaticMeta\\Entities\\TestEntityTest';
        $file         = $this->getCreator()->createTargetFileObject($newObjectFqn)->getTargetFile();
        $expected     = self::TEST;
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
    public function itCanCreateANewEntityTestFromEntityFqn()
    {
        $entityFqn = 'EdmondsCommerce\\DoctrineStaticMeta\\Entities\\TestEntity';
        $file      = $this->getCreator()
                          ->setNewObjectFqnFromEntityFqn($entityFqn)
                          ->createTargetFileObject()
                          ->getTargetFile();
        $expected  = self::TEST;
        $actual    = $file->getContents();
        self::assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function itCanCreateADeeplyNamespacedNewEntityFixture()
    {
        $newObjectFqn = 'EdmondsCommerce\\DoctrineStaticMeta\\Entities\\Deeply\\Nested\\Entities\\TestEntityTest';
        $file         = $this->getCreator()->createTargetFileObject($newObjectFqn)->getTargetFile();
        $expected     = self::TEST_NESTED;
        $actual       = $file->getContents();
        self::assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function itCanCreateADeeplyNamespacedNewEntityFixtureFromEntityFqn()
    {
        $entityFqn = 'EdmondsCommerce\\DoctrineStaticMeta\\Entities\\Deeply\\Nested\\Entities\\TestEntity';
        $file      = $this->getCreator()
                          ->setNewObjectFqnFromEntityFqn($entityFqn)
                          ->createTargetFileObject()
                          ->getTargetFile();
        $expected  = self::TEST_NESTED;
        $actual    = $file->getContents();
        self::assertSame($expected, $actual);
    }
}
