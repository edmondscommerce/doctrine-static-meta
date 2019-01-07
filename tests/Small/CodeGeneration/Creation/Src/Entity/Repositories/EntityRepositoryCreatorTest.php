<?php

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Small\CodeGeneration\Creation\Src\Entity\Repositories;


use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\Repositories\EntityRepositoryCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\Factory\FileFactory;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\Factory\FindReplaceFactory;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\File\Writer;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\Config;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Small\ConfigTest;
use PHPUnit\Framework\TestCase;


/**
 * @covers \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\Repositories\EntityRepositoryCreator
 * @covers \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\AbstractCreator
 * @small
 */
class EntityRepositoryCreatorTest extends TestCase
{
    private const REPOSITORY = <<<'PHP'
<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Repositories;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\TestEntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Repositories\AbstractEntityRepository as ProjectAbstractEntityRepository;


class TestEntityRepository extends ProjectAbstractEntityRepository
{
    public function find($id, ?int $lockMode = null, ?int $lockVersion = null): ?TestEntityInterface
    {
        $result = parent::find($id, $lockMode, $lockVersion);
        if ($result === null || $result instanceof TestEntityInterface) {
            return $result;
        }

        throw new \RuntimeException('Unknown entity type of ' . \get_class($result) . ' returned');
    }

    public function get($id, ?int $lockMode = null, ?int $lockVersion = null): TestEntityInterface
    {
        $result = parent::get($id, $lockMode, $lockVersion);
        if ($result instanceof TestEntityInterface) {
            return $result;
        }
        throw new \RuntimeException('Unknown entity type of ' . \get_class($result) . ' returned');
    }

    public function getOneBy(array $criteria, ?array $orderBy = null): TestEntityInterface
    {
        $result = $this->findOneBy($criteria, $orderBy);
        if ($result === null) {
            throw new \RuntimeException('Could not find the entity');
        }

        return $result;
    }

    public function findOneBy(array $criteria, ?array $orderBy = null): ?TestEntityInterface
    {
        $result = parent::findOneBy($criteria, $orderBy);
        if ($result === null || $result instanceof TestEntityInterface) {
            return $result;
        }

        throw new \RuntimeException('Unknown entity type of ' . \get_class($result) . ' returned');
    }

    /**
     * @param array      $criteria
     * @param array|null $orderBy
     * @param int|null   $limit
     * @param int|null   $offset
     *
     * @return TestEntityInterface[]|array|EntityInterface[]
     */
    public function findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null): array
    {
        return parent::findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @return TestEntityInterface[]|array|EntityInterface[]
     */
    public function findAll(): array
    {
        return parent::findAll();
    }




    public function getRandomOneBy(array $criteria): ?TestEntityInterface
    {
        $result = parent::getRandomOneBy($criteria);
        if ($result === null || $result instanceof TestEntityInterface) {
            return $result;
        }
        throw new \RuntimeException('Unknown entity type of ' . \get_class($result) . ' returned');
    }

    /**
     * @param array $criteria
     * @param int   $numToGet
     *
     * @return TestEntityInterface[]|array|EntityInterface[]
     */
    public function getRandomBy(array $criteria, int $numToGet = 1): array
    {
        return parent::getRandomBy($criteria, $numToGet);
    }

    /**
     * @param EntityInterface|TestEntityInterface $entity
     *
     * @return TestEntityInterface
     */
    public function initialiseEntity(EntityInterface $entity): TestEntityInterface
    {
        return parent::initialiseEntity($entity);
    }


}

PHP;

    private const NESTED_REPOSITORY = <<<'PHP'
<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Repositories\Super\Deeply\Nested;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\Super\Deeply\Nested\EntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\Super\Deeply\Nested\TestEntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Repositories\AbstractEntityRepository as ProjectAbstractEntityRepository;


class TestEntityRepository extends ProjectAbstractEntityRepository
{
    public function find($id, ?int $lockMode = null, ?int $lockVersion = null): ?TestEntityInterface
    {
        $result = parent::find($id, $lockMode, $lockVersion);
        if ($result === null || $result instanceof TestEntityInterface) {
            return $result;
        }

        throw new \RuntimeException('Unknown entity type of ' . \get_class($result) . ' returned');
    }

