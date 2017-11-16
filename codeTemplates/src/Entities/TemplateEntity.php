<?php declare(strict_types=1);

namespace TemplateNamespace\Entities;

use EdmondsCommerce\DoctrineStaticMeta\Traits\Fields\IdField;
use EdmondsCommerce\DoctrineStaticMeta\Traits\UsesPHPMetaData;

class TemplateEntity
{
    use UsesPHPMetaData;

    use IdField;
}