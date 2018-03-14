<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\PrimaryKey;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;

trait UuidFieldTrait
{

    /**
     * @var string
     */
    private $uuid;

    protected static function getPropertyDoctrineMetaForId(ClassMetadataBuilder $builder): void
    {
        $builder->createField('uuid', Type::GUID)
                ->makePrimaryKey()
                ->nullable(false)
                ->generatedValue('UUID')
                ->build();
    }

    /**
     * @return string
     */
    public function getUuid(): string
    {
        return $this->uuid;
    }
}
