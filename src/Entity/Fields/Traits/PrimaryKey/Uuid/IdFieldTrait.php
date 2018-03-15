<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\PrimaryKey\Uuid;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;

trait IdFieldTrait
{
    private $id;

    protected static function getPropertyDoctrineMetaForId(ClassMetadataBuilder $builder): void
    {
        $builder->createField('id', Type::GUID)
                ->makePrimaryKey()
                ->nullable(false)
                ->generatedValue('UUID')
                ->build();
    }

    public function getId()
    {
        return $this->id;
    }
}
