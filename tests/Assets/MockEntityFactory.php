<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Assets;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\ClassMetadata;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Traits\ImplementNotifyChangeTrackingPolicy;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Traits\UsesPHPMetaDataTrait;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Traits\ValidatedEntityTrait;

class MockEntityFactory
{
    public static function createMockEntity(): EntityInterface
    {
        return new class() implements EntityInterface
        {
            use ImplementNotifyChangeTrackingPolicy,
                UsesPHPMetaDataTrait,
                ValidatedEntityTrait;

            public function __construct()
            {
                self::getDoctrineStaticMeta()->setMetaData(new ClassMetadata('anon'));
            }

            protected static function setCustomRepositoryClass(ClassMetadataBuilder $builder)
            {
            }

            public function getId()
            {
                return 1;
            }
        };
    }
}
