<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Assets;

use Doctrine\ORM\Mapping\ClassMetadata;
use EdmondsCommerce\DoctrineStaticMeta\Entity as DSM;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Traits\ImplementNotifyChangeTrackingPolicy;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Traits\UsesPHPMetaDataTrait;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Traits\ValidatedEntityTrait;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * Class MockEntityFactory
 *
 * @package EdmondsCommerce\DoctrineStaticMeta\Tests\Assets
 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class MockEntityFactory
{
    public static function createMockEntity(): EntityInterface
    {
        return new class () implements EntityInterface
        {
            use ImplementNotifyChangeTrackingPolicy;
            use UsesPHPMetaDataTrait;
            use ValidatedEntityTrait;
            use DSM\Traits\AlwaysValidTrait;

            public function __construct()
            {
                self::getDoctrineStaticMeta()->setMetaData(new ClassMetadata('anon'));
            }

            public function getId(): UuidInterface
            {
                return Uuid::uuid1();
            }

            public function jsonSerialize()
            {
                return '';
            }
        };
    }
}
