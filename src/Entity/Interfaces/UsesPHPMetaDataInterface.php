<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces;

use Doctrine\ORM\Mapping\ClassMetadata as DoctrineClassMetaData;

interface UsesPHPMetaDataInterface
{
    /**
     * Protected static methods starting with this prefix will be used to load property Doctrine meta data
     */
    public const METHOD_PREFIX_GET_PROPERTY_DOCTRINE_META = 'metaFor';

    /**
     * private methods beginning with this will be run at construction time to do things like set up ArrayCollection
     * properties
     *
     * @var string
     */
    public const METHOD_PREFIX_INIT = 'init';

    public static function loadMetadata(DoctrineClassMetaData $metadata): void;

    public static function getIdField(): string;
}
