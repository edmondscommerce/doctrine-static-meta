<?php
declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Small\CodeGeneration\Creation\Src\Entity\Factories;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\Factories\EntityDtoFactoryCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\Factory\FileFactory;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\Factory\FindReplaceFactory;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\File\Writer;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\Config;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Small\ConfigTest;
use PHPUnit\Framework\TestCase;

/**
 * @covers \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\Factories\EntityDtoFactoryCreator
 * @covers \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\AbstractCreator
 * @small
 */
class EntityDtoFactoryCreatorTest extends TestCase
{
    private const FACTORY = '<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Factories;

// phpcs:disable -- line length
use EdmondsCommerce\DoctrineStaticMeta\Entity\DataTransferObjects\DtoFactory;
use InvalidArgumentException;
use EdmondsCommerce\DoctrineStaticMeta\Entities\TestEntity;
use EdmondsCommerce\DoctrineStaticMeta\Entity\DataTransferObjects\TestEntityDto;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\TestEntityInterface;
use function get_class;

// phpcs: enable
class TestEntityDtoFactory
{
    public function __construct(private DtoFactory $dtoFactory)
    {
    }

    public function create(): TestEntityDto
    {
        return $this->dtoFactory->createEmptyDtoFromEntityFqn(TestEntity::class);
    }

    public function createDtoFromTestEntity(TestEntityInterface $entity): TestEntityDto
    {
        if (false === ($entity instanceof TestEntity)) {
            throw new InvalidArgumentException(
                \'Invalid Entity: expecting instance of \' . TestEntity::class
                . \', got \' . get_class($entity));
        }

        return $this->dtoFactory->createDtoFromEntity($entity);

    }

}
';

    private const NESTED_FACTORY = '<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Factories\Super\Deeply\Nested;

// phpcs:disable -- line length
use EdmondsCommerce\DoctrineStaticMeta\Entity\DataTransferObjects\Super\Deeply\Nested\DtoFactory;
use InvalidArgumentException;
use EdmondsCommerce\DoctrineStaticMeta\Entities\Super\Deeply\Nested\TestEntity;
use EdmondsCommerce\DoctrineStaticMeta\Entity\DataTransferObjects\Super\Deeply\Nested\TestEntityDto;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\Super\Deeply\Nested\TestEntityInterface;
use function get_class;

// phpcs: enable
class TestEntityDtoFactory
{
    public function __construct(private DtoFactory $dtoFactory)
    {
    }

    public function create(): TestEntityDto
    {
        return $this->dtoFactory->createEmptyDtoFromEntityFqn(TestEntity::class);
    }

    public function createDtoFromTestEntity(TestEntityInterface $entity): TestEntityDto
    {
        if (false === ($entity instanceof TestEntity)) {
            throw new InvalidArgumentException(
                \'Invalid Entity: expecting instance of \' . TestEntity::class
                . \', got \' . get_class($entity));
        }

        return $this->dtoFactory->createDtoFromEntity($entity);

    }

}
';

    /**
     * @test
     */
    public function itCanCreateANewEntityDtoFactory(): void
    {
        $newObjectFqn = 'EdmondsCommerce\\DoctrineStaticMeta\\Entity\\Factories\\TestEntityDtoFactory';
        $file         = $this->getCreator()->createTargetFileObject($newObjectFqn)->getTargetFile();
        $expected     = self::FACTORY;
        $actual       = $file->getContents();
        self::assertSame($expected, $actual);
    }

    private function getCreator(): EntityDtoFactoryCreator
    {
        $namespaceHelper = new NamespaceHelper();
        $config          = new Config(ConfigTest::SERVER);

        return new EntityDtoFactoryCreator(
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
    public function itCanCreateANewEntityDtoFactoryFromEntityFqn(): void
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
    public function itCanCreateANewDeeplyNestedEntityDtoFactory(): void
    {
        $newObjectFqn =
            'EdmondsCommerce\\DoctrineStaticMeta\\Entity\\Factories\\Super\\Deeply\\Nested\\TestEntityDtoFactory';
        $file         = $this->getCreator()->createTargetFileObject($newObjectFqn)->getTargetFile();
        $expected     = self::NESTED_FACTORY;
        $actual       = $file->getContents();
        self::assertNotEmpty($actual);
        self::assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function itCanCreateANewDeeplyNestedEntityDtoFactoryFromEntityFqn(): void
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
