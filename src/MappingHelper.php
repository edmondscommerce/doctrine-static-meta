<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta;

use Doctrine\Common\Util\Inflector;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;

class MappingHelper
{

    const TYPE_STRING   = 'string';
    const TYPE_DATETIME = 'dateTime';
    const TYPE_FLOAT    = 'float';
    const TYPE_DECIMAL  = 'decimal';
    const TYPE_INTEGER  = 'integer';

    public static function getSingularForFqn(string $entityFqn): string
    {
        $shortName = substr($entityFqn, strrpos($entityFqn, '\\') + 1);
        return lcfirst(Inflector::singularize($shortName));
    }

    public static function getPluralForFqn(string $entityFqn): string
    {
        $singular = self::getSingularForFqn($entityFqn);
        return Inflector::pluralize($singular);
    }

    /**
     * Set bog standard string fields quickly in bulk
     *
     * @param array                $fields
     * @param ClassMetadataBuilder $builder
     */
    public static function setSimpleStringFields(array $fields, ClassMetadataBuilder $builder)
    {
        foreach ($fields as $field) {
            $builder->createField($field, Type::STRING)
                    ->nullable(true)
                    ->length(255)
                    ->build();
        }
    }


    /**
     * Set bog standard float fields quickly in bulk
     *
     * @param array                $fields
     * @param ClassMetadataBuilder $builder
     */
    public static function setSimpleFloatFields(array $fields, ClassMetadataBuilder $builder)
    {
        foreach ($fields as $field) {
            $builder->createField($field, Type::FLOAT)
                    ->nullable(true)
                    ->precision(15)
                    ->scale(2)
                    ->build();
        }
    }

    /**
     * Set bog standard decimal fields quickly in bulk
     *
     * @param array                $fields
     * @param ClassMetadataBuilder $builder
     */
    public static function setSimpleDecimalFields(array $fields, ClassMetadataBuilder $builder)
    {
        foreach ($fields as $field) {
            $builder->createField($field, Type::DECIMAL)
                    ->nullable(true)
                    ->precision(18)
                    ->scale(12)
                    ->build();
        }
    }

    /**
     * Set bog standard dateTime fields quickly in bulk
     *
     * @param array                $fields
     * @param ClassMetadataBuilder $builder
     */
    public static function setSimpleDateTimeFields(array $fields, ClassMetadataBuilder $builder)
    {
        foreach ($fields as $field) {
            $builder->createField($field, Type::DATETIME)
                    ->nullable(true)
                    ->build();
        }
    }

    /**
     * Set bog standard integer fields quickly in bulk
     *
     * @param array                $fields
     * @param ClassMetadataBuilder $builder
     */
    public static function setSimpleIntegerFields(array $fields, ClassMetadataBuilder $builder)
    {
        foreach ($fields as $field) {
            $builder->createField($field, Type::INTEGER)
                    ->nullable(true)
                    ->build();
        }
    }


    /**
     * Bulk create multiple fields of different simple types
     *
     * @param array                $fieldToType [
     *                                          'fieldName'=>'fieldSimpleType'
     *                                          ]
     * @param ClassMetadataBuilder $builder
     */
    public static function setSimpleFields(array $fieldToType, ClassMetadataBuilder $builder)
    {
        foreach ($fieldToType as $field => $type) {
            $method = "setSimple$type" . 'fields';
            static::$method([$field], $builder);
        }
    }
}
