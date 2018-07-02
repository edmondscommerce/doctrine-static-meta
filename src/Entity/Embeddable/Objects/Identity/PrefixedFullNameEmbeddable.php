<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Objects\Identity;

use Doctrine\ORM\Mapping\ClassMetadata;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Interfaces\Objects\Identity\PrefixedPrefixedFullNameEmbeddableInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Objects\AbstractEmbeddableObject;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;

class PrefixedPrefixedFullNameEmbeddable extends AbstractEmbeddableObject implements PrefixedPrefixedFullNameEmbeddableInterface
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
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return PrefixedPrefixedFullNameEmbeddableInterface
     */
    public function setTitle(string $title): PrefixedPrefixedFullNameEmbeddableInterface
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     *
     * @return PrefixedPrefixedFullNameEmbeddableInterface
     */
    public function setFirstName(string $firstName): PrefixedPrefixedFullNameEmbeddableInterface
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * @return array
     */
    public function getMiddleNames(): array
    {
        return $this->middleNames;
    }

    /**
     * @param array $middleNames
     *
     * @return PrefixedPrefixedFullNameEmbeddableInterface
     */
    public function setMiddleNames(array $middleNames): PrefixedPrefixedFullNameEmbeddableInterface
    {
        $this->middleNames = $middleNames;

        return $this;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     *
     * @return PrefixedPrefixedFullNameEmbeddableInterface
     */
    public function setLastName(string $lastName): PrefixedPrefixedFullNameEmbeddableInterface
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * @return string
     */
    public function getSuffix(): string
    {
        return $this->suffix;
    }

    /**
     * @param string $suffix
     *
     * @return PrefixedPrefixedFullNameEmbeddableInterface
     */
    public function setSuffix(string $suffix): PrefixedPrefixedFullNameEmbeddableInterface
    {
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
                $this->title,
                $this->firstName,
                $this->format($this->middleNames),
                $this->lastName,
                $this->suffix,
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
                PrefixedPrefixedFullNameEmbeddableInterface::EMBEDDED_PROP_TITLE       => MappingHelper::TYPE_STRING,
                PrefixedPrefixedFullNameEmbeddableInterface::EMBEDDED_PROP_FIRSTNAME   => MappingHelper::TYPE_STRING,
                PrefixedPrefixedFullNameEmbeddableInterface::EMBEDDED_PROP_MIDDLENAMES => MappingHelper::TYPE_JSON,
                PrefixedPrefixedFullNameEmbeddableInterface::EMBEDDED_PROP_LASTNAME    => MappingHelper::TYPE_STRING,
                PrefixedPrefixedFullNameEmbeddableInterface::EMBEDDED_PROP_SUFFIX      => MappingHelper::TYPE_STRING,
            ],
            $builder
        );
    }
}
