<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\EntityGenerator;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\DoctrineStaticMeta;
use EdmondsCommerce\DoctrineStaticMeta\Entity\DataTransferObjects\DtoFactory;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Factory\EntityFactoryInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\DataTransferObjectInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;

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
     * @var FakerDataFiller
     */
    private $fakerDataFiller;


    /**
     * TestEntityGenerator constructor.
     *
     * @param DoctrineStaticMeta          $testedEntityDsm
     * @param EntityFactoryInterface|null $entityFactory
     * @param DtoFactory                  $dtoFactory
     * @param TestEntityGeneratorFactory  $testEntityGeneratorFactory
     * @param FakerDataFiller             $fakerDataFiller
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function __construct(
        DoctrineStaticMeta $testedEntityDsm,
        EntityFactoryInterface $entityFactory,
        DtoFactory $dtoFactory,
        TestEntityGeneratorFactory $testEntityGeneratorFactory,
        FakerDataFiller $fakerDataFiller
    ) {
        $this->testedEntityDsm            = $testedEntityDsm;
        $this->entityFactory              = $entityFactory;
        $this->dtoFactory                 = $dtoFactory;
        $this->testEntityGeneratorFactory = $testEntityGeneratorFactory;
        $this->fakerDataFiller            = $fakerDataFiller;
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
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @throws \ErrorException
     * @throws \ReflectionException
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
        $this->fakerDataFiller->fillDtoFieldsWithData($dto);

        return $dto;
    }

    /**
     * @param EntityInterface $generated
     *
     * @throws \ErrorException
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
                (\interface_exists($mappingEntityPluralInterface) &&
                 $testedEntityReflection->implementsInterface($mappingEntityPluralInterface))
                ||
                (\interface_exists($mappingEntityPluralInterfaceRequired)
                 && $testedEntityReflection->implementsInterface($mappingEntityPluralInterfaceRequired))
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
            } catch (\TypeError $e) {
                $currentlySet = null;
            }
            switch (true) {
                case $currentlySet === null:
                case $currentlySet === []:
                case $currentlySet instanceof Collection:
                    $mappingEntity = $this->testEntityGeneratorFactory
                        ->createForEntityFqn($mappingEntityFqn)
                        ->createEntityRelatedToEntity($generated);
                    $generated->$method($mappingEntity);
                    break;
            }
        }
    }

    /**
     * Stub of PHPUnit Assertion method
     *
     * @param mixed  $expected
     * @param mixed  $actual
     * @param string $error
     *
     * @throws \ErrorException
     */
    protected function assertSame($expected, $actual, string $error): void
    {
        if ($expected !== $actual) {
            throw new \ErrorException($error);
        }
    }

    /**
     * Stub of PHPUnit Assertion method
     *
     * @param mixed  $needle
     * @param array  $haystack
     * @param string $error
     *
     * @throws \ErrorException
     */
    protected function assertInArray($needle, array $haystack, string $error): void
    {
        if (false === \in_array($needle, $haystack, true)) {
            throw new \ErrorException($error);
        }
    }

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
        $this->fakerDataFiller->fillDtoFieldsWithData($dto);

        return $dto;
    }

    /**
     * Generate Entities.
     *
     * Optionally discard the first generated entities up to the value of offset
     *
     * @param EntityManagerInterface $entityManager
     * @param string                 $entityFqn
     * @param int                    $num
     *
     * @param int                    $offset
     *
     * @return array|EntityInterface[]
     * @throws \Doctrine\ORM\Mapping\MappingException
     * @throws \ReflectionException
     */
    public function generateEntities(
        EntityManagerInterface $entityManager,
        string $entityFqn,
        int $num,
        int $offset = 0
    ): array {

        return $this->generateUnsavedEntities($entityManager, $entityFqn, $num, $offset);
    }

    /**
     * Generate Entities but do not save them
     *
     * @param EntityManagerInterface $entityManager
     * @param string                 $entityFqn
     * @param int                    $num
     * @param int                    $offset
     *
     * @return array
     * @throws \Doctrine\ORM\Mapping\MappingException
     */
    public function generateUnsavedEntities(
        EntityManagerInterface $entityManager,
        string $entityFqn,
        int $num,
        int $offset = 0
    ): array {
        $this->entityManager = $entityManager;
        $entities            = [];
        $generator           = $this->getGenerator($entityManager, $entityFqn);
        $count               = 0;
        foreach ($generator as $entity) {
            $count++;
            if ($count + $offset === $num) {
                $this->entityManager->getUnitOfWork()->detach($entity);
                break;
            }
            $id = (string)$entity->getId();
            if (array_key_exists($id, $entities)) {
                throw new \RuntimeException('Entity with ID ' . $id . ' is already generated');
            }
            $entities[$id] = $entity;
        }

        return $entities;
    }

    public function getGenerator(EntityManagerInterface $entityManager, string $entityFqn): \Generator
    {
        $this->entityManager = $entityManager;
        while (true) {
            $dto    = $this->generateDto();
            $entity = $this->entityFactory->setEntityManager($entityManager)->create($entityFqn, $dto);
            yield $entity;
        }
    }
}
