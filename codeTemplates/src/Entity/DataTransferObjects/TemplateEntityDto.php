<?php declare(strict_types=1);

namespace TemplateNamespace\Entity\DataTransferObjects;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\DataTransferObjectInterface;

/**
 * This data transfer object should be used to hold unvalidated update data,
 * ready to be fed into the Entity::update method
 *
 * This class should never have any logic beyond getters and setters
 */
final class TemplateEntityDto implements DataTransferObjectInterface
{

}