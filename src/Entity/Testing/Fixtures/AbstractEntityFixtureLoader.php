<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\Fixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\ReferenceRepository;
use Doctrine\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\MappingException;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\EntitySaverFactory;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\EntitySaverInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\EntityGenerator\TestEntityGenerator;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\EntityGenerator\TestEntityGeneratorFactory;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use ErrorException;
use LogicException;
use Psr\Container\ContainerInterface;
use ReflectionException;
use RuntimeException;

use function get_class;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
abstract class AbstractEntityFixtureLoader extends AbstractFixture
{
    /**
     * If you override the loadBulk method, please ensure you update this number to reflect the number of Entities you
     * are actually generating
     */
    public const BULK_AMOUNT_TO_GENERATE = 100;

    public const REFERENCE_PREFIX = 'OVERRIDE ME';

    /**
     * @var TestEntityGenerator
     */
    protected TestEntityGenerator $testEntityGenerator;
    /**
     * @var EntitySaverInterface
     */
    protected EntitySaverInterface $saver;

    /**
     * @var null|FixtureEntitiesModifierInterface
     */
    protected ?FixtureEntitiesModifierInterface $modifier;

    /**
     * @var string
     */
    protected string $entityFqn;
    /**
     * @var NamespaceHelper
     */
    protected NamespaceHelper $namespaceHelper;

    /**
     * @var TestEntityGeneratorFactory
     */
    private TestEntityGeneratorFactory $testEntityGeneratorFactory;
    /**
     * @var ContainerInterface
     */
    private ContainerInterface $container;

    private bool $usingReferences = false;
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;
    /**
     * @var bool
     */
    protected bool $generateCustomFixtures = false;
    /**
     * @var int
     */
    protected int $numberToGenerate;
    /**
     * @var array
     */
    protected array $customData;

    public function __construct(
        TestEntityGeneratorFactory $testEntityGeneratorFactory,
        EntitySaverFactory $saverFactory,
        NamespaceHelper $namespaceHelper,
        EntityManagerInterface $entityManager,
        ContainerInterface $container,
        ?FixtureEntitiesModifierInterface $modifier = null
    ) {
        $this->namespaceHelper = $namespaceHelper;
        $this->entityFqn       = $this->getEntityFqn();
        $this->saver           = $saverFactory->getSaverForEntityFqn($this->entityFqn);
        if (null !== $modifier) {
            $this->setModifier($modifier);
        }
        $this->testEntityGeneratorFactory = $testEntityGeneratorFactory;
        $this->container                  = $container;
        $this->assertReferencePrefixOverridden();
        $this->entityManager = $entityManager;
    }

    /**
     * Get the fully qualified name of the Entity we are testing,
     * assumes EntityNameTest as the entity class short name
     *
     * @return string
     */
    protected function getEntityFqn(): string
    {
        if (null === $this->entityFqn) {
            $this->entityFqn = $this->namespaceHelper->getEntityFqnFromFixtureFqn(static::class);
        }

        return $this->entityFqn;
    }

    /**
     * Use this method to inject your own modifier that will receive the array of generated entities and can then
     * update them as you see fit
     *
     * @param FixtureEntitiesModifierInterface $modifier
     */
    public function setModifier(FixtureEntitiesModifierInterface $modifier): void
    {
        $this->modifier = $modifier;
    }

    private function assertReferencePrefixOverridden(): void
    {
        if (static::REFERENCE_PREFIX === self::REFERENCE_PREFIX) {
            throw new LogicException('You must override the REFERENCE_PREFIX constant in your Fixture');
        }
    }

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     *
     * @throws ReflectionException
     */
    public function load(ObjectManager $manager)
    {
        if (!$manager instanceof EntityManagerInterface) {
            throw new RuntimeException(
                'Expecting $manager to be EntityManagerInterface but got ' . get_class($manager)
            );
        }
        $this->testEntityGenerator = $this->testEntityGeneratorFactory->createForEntityFqn($this->entityFqn, $manager);
        $this->testEntityGenerator->assertSameEntityManagerInstance($manager);
        $entities = $this->loadBulk();
        $this->validateNumberGenerated($entities);
        $this->updateGenerated($entities);
        $this->saver->saveAll($entities);
    }

