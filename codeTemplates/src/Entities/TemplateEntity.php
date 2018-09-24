<?php declare(strict_types=1);

namespace TemplateNamespace\Entities;

// phpcs:disable
use EdmondsCommerce\DoctrineStaticMeta\Entity as DSM;
use TemplateNamespace\Entity\Interfaces\TemplateEntityInterface;

class TemplateEntity implements TemplateEntityInterface
{
    // phpcs:enable
    use DSM\Traits\UsesPHPMetaDataTrait;

    use DSM\Traits\ValidatedEntityTrait;

    use DSM\Traits\ImplementNotifyChangeTrackingPolicy;

    use DSM\Fields\Traits\PrimaryKey\IdFieldTrait;


    public function __construct()
    {
        $this->runInitMethods();
    }
}
