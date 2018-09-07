<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\PrimaryKey;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use Ramsey\Uuid\Doctrine\UuidOrderedTimeGenerator;

trait UuidFieldTrait
{
    /**
     * @var string|null
     */
    private $id;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @see https://github.com/ramsey/uuid-doctrine#innodb-optimised-binary-uuids
     */
    protected static function metaForId(ClassMetadataBuilder $builder): void
    {
        $builder->createField('id', MappingHelper::TYPE_UUID)
                ->makePrimaryKey()
                ->nullable(false)
                ->unique(true)
                ->setCustomIdGenerator(UuidOrderedTimeGenerator::class)
                ->build();
    }

    public function getId(): ?string
    {
        return $this->id;
    }
}
