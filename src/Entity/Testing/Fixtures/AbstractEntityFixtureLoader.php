<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\Fixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\EntitySaverFactory;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\EntitySaverInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\EntityGenerator\TestEntityGenerator;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\EntityGenerator\TestEntityGeneratorFactory;
use Psr\Container\ContainerInterface;

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
    protected $testEntityGenerator;
    /**
     * @var EntitySaverInterface
     */
    protected $saver;

    /**
     * @var null|FixtureEntitiesModifierInterface
     */
    protected $modifier;

    /**
     * @var string
     */
    protected $entityFqn;
    /**
     * @var NamespaceHelper
     */
    protected $namespaceHelper;

    /**
     * @var TestEntityGeneratorFactory
     */
    private $testEntityGeneratorFactory;
    /**
     * @var ContainerInterface
     */
    private $container;

    private $usingReferences = true;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

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
            throw new \LogicException('You must override the REFERENCE_PREFIX constant in your Fixture');
        }
    }

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     *
     * @throws \Doctrine\ORM\Mapping\MappingException
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @throws \ErrorException
     * @throws \ReflectionException
     */
    public function load(ObjectManager $manager)
    {
        if (!$manager instanceof EntityManagerInterface) {
            throw new \RuntimeException(
                'Expecting $manager to be EntityManagerInterface but got ' . \get_class($manager)
            );
        }
        $this->testEntityGenerator = $this->testEntityGeneratorFactory->createForEntityFqn($this->entityFqn, $manager);
        $this->testEntityGenerator->assertSameEntityManagerInstance($manager);
        $entities = $this->loadBulk();
        if (count($entities) !== static::BULK_AMOUNT_TO_GENERATE) {
            throw new \RuntimeException(
                'generated ' . count($entities) .
                ' but the constant ' . get_class($this) . '::BULK_AMOUNT_TO_GENERATE is ' .
                static::BULK_AMOUNT_TO_GENERATE
            );
        }
        $this->updateGenerated($entities);
        $this->saver->saveAll($entities);
    }

    /**
     * @return array|EntityInterface[]
     * @throws \Doctrine\ORM\Mapping\MappingException
     * @throws \ErrorException
     * @throws \ReflectionException
     */
    protected function loadBulk(): array
    {
        $entities = $this->testEntityGenerator->generateEntities(
            static::BULK_AMOUNT_TO_GENERATE
        );
        $num      = 0;
        foreach ($entities as $generated) {
            $this->addReference(static::REFERENCE_PREFIX . $num++, $generated);
        }

        return $entities;
    }

    public function addReference($name, $object)
    {
        if (false === $this->usingReferences) {
            return;
        }
        parent::addReference($name, $object);
    }

    protected function updateGenerated(array &$entities)
    {
        if (null === $this->modifier) {
            return;
        }
        $this->modifier->modifyEntities($entities);
    }

    public function getReference($name): EntityInterface
    {
        $reference = parent::getReference($name);
        $this->entityManager->initializeObject($reference);
        if ($reference instanceof EntityInterface) {
            return $reference;
        }
        throw new \RuntimeException('Failed initialising refernce into Entity');
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
