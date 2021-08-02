<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Objects\Attribute;

use Doctrine\ORM\Mapping\ClassMetadata;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Interfaces\Attribute\HasWeightEmbeddableInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Interfaces\Objects\Attribute\WeightEmbeddableInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Objects\AbstractEmbeddableObject;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use InvalidArgumentException;

use function in_array;

class WeightEmbeddable extends AbstractEmbeddableObject implements WeightEmbeddableInterface
{
    /**
     * @var string
     */
    private $unit;
    /**
     * @var float
     */
    private $value;

    final public function __construct(string $unit, float $value)
    {
        $this->validateUnit($unit);
        $this->unit  = $unit;
        $this->value = $value;
    }

    private function validateUnit(string $unit): void
    {
        $errors = [];
        if ('' === $unit) {
            $errors[] = 'unit is empty';
        }
        if (!in_array($unit, WeightEmbeddableInterface::VALID_UNITS, true)) {
            $errors[] = 'invalid unit';
        }
        if ([] === $errors) {
            return;
        }
        throw new InvalidArgumentException('Invalid arguments: ' . print_r($errors, true));
    }

    /**
     * @param ClassMetadata<EntityInterface> $metadata
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function loadMetadata(ClassMetadata $metadata): void
    {
        $builder = self::setEmbeddableAndGetBuilder($metadata);
        MappingHelper::setSimpleFields(
            [
                WeightEmbeddableInterface::EMBEDDED_PROP_UNIT  => MappingHelper::TYPE_STRING,
                WeightEmbeddableInterface::EMBEDDED_PROP_VALUE => MappingHelper::TYPE_STRING,
            ],
            $builder
        );
    }

    /**
     * @param array $properties
     */
    public static function create(array $properties): static
    {
        if (array_key_exists(WeightEmbeddableInterface::EMBEDDED_PROP_UNIT, $properties)) {
            return new static(
                $properties[WeightEmbeddableInterface::EMBEDDED_PROP_UNIT],
                $properties[WeightEmbeddableInterface::EMBEDDED_PROP_VALUE]
            );
        }

        return new static(...array_values($properties));
    }

    public function __toString(): string
    {
        return (string)print_r(
            [
                'weightEmbeddable' => [
                    WeightEmbeddableInterface::EMBEDDED_PROP_UNIT  => $this->getUnit(),
                    WeightEmbeddableInterface::EMBEDDED_PROP_VALUE => $this->getValue(),
                ],
            ],
            true
        );
    }

    public function getUnit(): string
    {
        return $this->unit;
    }

    public function getValue(): float
    {
        return $this->value;
    }

    protected function getPrefix(): string
    {
        return HasWeightEmbeddableInterface::PROP_WEIGHT_EMBEDDABLE;
    }
}
