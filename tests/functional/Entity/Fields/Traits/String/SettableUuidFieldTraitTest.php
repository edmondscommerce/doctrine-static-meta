<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String;

use EdmondsCommerce\DoctrineStaticMeta\AbstractIntegrationTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\String\SettableUuidFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\AbstractFieldTraitFunctionalTest;

class SettableUuidFieldTraitTest extends AbstractFieldTraitFunctionalTest
{
    public const    WORK_DIR           = AbstractIntegrationTest::VAR_PATH .
                                         '/' .
                                         self::TEST_TYPE .
                                         '/SettableUuidFieldTraitTest/';
    protected const TEST_FIELD_FQN     = SettableUuidFieldTrait::class;
    protected const TEST_FIELD_PROP    = SettableUuidFieldInterface::PROP_SETTABLE_UUID;
    protected const TEST_FIELD_DEFAULT = SettableUuidFieldInterface::DEFAULT_SETTABLE_UUID;
}
