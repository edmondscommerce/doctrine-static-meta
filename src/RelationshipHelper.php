<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta;

use Doctrine\ORM\Mapping\ClassMetadataInfo;
use InvalidArgumentException;
use function ucfirst;

/**
 * This is used to pull out various methods from the doctrine meta data. It is based on the assumption that the methods
 * follow the ${methodType}${$propertyName} convention, with add methods using the singular version of the property
 */
class RelationshipHelper
{
    /**
     * Use this to get the getter for the property, can be used with all relations
     *
     * @param array $mapping
     *
     * @return string
     */
    public function getGetterFromDoctrineMapping(array $mapping): string
    {
        $this->assertMappingLevel($mapping);
        $property = $this->getUppercaseProperty($mapping);

        return "get${property}";
    }

    /**
     * Use this to get the setter for the property, can be used with all relations
     *
     * @param array $mapping
     *
     * @return string
     */
    public function getSetterFromDoctrineMapping(array $mapping): string
    {
        $this->assertMappingLevel($mapping);
        $property = $this->getUppercaseProperty($mapping);

        return "set${property}";
    }

    /**
     * Use this to get the adder for the property, can only be used with *_TO_MANY relations
     *
     * @param array $mapping
     *
     * @return string
     */
    public function getAdderFromDoctrineMapping(array $mapping): string
    {
        $this->assertMappingLevel($mapping);
        $property = $this->getUppercaseProperty($mapping);
        if ($this->isPlural($mapping) === false) {
            throw new InvalidArgumentException("$property is singular, so doesn't have an add method");
        }

        return 'add' . MappingHelper::singularize($property);
    }

    /**
     * User this to get the remover for the property, can only be used with *_TO_MANY relations
     *
     * @param array $mapping
     *
     * @return string
     */
    public function getRemoverFromDoctrineMapping(array $mapping): string
    {
        $this->assertMappingLevel($mapping);
        $property = $this->getUppercaseProperty($mapping);
        if ($this->isPlural($mapping) === false) {
            throw new InvalidArgumentException("$property is singular, so doesn't have an add method");
        }

        return 'remove' . MappingHelper::singularize($property);
    }

    /**
     * Use this to check if the relationship is plural (*_TO_MANY) or singular (*_TO_ONE)
     *
     * @param array $mapping
     *
     * @return bool
     */
    public function isPlural(array $mapping): bool
    {
        $this->assertMappingLevel($mapping);
        $type = $mapping['type'];
        switch ($type) {
            case ClassMetadataInfo::ONE_TO_ONE:
            case ClassMetadataInfo::MANY_TO_ONE:
                return false;
            case ClassMetadataInfo::ONE_TO_MANY:
            case ClassMetadataInfo::MANY_TO_MANY:
                return true;
        }

        throw new InvalidArgumentException("Unknown relationship of $type");
    }

    public function getFieldName(array $mapping): string
    {
        $this->assertMappingLevel($mapping);

        return $mapping['fieldName'];
    }

    private function getUppercaseProperty(array $mapping): string
    {
        return ucfirst($this->getFieldName($mapping));
    }

    private function assertMappingLevel(array $mapping): void
    {
        if (!isset($mapping['type'])) {
            throw new InvalidArgumentException('Could not find the type key, are you using the correct mapping array');
        }
    }
}
