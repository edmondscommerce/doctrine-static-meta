<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\EntityGenerator\Faker;

use Doctrine\ORM\Mapping\ClassMetadataInfo;

/**
 * This is a fork of the standard Faker column type guesser with DSM specific changes
 *
 * @see \Faker\ORM\Doctrine\ColumnTypeGuesser
 */
class ColumnTypeGuesser
{
    protected $generator;

    /**
     * @param \Faker\Generator $generator
     */
    public function __construct(\Faker\Generator $generator)
    {
        $this->generator = $generator;
    }

    /**
     * @param string            $fieldName
     * @param ClassMetadataInfo $class
     *
     * @return \Closure|null
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function guessFormat(string $fieldName, ClassMetadataInfo $class): ?callable
    {
        $type = $class->getTypeOfField($fieldName);
        switch ($type) {
            case 'boolean':
                return $this->getBool();
            case 'decimal':
                return $this->getDecimal($fieldName, $class);
            case 'smallint':
                return $this->getSmallInt();
            case 'integer':
                return $this->getInt();
            case 'bigint':
                return $this->getBigInt();
            case 'float':
                return $this->getFloat();
            case 'string':
                return $this->getString($fieldName, $class);
            case 'text':
                return $this->getText();
            case 'datetime':
            case 'date':
            case 'time':
                return $this->getDateTimeImmutable();
            default:
                // no smart way to guess what the user expects here
                return null;
        }
    }

    private function getBool(): callable
    {
        $generator = $this->generator;

        return function () use ($generator) {
            return $generator->boolean;
        };
    }

    private function getDecimal(string $fieldName, ClassMetadataInfo $class): callable
    {
        $nbDigits = $class->fieldMappings[$fieldName]['precision'] ?? 4;
        while ($nbDigits > 4) {
            $max = 10 ** $nbDigits;
            if ($max > mt_getrandmax()) {
                $nbDigits--;
                continue;
            }
            break;
        }
        $generator = $this->generator;

        return function () use ($generator, $nbDigits) {
            return $generator->randomNumber($nbDigits) / 100;
        };
    }

    private function getSmallInt(): callable
    {
        return function () {
            return mt_rand(0, 65535);
        };
    }

    private function getInt(): callable
    {
        return function () {
            return mt_rand(0, (int)'2147483647');
        };
    }

    private function getBigInt(): callable
    {
        return function () {
            return mt_rand(0, (int)'18446744073709551615');
        };
    }

    private function getFloat(): callable
    {
        return function () {
            return mt_rand(0, (int)'4294967295') / mt_rand(1, (int)'4294967295');
        };
    }

    private function getString(string $fieldName, ClassMetadataInfo $class): callable
    {
        $size      =
            $class->fieldMappings[$fieldName]['length'] ?? 255;
        $generator = $this->generator;

        return function () use ($generator, $size) {
            return $generator->text($size);
        };
    }

    private function getText(): callable
    {
        $generator = $this->generator;

        return function () use ($generator) {
            return $generator->text;
        };
    }

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @return callable
     */
    private function getDateTimeImmutable(): callable
    {
        $generator = $this->generator;

        return function () use ($generator) {
            return \DateTimeImmutable::createFromMutable($generator->datetime);
        };
    }
}
