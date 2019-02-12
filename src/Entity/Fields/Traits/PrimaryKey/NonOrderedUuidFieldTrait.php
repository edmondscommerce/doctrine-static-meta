<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\PrimaryKey;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Factories\UuidFactory;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use Ramsey\Uuid\UuidInterface;

trait NonOrderedUuidFieldTrait
{
    use AbstractUuidFieldTrait;

    public static function buildUuid(UuidFactory $uuidFactory): UuidInterface
    {
        return $uuidFactory->getUuid();
    }

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @see https://github.com/ramsey/uuid-doctrine#innodb-optimised-binary-uuids
     */
    protected static function metaForId(ClassMetadataBuilder $builder): void
    {
        $builder->createField('id', MappingHelper::TYPE_NON_ORDERED_BINARY_UUID)
                ->makePrimaryKey()
                ->nullable(false)
                ->unique(true)
                ->generatedValue('NONE')
                ->build();
    }
}
