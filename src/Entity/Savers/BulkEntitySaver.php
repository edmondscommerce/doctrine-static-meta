<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Savers;

class BulkEntitySaver extends AbstractBulkProcess
{
    protected function doSave(): void
    {
        foreach ($this->entitiesToSave as $entity) {
            $this->entityManager->persist($entity);
        }

        $this->entityManager->flush();
    }

}
