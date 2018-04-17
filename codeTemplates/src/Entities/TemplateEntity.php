<?php declare(strict_types=1);

namespace TemplateNamespace\Entities;
// phpcs:disable
use EdmondsCommerce\DoctrineStaticMeta\Entity as DSM;
use TemplateNamespace\Entity\Interfaces\TemplateEntityInterface;
// phpcs:enable
class TemplateEntity 
    implements TemplateEntityInterface
{
    use DSM\Traits\UsesPHPMetaDataTrait;

    use DSM\Traits\ValidateTrait;

    use DSM\Fields\Traits\PrimaryKey\IdFieldTrait;
}
