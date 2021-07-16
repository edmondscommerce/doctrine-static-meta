<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta;

use DateTimeImmutable;
use Doctrine\DBAL\Types\Type;
use Doctrine\Inflector\Inflector;
use Doctrine\Inflector\InflectorFactory;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\Builder\FieldBuilder;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\TypeHelper;
use EdmondsCommerce\DoctrineStaticMeta\Schema\Database;
use InvalidArgumentException;
use Ramsey\Uuid\Doctrine\UuidBinaryOrderedTimeType;
use Ramsey\Uuid\Doctrine\UuidBinaryType;
use Ramsey\Uuid\Doctrine\UuidType;
use function is_bool;
use function is_float;
use function is_int;
use function is_string;
use function str_replace;
use function strlen;

/**
 * Class MappingHelper
 *
 * Helper functions to assist with setting up Doctrine mapping meta data
 *
 * @package EdmondsCommerce\DoctrineStaticMeta
 *
 * @SuppressWarnings(PHPMD.StaticAccess)
 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
 */
class MappingHelper
{

    /**
     * Primary Key types (beyond the common types)
     */
    public const TYPE_UUID                    = UuidBinaryOrderedTimeType::NAME;
    public const TYPE_NON_BINARY_UUID         = UuidType::NAME;
    public const TYPE_NON_ORDERED_BINARY_UUID = UuidBinaryType::NAME;

    /**
     * Quick accessors for common types that are supported by methods in this helper
     *
     * Note this is not all of the types supported by Doctrine
     *
     * Each of these methods has a corresponding `setSimple{Type}Fields` method
     */
    public const TYPE_STRING   = 'string';
    public const TYPE_DATETIME = 'datetime';// actually datetime is implemented as datetime_immutable
    public const TYPE_FLOAT    = 'float';
    public const TYPE_DECIMAL  = 'decimal';
    public const TYPE_INTEGER  = 'integer';
    public const TYPE_TEXT     = 'text';
    public const TYPE_BOOLEAN  = 'boolean';
    public const TYPE_ARRAY    = 'array';
    public const TYPE_OBJECT   = 'object';


    /**
     * This is the list of common types that mapping helper fully supports with a `setSimple{Type}Fields` method
     */
    public const COMMON_TYPES = [
        self::TYPE_STRING,
        self::TYPE_DATETIME,
        self::TYPE_FLOAT,
        self::TYPE_DECIMAL,
        self::TYPE_INTEGER,
        self::TYPE_TEXT,
        self::TYPE_BOOLEAN,
        self::TYPE_ARRAY,
        self::TYPE_OBJECT,
    ];

    /**
     * Which types do we support marking as unique
     */
    public const UNIQUEABLE_TYPES = [
        self::TYPE_STRING,
        self::TYPE_INTEGER,
    ];

    public const PHP_TYPE_STRING   = 'string';
    public const PHP_TYPE_DATETIME = '\\' . DateTimeImmutable::class;
    public const PHP_TYPE_FLOAT    = 'float';
    public const PHP_TYPE_INTEGER  = 'int';
    public const PHP_TYPE_BOOLEAN  = 'bool';
    public const PHP_TYPE_ARRAY    = 'array';
    public const PHP_TYPE_OBJECT   = 'object';
    public const PHP_TYPE_NUMERIC  = 'string|int|float';

    public const PHP_TYPES = [
        self::PHP_TYPE_STRING,
        self::PHP_TYPE_DATETIME,
        self::PHP_TYPE_FLOAT,
        self::PHP_TYPE_INTEGER,
        self::PHP_TYPE_BOOLEAN,
        self::PHP_TYPE_ARRAY,
        self::PHP_TYPE_OBJECT,
    ];

