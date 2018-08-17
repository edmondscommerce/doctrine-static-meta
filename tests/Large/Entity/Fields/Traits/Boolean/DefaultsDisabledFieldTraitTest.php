<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\Entity\Fields\Traits\Boolean;

use EdmondsCommerce\DoctrineStaticMeta\AbstractIntegrationTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\Boolean\DefaultsDisabledFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\AbstractFieldTraitFunctionalTest;

class DefaultsDisabledFieldTraitTest extends AbstractFieldTraitFunctionalTest
{
    public const    WORK_DIR           = AbstractIntegrationTest::VAR_PATH .
                                         '/' .
                                         self::TEST_TYPE .
                                         '/DefaultsDisabledFieldTraitTest/';
    protected const TEST_FIELD_FQN     = DefaultsDisabledFieldTrait::class;
    protected const TEST_FIELD_PROP    = DefaultsDisabledFieldInterface::PROP_DEFAULTS_DISABLED;
    protected const TEST_FIELD_DEFAULT = DefaultsDisabledFieldInterface::DEFAULT_DEFAULTS_DISABLED;
}
