<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta;

use Doctrine\Common\Util\Inflector;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\AbstractCommand;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\Schema\Database;

class MappingHelper
{

    public const TYPE_STRING = 'string';
    public const TYPE_DATETIME = 'dateTime';
    public const TYPE_FLOAT = 'float';
    public const TYPE_DECIMAL = 'decimal';
    public const TYPE_INTEGER = 'integer';
    public const TYPE_TEXT = 'text';

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
     * @param null|\ReflectionClass $reflection
     * @param string $entitiesFolder
     *
     * @return string
     * @throws Exception\DoctrineStaticMetaException
     * @throws \ReflectionException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function getTableNameForEntityFqn(
        string $entityFqn,
        ?\ReflectionClass $reflection = null,
        string $entitiesFolder = AbstractCommand::DEFAULT_ENTITIES_ROOT_FOLDER
    ): string {
        if (null === $reflection) {
            $reflection = new \ReflectionClass($entityFqn);
        }
        $namespaceHelper = new NamespaceHelper();
        $subFqn = $namespaceHelper->getEntitySubNamespace(
            $entityFqn,
            $namespaceHelper->getEntityNamespaceRootFromEntityReflection($reflection, $entitiesFolder)
        );
        $tableName = \str_replace('\\', '', $subFqn);
        $tableName = Inflector::tableize($tableName);
        if (\strlen($tableName) > Database::MAX_IDENTIFIER_LENGTH) {
            $tableName = substr($tableName, -Database::MAX_IDENTIFIER_LENGTH);
        }

        return $tableName;
    }

    /**
     * Set bog standard string fields quickly in bulk
     *
     * @param array $fields
     * @param ClassMetadataBuilder $builder
     */
    public static function setSimpleStringFields(array $fields, ClassMetadataBuilder $builder): void
    {
        foreach ($fields as $field) {
            $builder->createField($field, Type::STRING)
                ->nullable(true)
                ->length(255)
                ->build();
        }
    }

    /**
     * Set bog standard text fields quickly in bulk
     *
     * @param array $fields
     * @param ClassMetadataBuilder $builder
     */
    public static function setSimpleTextFields(array $fields, ClassMetadataBuilder $builder): void
    {
        foreach ($fields as $field) {
            $builder->createField($field, Type::TEXT)
                ->nullable(true)
                ->build();
        }
    }


    /**
     * Set bog standard float fields quickly in bulk
     *
     * @param array $fields
     * @param ClassMetadataBuilder $builder
     */
    public static function setSimpleFloatFields(array $fields, ClassMetadataBuilder $builder): void
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
     * @param array $fields
     * @param ClassMetadataBuilder $builder
     */
    public static function setSimpleDecimalFields(array $fields, ClassMetadataBuilder $builder): void
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
     * @param array $fields
     * @param ClassMetadataBuilder $builder
     */
    public static function setSimpleDateTimeFields(array $fields, ClassMetadataBuilder $builder): void
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
     * @param array $fields
     * @param ClassMetadataBuilder $builder
     */
    public static function setSimpleIntegerFields(array $fields, ClassMetadataBuilder $builder): void
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
     * @param array $fieldToType [
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
