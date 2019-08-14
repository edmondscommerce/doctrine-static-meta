<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Validation\Constraints;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use RuntimeException;
use Symfony\Component\Validator\ConstraintValidator;

abstract class AbstractEntityConstraintValidator extends ConstraintValidator
{
    /**
     * @return EntityInterface
     */
    protected function getEntity(): EntityInterface
    {
        $entity = $this->context->getObject();
        if ($entity instanceof EntityInterface) {
            return $entity;
        }
        throw new RuntimeException(
            'The object being validated is not an Entity, it is an instance of  ' . get_class($entity)
        );
    }
}
