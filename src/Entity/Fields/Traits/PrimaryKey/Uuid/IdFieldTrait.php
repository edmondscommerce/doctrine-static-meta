<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\PrimaryKey\Uuid;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;

trait IdFieldTrait
{

    /**
     * @var string
     */
    private $id;

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
    public function getId(): string
    {
        return $this->id;
    }
}
