<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\Fixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\EntitySaverFactory;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\EntitySaverInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\EntityGenerator\TestEntityGenerator;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\EntityGenerator\TestEntityGeneratorFactory;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
abstract class AbstractEntityFixtureLoader extends AbstractFixture implements OrderedFixtureInterface
{
    public const ORDER_FIRST = 1000;

    public const ORDER_DEFAULT = 2000;

    public const ORDER_LAST = 3000;

    public const BULK_AMOUNT_TO_GENERATE = 100;
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
     * @var int
     */
    protected $order = self::ORDER_DEFAULT;

    public function __construct(
        TestEntityGeneratorFactory $testEntityGeneratorFactory,
        EntitySaverFactory $saverFactory,
        ?FixtureEntitiesModifierInterface $modifier = null
    ) {
        $this->entityFqn           = $this->getEntityFqn();
        $this->saver               = $saverFactory->getSaverForEntityFqn($this->entityFqn);
        $this->testEntityGenerator = $testEntityGeneratorFactory->createForEntityFqn($this->entityFqn);
        if (null !== $modifier) {
            $this->setModifier($modifier);
        }
    }

    /**
     * @return int
     */
    public function getOrder(): int
    {
        return $this->order;
    }

    /**
     * @param int $order
     *
     * @return AbstractEntityFixtureLoader
     */
    public function setOrder(int $order): self
    {
        $this->order = $order;

        return $this;
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
        $entities = $this->loadBulk($manager);
        $this->updateGenerated($entities);
        $this->saver->saveAll($entities);
    }

    protected function updateGenerated(array &$entities)
    {
        if (null === $this->modifier) {
            return;
        }
        $this->modifier->modifyEntities($entities);
    }

    /**
     * @param EntityManagerInterface $entityManager
     *
     * @return array|EntityInterface[]
     * @throws \Doctrine\ORM\Mapping\MappingException
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @throws \ErrorException
     * @throws \ReflectionException
     */
    protected function loadBulk(EntityManagerInterface $entityManager): array
    {
        $entities = $this->testEntityGenerator->generateEntities(
            $entityManager,
            $this->entityFqn,
            static::BULK_AMOUNT_TO_GENERATE
        );
        foreach ($entities as $generated) {
            $this->testEntityGenerator->addAssociationEntities($entityManager, $generated);
        }

        return $entities;
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
            $this->entityFqn = \str_replace(
                '\\Assets\\EntityFixtures\\',
                '\\Entities\\',
                \substr(static::class, 0, -7)
            );
        }

        return $this->entityFqn;
    }
}
