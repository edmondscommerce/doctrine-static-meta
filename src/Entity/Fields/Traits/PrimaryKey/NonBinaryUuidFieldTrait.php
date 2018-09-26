<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\PrimaryKey;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;

/**
 * This trait implements a text based UUID primary key which will then be stored as a string
 */
trait NonBinaryUuidFieldTrait
{
    /**
     * @var UuidInterface
     */
    private $id;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @see https://github.com/ramsey/uuid-doctrine#usage
     */
    protected static function metaForId(ClassMetadataBuilder $builder): void
    {
        $builder->createField('id', MappingHelper::TYPE_NON_BINARY_UUID)
                ->makePrimaryKey()
                ->nullable(false)
                ->unique(true)
                ->generatedValue('CUSTOM')
                ->setCustomIdGenerator(UuidGenerator::class)
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
    private function setId(UuidInterface $id)
    {
        if (null !== $this->id) {
            throw new \RuntimeException(
                'You can not overwrite a UUID that has alreasy been set.' .
                ' This method should only be used for setting the ID on newly created Entities'
            );
        }
        $this->id = $id;

        return $this;
    }
}
