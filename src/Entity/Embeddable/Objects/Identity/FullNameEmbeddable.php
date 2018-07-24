<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Objects\Identity;

use Doctrine\ORM\Mapping\ClassMetadata;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Interfaces\Identity\HasFullNameEmbeddableInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Interfaces\Objects\Identity\FullNameEmbeddableInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Objects\AbstractEmbeddableObject;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;

class FullNameEmbeddable extends AbstractEmbeddableObject implements FullNameEmbeddableInterface
{
    /**
     * The title or Honorific Prefix, for example Mr, Dr
     *
     * @var string
     */
    private $title;

    /**
     * The first or given name
     *
     * @var string
     */
    private $firstName;

    /**
     * An array of middle names
     *
     * @var array
     */
    private $middleNames;

    /**
     * The last or surname
     *
     * @var string
     */
    private $lastName;

    /**
     * The honorific suffix, for example Jr
     *
     * @var string
     */
    private $suffix;

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title ?? '';
    }

    /**
     * @param string $title
     *
     * @return FullNameEmbeddableInterface
     */
    public function setTitle(string $title): FullNameEmbeddableInterface
    {
        $this->owningEntity->notifyEmbeddablePrefixedProperties(
            $this->getPrefix(),
            'title',
            $this->title,
            $title
        );
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName ?? '';
    }

    /**
     * @param string $firstName
     *
     * @return FullNameEmbeddableInterface
     */
    public function setFirstName(string $firstName): FullNameEmbeddableInterface
    {
        $this->owningEntity->notifyEmbeddablePrefixedProperties(
            $this->getPrefix(),
            'firstName',
            $this->firstName,
            $firstName
        );
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * @return array
     */
    public function getMiddleNames(): array
    {
        return $this->middleNames ?? [];
    }

    /**
     * @param array $middleNames
     *
     * @return FullNameEmbeddableInterface
     */
    public function setMiddleNames(array $middleNames): FullNameEmbeddableInterface
    {
        $this->owningEntity->notifyEmbeddablePrefixedProperties(
            $this->getPrefix(),
            'middleNames',
            $this->middleNames,
            $middleNames
        );
        $this->middleNames = $middleNames;

        return $this;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName ?? '';
    }

    /**
     * @param string $lastName
     *
     * @return FullNameEmbeddableInterface
     */
    public function setLastName(string $lastName): FullNameEmbeddableInterface
    {
        $this->owningEntity->notifyEmbeddablePrefixedProperties(
            $this->getPrefix(),
            'lastName',
            $this->lastName,
            $lastName
        );
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * @return string
     */
    public function getSuffix(): string
    {
        return $this->suffix ?? '';
    }

    /**
     * @param string $suffix
     *
     * @return FullNameEmbeddableInterface
     */
    public function setSuffix(string $suffix): FullNameEmbeddableInterface
    {
        $this->owningEntity->notifyEmbeddablePrefixedProperties(
            $this->getPrefix(),
            'suffix',
            $this->suffix,
            $suffix
        );
        $this->suffix = $suffix;

        return $this;
    }

    /**
     * Get the full name as a single string
     *
     * @return string
     */
    public function getFormatted(): string
    {
        return $this->format(
            [
                $this->getTitle(),
                $this->getFirstName(),
                $this->format($this->middleNames),
                $this->getLastName(),
                $this->getSuffix(),
            ]
        );
    }

    /**
     * Convert the array into a single space separated list
     *
     * @param array $items
     *
     * @return string
     */
    private function format(array $items): string
    {
        return trim(
            implode(
                ' ',
                array_map(
                    'trim',
                    array_filter(
                        $items
                    )
                )
            )
        );
    }

    /**
     * @param ClassMetadata $metadata
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function loadMetadata(ClassMetadata $metadata): void
    {
        $builder = self::setEmbeddableAndGetBuilder($metadata);
        MappingHelper::setSimpleFields(
            [
                FullNameEmbeddableInterface::EMBEDDED_PROP_TITLE       => MappingHelper::TYPE_STRING,
                FullNameEmbeddableInterface::EMBEDDED_PROP_FIRSTNAME   => MappingHelper::TYPE_STRING,
                FullNameEmbeddableInterface::EMBEDDED_PROP_MIDDLENAMES => MappingHelper::TYPE_JSON,
                FullNameEmbeddableInterface::EMBEDDED_PROP_LASTNAME    => MappingHelper::TYPE_STRING,
                FullNameEmbeddableInterface::EMBEDDED_PROP_SUFFIX      => MappingHelper::TYPE_STRING,
            ],
            $builder
        );
    }

    protected function getPrefix(): string
    {
        return HasFullNameEmbeddableInterface::PROP_FULL_NAME_EMBEDDABLE;
    }
}
