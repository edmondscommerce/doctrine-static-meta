<?php declare(strict_types=1);

namespace TemplateNamespace\Entities;
// phpcs:disable
use EdmondsCommerce\DoctrineStaticMeta\Entity as DSM;
// phpcs:enable
class TemplateEntity implements
    DSM\Interfaces\UsesPHPMetaDataInterface,
    DSM\Interfaces\ValidateInterface,
    DSM\Fields\Interfaces\PrimaryKey\IdFieldInterface
{

    use DSM\Traits\UsesPHPMetaDataTrait;

    use DSM\Traits\ValidateTrait;

    use DSM\Fields\Traits\PrimaryKey\Id\IdFieldTrait;
}
