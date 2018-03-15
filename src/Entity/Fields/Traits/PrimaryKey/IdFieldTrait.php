<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\PrimaryKey;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;

trait IdFieldTrait
{
    private $id;

    protected static function getPropertyDoctrineMetaForId(ClassMetadataBuilder $builder): void
    {
        $builder->createField('id', Type::INTEGER)
                ->makePrimaryKey()
                ->nullable(false)
                ->generatedValue('IDENTITY')
                ->build();
    }

    public function getId()
    {
        return $this->id;
    }
}