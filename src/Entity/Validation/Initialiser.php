<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Validation;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\PersistentCollection;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;

class Initialiser
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function initialise(EntityInterface $entity): void
    {
        $this->initialiseObject($entity);
        $getters = $entity::getDoctrineStaticMeta()->getGetters();
        foreach ($getters as $getter) {
            $got = $entity->$getter();
            if ($got instanceof EntityInterface) {
                $this->initialiseObject($got);
                continue;
            }
            if ($got instanceof PersistentCollection) {
                $this->initialiseObject($got);
                continue;
            }
        }
    }

    private function initialiseObject(object $object): void
    {
        $this->entityManager->initializeObject($object);
    }
}