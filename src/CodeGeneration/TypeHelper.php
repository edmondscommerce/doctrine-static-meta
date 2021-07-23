<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration;

use Doctrine\Common\Collections\Collection;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use RuntimeException;
use function gettype;
use function is_bool;

class TypeHelper
{
    public const ITERABLE_TYPES = [
        MappingHelper::TYPE_ARRAY,
        '\\' . Collection::class,
    ];

    /**
     * The standard gettype function output is not brilliantly up to date with modern PHP types
     *
     * @param mixed $var
     *
     * @return string
     */
    public function getType(mixed $var): string
    {
        $type = gettype($var);
        if ('double' === $type) {
            return 'float';
        }
        if ('integer' === $type) {
            return 'int';
        }
        if ('NULL' === $type) {
            return 'null';
        }
        if ('boolean' === $type) {
            return 'bool';
        }

        return $type;
    }

    public function isImportableType(string $type): bool
    {
        $builtin   = MappingHelper::PHP_TYPES;
        $builtin[] = 'mixed';

        return !\in_array($type, $builtin, true);
    }

    /**
     * When a default is passed in via CLI then the type can not be expected to be set correctly
     *
     * This function takes the string value and normalises it into the correct value based on the expected type
     *
     * @param mixed  $value
     * @param string $expectedType
     *
     * @return mixed
     */
    public function normaliseValueToType(mixed $value, string $expectedType)
    {
        if (null === $value) {
            return null;
        }
        switch ($expectedType) {
            case 'string':
                return $this->normaliseString($value);
            case 'bool':
                return $this->normaliseBool($value);
            case 'int':
                return $this->normaliseInt($value);
            case 'float':
                return $this->normaliseFloat($value);
            case MappingHelper::PHP_TYPE_DATETIME:
                return $this->normaliseDateTime($value);
            default:
                throw new RuntimeException('hit unexpected type ' . $expectedType . ' in ' . __METHOD__);
        }
    }

    private function normaliseString($value): string
    {
        return trim((string)$value);
    }

    private function normaliseBool($value): bool
    {
        if (is_bool($value)) {
            return $value;
        }
        $value = trim($value);
        if (0 === strcasecmp('true', $value)) {
            return true;
        }
        if (0 === strcasecmp('false', $value)) {
            return false;
        }
        throw new RuntimeException('Invalid bool value: ' . $value);
    }

    private function normaliseInt($value): int
    {
        $value = trim((string)$value);
        if (is_numeric($value)) {
            return (int)$value;
        }
        throw new RuntimeException('Invalid int default value: ' . $value);
    }

    private function normaliseFloat($value): float
    {
        $value = trim((string)$value);
        if (is_numeric($value)) {
            return (float)$value;
        }
        throw new RuntimeException('Invalid float default value: ' . $value);
    }

    /**
     * @param mixed $value
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @return mixed|string
     */
    private function normaliseDateTime(mixed $value)
    {
        $value = trim($value);
        switch (true) {
            case (null === $value):
                return $value;
        }
        throw new RuntimeException('Invalid DateTime default value: ' . $value);
    }

    public function stripNull(string $type): string
    {
        return str_replace(['null|', '|null', '?'], '', $type);
    }

    public function isIterableType(string $type): bool
    {
        $type = $this->stripNull($type);

        return \in_array($type, self::ITERABLE_TYPES, true);
    }
}
