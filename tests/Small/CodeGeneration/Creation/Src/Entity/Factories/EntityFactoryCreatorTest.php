<?php
declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Small\CodeGeneration\Creation\Src\Entity\Factories;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\Factories\EntityFactoryCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\Factory\FileFactory;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\Factory\FindReplaceFactory;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\File\Writer;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\Config;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Small\ConfigTest;
use PHPUnit\Framework\TestCase;

/**
 * @covers \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\Factories\EntityFactoryCreator
 * @covers \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\AbstractCreator
 * @small
 */
class EntityFactoryCreatorTest extends TestCase
{
    private const FACTORY = '<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Factories;

// phpcs:disable -- line length
use EdmondsCommerce\DoctrineStaticMeta\Entities\TestEntity;
use EdmondsCommerce\DoctrineStaticMeta\Entity\DataTransferObjects\TestEntityDto;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Factories\AbstractEntityFactory as DsmAbstractEntityFactory;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\TestEntityInterface;

// phpcs: enable
class TestEntityFactory extends DsmAbstractEntityFactory
{
    public function create(TestEntityDto $dto = null): TestEntityInterface
    {
        return $this->entityFactory->create(TestEntity::class, $dto);
    }
}
';

    private const NESTED_FACTORY = '<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Factories\Super\Deeply\Nested;

// phpcs:disable -- line length
use EdmondsCommerce\DoctrineStaticMeta\Entities\Super\Deeply\Nested\TestEntity;
use EdmondsCommerce\DoctrineStaticMeta\Entity\DataTransferObjects\Super\Deeply\Nested\TestEntityDto;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Factories\AbstractEntityFactory as DsmAbstractEntityFactory;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\Super\Deeply\Nested\TestEntityInterface;

// phpcs: enable
class TestEntityFactory extends DsmAbstractEntityFactory
{
    public function create(TestEntityDto $dto = null): TestEntityInterface
    {
        return $this->entityFactory->create(TestEntity::class, $dto);
    }
}
';

    /**
     * @test
     */
    public function itCanCreateANewEntityFactory(): void
    {
        $newObjectFqn = 'EdmondsCommerce\\DoctrineStaticMeta\\Entity\\Factories\\TestEntityFactory';
        $file         = $this->getCreator()->createTargetFileObject($newObjectFqn)->getTargetFile();
        $expected     = self::FACTORY;
        $actual       = $file->getContents();
        self::assertSame($expected, $actual);
    }

    private function getCreator(): EntityFactoryCreator
    {
        $namespaceHelper = new NamespaceHelper();
        $config          = new Config(ConfigTest::SERVER);

        return new EntityFactoryCreator(
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
    public function itCanCreateANewEntityFactoryFromEntityFqn(): void
    {
        $entityFqn = 'EdmondsCommerce\\DoctrineStaticMeta\\Entities\\TestEntity';
        $file      = $this->getCreator()
                          ->setNewObjectFqnFromEntityFqn($entityFqn)
                          ->createTargetFileObject()
                          ->getTargetFile();
        $expected  = self::FACTORY;
        $actual    = $file->getContents();
        self::assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function itCanCreateANewDeeplyNestedEntityFactory(): void
    {
        $newObjectFqn =
            'EdmondsCommerce\\DoctrineStaticMeta\\Entity\\Factories\\Super\\Deeply\\Nested\\TestEntityFactory';
        $file         = $this->getCreator()->createTargetFileObject($newObjectFqn)->getTargetFile();
        $expected     = self::NESTED_FACTORY;
        $actual       = $file->getContents();
        self::assertNotEmpty($actual);
        self::assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function itCanCreateANewDeeplyNestedEntityFactoryFromEntityFqn(): void
    {
        $entityFqn = 'EdmondsCommerce\\DoctrineStaticMeta\\Entities\\Super\\Deeply\\Nested\\TestEntity';
        $file      = $this->getCreator()
                          ->setNewObjectFqnFromEntityFqn($entityFqn)
                          ->createTargetFileObject()
                          ->getTargetFile();
        $expected  = self::NESTED_FACTORY;
        $actual    = $file->getContents();
        self::assertNotEmpty($actual);
        self::assertSame($expected, $actual);
    }
}
