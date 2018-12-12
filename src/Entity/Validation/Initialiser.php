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

    private $visited = [];

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function initialise(EntityInterface $entity): void
    {
        $this->visited = [];
        $this->initialiseObject($entity);
    }

    private function initialiseObject(object $object): void
    {
        if (true === $this->isVisited($object)) {
            return;
        }
        $this->setAsVisited($object);
        $this->entityManager->initializeObject($object);
        if ($object instanceof EntityInterface) {
            $this->initialiseProperties($object);
        }
    }

    private function isVisited(object $object): bool
    {
        return isset($this->visited[spl_object_hash($object)]);
    }

    private function setAsVisited(object $object): void
    {
        $this->visited[spl_object_hash($object)] = true;
    }

    private function initialiseProperties(EntityInterface $entity): void
    {
        $getters = $entity::getDoctrineStaticMeta()->getGetters();
        foreach ($getters as $getter) {
            $got = $entity->$getter();
            if (false === is_object($got)) {
                continue;
            }
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
}