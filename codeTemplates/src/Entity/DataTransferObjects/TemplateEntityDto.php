<?php declare(strict_types=1);
// phpcs:disable Generic.Files.LineLength.TooLong
namespace TemplateNamespace\Entity\DataTransferObjects;

use Doctrine\Common\Collections\ArrayCollection;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\DataTransferObjectInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use TemplateNamespace\Entities\TemplateEntity;

/**
 * This data transfer object should be used to hold potentially unvalidated update data,
 * ready to be fed into the Entity::update method
 *
 * You can choose to validate the DTO, but it the Entity will still be validated at the Entity::update stage
 *
 * Entity Properties use a single class property which can be either
 * - DataTransferObjectInterface
 * - EntityInterface
 *
 * This class should never have any logic beyond getters and setters
 * @SuppressWarnings(PHPMD)
 */
final class TemplateEntityDto implements DataTransferObjectInterface
{
    /**
     * These are required imports that we have in this comment to prevent PHPStorm from removing them
     *
     * @see ArrayCollection
     * @see EntityInterface
     */

    public const ENTITY_FQN = TemplateEntity::class;

    /**
     * This method is called by the Symfony validation component when loading the meta data
     *
     * In this method, we pass the meta data through to the Entity so that it can be configured
     *
     * @param ValidatorClassMetaData $metadata
     *
     * @throws DoctrineStaticMetaException
     */
    public static function loadValidatorMetaData(ValidatorClassMetaData $metadata): void
    {
        TemplateEntity::loadValidatorMetaData($metadata);
    }

    public static function getEntityFqn(): string
    {
        return self::ENTITY_FQN;
    }
}