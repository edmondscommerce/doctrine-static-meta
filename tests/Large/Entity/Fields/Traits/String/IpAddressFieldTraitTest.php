<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\Entity\Fields\Traits\String;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\String\IpAddressFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String\IpAddressFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Large\Entity\Fields\Traits\AbstractFieldTraitLargeTest;

/**
 * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String\IpAddressFieldTrait
 */
class IpAddressFieldTraitTest extends AbstractFieldTraitLargeTest
{
    public const    WORK_DIR           = AbstractTest::VAR_PATH .
                                         '/' .
                                         self::TEST_TYPE_LARGE .
                                         '/IpAddressFieldTraitTest/';
    protected const TEST_FIELD_FQN     = IpAddressFieldTrait::class;
    protected const TEST_FIELD_PROP    = IpAddressFieldInterface::PROP_IP_ADDRESS;
    protected const TEST_FIELD_DEFAULT = IpAddressFieldInterface::DEFAULT_IP_ADDRESS;

}
