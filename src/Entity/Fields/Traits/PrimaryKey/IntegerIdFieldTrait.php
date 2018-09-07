<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\PrimaryKey;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;

trait IntegerIdFieldTrait
{
    /**
     * @var int|null
     */
    private $id;

    protected static function metaForId(ClassMetadataBuilder $builder): void
    {
        $builder->createField('id', Type::INTEGER)
                ->makePrimaryKey()
                ->nullable(false)
                ->generatedValue('IDENTITY')
                ->build();
    }

    public function getId(): ?int
    {
        return $this->id;
    }
}
