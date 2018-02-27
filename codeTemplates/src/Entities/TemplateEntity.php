<?php declare(strict_types=1);

namespace TemplateNamespace\Entities;
// phpcs:disable
use EdmondsCommerce\DoctrineStaticMeta\Entity as DSM;
// phpcs:enable
class TemplateEntity implements TemplateNamespace\Entity\Interfaces\Interfaces\TemplateEntityInterface
{
    use DSM\Traits\UsesPHPMetaDataTrait;

    use DSM\Traits\ValidateTrait;

    use DSM\Fields\Traits\IdFieldTrait;
}
