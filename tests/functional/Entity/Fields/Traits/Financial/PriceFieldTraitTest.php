<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\Flag;

use EdmondsCommerce\DoctrineStaticMeta\AbstractIntegrationTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\Financial\PriceFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\AbstractFieldTraitFunctionalTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\Financial\PriceFieldTrait;

class PriceFieldTraitTest extends AbstractFieldTraitFunctionalTest
{
    public const    WORK_DIR           = AbstractIntegrationTest::VAR_PATH.'/'.self::TEST_TYPE.'/PriceFieldTraitTest/';
    protected const TEST_FIELD_FQN     = PriceFieldTrait::class;
    protected const TEST_FIELD_PROP    = PriceFieldInterface::PROP_PRICE;
    protected const TEST_FIELD_DEFAULT = PriceFieldInterface::DEFAULT_PRICE;
}
