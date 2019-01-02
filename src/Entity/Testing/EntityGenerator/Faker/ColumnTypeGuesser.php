<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\EntityGenerator\Faker;

use Doctrine\Common\Persistence\Mapping\ClassMetadata;

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
     * @param string        $fieldName
     * @param ClassMetadata $class
     *
     * @return \Closure|null
     */
    public function guessFormat(string $fieldName, ClassMetadata $class): ?callable
    {
        $generator = $this->generator;
        $type      = $class->getTypeOfField($fieldName);
        switch ($type) {
            case 'boolean':
                return function () use ($generator) {
                    return $generator->boolean;
                };
            case 'decimal':
                $nbDigits = $class->fieldMappings[$fieldName]['precision'] ?? 4;
                while ($nbDigits > 4) {
                    $max = pow(10, $nbDigits);
                    if ($max > mt_getrandmax()) {
                        $nbDigits--;
                        continue;
                    }
                    break;
                }

                return function () use ($generator, $nbDigits) {
                    return $generator->randomNumber($nbDigits) / 100;
                };
            case 'smallint':
                return function () {
                    return mt_rand(0, 65535);
                };
            case 'integer':
                return function () {
                    return mt_rand(0, (int)'2147483647');
                };
            case 'bigint':
                return function () {
                    return mt_rand(0, (int)'18446744073709551615');
                };
            case 'float':
                return function () {
                    return mt_rand(0, (int)'4294967295') / mt_rand(1, (int)'4294967295');
                };
            case 'string':
                $size =
                    $class->fieldMappings[$fieldName]['length'] ?? 255;

                return function () use ($generator, $size) {
                    return $generator->text($size);
                };
            case 'text':
                return function () use ($generator) {
                    return $generator->text;
                };
            case 'datetime':
            case 'date':
            case 'time':
                return function () use ($generator) {
                    return \DateTimeImmutable::createFromMutable($generator->datetime);
                };
            default:
                // no smart way to guess what the user expects here
                return null;
        }
    }

}