<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\DataTransferObjects;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;

interface DtoFactoryInterface
{
    public function createEmptyDtoFromEntityFqn(string $entityFqn);
}