<?php declare(strict_types=1);

namespace TemplateNamespace\Entities;

// phpcs:disable
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity as DSM;
use TemplateNamespace\Entity\Interfaces\TemplateEntityInterface;
use TemplateNamespace\Entity\Repositories\TemplateEntityRepository;

class TemplateEntity implements TemplateEntityInterface
{
    // phpcs:enable
    use DSM\Traits\UsesPHPMetaDataTrait;

    use DSM\Traits\ValidatedEntityTrait;

    use DSM\Traits\ImplementNotifyChangeTrackingPolicy;

    use DSM\Fields\Traits\PrimaryKey\IdFieldTrait;


    public function __construct(DSM\Validation\EntityValidatorFactory $entityValidatorFactory)
    {
        $this->runInitMethods();
        $this->injectValidator($entityValidatorFactory->getEntityValidator());
    }

    /**
     * This is called in UsesPHPMetaDataTrait::loadClassDoctrineMetaData
     *
     * @param ClassMetadataBuilder $builder
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private static function setCustomRepositoryClass(ClassMetadataBuilder $builder)
    {
        $builder->setCustomRepositoryClass(TemplateEntityRepository::class);
    }


}
