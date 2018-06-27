<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration;

class TypeHelper
{

    /**
     * The standard gettype function output is not brilliantly up to date with modern PHP types
     *
     * @param mixed $var
     *
     * @return string
     */
    public function getType($var): string
    {
        $type = \gettype($var);
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
    public function normaliseValueToType($value, string $expectedType)
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
            case '\DateTime':
                return $this->normaliseDateTime($value);
            default:
                throw new \RuntimeException('hit unexpected type '.$expectedType.' in '.__METHOD__);
        }
    }

    private function normaliseString($value): string
    {
        return trim((string)$value);
    }

    private function normaliseBool($value): bool
    {
        if (\is_bool($value)) {
            return $value;
        }
        $value = trim($value);
        if (0 === strcasecmp('true', $value)) {
            return true;
        }
        if (0 === strcasecmp('false', $value)) {
            return false;
        }
        throw new \RuntimeException('Invalid bool value: '.$value);
    }

    private function normaliseInt($value): int
    {
        $value = trim((string)$value);
        if (is_numeric($value)) {
            return (int)$value;
        }
        throw new \RuntimeException('Invalid int default value: '.$value);
    }

    private function normaliseFloat($value): float
    {
        $value = trim((string)$value);
        if (is_numeric($value)) {
            return (float)$value;
        }
        throw new \RuntimeException('Invalid float default value: '.$value);
    }

    /**
     * @param mixed $value
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @return mixed|string
     */
    private function normaliseDateTime($value)
    {
        $value = trim($value);
        switch (true) {
            case (null === $value):
                return $value;
        }
        throw new \RuntimeException('Invalid DateTime default value: '.$value);
    }
}
