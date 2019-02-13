<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Repositories;

use Doctrine\ORM\QueryBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use Ramsey\Uuid\UuidInterface;

class UuidQueryBuilder extends QueryBuilder
{
    public function setParameter($key, $value, $type = null): self
    {
        if ($value instanceof EntityInterface) {
            $value = $value->getId();
        }
        if ($value instanceof UuidInterface) {
            if (null === $type) {
                $type = MappingHelper::TYPE_UUID;
            }
            $value = $value->toString();
        }

        parent::setParameter($key, $value, $type);

        return $this;
    }
}
