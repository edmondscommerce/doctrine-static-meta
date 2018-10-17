<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Assets;

use Doctrine\ORM\Mapping\ClassMetadata;
use EdmondsCommerce\DoctrineStaticMeta\Entity as DSM;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Traits\ImplementNotifyChangeTrackingPolicy;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Traits\UsesPHPMetaDataTrait;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Traits\ValidatedEntityTrait;

/**
 * Class MockEntityFactory
 *
 * @package EdmondsCommerce\DoctrineStaticMeta\Tests\Assets
 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
 */
class MockEntityFactory
{
    public static function createMockEntity(): EntityInterface
    {
        return new class() implements EntityInterface
        {
            use ImplementNotifyChangeTrackingPolicy,
                UsesPHPMetaDataTrait,
                ValidatedEntityTrait,
                DSM\Traits\AlwaysValidTrait;

            public function __construct()
            {
                self::getDoctrineStaticMeta()->setMetaData(new ClassMetadata('anon'));
            }

            public function getId()
            {
                return 1;
            }
        };
    }
}
