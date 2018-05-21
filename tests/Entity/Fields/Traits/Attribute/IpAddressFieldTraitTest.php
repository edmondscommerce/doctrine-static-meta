<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\Attribute;

use EdmondsCommerce\DoctrineStaticMeta\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\Attribute\IpAddressFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\AbstractFieldTraitTest;

class IpAddressFieldTraitTest extends AbstractFieldTraitTest
{
    public const    WORK_DIR        = AbstractTest::VAR_PATH.'/IpAddressFieldTraitTest/';
    protected const TEST_FIELD_FQN  = IpAddressFieldTrait::class;
    protected const TEST_FIELD_PROP = IpAddressFieldInterface::PROP_IP_ADDRESS;
}