    /**
     * This method can be used to generate ad hoc fixture with specified data. To use it pass in an array of arrays,
     * with each child array keyed with the properties that you want to set, e.g.
     * [
     *      [
     *          'propertyOne' => true,
     *          'propertyTwo' => DifferentEntity,
     *          ...
     *      ],
     *      ...
     * ]
     *
     * The entity will be created as normal, with Faker data used to populate each field and then the data in the array
     * will be used to override the properties
     *
     * @param array $customData
     */
    public function setCustomData(array $customData): void
    {
        $this->numberToGenerate       = count($customData);
        $this->customData             = $customData;
        $this->generateCustomFixtures = true;
    }

    /**
     * This method will be used to check that the number of entities generated matches the number expected. If you are
     * generating custom fixtures then this will check the number generated matches the number of custom array items
     * passed in, otherwise it will check the constant defined in the class
     *
     * @param array $entities
     */
    protected function validateNumberGenerated(array $entities): void
    {
        $expected = $this->generateCustomFixtures === true ? $this->numberToGenerate : static::BULK_AMOUNT_TO_GENERATE;
        if (count($entities) !== $expected) {
            throw new RuntimeException(
                'generated ' . count($entities) .
                ' but the constant ' . get_class($this) . '::BULK_AMOUNT_TO_GENERATE is ' . $expected
            );
        }
    }

    /**
     * @return array|EntityInterface[]
     */
    protected function loadBulk(): array
    {
        if ($this->generateCustomFixtures === true) {
            return $this->generateCustomFixtures();
        }
        $entities = $this->testEntityGenerator->generateEntities(
            static::BULK_AMOUNT_TO_GENERATE
        );
        $num      = 0;
        foreach ($entities as $generated) {
            $this->addReference(static::REFERENCE_PREFIX . $num++, $generated);
        }

        return $entities;
    }

    /**
     * This loops over the custom array and passes the data to to a function used to create and update the entity
     *
     * @return array
     */
    protected function generateCustomFixtures(): array
    {
        $customFixtures = [];
        for ($numberToGenerate = 0; $numberToGenerate < $this->numberToGenerate; $numberToGenerate++) {
            $customFixtures[] = $this->generateCustomFixture($this->customData[$numberToGenerate], $numberToGenerate);
        }

        return $customFixtures;
    }

    /**
     * This is used to create the custom entity. It can be overwritten if you want to use customise it further, e.g.
     * using the same vaule for each entity. The method is passed the array of custom data and the number, zero indexed,
     * of the entity being generated
     *
     * @param array $customData
     * @param int   $fixtureNumber
     * @SuppressWarnings(PHPMD.UnusedFormalParameter) - We don't need the fixture number in this method, but it may be
     *                                                useful if the method is overwritten
     *
     * @return EntityInterface
     */
    protected function generateCustomFixture(array $customData, int $fixtureNumber): EntityInterface
    {
        return $this->testEntityGenerator->create($customData);
    }

    public function addReference($name, $object)
    {
        if (false === $this->usingReferences) {
            return;
        }
        parent::addReference($name, $object);
    }

    protected function updateGenerated(array &$entities): void
    {
        if (null === $this->modifier) {
            return;
        }
        $this->modifier->modifyEntities($entities);
    }

    public function setReferenceRepository(ReferenceRepository $referenceRepository)
    {
        $this->setUsingReferences(true);
        parent::setReferenceRepository($referenceRepository); // TODO: Change the autogenerated stub
    }

    /**
     * @param bool $usingReferences
     *
     * @return AbstractEntityFixtureLoader
     */
    public function setUsingReferences(bool $usingReferences): AbstractEntityFixtureLoader
    {
        $this->usingReferences = $usingReferences;

        return $this;
    }

    public function getReference($name): EntityInterface
    {
        $reference = parent::getReference($name);
        $this->entityManager->initializeObject($reference);
        if ($reference instanceof EntityInterface) {
            return $reference;
        }
        throw new RuntimeException('Failed initialising refernce into Entity');
    }

    /**
     * Generally we should avoid using the container as a service locator, however for test assets it is acceptable if
     * really necessary
     *
     * @return ContainerInterface
     */
    protected function getContainer(): ContainerInterface
    {
        return $this->container;
    }
}
