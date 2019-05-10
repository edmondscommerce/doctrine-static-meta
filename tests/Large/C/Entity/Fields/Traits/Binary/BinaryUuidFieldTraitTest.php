<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\C\Entity\Fields\Traits\Binary;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\Binary\BinaryUuidFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\Binary\BinaryUuidFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Large\C\Entity\Fields\Traits\AbstractFieldTraitTest;

/**
 * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\Boolean\DefaultsNullFieldTrait
 */
class BinaryUuidFieldTraitTest extends AbstractFieldTraitTest
{
    public const    WORK_DIR           = AbstractTest::VAR_PATH .
                                         '/' .
                                         self::TEST_TYPE_LARGE .
                                         '/BinaryUuidFieldTraitTest/';
    protected const TEST_FIELD_FQN     = BinaryUuidFieldTrait::class;
    protected const TEST_FIELD_PROP    = BinaryUuidFieldInterface::PROP_BINARY_UUID;
    protected const TEST_FIELD_DEFAULT = BinaryUuidFieldInterface::DEFAULT_BINARY_UUID;
    protected const VALIDATES          = false;
}
