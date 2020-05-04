<?php declare(strict_types=1);

namespace TemplateNamespace\Entity\Interfaces;

use EdmondsCommerce\DoctrineStaticMeta\Entity as DSM;

interface TemplateEntityInterface extends
    DSM\Interfaces\EntityInterface
{
    public const PROP_ENTITY_NAME_ID = 'template_entity_id';
}
