<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces;

use Doctrine\ORM\Mapping\ClassMetadata;

interface UsesPHPMetaData
{
    public static function loadMetaData(ClassMetadata $metadata);

    public static function getPlural(): string;

    public static function getSingular(): string;

    public static function getIdField(): string;
}
