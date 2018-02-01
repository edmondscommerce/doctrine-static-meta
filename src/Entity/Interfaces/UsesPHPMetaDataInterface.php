<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces;

use Doctrine\ORM\Mapping\ClassMetadata;

interface UsesPHPMetaDataInterface
{
    /**
     * Protected static methods starting with this prefix will be used to load property meta data
     */
    const METHOD_PREFIX_GET_PROPERTY_META = 'getPropertyMetaFor';

    /**
     * private methods beginning with this will be run at construction time to do things like set up ArrayCollection
     * properties
     *
     * @var string
     */
    const METHOD_PREFIX_INIT = 'init';

    public static function loadMetaData(ClassMetadata $metadata);

    public static function getPlural(): string;

    public static function getSingular(): string;

    public static function getIdField(): string;
}
