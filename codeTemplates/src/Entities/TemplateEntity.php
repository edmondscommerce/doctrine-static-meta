<?php declare(strict_types=1);

namespace TemplateNamespace\Entities;

use EdmondsCommerce\DoctrineStaticMeta\Entity as DSM;
use TemplateNamespace\Entity\Interfaces\TemplateEntityInterface;

class TemplateEntity implements TemplateEntityInterface
{
    use DSM\DSMEntityTrait;
    use DSM\Fields\Traits\PrimaryKey\IdFieldTrait;
}
