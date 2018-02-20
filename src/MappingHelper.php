<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta;

use Doctrine\Common\Util\Inflector;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\Schema\Database;

class MappingHelper
{

    /**
     * Quick accessors for common types that are supported by methods in this helper
     */
    public const TYPE_STRING   = Type::STRING;
    public const TYPE_DATETIME = Type::DATETIME;
    public const TYPE_FLOAT    = Type::FLOAT;
    public const TYPE_DECIMAL  = Type::DECIMAL;
    public const TYPE_INTEGER  = Type::INTEGER;
    public const TYPE_TEXT     = Type::TEXT;

    /**
     * This is the list of common types, listed above
     */
    public const COMMON_TYPES = [
        self::TYPE_STRING,
        self::TYPE_DATETIME,
        self::TYPE_FLOAT,
        self::TYPE_DECIMAL,
        self::TYPE_INTEGER,
        self::TYPE_TEXT,
    ];

    /**
     * The PHP type associated with the mapping type
     */
    public const COMMON_TYPES_TO_PHP_TYPES = [
        self::TYPE_STRING   => 'string',
        self::TYPE_DATETIME => \DateTime::class,
        self::TYPE_FLOAT    => 'float',
        self::TYPE_DECIMAL  => 'float',
        self::TYPE_INTEGER  => 'int',
        self::TYPE_TEXT     => 'string',
    ];

    /**
     * This is the full list of mapping types
     */
    public const ALL_TYPES = [
        Type::TARRAY,
        Type::SIMPLE_ARRAY,
        Type::JSON,
        Type::BIGINT,
        Type::BOOLEAN,
        Type::DATETIME,
        Type::DATETIME_IMMUTABLE,
        Type::DATETIMETZ,
        Type::DATETIMETZ_IMMUTABLE,
        Type::DATE,
        Type::DATE_IMMUTABLE,
        Type::TIME,
        Type::TIME_IMMUTABLE,
        Type::DECIMAL,
        Type::INTEGER,
        Type::OBJECT,
        Type::SMALLINT,
        Type::STRING,
        Type::TEXT,
        Type::BINARY,
        Type::BLOB,
        Type::FLOAT,
        Type::GUID,
        Type::DATEINTERVAL,
    ];

    public function getPhpTypeForDbalType(string $dbalType)
    {

    }

    /**
     * @param string $entityFqn
     *
     * @return string
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function getSingularForFqn(string $entityFqn): string
    {
        $shortName = substr($entityFqn, strrpos($entityFqn, '\\') + 1);

        return lcfirst(Inflector::singularize($shortName));
    }

    /**
     * @param string $entityFqn
     *
     * @return string
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function getPluralForFqn(string $entityFqn): string
    {
        $singular = self::getSingularForFqn($entityFqn);

        return Inflector::pluralize($singular);
    }

    /**
     * @param string $entityFqn
     *
     * @return string
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function getTableNameForEntityFqn(
        string $entityFqn
    ): string {
        $namespaceHelper = new NamespaceHelper();
        $subFqn          = $namespaceHelper->getEntitySubNamespace(
            $entityFqn
        );
        $tableName       = \str_replace('\\', '', $subFqn);
        $tableName       = Inflector::tableize($tableName);
        if (\strlen($tableName) > Database::MAX_IDENTIFIER_LENGTH) {
            $tableName = substr($tableName, -Database::MAX_IDENTIFIER_LENGTH);
        }

        return $tableName;
    }

    /**
     * Set bog standard string fields quickly in bulk
     *
     * @param array                $fields
     * @param ClassMetadataBuilder $builder
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function setSimpleStringFields(array $fields, ClassMetadataBuilder $builder): void
    {
        foreach ($fields as $field) {
            $builder->createField($field, Type::STRING)
                    ->columnName(Inflector::tableize($field))
                    ->nullable(true)
                    ->length(255)
                    ->build();
        }
    }

    /**
     * Set bog standard text fields quickly in bulk
     *
     * @param array                $fields
     * @param ClassMetadataBuilder $builder
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function setSimpleTextFields(array $fields, ClassMetadataBuilder $builder): void
    {
        foreach ($fields as $field) {
            $builder->createField($field, Type::TEXT)
                    ->columnName(Inflector::tableize($field))
                    ->nullable(true)
                    ->build();
        }
    }


    /**
     * Set bog standard float fields quickly in bulk
     *
     * @param array                $fields
     * @param ClassMetadataBuilder $builder
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function setSimpleFloatFields(array $fields, ClassMetadataBuilder $builder): void
    {
        foreach ($fields as $field) {
            $builder->createField($field, Type::FLOAT)
                    ->columnName(Inflector::tableize($field))
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
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function setSimpleDecimalFields(array $fields, ClassMetadataBuilder $builder): void
    {
        foreach ($fields as $field) {
            $builder->createField($field, Type::DECIMAL)
                    ->columnName(Inflector::tableize($field))
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
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function setSimpleDateTimeFields(array $fields, ClassMetadataBuilder $builder): void
    {
        foreach ($fields as $field) {
            $builder->createField($field, Type::DATETIME)
                    ->columnName(Inflector::tableize($field))
                    ->nullable(true)
                    ->build();
        }
    }

    /**
     * Set bog standard integer fields quickly in bulk
     *
     * @param array                $fields
     * @param ClassMetadataBuilder $builder
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function setSimpleIntegerFields(array $fields, ClassMetadataBuilder $builder): void
    {
        foreach ($fields as $field) {
            $builder->createField($field, Type::INTEGER)
                    ->columnName(Inflector::tableize($field))
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
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function setSimpleFields(array $fieldToType, ClassMetadataBuilder $builder): void
    {
        foreach ($fieldToType as $field => $type) {
            $method = "setSimple$type".'fields';
            static::$method([$field], $builder);
        }
    }
}
