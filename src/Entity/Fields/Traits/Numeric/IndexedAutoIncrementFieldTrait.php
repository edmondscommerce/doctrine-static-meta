<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\Numeric;

// phpcs:disable Generic.Files.LineLength.TooLong

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\Builder\FieldBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\Numeric\IndexedAutoIncrementFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;

// phpcs:enable

/**
 *
 * ########### WARNING ############
 *
 * IF you want to access the auto increment values immediately after persisting the Entity, you will need to flush the
 * Entity from the unit of work before loading it from your repository
 *
 * Eg something like:
 * $this->getEntityManager()->getUnitOfWork()->clear(EntityFqn);
 * $withAutoIncValueSet=$this->getRepostitory()->loadEntity();
 *
 *
 */
trait IndexedAutoIncrementFieldTrait
{
    /**
     * @var int
     */
    private $indexedAutoIncrement;

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @param ClassMetadataBuilder $builder
     */
    public static function metaForIndexedAutoIncrement(ClassMetadataBuilder $builder): void
    {
        $fieldBuilder = new FieldBuilder(
            $builder,
            [
                'fieldName' => IndexedAutoIncrementFieldInterface::PROP_INDEXED_AUTO_INCREMENT,
                'type'      => Type::INTEGER,
            ]
        );
        $fieldBuilder
            ->columnName(
                MappingHelper::getColumnNameForField(
                    IndexedAutoIncrementFieldInterface::PROP_INDEXED_AUTO_INCREMENT
                )
            )
            ->nullable(false)
            ->unique(false)
            ->columnDefinition('INT AUTO_INCREMENT UNIQUE')
            ->build();
    }

    /**
     * @return int
     */
    public function getIndexedAutoIncrement(): ?int
    {
        return $this->indexedAutoIncrement;
    }

    private function initIndexedAutoIncrement()
    {
        $this->indexedAutoIncrement = IndexedAutoIncrementFieldInterface::DEFAULT_INDEXED_AUTO_INCREMENT;
    }
}