    public function get($id, ?int $lockMode = null, ?int $lockVersion = null): TestEntityInterface
    {
        $result = parent::get($id, $lockMode, $lockVersion);
        if ($result instanceof TestEntityInterface) {
            return $result;
        }
        throw new \RuntimeException('Unknown entity type of ' . \get_class($result) . ' returned');
    }

    public function getOneBy(array $criteria, ?array $orderBy = null): TestEntityInterface
    {
        $result = $this->findOneBy($criteria, $orderBy);
        if ($result === null) {
            throw new \RuntimeException('Could not find the entity');
        }

        return $result;
    }

    public function findOneBy(array $criteria, ?array $orderBy = null): ?TestEntityInterface
    {
        $result = parent::findOneBy($criteria, $orderBy);
        if ($result === null || $result instanceof TestEntityInterface) {
            return $result;
        }

        throw new \RuntimeException('Unknown entity type of ' . \get_class($result) . ' returned');
    }

    /**
     * @param array      $criteria
     * @param array|null $orderBy
     * @param int|null   $limit
     * @param int|null   $offset
     *
     * @return TestEntityInterface[]|array|EntityInterface[]
     */
    public function findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null): array
    {
        return parent::findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @return TestEntityInterface[]|array|EntityInterface[]
     */
    public function findAll(): array
    {
        return parent::findAll();
    }




    public function getRandomOneBy(array $criteria): ?TestEntityInterface
    {
        $result = parent::getRandomOneBy($criteria);
        if ($result === null || $result instanceof TestEntityInterface) {
            return $result;
        }
        throw new \RuntimeException('Unknown entity type of ' . \get_class($result) . ' returned');
    }

    /**
     * @param array $criteria
     * @param int   $numToGet
     *
     * @return TestEntityInterface[]|array|EntityInterface[]
     */
    public function getRandomBy(array $criteria, int $numToGet = 1): array
    {
        return parent::getRandomBy($criteria, $numToGet);
    }

    /**
     * @param EntityInterface|TestEntityInterface $entity
     *
     * @return TestEntityInterface
     */
    public function initialiseEntity(EntityInterface $entity): TestEntityInterface
    {
        return parent::initialiseEntity($entity);
    }


}

PHP;

    /**
     * @test
     */
    public function itCanCreateANewEntityRepository(): void
    {
        $newObjectFqn = 'EdmondsCommerce\\DoctrineStaticMeta\\Entity\\Repositories\\TestEntityRepository';
        $file         = $this->getCreator()->createTargetFileObject($newObjectFqn)->getTargetFile();
        $expected     = self::REPOSITORY;
        $actual       = $file->getContents();
        self::assertSame($expected, $actual);
    }

    private function getCreator(): EntityRepositoryCreator
    {
        $namespaceHelper = new NamespaceHelper();
        $config          = new Config(ConfigTest::SERVER);

        return new EntityRepositoryCreator(
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
    public function itCanCreateANewEntityRepositoryFromEntityFqn(): void
    {
        $entityFqn = 'EdmondsCommerce\\DoctrineStaticMeta\\Entities\\TestEntity';
        $file      = $this->getCreator()
                          ->setNewObjectFqnFromEntityFqn($entityFqn)
                          ->createTargetFileObject()
                          ->getTargetFile();
        $expected  = self::REPOSITORY;
        $actual    = $file->getContents();
        self::assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function itCanCreateANewDeeplyNestedEntityRepository(): void
    {
        $newObjectFqn =
            'EdmondsCommerce\\DoctrineStaticMeta\\Entity\\Repositories\\Super\\Deeply\\Nested\\TestEntityRepository';
        $file         = $this->getCreator()->createTargetFileObject($newObjectFqn)->getTargetFile();
        $expected     = self::NESTED_REPOSITORY;
        $actual       = $file->getContents();
        self::assertNotEmpty($actual);
        self::assertSame($expected, $actual);
    }


    /**
     * @test
     */
    public function itCanCreateANewDeeplyNestedEntityRepositoryFromEntityFqn(): void
    {
        $entityFqn = 'EdmondsCommerce\\DoctrineStaticMeta\\Entities\\Super\\Deeply\\Nested\\TestEntity';
        $file      = $this->getCreator()
                          ->setNewObjectFqnFromEntityFqn($entityFqn)
                          ->createTargetFileObject()
                          ->getTargetFile();
        $expected  = self::NESTED_REPOSITORY;
        $actual    = $file->getContents();
        self::assertNotEmpty($actual);
        self::assertSame($expected, $actual);
    }
}
