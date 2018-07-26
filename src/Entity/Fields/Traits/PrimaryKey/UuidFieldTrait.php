<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\PrimaryKey;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;

trait UuidFieldTrait
{
    /**
     * @var string|null
     */
    private $id;

    protected static function metaForId(ClassMetadataBuilder $builder): void
    {
        $builder->createField('id', Type::GUID)
                ->makePrimaryKey()
                ->nullable(false)
                ->generatedValue('UUID')
                ->build();
    }

    public function getId(): ?string
    {
        return $this->id;
    }
}
