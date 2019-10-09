<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\EntityGenerator;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\DoctrineStaticMeta;
use EdmondsCommerce\DoctrineStaticMeta\Entity\DataTransferObjects\DtoFactory;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Factory\EntityFactoryInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\DataTransferObjectInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use ErrorException;
use Generator;
use RuntimeException;
use TypeError;

use function in_array;
use function interface_exists;

/**
 * Class TestEntityGenerator
 *
 * This class handles utilising Faker to build up an Entity and then also possible build associated entities and handle
 * the association
 *
 * Unique columns are guaranteed to have a totally unique value in this particular process, but not between processes
 *
 * This Class provides you a few ways to generate test Entities, either in bulk or one at a time
 *ExcessiveClassComplexity
 *
 * @package EdmondsCommerce\DoctrineStaticMeta\Entity\Testing
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
class TestEntityGenerator
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var DoctrineStaticMeta
     */
    protected $testedEntityDsm;

    /**
     * @var EntityFactoryInterface
     */
    protected $entityFactory;
    /**
     * @var DtoFactory
     */
    private $dtoFactory;
    /**
     * @var TestEntityGeneratorFactory
     */
    private $testEntityGeneratorFactory;
    /**
     * @var FakerDataFillerInterface
     */
    private $fakerDataFiller;


    /**
     * TestEntityGenerator constructor.
     *
     * @param DoctrineStaticMeta          $testedEntityDsm
     * @param EntityFactoryInterface|null $entityFactory
     * @param DtoFactory                  $dtoFactory
     * @param TestEntityGeneratorFactory  $testEntityGeneratorFactory
     * @param FakerDataFillerInterface    $fakerDataFiller
     * @param EntityManagerInterface      $entityManager
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function __construct(
        DoctrineStaticMeta $testedEntityDsm,
        EntityFactoryInterface $entityFactory,
        DtoFactory $dtoFactory,
        TestEntityGeneratorFactory $testEntityGeneratorFactory,
        FakerDataFillerInterface $fakerDataFiller,
        EntityManagerInterface $entityManager
    ) {
        $this->testedEntityDsm            = $testedEntityDsm;
        $this->entityFactory              = $entityFactory;
        $this->dtoFactory                 = $dtoFactory;
        $this->testEntityGeneratorFactory = $testEntityGeneratorFactory;
        $this->fakerDataFiller            = $fakerDataFiller;
        $this->entityManager              = $entityManager;
    }


    public function assertSameEntityManagerInstance(EntityManagerInterface $entityManager): void
    {
        if ($entityManager === $this->entityManager) {
            return;
        }
        throw new RuntimeException('EntityManager instance is not the same as the one loaded in this factory');
    }

    /**
     * Use the factory to generate a new Entity, possibly with values set as well
     *
     * @param array $values
     *
     * @return EntityInterface
     */
    public function create(array $values = []): EntityInterface
    {
        $dto = $this->dtoFactory->createEmptyDtoFromEntityFqn($this->testedEntityDsm->getReflectionClass()->getName());
        if ([] !== $values) {
            foreach ($values as $property => $value) {
                $setter = 'set' . $property;
                $dto->$setter($value);
            }
        }

        return $this->entityFactory->create(
            $this->testedEntityDsm->getReflectionClass()->getName(),
            $dto
        );
    }

    /**
     * Generate an Entity. Optionally provide an offset from the first entity
     *
     * @return EntityInterface
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function generateEntity(): EntityInterface
    {
        return $this->createEntityWithData();
    }

    private function createEntityWithData(): EntityInterface
    {
        $dto = $this->generateDto();

        return $this->entityFactory->create($this->testedEntityDsm->getReflectionClass()->getName(), $dto);
    }

    public function generateDto(): DataTransferObjectInterface
    {
        $dto = $this->dtoFactory->createEmptyDtoFromEntityFqn(
            $this->testedEntityDsm->getReflectionClass()->getName()
        );
        $this->fakerUpdateDto($dto);

        return $dto;
    }

    public function fakerUpdateDto(DataTransferObjectInterface $dto): void
    {
        $this->fakerDataFiller->updateDtoWithFakeData($dto);
    }

    /**
     * @param EntityInterface $generated
     *
     * @throws ErrorException
     * @SuppressWarnings(PHPMD.ElseExpression)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function addAssociationEntities(
        EntityInterface $generated
    ): void {
        $testedEntityReflection = $this->testedEntityDsm->getReflectionClass();
        $class                  = $testedEntityReflection->getName();
        $meta                   = $this->testedEntityDsm->getMetaData();
        $mappings               = $meta->getAssociationMappings();
        if (empty($mappings)) {
            return;
        }
        $namespaceHelper = new NamespaceHelper();
        $methods         = array_map('strtolower', get_class_methods($generated));
        foreach ($mappings as $mapping) {
            $mappingEntityFqn                     = $mapping['targetEntity'];
            $errorMessage                         = "Error adding association entity $mappingEntityFqn to $class: %s";
            $mappingEntityPluralInterface         =
                $namespaceHelper->getHasPluralInterfaceFqnForEntity($mappingEntityFqn);
            $mappingEntityPluralInterfaceRequired =
                str_replace('\\Has', '\\HasRequired', $mappingEntityPluralInterface);
            if (
                (interface_exists($mappingEntityPluralInterface)
                 && $testedEntityReflection->implementsInterface($mappingEntityPluralInterface)
                )
                ||
                (interface_exists($mappingEntityPluralInterfaceRequired)
                 && $testedEntityReflection->implementsInterface($mappingEntityPluralInterfaceRequired)
                )
            ) {
                $this->assertSame(
                    $mappingEntityFqn::getDoctrineStaticMeta()->getPlural(),
                    $mapping['fieldName'],
                    sprintf($errorMessage, ' mapping should be plural')
                );
                $getter = 'get' . $mappingEntityFqn::getDoctrineStaticMeta()->getPlural();
                $method = 'add' . $mappingEntityFqn::getDoctrineStaticMeta()->getSingular();
            } else {
                $this->assertSame(
                    $mappingEntityFqn::getDoctrineStaticMeta()->getSingular(),
                    $mapping['fieldName'],
                    sprintf($errorMessage, ' mapping should be singular')
                );
                $getter = 'get' . $mappingEntityFqn::getDoctrineStaticMeta()->getSingular();
                $method = 'set' . $mappingEntityFqn::getDoctrineStaticMeta()->getSingular();
            }
            $this->assertInArray(
                strtolower($method),
                $methods,
                sprintf($errorMessage, $method . ' method is not defined')
            );
            try {
                $currentlySet = $generated->$getter();
            } catch (TypeError $e) {
                $currentlySet = null;
            }
            $this->addAssociation($generated, $method, $mappingEntityFqn, $currentlySet);
        }
    }

    /**
     * Stub of PHPUnit Assertion method
     *
     * @param mixed  $expected
     * @param mixed  $actual
     * @param string $error
     *
     * @throws ErrorException
     */
    protected function assertSame($expected, $actual, string $error): void
    {
        if ($expected !== $actual) {
            throw new ErrorException($error);
        }
    }

    /**
     * Stub of PHPUnit Assertion method
     *
     * @param mixed  $needle
     * @param array  $haystack
     * @param string $error
     *
     * @throws ErrorException
     */
    protected function assertInArray($needle, array $haystack, string $error): void
    {
        if (false === in_array($needle, $haystack, true)) {
            throw new ErrorException($error);
        }
    }

    private function addAssociation(
        EntityInterface $generated,
        string $setOrAddMethod,
        string $mappingEntityFqn,
        $currentlySet
    ): void {
        $testEntityGenerator = $this->testEntityGeneratorFactory
            ->createForEntityFqn($mappingEntityFqn);
        switch (true) {
            case $currentlySet === null:
            case $currentlySet === []:
            case $currentlySet instanceof Collection:
                $mappingEntity = $testEntityGenerator->createEntityRelatedToEntity($generated);
                break;
            default:
                return;
        }
        $generated->$setOrAddMethod($mappingEntity);
        $this->entityManager->persist($mappingEntity);
    }

    /**
     * @param EntityInterface $entity
     *
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod - it is being used)
     */
    private function createEntityRelatedToEntity(EntityInterface $entity)
    {
        $dto = $this->generateDtoRelatedToEntity($entity);

        return $this->entityFactory->create(
            $this->testedEntityDsm->getReflectionClass()->getName(),
            $dto
        );
    }

    public function generateDtoRelatedToEntity(EntityInterface $entity): DataTransferObjectInterface
    {
        $dto = $this->dtoFactory->createDtoRelatedToEntityInstance(
            $entity,
            $this->testedEntityDsm->getReflectionClass()->getName()
        );
        $this->fakerDataFiller->updateDtoWithFakeData($dto);

        return $dto;
    }

    /**
     * Generate Entities.
     *
     * Optionally discard the first generated entities up to the value of offset
     *
     * @param int $num
     *
     * @return array|EntityInterface[]
     */
    public function generateEntities(
        int $num
    ): array {
        $entities  = [];
        $generator = $this->getGenerator($num);
        foreach ($generator as $entity) {
            $id = (string)$entity->getId();
            if (array_key_exists($id, $entities)) {
                throw new RuntimeException('Entity with ID ' . $id . ' is already generated');
            }
            $entities[$id] = $entity;
        }

        return $entities;
    }

    public function getGenerator(int $numToGenerate = 100): Generator
    {
        $entityFqn = $this->testedEntityDsm->getReflectionClass()->getName();
        $generated = 0;
        while ($generated < $numToGenerate) {
            $dto    = $this->generateDto();
            $entity = $this->entityFactory->setEntityManager($this->entityManager)->create($entityFqn, $dto);
            yield $entity;
            $generated++;
        }
    }

    /**
     * @return EntityFactoryInterface
     */
    public function getEntityFactory(): EntityFactoryInterface
    {
        return $this->entityFactory;
    }

    /**
     * @return DtoFactory
     */
    public function getDtoFactory(): DtoFactory
    {
        return $this->dtoFactory;
    }

    /**
     * @return FakerDataFillerInterface
     */
    public function getFakerDataFiller(): FakerDataFillerInterface
    {
        return $this->fakerDataFiller;
    }

    /**
     * @return EntityManagerInterface
     */
    public function getEntityManager(): EntityManagerInterface
    {
        return $this->entityManager;
    }

    /**
     * @return TestEntityGeneratorFactory
     */
    public function getTestEntityGeneratorFactory(): TestEntityGeneratorFactory
    {
        return $this->testEntityGeneratorFactory;
    }
}
