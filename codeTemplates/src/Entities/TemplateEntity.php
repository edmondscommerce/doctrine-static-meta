<?php declare(strict_types=1);

namespace TemplateNamespace\Entities;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Traits\Fields\IdField;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Traits\UsesPHPMetaData;

class TemplateEntity
{
    use UsesPHPMetaData;

    use IdField;
}
