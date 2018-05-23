<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\Attribute;

use EdmondsCommerce\DoctrineStaticMeta\AbstractIntegrationTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\Attribute\IpAddressFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\AbstractFieldTraitFunctionalTest;

class IpAddressFieldTraitTest extends AbstractFieldTraitFunctionalTest
{
    public const    WORK_DIR        = AbstractIntegrationTest::VAR_PATH.'/'.self::TEST_TYPE.'/IpAddressFieldTraitTest/';
    protected const TEST_FIELD_FQN  = IpAddressFieldTrait::class;
    protected const TEST_FIELD_PROP = IpAddressFieldInterface::PROP_IP_ADDRESS;
}
