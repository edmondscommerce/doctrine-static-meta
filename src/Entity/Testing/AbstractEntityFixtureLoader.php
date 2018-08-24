<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Testing;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\EntitySaverFactory;

abstract class AbstractEntityFixtureLoader implements FixtureInterface
{
    protected const BULK_AMOUNT_TO_GENERATE = 100;
    /**
     * @var TestEntityGenerator
     */
    protected $testEntityGenerator;
    /**
     * @var EntitySaverFactory
     */
    protected $saverFactory;

    /**
     * @var null|FixtureEntitiesModifierInterface
     */
    protected $modifier;

    public function __construct(TestEntityGenerator $testEntityGenerator, EntitySaverFactory $saverFactory)
    {
        $this->testEntityGenerator = $testEntityGenerator;
        $this->saverFactory        = $saverFactory;
    }

    /**
     * Use this method to inject your own modifier that will recieve the array of generated entities and can then
     * update them as you see fit
     *
     * @param FixtureEntitiesModifierInterface $modifier
     */
    public function setModifier(FixtureEntitiesModifierInterface $modifier): void
    {
        $this->modifier = $modifier;
    }

    /**
     * @var string
     */
    protected $entityFqn;

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
        $entities = $this->loadBulk($manager);
        $this->updateGenerated($entities);
        $this->saverFactory->getSaverForEntity($this->entityFqn)->saveAll($entities);
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
            $this->entityFqn = \substr(static::class, 0, -7);
        }

        return $this->entityFqn;
    }
}
