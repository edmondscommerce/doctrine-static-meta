<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Interfaces\Objects\Identity;

interface FullNameEmbeddableInterface
{
    public const EMBEDDED_PROP_TITLE       = 'title';
    public const EMBEDDED_PROP_FIRSTNAME   = 'firstName';
    public const EMBEDDED_PROP_MIDDLENAMES = 'middleNames';
    public const EMBEDDED_PROP_LASTNAME    = 'lastName';
    public const EMBEDDED_PROP_SUFFIX      = 'suffix';

    public const DEFAULTS = [
        self::EMBEDDED_PROP_TITLE       => '',
        self::EMBEDDED_PROP_FIRSTNAME   => '',
        self::EMBEDDED_PROP_MIDDLENAMES => [],
        self::EMBEDDED_PROP_LASTNAME    => '',
        self::EMBEDDED_PROP_SUFFIX      => '',
    ];


    /**
     * @return string
     */
    public function getTitle(): string;

    /**
     * @return string
     */
    public function getFirstName(): string;

    /**
     * @return array
     */
    public function getMiddleNames(): array;

    /**
     * @return string
     */
    public function getLastName(): string;

    /**
     * @return string
     */
    public function getSuffix(): string;

}
