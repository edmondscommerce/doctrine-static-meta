<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Savers;

use Doctrine\ORM\EntityManager;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;

class EntitySaverFactory
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var EntitySaver
     */
    protected $genericEntitySaver;
    /**
     * @var EntitySaver
     */
    protected $genericSaver;
    /**
     * @var NamespaceHelper
     */
    protected $namespaceHelper;

    public function __construct(
        EntityManager $entityManager,
        EntitySaver $genericSaver,
        NamespaceHelper $namespaceHelper
    ) {
        $this->entityManager   = $entityManager;
        $this->genericSaver    = $genericSaver;
        $this->namespaceHelper = $namespaceHelper;
    }

    /**
     * Gets the Entity Specific Saver if one is defined, otherwise the standard Entity Saver
     *
     * @param EntityInterface $entity
     *
     * @return EntitySaverInterface
     */
    public function getSaverForEntity(
        EntityInterface $entity
    ): EntitySaverInterface {
        return $this->getSaverForEntityFqn($this->namespaceHelper->getObjectFqn($entity));
    }

    /**
     * @param string $entityFqn
     *
     * @return EntitySaverInterface
     */
    public function getSaverForEntityFqn(string $entityFqn): EntitySaverInterface
    {
        $saverFqn = $this->getSaverFqn($entityFqn);
        if (class_exists($saverFqn)) {
            return new $saverFqn($this->entityManager, $this->namespaceHelper);
        }

        return $this->genericSaver;
    }

    /**
     * Get the fully qualified name of the saver for the entity we are testing.
     *
     * @param string $entityFqn
     *
     * @return string
     */
    protected function getSaverFqn(
        string $entityFqn
    ): string {

        return \str_replace(
                   'Entities',
                   'Entity\\Savers',
                   $entityFqn
               ).'Saver';
    }
}
