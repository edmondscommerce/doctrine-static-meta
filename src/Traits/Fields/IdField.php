<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Traits\Fields;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;

Trait IdField
{

    /**
     * @var int
     */
    private $id;

    protected static function getPropertyMetaForId(ClassMetadataBuilder $builder)
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
