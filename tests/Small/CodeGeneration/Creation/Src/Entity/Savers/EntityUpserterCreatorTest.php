<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Small\CodeGeneration\Creation\Src\Entity\Upserters;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\Savers\EntityUpserterCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\Factory\FileFactory;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\Factory\FindReplaceFactory;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\File\Writer;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\Config;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Small\ConfigTest;
use PHPUnit\Framework\TestCase;

/**
 * @covers \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\Savers\EntityUpserterCreator
 * @small
 */
class EntityUpserterCreatorTest extends TestCase
{
    private const BASE_NAMESPACE = 'EdmondsCommerce\DoctrineStaticMeta';

    /**
     * @test
     */
    public function itCanCreateANewDeeplyNestedEntityUpserter(): void
    {
        $entityName      = 'TestEntity';
        $nestedNamespace = '\\Deeply\\Ne\\S\\ted';
        $newObjectFqn    = self::BASE_NAMESPACE . "\\Entity\\Upserters$nestedNamespace\\${entityName}Upserter";
        $file            = $this->getCreator()->createTargetFileObject($newObjectFqn)->getTargetFile();
        $expected        = $this->getExceptedClass($entityName, $nestedNamespace);
        $actual          = $file->getContents();
        self::assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function itCanCreateANewDeeplyNestedEntityUpserterFromEntityFqn(): void
    {
        $entityName      = 'TestEntity';
        $nestedNamespace = '\\Deeply\\Ne\\S\\ted';
        $entityFqn       = self::BASE_NAMESPACE . "\\Entities$nestedNamespace\\$entityName";
        $file            = $this->getCreator()
                                ->setNewObjectFqnFromEntityFqn($entityFqn)
                                ->createTargetFileObject()
                                ->getTargetFile();
        $expected        = $this->getExceptedClass($entityName, $nestedNamespace);
        $actual          = $file->getContents();
        self::assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function itCanCreateANewEntityUpserter(): void
    {
        $entityName   = 'TestEntity';
        $newObjectFqn = self::BASE_NAMESPACE . "\\Entity\\Upserters\\${entityName}Upserter";
        $file         = $this->getCreator()->createTargetFileObject($newObjectFqn)->getTargetFile();
        $expected     = $this->getExceptedClass($entityName, '');
        $actual       = $file->getContents();
        self::assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function itCanCreateANewEntityUpserterFromEntityFqn(): void
    {
        $entityName = 'TestEntity';
        $entityFqn  = self::BASE_NAMESPACE . "\\Entities\\${entityName}";
        $file       = $this->getCreator()
                           ->setNewObjectFqnFromEntityFqn($entityFqn)
                           ->createTargetFileObject()
                           ->getTargetFile();
        $expected   = $this->getExceptedClass($entityName, '');
        $actual     = $file->getContents();
        self::assertSame($expected, $actual);
    }

    private function getCreator(): EntityUpserterCreator
    {
        $namespaceHelper = new NamespaceHelper();
        $config          = new Config(ConfigTest::SERVER);

        return new EntityUpserterCreator(
            new FileFactory($namespaceHelper, $config),
            $namespaceHelper,
            new Writer(),
            $config,
            new FindReplaceFactory()
        );
    }

    private function getExceptedClass(string $entityName, string $root = ''): string
    {
        $base            = self::BASE_NAMESPACE;
        $namespace       = "$base\\Entity\\Savers$root";
        $entity          = "$base\\Entities$root\\$entityName";
        $dto             = "$base\\Entity\\DataTransferObjects$root\\${entityName}Dto";
        $dtoFactory      = "$base\\Entity\\Factories$root\\${entityName}DtoFactory";
        $entityFactory   = "$base\\Entity\\Factories$root\\${entityName}Factory";
        $entityInterface = "$base\\Entity\\Interfaces$root\\${entityName}Interface";
        $repository      = "$base\\Entity\\Repositories$root\\${entityName}Repository";


        return <<<PHP
<?php

namespace $namespace;

use $entity;
use $dto;
use $dtoFactory;
use $entityFactory;
use $entityInterface;
use $repository;

class ${entityName}Upserter
{
    /**
     * @var ${entityName}DtoFactory
     */
    private \$dtoFactory;
    /**
     * @var array
     */
    private \$entities = [];
    /**
     * @var ${entityName}Factory
     */
    private \$entityFactory;
    /**
     * @var ${entityName}Repository
     */
    private \$repository;
    /**
     * @var ${entityName}Saver
     */
    private \$saver;

    public function __construct(
        ${entityName}Repository \$repository,
        ${entityName}DtoFactory \$dtoFactory,
        ${entityName}Factory \$entityFactory,
        ${entityName}Saver \$saver
    ) {
        \$this->repository    = \$repository;
        \$this->dtoFactory    = \$dtoFactory;
        \$this->entityFactory = \$entityFactory;
        \$this->saver         = \$saver;
    }

    public function getUpsertDtoByCriteria(array \$criteria): ${entityName}Dto
    {
        \$entity = \$this->repository->findOneBy(\$criteria);
        if (\$entity === null) {
            \$dto = \$this->dtoFactory->create();
            \$this->addDataToNewlyCreatedDto(\$dto);

            return \$dto;
        }

        if (!\$entity instanceof ${entityName}) {
            throw new \LogicException('We still need to choose between interfaces and concretions');
        }

        \$key                  = \$this->getKeyForEntity(\$entity);
        \$this->entities[\$key] = \$entity;

        return \$this->dtoFactory->createDtoFrom${entityName}(\$entity);
    }

    public function persistUpsertDto(${entityName}Dto \$dto): ${entityName}Interface
    {
        \$key = \$this->getKeyForDto(\$dto);
        if (!isset(\$this->entities[\$key])) {
            \$this->entities[\$key] = \$this->entityFactory->create(\$dto);
            \$this->saver->save(\$this->entities[\$key]);

            return \$this->entities[\$key];
        }
        \$this->entities[\$key]->update(\$dto);
        \$this->saver->save(\$this->entities[\$key]);

        return \$this->entities[\$key];
    }

    protected function addDataToNewlyCreatedDto(${entityName}Dto \$dto): void
    {
        /* Here you can add any information to the DTO that should be there */
    }

    /**
     * Each entity must by uniquely identifiable using a string. Normally we use the string representation of the UUID,
     * however if you are using something else for the ID, e.g. a Compound Key, int etc, then you can override this
     * method and generate a unique string for the DTO.
     *
     * Note that the output of this must match the output of getKeyForEntity exactly for the same DTO / Entity
     *
     * @param ${entityName}Dto \$dto
     *
     * @return string
     */
    protected function getKeyForDto(${entityName}Dto \$dto): string
    {
        return \$dto->getId()->toString();
    }

    /**
     * @param ${entityName} \$entity
     *
     * @return string
     * @see getKeyForDto
     */
    protected function getKeyForEntity(${entityName} \$entity): string
    {
        return \$entity->getId()->toString();
    }
}

PHP;

    }
}
