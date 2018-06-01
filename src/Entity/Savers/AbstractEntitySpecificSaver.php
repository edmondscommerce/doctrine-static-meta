<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Savers;

use Doctrine\ORM\EntityManagerInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\PrimaryKey\IdFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;

abstract class AbstractEntitySpecificSaver extends EntitySaver
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var string
     */
    protected $entityFqn;


    /**
     *
     *
     * @param array $entities
     *
     * @throws DoctrineStaticMetaException
     * @throws \ReflectionException
     */
    public function saveAll(array $entities): void
    {
        if (empty($entities)) {
            return;
        }

        foreach ($entities as $entity) {
            $this->checkIsCorrectEntityType($entity);
            $this->entityManager->persist($entity);
        }

        $this->entityManager->flush();
    }


    /**
     * @param array $entities
     *
     * @throws DoctrineStaticMetaException
     * @throws \ReflectionException
     */
    public function removeAll(array $entities): void
    {
        foreach ($entities as $entity) {
            $this->checkIsCorrectEntityType($entity);
            $this->entityManager->remove($entity);
        }

        $this->entityManager->flush();
    }

    /**
     * @param IdFieldInterface $entity
     *
     * @return void
     * @throws DoctrineStaticMetaException
     * @throws \ReflectionException
     */
    protected function checkIsCorrectEntityType(IdFieldInterface $entity): void
    {
        $entityFqn = $this->getEntityFqn();

        if (!$entity instanceof $entityFqn) {
            $ref = new \ReflectionClass($entity);
            $msg = "[ {$ref->getName()} ] is not an instance of [ $entityFqn ]";
            throw new DoctrineStaticMetaException($msg);
        }
    }

    /**
     * Based on the convention that the Entity Specific Saver namespace has been generated,
     * We can do some simple find/replace to get the Entity namespace
     *
     * @return string
     */
    protected function getEntityFqn(): string
    {
        if (null === $this->entityFqn) {
            $this->entityFqn = \str_replace(
                '\\Entity\\Savers\\',
                '\\Entities\\',
                $this->cropSaver(static::class)
            );
        }

        return $this->entityFqn;
    }

    protected function cropSaver(string $fqn)
    {
        return \substr($fqn, 0, - \strlen('Saver'));
    }
}
