<?php declare(strict_types=1);

namespace TemplateNamespace\Entities;

use EdmondsCommerce\DoctrineStaticMeta\Entity as DSM;

class TemplateEntity implements
    DSM\Interfaces\UsesPHPMetaDataInterface,
    DSM\Interfaces\ValidateInterface,
    DSM\Fields\Interfaces\IdFieldInterface
{

    use DSM\Traits\UsesPHPMetaDataTrait;

    use DSM\Traits\ValidateTrait;

    use DSM\Fields\Traits\IdFieldTrait;
}