    /**
     * The PHP type associated with the mapping type
     */
    public const COMMON_TYPES_TO_PHP_TYPES = [
        self::TYPE_STRING   => self::PHP_TYPE_STRING,
        self::TYPE_DATETIME => self::PHP_TYPE_DATETIME,
        self::TYPE_FLOAT    => self::PHP_TYPE_FLOAT,
        self::TYPE_DECIMAL  => self::PHP_TYPE_NUMERIC,
        self::TYPE_INTEGER  => self::PHP_TYPE_INTEGER,
        self::TYPE_TEXT     => self::PHP_TYPE_STRING,
        self::TYPE_BOOLEAN  => self::PHP_TYPE_BOOLEAN,
        self::TYPE_ARRAY    => self::PHP_TYPE_ARRAY,
        self::TYPE_OBJECT   => self::PHP_TYPE_OBJECT,
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
        Type::JSON_ARRAY,
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
    /**
     * @var Inflector
     */
    private static $inflector;

    /**
     * @param string $entityFqn
     *
     * @return string
     */
    public static function getPluralForFqn(string $entityFqn): string
    {
        $singular = self::getSingularForFqn($entityFqn);

        $plural = self::pluralize($singular);
        if ($plural === $singular) {
            $plural = $singular . 's';
        }

        return $plural;
    }

    /**
     * @param string $entityFqn
     *
     * @return string
     */
    public static function getSingularForFqn(string $entityFqn): string
    {
        $shortName = self::getShortNameForFqn($entityFqn);
        $singular  = self::singularize($shortName);

        return lcfirst($singular);
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

    public static function singularize(string $item): string
    {
        $singular = self::getInflector()->singularize($item);
        if ('datum' === strtolower(substr($singular, -5))) {
            $singular = $item;
        }

        return $singular;
    }

    public static function pluralize(string $item): string
    {
        $plural = self::getInflector()->pluralize($item);
        if ($plural === $item) {
            $plural = $item . 's';
        }

        return $plural;
    }

    /**
     * @param string $entityFqn
     *
     * @return string
     */
    public static function getTableNameForEntityFqn(
        string $entityFqn
    ): string {
        $namespaceHelper = new NamespaceHelper();
        $subFqn          = $namespaceHelper->getEntitySubNamespace(
            $entityFqn
        );
        $tableName       = str_replace('\\', '', $subFqn);
        $tableName       = self::backticks(self::getInflector()->tableize($tableName));
        if (strlen($tableName) > Database::MAX_IDENTIFIER_LENGTH) {
            $tableName = substr($tableName, -Database::MAX_IDENTIFIER_LENGTH);
        }

        return $tableName;
    }

    /**
     * Wrap the name in backticks
     *
     * @param string $name
     *
     * @return string
     */
    public static function backticks(string $name): string
    {
        return '`' . $name . '`';
    }

    /**
     * Set bog standard string fields quickly in bulk
     *
     * @param array                $fields
     * @param ClassMetadataBuilder $builder
     * @param mixed|null           $default
     * @param bool                 $isUnique
     * In this case the boolean argument is simply data
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public static function setSimpleStringFields(
        array $fields,
        ClassMetadataBuilder $builder,
        mixed $default = null,
        bool $isUnique = false
    ): void {
        if (null !== $default && !is_string($default)) {
            throw new InvalidArgumentException(
                'Invalid default value ' . $default
                . ' with type ' . self::getType($default)
            );
        }
        foreach ($fields as $field) {
            $fieldBuilder = new FieldBuilder(
                $builder,
                [
                    'fieldName' => $field,
                    'type'      => Type::STRING,
                    'default'   => $default,
                ]
            );
            $fieldBuilder
                ->columnName(self::getColumnNameForField($field))
                ->nullable(null === $default)
                ->unique($isUnique)
                ->length(Database::MAX_VARCHAR_LENGTH)
                ->build();
        }
    }

    private static function getType($var): string
    {
        static $typeHelper;
        if (null === $typeHelper) {
            $typeHelper = new TypeHelper();
        }

        return $typeHelper->getType($var);
    }

    /**
     * Get the properly backticked and formatted column name for a field
     *
     * @param string $field
     *
     * @return string
     */
    public static function getColumnNameForField(string $field): string
    {
        return self::backticks(self::getInflector()->tableize($field));
    }

    /**
     * Set bog standard text fields quickly in bulk
     *
     * @param array                $fields
     * @param ClassMetadataBuilder $builder
     * @param mixed|null           $default
     * In this case the boolean argument is simply data
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public static function setSimpleTextFields(
        array $fields,
        ClassMetadataBuilder $builder,
        mixed $default = null
    ): void {
        if (null !== $default && !is_string($default)) {
            throw new InvalidArgumentException(
                'Invalid default value ' . $default
                . ' with type ' . self::getType($default)
            );
        }
        foreach ($fields as $field) {
            $fieldBuilder = new FieldBuilder(
                $builder,
                [
                    'fieldName' => $field,
                    'type'      => Type::TEXT,
                    'default'   => $default,
                ]
            );
            $fieldBuilder->columnName(self::getColumnNameForField($field))
                         ->nullable(null === $default)
                         ->build();
        }
    }


    /**
     * Set bog standard float fields quickly in bulk
     *
     * @param array                $fields
     * @param ClassMetadataBuilder $builder
     * @param mixed|null           $default
     * In this case the boolean argument is simply data
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public static function setSimpleFloatFields(
        array $fields,
        ClassMetadataBuilder $builder,
        mixed $default = null
    ): void {
        if (null !== $default && !is_float($default)) {
            throw new InvalidArgumentException(
                'Invalid default value ' . $default
                . ' with type ' . self::getType($default)
            );
        }
        foreach ($fields as $field) {
            $fieldBuilder = new FieldBuilder(
                $builder,
                [
                    'fieldName' => $field,
                    'type'      => Type::FLOAT,
                    'default'   => $default,
                ]
            );
            $fieldBuilder
                ->columnName(self::getColumnNameForField($field))
                ->nullable(null === $default)
                ->build();
        }
    }

    /**
     * Set bog standard decimal fields quickly in bulk
     *
     * @param array                $fields
     * @param ClassMetadataBuilder $builder
     * @param mixed|null           $default
     * In this case the boolean argument is simply data
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public static function setSimpleDecimalFields(
        array $fields,
        ClassMetadataBuilder $builder,
        mixed $default = null
    ): void {
        if (null !== $default && !is_string($default)) {
            throw new InvalidArgumentException(
                'Invalid default value ' . $default
                . ' with type ' . self::getType($default)
            );
        }
        if (null !== $default && !is_numeric($default)) {
            throw new InvalidArgumentException(
                'Invalid default value ' . $default
                . ', even though it is a string, it must be numeric '
            );
        }
        foreach ($fields as $field) {
            $fieldBuilder = new FieldBuilder(
                $builder,
                [
                    'fieldName' => $field,
                    'type'      => Type::DECIMAL,
                    'default'   => (string)(float)$default,
                ]
            );
            $fieldBuilder
                ->columnName(self::getColumnNameForField($field))
                ->nullable(null === $default)
                ->precision(Database::MAX_DECIMAL_PRECISION)
                ->scale(Database::MAX_DECIMAL_SCALE)
                ->build();
        }
    }

    /**
     * Set bog standard dateTime fields quickly in bulk
     *
     * @param array                $fields
     * @param ClassMetadataBuilder $builder
     * @param mixed|null           $default
     * In this case the boolean argument is simply data
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public static function setSimpleDatetimeFields(
        array $fields,
        ClassMetadataBuilder $builder,
        mixed $default = null
    ): void {
        if (null !== $default) {
            throw new InvalidArgumentException('DateTime currently only support null as a default value');
        }
        foreach ($fields as $field) {
            $fieldBuilder = new FieldBuilder(
                $builder,
                [
                    'fieldName' => $field,
                    'type'      => Type::DATETIME_IMMUTABLE,
                    'default'   => $default,
                ]
            );
            $fieldBuilder
                ->columnName(self::getColumnNameForField($field))
                ->nullable(null === $default)
                ->build();
        }
    }

    /**
     * @param array                $fields
     * @param ClassMetadataBuilder $builder
     * @param null                 $default
     * @param bool                 $isUnique
     */
    public static function setSimpleIntegerFields(
        array $fields,
        ClassMetadataBuilder $builder,
        $default = null,
        bool $isUnique = false
    ): void {
        if (null !== $default && !is_int($default)) {
            throw new InvalidArgumentException(
                'Invalid default value ' . $default
                . ' with type ' . self::getType($default)
            );
        }
        foreach ($fields as $field) {
            $fieldBuilder = new FieldBuilder(
                $builder,
                [
                    'fieldName' => $field,
                    'type'      => Type::INTEGER,
                    'default'   => $default,
                ]
            );
            $fieldBuilder
                ->columnName(self::getColumnNameForField($field))
                ->nullable(null === $default)
                ->unique($isUnique)
                ->build();
        }
    }

    /**
     * Set bog standard boolean fields quickly in bulk
     *
     * @param array                $fields
     * @param ClassMetadataBuilder $builder
     * @param mixed|null           $default
     * In this case the boolean argument is simply data
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public static function setSimpleBooleanFields(
        array $fields,
        ClassMetadataBuilder $builder,
        mixed $default = null
    ): void {
        if (null !== $default && !is_bool($default)) {
            throw new InvalidArgumentException(
                'Invalid default value ' . $default
                . ' with type ' . self::getType($default)
            );
        }
        foreach ($fields as $field) {
            $fieldBuilder = new FieldBuilder(
                $builder,
                [
                    'fieldName' => $field,
                    'type'      => Type::BOOLEAN,
                    'default'   => $default,
                ]
            );
            $fieldBuilder
                ->columnName(self::getColumnNameForField($field))
                ->nullable(null === $default)
                ->build();
        }
    }

    /**
     * Create JSON Array fields
     *
     * Will use real JSON in the DB engine if it is supported
     *
     * This should be used for any structured data, arrays, lists, simple objects
     *
     * @param array                $fields
     * @param ClassMetadataBuilder $builder
     * @param array|null           $default
     */
    public static function setSimpleArrayFields(
        array $fields,
        ClassMetadataBuilder $builder,
        ?array $default = null
    ): void {
        foreach ($fields as $field) {
            $fieldBuilder = new FieldBuilder(
                $builder,
                [
                    'fieldName' => $field,
                    'type'      => Type::JSON_ARRAY,
                    'default'   => $default,
                ]
            );
            $fieldBuilder
                ->columnName(self::getColumnNameForField($field))
                ->nullable(null === $default)
                ->build();
        }
    }

    /**
     * Create JSON Object fields
     *
     * Will use real JSON in the DB engine if it is supported
     *
     * This should be used for any structured data, arrays, lists, simple objects
     *
     * @param array                $fields
     * @param ClassMetadataBuilder $builder
     * @param object|null          $default
     */
    public static function setSimpleObjectFields(
        array $fields,
        ClassMetadataBuilder $builder,
        ?object $default = null
    ): void {
        foreach ($fields as $field) {
            $fieldBuilder = new FieldBuilder(
                $builder,
                [
                    'fieldName' => $field,
                    'type'      => Type::JSON,
                    'default'   => $default,
                ]
            );
            $fieldBuilder
                ->columnName(self::getColumnNameForField($field))
                ->nullable(null === $default)
                ->build();
        }
    }

    /**
     * Bulk create multiple fields of different simple types
     *
     * Always creates nullable fields, if you want to set a default, you must call the type based method
     *
     * @param array                $fieldToType [
     *                                          'fieldName'=>'fieldSimpleType'
     *                                          ]
     * @param ClassMetadataBuilder $builder
     */
    public static function setSimpleFields(
        array $fieldToType,
        ClassMetadataBuilder $builder
    ): void {
        foreach ($fieldToType as $field => $type) {
            $method = "setSimple$type" . 'fields';
            static::$method([$field], $builder);
        }
    }

    /**
     * @return Inflector
     */
    public static function getInflector(): Inflector
    {
        if (null === self::$inflector) {
            self::$inflector = InflectorFactory::create()->build();
        }

        return self::$inflector;
    }
}
