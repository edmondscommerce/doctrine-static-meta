<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Interfaces\Objects\Identity;

interface FullNameEmbeddableInterface
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
     * @return FullNameEmbeddableInterface
     */
    public function setTitle(string $title): FullNameEmbeddableInterface;


    /**
     * @return string
     */
    public function getFirstName(): string;

    /**
     * @param string $firstName
     *
     * @return FullNameEmbeddableInterface
     */
    public function setFirstName(string $firstName): FullNameEmbeddableInterface;

    /**
     * @return array
     */
    public function getMiddleNames(): array;

    /**
     * @param array $middleNames
     *
     * @return FullNameEmbeddableInterface
     */
    public function setMiddleNames(array $middleNames): FullNameEmbeddableInterface;

    /**
     * @return string
     */
    public function getLastName(): string;

    /**
     * @param string $lastName
     *
     * @return FullNameEmbeddableInterface
     */
    public function setLastName(string $lastName): FullNameEmbeddableInterface;

    /**
     * @return string
     */
    public function getSuffix(): string;

    /**
     * @param string $suffix
     *
     * @return FullNameEmbeddableInterface
     */
    public function setSuffix(string $suffix): FullNameEmbeddableInterface;
}
