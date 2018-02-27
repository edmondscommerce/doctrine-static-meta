<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;

trait IdFieldTrait
{

    /**
     * @var int
     */
    private $id;

    protected static function getPropertyDoctrineMetaForId(ClassMetadataBuilder $builder): void
    {
        $builder->createField('id', Type::INTEGER)
                ->makePrimaryKey()
                ->nullable(false)
                ->generatedValue('IDENTITY')
                ->build();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }
}
