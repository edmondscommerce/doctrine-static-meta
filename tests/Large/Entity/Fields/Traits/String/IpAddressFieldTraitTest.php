<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\Entity\Fields\Traits\String;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\String\IpAddressFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String\IpAddressFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Large\Entity\Fields\Traits\AbstractFieldTraitTest;

/**
 * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String\IpAddressFieldTrait
 */
class IpAddressFieldTraitTest extends AbstractFieldTraitTest
{
    public const    WORK_DIR           = AbstractTest::VAR_PATH .
                                         '/' .
                                         self::TEST_TYPE_LARGE .
                                         '/IpAddressFieldTraitTest/';
    protected const TEST_FIELD_FQN     = IpAddressFieldTrait::class;
    protected const TEST_FIELD_PROP    = IpAddressFieldInterface::PROP_IP_ADDRESS;
    protected const TEST_FIELD_DEFAULT = IpAddressFieldInterface::DEFAULT_IP_ADDRESS;
    protected const VALID_VALUES = [
        '192.168.1.1',
        '127.0.0.1',
        '1.1.1.1',
        '2001:0db8:85a3:0000:0000:8a2e:0370:7334',
        '::1',
        '::'

    ];
    protected const INVALID_VALUES = [
        'cheese',
        '192.168'
    ];
 }
