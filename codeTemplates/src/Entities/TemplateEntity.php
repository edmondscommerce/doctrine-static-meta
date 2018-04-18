<?php declare(strict_types=1);

namespace TemplateNamespace\Entities;

// phpcs:disable
use EdmondsCommerce\DoctrineStaticMeta\Entity as DSM;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\Validation\EntityValidatorInterface;
use TemplateNamespace\Entity\Interfaces\TemplateEntityInterface;

class TemplateEntity implements TemplateEntityInterface
{
    // phpcs:enable
    use DSM\Traits\UsesPHPMetaDataTrait;

    use DSM\Traits\ValidatedEntityTrait;

    use DSM\Fields\Traits\PrimaryKey\IdFieldTrait;

    public function __construct(EntityValidatorInterface $validator)
    {
        $this->setValidator($validator);
        $this->runInitMethods();
    }
}
