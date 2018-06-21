<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String;

use EdmondsCommerce\DoctrineStaticMeta\AbstractIntegrationTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\AbstractFieldTraitFunctionalTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\String\IpAddressFieldInterface;

class IpAddressFieldTraitTest extends AbstractFieldTraitFunctionalTest
{
    public const WORK_DIR = AbstractIntegrationTest::VAR_PATH.'/'.self::TEST_TYPE.'/IpAddressFieldTraitTest/';
    protected const TEST_FIELD_FQN =   IpAddressFieldTrait::class;
    protected const TEST_FIELD_PROP =  IpAddressFieldInterface::PROP_IP_ADDRESS;
    protected const TEST_FIELD_DEFAULT = IpAddressFieldInterface::DEFAULT_IP_ADDRESS;
}
