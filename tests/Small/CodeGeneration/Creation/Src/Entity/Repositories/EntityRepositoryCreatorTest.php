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
    /**
     * @test
     */
    public function itCanCreateANewEntityRepository(): void
    {
        $newObjectFqn = 'EdmondsCommerce\\DoctrineStaticMeta\\Entity\\Repositories\\TestEntityRepository';
        $file         = $this->getCreator()->createTargetFileObject($newObjectFqn)->getTargetFile();
        $expected     = <<<'PHP'
<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Repositories;

use FQNFor\AbstractEntityRepository;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces;

// phpcs:disable -- line length
class TestEntityRepository extends AbstractEntityRepository
{
// phpcs: enable

    public function find($id, ?int $lockMode = null, ?int $lockVersion = null): ?TemplateEntityInterface
    {
        $result = parent::find($id, $lockMode, $lockVersion);
        if ($result === null || $result instanceof TemplateEntityInterface) {
            return $result;
        }

        throw new \RuntimeException('Unknown entity type of ' . \get_class($result) . ' returned');
    }

    public function get($id, ?int $lockMode = null, ?int $lockVersion = null): TemplateEntityInterface
    {
        $result = parent::get($id, $lockMode, $lockVersion);
        if ($result instanceof TemplateEntityInterface) {
            return $result;
        }
        throw new \RuntimeException('Unknown entity type of ' . \get_class($result) . ' returned');
    }

    public function getOneBy(array $criteria, ?array $orderBy = null): TemplateEntityInterface
    {
        $result = $this->findOneBy($criteria, $orderBy);
        if ($result === null) {
            throw new \RuntimeException('Could not find the entity');
        }

        return $result;
    }

    public function findOneBy(array $criteria, ?array $orderBy = null): ?TemplateEntityInterface
    {
        $result = parent::findOneBy($criteria, $orderBy);
        if ($result === null || $result instanceof TemplateEntityInterface) {
            return $result;
        }

        throw new \RuntimeException('Unknown entity type of ' . \get_class($result) . ' returned');
    }
}
PHP;
        $actual = $file->getContents();
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
     * @small
     */
    public function itCanCreateANewDeeplyNestedEntityFactory(): void
    {
        $newObjectFqn =
            'EdmondsCommerce\\DoctrineStaticMeta\\Entity\\Repositories\\Super\\Deeply\\Nested\\TestEntityRepository';
        $file         = $this->getCreator()->createTargetFileObject($newObjectFqn)->getTargetFile();
        $expected     = '';
        $actual       = $file->getContents();
        self::assertNotEmpty($actual);
        self::assertSame($expected, $actual);
    }
}
