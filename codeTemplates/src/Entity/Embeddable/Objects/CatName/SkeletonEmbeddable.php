<?php declare(strict_types=1);

namespace TemplateNamespace\Entity\Embeddable\Objects\CatName;

use Doctrine\ORM\Mapping\ClassMetadata;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Objects\AbstractEmbeddableObject;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use InvalidArgumentException;
use TemplateNamespace\Entity\Embeddable\Interfaces\CatName\HasSkeletonEmbeddableInterface;
use TemplateNamespace\Entity\Embeddable\Interfaces\Objects\CatName\SkeletonEmbeddableInterface;

class SkeletonEmbeddable extends AbstractEmbeddableObject implements SkeletonEmbeddableInterface
{
    /**
     * @var string
     */
    private string $propertyOne;
    /**
     * @var string
     */
    private string $propertyTwo;

    public function __construct(string $propertyOne, string $propertyTwo)
    {
        $this->validate($propertyOne, $propertyTwo);
        $this->propertyOne = $propertyOne;
        $this->propertyTwo = $propertyTwo;
    }

    private function validate(string $propertyOne, string $propertyTwo): void
    {
        $errors = [];
        if ('' === $propertyOne) {
            $errors[] = 'property one is empty';
        }
        if ('' === $propertyTwo) {
            $errors[] = 'property two is empty';
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
                SkeletonEmbeddableInterface::EMBEDDED_PROP_PROPERTY_ONE => MappingHelper::TYPE_STRING,
                SkeletonEmbeddableInterface::EMBEDDED_PROP_PROPERTY_TWO => MappingHelper::TYPE_STRING,
            ],
            $builder
        );
    }

    /**
     * @param array $properties
     */
    public static function create(array $properties): static
    {
        if (array_key_exists(SkeletonEmbeddableInterface::EMBEDDED_PROP_PROPERTY_ONE, $properties)) {
            return new self(
                $properties[SkeletonEmbeddableInterface::EMBEDDED_PROP_PROPERTY_ONE],
                $properties[SkeletonEmbeddableInterface::EMBEDDED_PROP_PROPERTY_TWO]
            );
        }

        return new self(...array_values($properties));
    }

    public function __toString(): string
    {
        return (string)print_r(
            [
                'skeletonEmbeddable' => [
                    SkeletonEmbeddableInterface::EMBEDDED_PROP_PROPERTY_ONE => $this->getPropertyOne(),
                    SkeletonEmbeddableInterface::EMBEDDED_PROP_PROPERTY_TWO => $this->getPropertyTwo(),
                ],
            ],
            true
        );
    }

    /**
     * @return string
     */
    public function getPropertyOne(): string
    {
        return $this->propertyOne;
    }

    /**
     * @return string
     */
    public function getPropertyTwo(): string
    {
        return $this->propertyTwo;
    }

    protected function getPrefix(): string
    {
        return HasSkeletonEmbeddableInterface::PROP_SKELETON_EMBEDDABLE;
    }
}