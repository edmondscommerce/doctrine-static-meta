<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Interfaces\Objects\Identity;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Interfaces\Objects\AbstractEmbeddableObjectInterface;

interface FullNameEmbeddableInterface extends AbstractEmbeddableObjectInterface
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

    public function getTitle(): string;

    public function getFirstName(): string;

    public function getMiddleNames(): array;

    public function getLastName(): string;

    public function getSuffix(): string;

    public function getFormatted(): string;

}
