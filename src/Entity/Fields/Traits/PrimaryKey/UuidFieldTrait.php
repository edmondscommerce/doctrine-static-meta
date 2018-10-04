<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\PrimaryKey;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use Ramsey\Uuid\Doctrine\UuidOrderedTimeGenerator;
use Ramsey\Uuid\UuidInterface;

trait UuidFieldTrait
{
    /**
     * @var UuidInterface
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
                ->generatedValue('CUSTOM')
                ->setCustomIdGenerator(UuidOrderedTimeGenerator::class)
                ->build();
    }

    public function getId(): ?UuidInterface
    {
        return $this->id;
    }

    /**
     * @param UuidInterface $id
     *
     * @return UuidFieldTrait
     */
    public function setId(UuidInterface $id)
    {
        if (null !== $this->id) {
            throw new \RuntimeException(
                'You can not overwrite a UUID that has already been set.' .
                ' This method should only be used for setting the ID on newly created Entities'
            );
        }
        $this->id = $id;

        return $this;
    }
}
