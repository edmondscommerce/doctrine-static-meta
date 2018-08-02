<?php declare(strict_types=1);

namespace TemplateNamespace\Entities;

// phpcs:disable
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity as DSM;
use TemplateNamespace\Entity\Interfaces\TemplateEntityInterface;

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
        $this->injectDependencies($entityValidatorFactory);

    }

    /**
     * This method is called when your Entity is loaded from your EntityRepository
     *
     * It should duplicate the dependencies that are injected via the __construct
     *
     * You must have a property that is the ucfirst of the dependency shortName
     *
     * @param object[] ...$extraDependencies
     */
    public function injectDependencies(...$extraDependencies): void
    {
        foreach ($extraDependencies as $extraDependency) {
            $property        = ucfirst(basename(str_replace('\\', '/', \get_class($extraDependency))));
            $this->$property = $extraDependency;
        }
    }

    /**
     * In this method, we deliberately disable the concept of loading a repository via entityManager.
     *
     * This forces you to use dependency injection or manual instantiation of TemplateEntityRepository
     *
     * This is called in UsesPHPMetaDataTrait::loadClassDoctrineMetaData
     *
     * @param ClassMetadataBuilder $builder
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private static function setCustomRepositoryClass(ClassMetadataBuilder $builder)
    {
        $builder->setCustomRepositoryClass(DSM\Repositories\DisabledRepository::class);
    }


}
