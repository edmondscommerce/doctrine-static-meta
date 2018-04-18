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
    public const TYPE_BOOLEAN  = Type::BOOLEAN;

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
        self::TYPE_BOOLEAN,
    ];

    public const PHP_TYPE_STRING   = 'string';
    public const PHP_TYPE_DATETIME = '\\'.\DateTime::class;
    public const PHP_TYPE_FLOAT    = 'float';
    public const PHP_TYPE_DECIMAL  = 'string';
    public const PHP_TYPE_INTEGER  = 'int';
    public const PHP_TYPE_TEXT     = 'string';
    public const PHP_TYPE_BOOLEAN  = 'bool';

    public const PHP_TYPES = [
        self::PHP_TYPE_STRING,
        self::PHP_TYPE_DATETIME,
        self::PHP_TYPE_FLOAT,
        self::PHP_TYPE_DECIMAL,
        self::PHP_TYPE_INTEGER,
        self::PHP_TYPE_TEXT,
        self::PHP_TYPE_BOOLEAN,
    ];

    /**
     * The PHP type associated with the mapping type
     */
    public const COMMON_TYPES_TO_PHP_TYPES = [
        self::TYPE_STRING   => self::PHP_TYPE_STRING,
        self::TYPE_DATETIME => self::PHP_TYPE_DATETIME,
        self::TYPE_FLOAT    => self::PHP_TYPE_FLOAT,
        self::TYPE_DECIMAL  => self::PHP_TYPE_DECIMAL,
        self::TYPE_INTEGER  => self::PHP_TYPE_INTEGER,
        self::TYPE_TEXT     => self::PHP_TYPE_TEXT,
        self::TYPE_BOOLEAN  => self::PHP_TYPE_BOOLEAN,
    ];

    /**
     * This is the full list of mapping types
     *
     * @see \Doctrine\DBAL\Types\Type
     */
    public const ALL_DBAL_TYPES = [
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

    public const MIXED_TYPES = [
        // Doctrine hydrates decimal values as strings.
        // However, setting these using an int or float is also valid.
        Type::DECIMAL,
    ];

    /**
     * Wrap the name in backticks
     *
     * @param string $name
     *
     * @return string
     */
    public static function backticks(string $name): string
    {
        return '`'.$name.'`';
    }

    /**
     * @param string $entityFqn
     *
     * @return string
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function getSingularForFqn(string $entityFqn): string
    {
        $shortName = self::getShortNameForFqn($entityFqn);

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
     */
    public static function getShortNameForFqn(string $entityFqn): string
    {
        return substr($entityFqn, strrpos($entityFqn, '\\') + 1);
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
        $tableName       = self::backticks(Inflector::tableize($tableName));
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
     * @param bool                 $isNullable
     * @SuppressWarnings(PHPMD.StaticAccess)
     * In this case the boolean argument is simply data
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public static function setSimpleStringFields(
        array $fields,
        ClassMetadataBuilder $builder,
        bool $isNullable = true
    ): void {
        foreach ($fields as $field) {
            $builder->createField($field, Type::STRING)
                    ->columnName(self::backticks(Inflector::tableize($field)))
                    ->nullable($isNullable)
                    ->length(255)
                    ->build();
        }
    }

    /**
     * Set bog standard text fields quickly in bulk
     *
     * @param array                $fields
     * @param ClassMetadataBuilder $builder
     * @param bool                 $isNullable
     * @SuppressWarnings(PHPMD.StaticAccess)
     * In this case the boolean argument is simply data
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public static function setSimpleTextFields(
        array $fields,
        ClassMetadataBuilder $builder,
        bool $isNullable = true
    ): void {
        foreach ($fields as $field) {
            $builder->createField($field, Type::TEXT)
                    ->columnName(self::backticks(Inflector::tableize($field)))
                    ->nullable($isNullable)
                    ->build();
        }
    }


    /**
     * Set bog standard float fields quickly in bulk
     *
     * @param array                $fields
     * @param ClassMetadataBuilder $builder
     * @param bool                 $isNullable
     * @SuppressWarnings(PHPMD.StaticAccess)
     * In this case the boolean argument is simply data
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public static function setSimpleFloatFields(
        array $fields,
        ClassMetadataBuilder $builder,
        bool $isNullable = true
    ): void {
        foreach ($fields as $field) {
            $builder->createField($field, Type::FLOAT)
                    ->columnName(self::backticks(Inflector::tableize($field)))
                    ->nullable($isNullable)
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
     * @param bool                 $isNullable
     * @SuppressWarnings(PHPMD.StaticAccess)
     * In this case the boolean argument is simply data
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public static function setSimpleDecimalFields(
        array $fields,
        ClassMetadataBuilder $builder,
        bool $isNullable = true
    ): void {
        foreach ($fields as $field) {
            $builder->createField($field, Type::DECIMAL)
                    ->columnName(self::backticks(Inflector::tableize($field)))
                    ->nullable($isNullable)
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
     * @param bool                 $isNullable
     * @SuppressWarnings(PHPMD.StaticAccess)
     * In this case the boolean argument is simply data
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public static function setSimpleDatetimeFields(
        array $fields,
        ClassMetadataBuilder $builder,
        bool $isNullable = true
    ): void {
        foreach ($fields as $field) {
            $builder->createField($field, Type::DATETIME)
                    ->columnName(self::backticks(Inflector::tableize($field)))
                    ->nullable($isNullable)
                    ->build();
        }
    }

    /**
     * Set bog standard integer fields quickly in bulk
     *
     * @param array                $fields
     * @param ClassMetadataBuilder $builder
     * @param bool                 $isNullable
     * @SuppressWarnings(PHPMD.StaticAccess)
     * In this case the boolean argument is simply data
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public static function setSimpleIntegerFields(
        array $fields,
        ClassMetadataBuilder $builder,
        bool $isNullable = true
    ): void {
        foreach ($fields as $field) {
            $builder->createField($field, Type::INTEGER)
                    ->columnName(self::backticks(Inflector::tableize($field)))
                    ->nullable($isNullable)
                    ->build();
        }
    }

    /**
     * Set bog standard boolean fields quickly in bulk
     *
     * @param array                $fields
     * @param ClassMetadataBuilder $builder
     * @param bool                 $isNullable
     * @SuppressWarnings(PHPMD.StaticAccess)
     * In this case the boolean argument is simply data
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public static function setSimpleBooleanFields(
        array $fields,
        ClassMetadataBuilder $builder,
        bool $isNullable = true
    ): void {
        foreach ($fields as $field) {
            $builder->createField($field, Type::BOOLEAN)
                    ->columnName(self::backticks(Inflector::tableize($field)))
                    ->nullable($isNullable)
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
