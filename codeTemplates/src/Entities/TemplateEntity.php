<?php declare(strict_types=1);

namespace TemplateNamespace\Entities;

use EdmondsCommerce\DoctrineStaticMeta\Entity as DSM;

class TemplateEntity implements DSM\Interfaces\UsesPHPMetaDataInterface
{
    use DSM\Traits\UsesPHPMetaData;

    use DSM\Traits\Fields\IdField;
}
