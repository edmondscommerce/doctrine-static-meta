<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\Entity\Fields\Traits\Boolean;

use EdmondsCommerce\DoctrineStaticMeta\AbstractIntegrationTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\Boolean\DefaultsEnabledFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\AbstractFieldTraitFunctionalTest;

class DefaultsEnabledFieldTraitTest extends AbstractFieldTraitFunctionalTest
{
    public const    WORK_DIR           = AbstractIntegrationTest::VAR_PATH .
                                         '/' .
                                         self::TEST_TYPE .
                                         '/DefaultsEnabledFieldTraitTest/';
    protected const TEST_FIELD_FQN     = DefaultsEnabledFieldTrait::class;
    protected const TEST_FIELD_PROP    = DefaultsEnabledFieldInterface::PROP_DEFAULTS_ENABLED;
    protected const TEST_FIELD_DEFAULT = DefaultsEnabledFieldInterface::DEFAULT_DEFAULTS_ENABLED;
}
