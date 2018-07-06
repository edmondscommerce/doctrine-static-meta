<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Interfaces\Objects\Identity;

interface PrefixedPrefixedFullNameEmbeddableInterface
{
    public const EMBEDDED_PROP_TITLE       = 'title';
    public const EMBEDDED_PROP_FIRSTNAME   = 'firstName';
    public const EMBEDDED_PROP_MIDDLENAMES = 'middleNames';
    public const EMBEDDED_PROP_LASTNAME    = 'lastName';
    public const EMBEDDED_PROP_SUFFIX      = 'suffix';

    /**
     * @return string
     */
    public function getTitle(): string;

    /**
     * @param string $title
     *
     * @return PrefixedPrefixedFullNameEmbeddableInterface
     */
    public function setTitle(string $title): PrefixedPrefixedFullNameEmbeddableInterface;


    /**
     * @return string
     */
    public function getFirstName(): string;

    /**
     * @param string $firstName
     *
     * @return PrefixedPrefixedFullNameEmbeddableInterface
     */
    public function setFirstName(string $firstName): PrefixedPrefixedFullNameEmbeddableInterface;

    /**
     * @return array
     */
    public function getMiddleNames(): array;

    /**
     * @param array $middleNames
     *
     * @return PrefixedPrefixedFullNameEmbeddableInterface
     */
    public function setMiddleNames(array $middleNames): PrefixedPrefixedFullNameEmbeddableInterface;

    /**
     * @return string
     */
    public function getLastName(): string;

    /**
     * @param string $lastName
     *
     * @return PrefixedPrefixedFullNameEmbeddableInterface
     */
    public function setLastName(string $lastName): PrefixedPrefixedFullNameEmbeddableInterface;

    /**
     * @return string
     */
    public function getSuffix(): string;

    /**
     * @param string $suffix
     *
     * @return PrefixedPrefixedFullNameEmbeddableInterface
     */
    public function setSuffix(string $suffix): PrefixedPrefixedFullNameEmbeddableInterface;
}
